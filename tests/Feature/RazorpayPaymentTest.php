<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\PaymentAttempt;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RazorpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.razorpay.key_id', 'rzp_test_123');
        config()->set('services.razorpay.key_secret', 'secret_123');
        config()->set('services.razorpay.base_url', 'https://api.razorpay.com/v1');
    }

    public function test_student_can_create_razorpay_order_for_own_fee(): void
    {
        Http::fake([
            'https://api.razorpay.com/v1/orders' => Http::response([
                'id' => 'order_test_123',
            ], 200),
        ]);

        $studentFee = $this->createStudentFee();
        $student = $studentFee->student;

        $response = $this->actingAs($student)
            ->postJson(route('student.fees.razorpay.order', $studentFee), [
                'amount' => '500.00',
            ]);

        $response->assertOk()
            ->assertJson([
                'order_id' => 'order_test_123',
                'currency' => 'INR',
            ]);

        $this->assertDatabaseHas('payment_attempts', [
            'student_fee_id' => $studentFee->id,
            'initiated_by_id' => $student->id,
            'provider' => 'razorpay',
            'provider_order_id' => 'order_test_123',
            'amount' => '500.00',
            'status' => 'created',
        ]);
    }

    public function test_student_cannot_create_razorpay_order_for_other_students_fee(): void
    {
        Http::fake();

        $studentFee = $this->createStudentFee();
        $otherStudent = User::factory()->create(['role' => 'student']);

        $this->actingAs($otherStudent)
            ->postJson(route('student.fees.razorpay.order', $studentFee), [
                'amount' => '500.00',
            ])->assertForbidden();
    }

    public function test_student_payment_verification_records_payment_and_updates_fee(): void
    {
        $studentFee = $this->createStudentFee();
        $student = $studentFee->student;

        $attempt = PaymentAttempt::create([
            'student_fee_id' => $studentFee->id,
            'initiated_by_id' => $student->id,
            'provider' => 'razorpay',
            'provider_order_id' => 'order_verify_123',
            'receipt' => 'receipt_verify_123',
            'amount' => '500.00',
            'currency' => 'INR',
            'status' => 'created',
        ]);

        $paymentId = 'pay_verify_123';
        $signature = hash_hmac('sha256', 'order_verify_123|'.$paymentId, 'secret_123');

        $response = $this->actingAs($student)
            ->postJson(route('student.fees.razorpay.verify', $attempt), [
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id' => 'order_verify_123',
                'razorpay_signature' => $signature,
            ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Payment completed successfully.',
                'redirect_url' => route('student.fees.index'),
            ]);

        $this->assertDatabaseHas('payments', [
            'student_fee_id' => $studentFee->id,
            'amount' => '500.00',
            'payment_method' => 'Razorpay',
            'transaction_id' => $paymentId,
        ]);

        $this->assertDatabaseHas('student_fees', [
            'id' => $studentFee->id,
            'paid_amount' => '500.00',
            'status' => 'partial',
        ]);

        $this->assertDatabaseHas('payment_attempts', [
            'id' => $attempt->id,
            'provider_payment_id' => $paymentId,
            'status' => 'verified',
        ]);
    }

    public function test_parent_can_create_razorpay_order_for_linked_child_fee_only(): void
    {
        Http::fake([
            'https://api.razorpay.com/v1/orders' => Http::response([
                'id' => 'order_parent_123',
            ], 200),
        ]);

        $studentFee = $this->createStudentFee();
        $student = $studentFee->student;
        $parent = User::factory()->create(['role' => 'parent']);
        $parent->children()->attach($student->id);

        $this->actingAs($parent)
            ->postJson(route('parent.fees.razorpay.order', $studentFee), [
                'amount' => '250.00',
            ])->assertOk();

        $this->assertDatabaseHas('payment_attempts', [
            'student_fee_id' => $studentFee->id,
            'initiated_by_id' => $parent->id,
            'provider_order_id' => 'order_parent_123',
        ]);

        $otherStudentFee = $this->createStudentFee('Unlinked Child');

        $this->actingAs($parent)
            ->postJson(route('parent.fees.razorpay.order', $otherStudentFee), [
                'amount' => '250.00',
            ])->assertForbidden();
    }

    private function createStudentFee(string $className = 'Finance Class'): StudentFee
    {
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => $className,
            'type' => 'optional',
        ]);

        $fee = Fee::create([
            'class_room_id' => $classRoom->id,
            'title' => 'Semester Fee',
            'amount' => '1000.00',
            'due_date' => '2026-06-01',
        ]);

        return StudentFee::create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'total_amount' => '1000.00',
            'paid_amount' => '0.00',
            'status' => 'pending',
        ]);
    }
}
