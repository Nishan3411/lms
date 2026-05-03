<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_assignment_for_assigned_class(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Algebra',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.assignments.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Linear Equations Homework',
            'description' => 'Solve the attached worksheet.',
            'max_marks' => 50,
            'due_at' => '2026-04-25 17:00:00',
            'attachment' => UploadedFile::fake()->create('worksheet.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('teacher.assignments.index'));

        $assignment = Assignment::where('title', 'Linear Equations Homework')->firstOrFail();

        $this->assertDatabaseHas('assignments', [
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'max_marks' => 50,
        ]);

        Storage::disk('local')->assertExists($assignment->attachment_path);
    }

    public function test_student_can_submit_assignment_and_teacher_can_grade_it(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Physics',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $assignment = Assignment::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Motion Assignment',
            'description' => 'Submit answers.',
            'max_marks' => 40,
            'due_at' => '2026-04-25 17:00:00',
            'disk' => 'local',
        ]);

        $submitResponse = $this->actingAs($student)->post(route('student.assignments.submit', $assignment), [
            'answer_text' => 'My answers are attached.',
            'submission_file' => UploadedFile::fake()->create('motion.pdf', 100, 'application/pdf'),
        ]);

        $submitResponse->assertRedirect(route('student.assignments.index'));

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        Storage::disk('local')->assertExists($submission->file_path);

        $gradeResponse = $this->actingAs($teacher)->patch(route('teacher.assignment-submissions.grade', $submission), [
            'marks_obtained' => 35,
            'teacher_feedback' => 'Good work.',
        ]);

        $gradeResponse->assertRedirect(route('teacher.assignments.index'));

        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'status' => 'graded',
            'marks_obtained' => '35.00',
            'teacher_feedback' => 'Good work.',
        ]);
    }

    public function test_teacher_cannot_create_assignment_for_unassigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);

        $response = $this->actingAs($teacher)->post(route('teacher.assignments.store'), [
            'class_room_id' => $classRoom->id,
            'title' => 'Essay',
            'max_marks' => 20,
            'due_at' => '2026-04-25 17:00:00',
        ]);

        $response->assertSessionHasErrors('class_room_id');
        $this->assertDatabaseMissing('assignments', [
            'title' => 'Essay',
        ]);
    }

    public function test_student_cannot_submit_assignment_for_unenrolled_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Computer',
            'type' => 'optional',
        ]);

        $assignment = Assignment::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'title' => 'Coding Task',
            'max_marks' => 30,
            'due_at' => '2026-04-25 17:00:00',
            'disk' => 'local',
        ]);

        $this->actingAs($student)->post(route('student.assignments.submit', $assignment), [
            'answer_text' => 'Attempted.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);
    }
}
