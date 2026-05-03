<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RazorpayGateway
{
    public function isConfigured(): bool
    {
        return filled(config('services.razorpay.key_id'))
            && filled(config('services.razorpay.key_secret'));
    }

    public function createOrder(StudentFee $studentFee, User $initiator, float $amount): PaymentAttempt
    {
        $this->ensureConfigured();

        $studentFee->loadMissing(['student', 'fee.classRoom']);

        $receipt = Str::limit(
            sprintf('sf-%d-u-%d-%s', $studentFee->id, $initiator->id, Str::lower(Str::random(20))),
            40,
            ''
        );

        $response = Http::withBasicAuth(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        )->acceptJson()->post($this->baseUrl().'/orders', [
            'amount' => (int) round($amount * 100),
            'currency' => 'INR',
            'receipt' => $receipt,
            'notes' => [
                'student_fee_id' => (string) $studentFee->id,
                'student_id' => (string) $studentFee->user_id,
                'initiated_by_id' => (string) $initiator->id,
            ],
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'payment' => $response->json('error.description')
                    ?? 'Unable to create Razorpay order right now. Please try again.',
            ]);
        }

        return PaymentAttempt::create([
            'student_fee_id' => $studentFee->id,
            'initiated_by_id' => $initiator->id,
            'provider' => 'razorpay',
            'provider_order_id' => $response->json('id'),
            'receipt' => $receipt,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'created',
        ]);
    }

    public function verifyPayment(
        PaymentAttempt $paymentAttempt,
        string $paymentId,
        string $orderId,
        string $signature
    ): Payment {
        $this->ensureConfigured();

        if ($paymentAttempt->provider_order_id !== $orderId) {
            throw ValidationException::withMessages([
                'payment' => 'The Razorpay order does not match this payment attempt.',
            ]);
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $paymentAttempt->provider_order_id.'|'.$paymentId,
            config('services.razorpay.key_secret')
        );

        if (! hash_equals($expectedSignature, $signature)) {
            $paymentAttempt->forceFill([
                'provider_payment_id' => $paymentId,
                'provider_signature' => $signature,
                'status' => 'failed',
            ])->save();

            throw ValidationException::withMessages([
                'payment' => 'Razorpay signature verification failed.',
            ]);
        }

        return DB::transaction(function () use ($paymentAttempt, $paymentId, $signature): Payment {
            $attempt = PaymentAttempt::query()
                ->lockForUpdate()
                ->findOrFail($paymentAttempt->id);

            $studentFee = StudentFee::query()
                ->lockForUpdate()
                ->findOrFail($attempt->student_fee_id);

            $existingPayment = Payment::query()
                ->where('transaction_id', $paymentId)
                ->first();

            if ($existingPayment) {
                if ($attempt->status !== 'verified') {
                    $attempt->forceFill([
                        'provider_payment_id' => $paymentId,
                        'provider_signature' => $signature,
                        'status' => 'verified',
                        'verified_at' => now(),
                    ])->save();
                }

                return $existingPayment;
            }

            if ((float) $attempt->amount > $studentFee->pendingAmount()) {
                $attempt->forceFill([
                    'provider_payment_id' => $paymentId,
                    'provider_signature' => $signature,
                    'status' => 'failed',
                ])->save();

                throw ValidationException::withMessages([
                    'payment' => 'This payment is larger than the remaining due amount.',
                ]);
            }

            $payment = Payment::create([
                'student_fee_id' => $studentFee->id,
                'amount' => $attempt->amount,
                'payment_method' => 'Razorpay',
                'transaction_id' => $paymentId,
                'paid_at' => now(),
            ]);

            $studentFee->paid_amount = (float) $studentFee->paid_amount + (float) $attempt->amount;
            $studentFee->syncStatus();
            $studentFee->save();

            $attempt->forceFill([
                'provider_payment_id' => $paymentId,
                'provider_signature' => $signature,
                'status' => 'verified',
                'verified_at' => now(),
            ])->save();

            return $payment;
        });
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw ValidationException::withMessages([
                'payment' => 'Razorpay is not configured yet. Add your Razorpay keys first.',
            ]);
        }
    }

    private function baseUrl(): string
    {
        return rtrim(config('services.razorpay.base_url', 'https://api.razorpay.com/v1'), '/');
    }
}
