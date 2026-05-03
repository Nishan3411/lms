<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_filtered_fee_report_and_view_documents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Semester 4',
            'type' => 'optional',
        ]);
        $fee = Fee::create([
            'class_room_id' => $classRoom->id,
            'title' => 'Semester 4 Fee',
            'amount' => 5000,
            'due_date' => now()->addMonth(),
        ]);
        $studentFee = StudentFee::create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'total_amount' => 5000,
            'paid_amount' => 2500,
            'status' => 'partial',
        ]);
        $payment = Payment::create([
            'student_fee_id' => $studentFee->id,
            'amount' => 2500,
            'payment_method' => 'Cash',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.fees.export', ['status' => 'partial']));

        $response->assertOk();
        $this->assertStringContainsString('Semester 4 Fee', $response->streamedContent());

        $this->actingAs($admin)
            ->get(route('admin.fees.invoices.show', $studentFee))
            ->assertOk()
            ->assertSee('LMS Fee Invoice');

        $this->actingAs($admin)
            ->get(route('admin.fees.payments.receipt', $payment))
            ->assertOk()
            ->assertSee('LMS Payment Receipt');
    }

    public function test_attendance_export_respects_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Physics',
        ]);
        $attendance = Attendance::create([
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => '2026-04-16',
        ]);
        $attendance->records()->create([
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.export', [
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'date' => '2026-04-16',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Physics', $content);
        $this->assertStringContainsString('present', $content);
    }

    public function test_students_cannot_view_other_students_fee_documents(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $otherStudent = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Commerce',
            'type' => 'optional',
        ]);
        $fee = Fee::create([
            'class_room_id' => $classRoom->id,
            'title' => 'Semester 4 Fee',
            'amount' => 5000,
            'due_date' => now()->addMonth(),
        ]);
        $studentFee = StudentFee::create([
            'user_id' => $otherStudent->id,
            'fee_id' => $fee->id,
            'total_amount' => 5000,
            'paid_amount' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->get(route('student.fees.invoices.show', $studentFee))
            ->assertForbidden();
    }
}
