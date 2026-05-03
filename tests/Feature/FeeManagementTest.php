<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_fee_and_assign_it_to_students_in_the_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentOne = User::factory()->create(['role' => 'student']);
        $studentTwo = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Finance Basics',
            'type' => 'optional',
        ]);

        $classRoom->students()->syncWithoutDetaching([$studentOne->id, $studentTwo->id]);

        $response = $this->actingAs($admin)->post(route('admin.fees.store'), [
            'class_room_id' => $classRoom->id,
            'title' => 'Tuition Fee',
            'amount' => '1500.00',
            'due_date' => '2026-05-15',
        ]);

        $response->assertRedirect(route('admin.fees.index'));

        $fee = Fee::where('title', 'Tuition Fee')->firstOrFail();

        $this->assertDatabaseHas('fees', [
            'class_room_id' => $classRoom->id,
            'title' => 'Tuition Fee',
            'amount' => '1500.00',
        ]);

        $this->assertDatabaseHas('student_fees', [
            'user_id' => $studentOne->id,
            'fee_id' => $fee->id,
            'total_amount' => '1500.00',
            'paid_amount' => '0.00',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('student_fees', [
            'user_id' => $studentTwo->id,
            'fee_id' => $fee->id,
            'total_amount' => '1500.00',
            'paid_amount' => '0.00',
            'status' => 'pending',
        ]);
    }

    public function test_payment_updates_student_fee_amounts_and_status_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Science Lab',
            'type' => 'optional',
        ]);
        $fee = Fee::create([
            'class_room_id' => $classRoom->id,
            'title' => 'Lab Fee',
            'amount' => '1000.00',
            'due_date' => '2026-06-01',
        ]);
        $studentFee = StudentFee::create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'total_amount' => '1000.00',
            'paid_amount' => '0.00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.fees.payments.store'), [
            'student_fee_id' => $studentFee->id,
            'amount' => '400.00',
            'payment_method' => 'Cash',
            'transaction_id' => 'TXN-001',
            'paid_at' => '2026-04-10',
        ])->assertRedirect(route('admin.fees.index'));

        $this->assertDatabaseHas('payments', [
            'student_fee_id' => $studentFee->id,
            'amount' => '400.00',
            'payment_method' => 'Cash',
            'transaction_id' => 'TXN-001',
        ]);

        $this->assertDatabaseHas('student_fees', [
            'id' => $studentFee->id,
            'paid_amount' => '400.00',
            'status' => 'partial',
        ]);

        $this->actingAs($admin)->post(route('admin.fees.payments.store'), [
            'student_fee_id' => $studentFee->id,
            'amount' => '600.00',
            'payment_method' => 'UPI',
            'transaction_id' => 'TXN-002',
            'paid_at' => '2026-04-11',
        ])->assertRedirect(route('admin.fees.index'));

        $this->assertDatabaseHas('student_fees', [
            'id' => $studentFee->id,
            'paid_amount' => '1000.00',
            'status' => 'paid',
        ]);
    }

    public function test_overpayment_is_blocked(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Commerce',
            'type' => 'optional',
        ]);
        $fee = Fee::create([
            'class_room_id' => $classRoom->id,
            'title' => 'Exam Fee',
            'amount' => '500.00',
            'due_date' => '2026-06-15',
        ]);
        $studentFee = StudentFee::create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'total_amount' => '500.00',
            'paid_amount' => '100.00',
            'status' => 'partial',
        ]);

        $response = $this->actingAs($admin)->from(route('admin.fees.index'))->post(route('admin.fees.payments.store'), [
            'student_fee_id' => $studentFee->id,
            'amount' => '450.00',
            'payment_method' => 'Bank Transfer',
            'transaction_id' => 'TXN-OVERPAY',
            'paid_at' => '2026-04-10',
        ]);

        $response->assertRedirect(route('admin.fees.index'));
        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('student_fees', [
            'id' => $studentFee->id,
            'paid_amount' => '100.00',
            'status' => 'partial',
        ]);
    }

    public function test_non_admin_users_cannot_manage_fees(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Accounts',
            'type' => 'optional',
        ]);

        $this->actingAs($teacher)
            ->get(route('admin.fees.index'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('admin.fees.store'), [
                'class_room_id' => $classRoom->id,
                'title' => 'Tuition Fee',
                'amount' => '1200.00',
                'due_date' => '2026-05-01',
            ])
            ->assertForbidden();
    }
}
