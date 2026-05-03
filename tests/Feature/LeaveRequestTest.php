<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_leave_request(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Computer Science',
            'type' => 'optional',
        ]);

        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $this->actingAs($student)->post(route('student.leave-requests.store'), [
            'class_room_id' => $classRoom->id,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-02',
            'reason' => 'Medical appointment.',
        ])->assertRedirect(route('student.leave-requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'requester_id' => $student->id,
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);
    }

    public function test_parent_can_submit_leave_request_for_linked_child(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);

        $parent->children()->syncWithoutDetaching([$student->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $this->actingAs($parent)->post(route('parent.leave-requests.store'), [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'start_date' => '2026-05-03',
            'end_date' => '2026-05-03',
            'reason' => 'Family function.',
        ])->assertRedirect(route('parent.leave-requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'requester_id' => $parent->id,
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
        ]);
    }

    public function test_parent_cannot_submit_leave_request_for_unlinked_student(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($parent)->post(route('parent.leave-requests.store'), [
            'student_id' => $student->id,
            'start_date' => '2026-05-03',
            'end_date' => '2026-05-03',
            'reason' => 'Family function.',
        ])->assertSessionHasErrors('student_id');

        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_teacher_can_review_leave_request_for_assigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $leaveRequest = LeaveRequest::create([
            'requester_id' => $student->id,
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'start_date' => '2026-05-04',
            'end_date' => '2026-05-04',
            'reason' => 'Sick leave.',
        ]);

        $this->actingAs($teacher)->patch(route('teacher.leave-requests.update', $leaveRequest), [
            'status' => 'approved',
            'review_note' => 'Approved. Take care.',
        ])->assertRedirect(route('teacher.leave-requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved',
            'reviewed_by' => $teacher->id,
            'review_note' => 'Approved. Take care.',
        ]);
    }

    public function test_teacher_cannot_review_leave_request_for_unassigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);

        $leaveRequest = LeaveRequest::create([
            'requester_id' => $student->id,
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'start_date' => '2026-05-04',
            'end_date' => '2026-05-04',
            'reason' => 'Sick leave.',
        ]);

        $this->actingAs($teacher)->patch(route('teacher.leave-requests.update', $leaveRequest), [
            'status' => 'approved',
        ])->assertForbidden();

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'pending',
            'reviewed_by' => null,
        ]);
    }

    public function test_unauthorized_users_cannot_access_admin_leave_requests(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('admin.leave-requests.index'))
            ->assertForbidden();
    }
}
