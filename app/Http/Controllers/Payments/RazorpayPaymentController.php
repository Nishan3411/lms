<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\PaymentAttempt;
use App\Models\StudentFee;
use App\Models\User;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RazorpayPaymentController extends Controller
{
    public function createStudentOrder(
        Request $request,
        StudentFee $studentFee,
        RazorpayGateway $gateway
    ): JsonResponse {
        abort_unless($studentFee->user_id === $request->user()->id, 403);

        return $this->createOrder($request, $studentFee, $gateway);
    }

    public function verifyStudentOrder(
        Request $request,
        PaymentAttempt $paymentAttempt,
        RazorpayGateway $gateway
    ): JsonResponse {
        abort_unless($paymentAttempt->studentFee->user_id === $request->user()->id, 403);

        return $this->verifyOrder($request, $paymentAttempt, $gateway, route('student.fees.index'));
    }

    public function createParentOrder(
        Request $request,
        StudentFee $studentFee,
        RazorpayGateway $gateway
    ): JsonResponse {
        abort_unless($request->user()->children()->whereKey($studentFee->user_id)->exists(), 403);

        return $this->createOrder($request, $studentFee, $gateway);
    }

    public function verifyParentOrder(
        Request $request,
        PaymentAttempt $paymentAttempt,
        RazorpayGateway $gateway
    ): JsonResponse {
        abort_unless($request->user()->children()->whereKey($paymentAttempt->studentFee->user_id)->exists(), 403);

        return $this->verifyOrder($request, $paymentAttempt, $gateway, route('parent.fees.index'));
    }

    private function createOrder(
        Request $request,
        StudentFee $studentFee,
        RazorpayGateway $gateway
    ): JsonResponse {
        $studentFee->loadMissing(['student', 'fee.classRoom']);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = round((float) $validated['amount'], 2);

        if ($amount > $studentFee->pendingAmount()) {
            return response()->json([
                'message' => 'Payment amount cannot exceed pending dues.',
            ], 422);
        }

        $paymentAttempt = $gateway->createOrder($studentFee, $request->user(), $amount);

        return response()->json([
            'attempt_id' => $paymentAttempt->id,
            'order_id' => $paymentAttempt->provider_order_id,
            'amount' => (int) round($amount * 100),
            'currency' => $paymentAttempt->currency,
            'key' => config('services.razorpay.key_id'),
            'name' => config('app.name', 'LMS'),
            'description' => $studentFee->fee->title.' - '.$studentFee->student->name,
            'prefill' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
            'notes' => [
                'student_fee_id' => (string) $studentFee->id,
                'student_name' => $studentFee->student->name,
                'class_room' => $studentFee->fee->classRoom->name,
            ],
        ]);
    }

    private function verifyOrder(
        Request $request,
        PaymentAttempt $paymentAttempt,
        RazorpayGateway $gateway,
        string $redirectRoute
    ): JsonResponse {
        $validated = $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $gateway->verifyPayment(
            $paymentAttempt,
            $validated['razorpay_payment_id'],
            $validated['razorpay_order_id'],
            $validated['razorpay_signature']
        );

        return response()->json([
            'message' => 'Payment completed successfully.',
            'redirect_url' => $redirectRoute,
        ]);
    }
}
