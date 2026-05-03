<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamResultWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_schedule_exam_enter_marks_and_publish_results(): void
    {
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

        $this->actingAs($teacher)->post(route('teacher.exams.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Mid Semester Exam',
            'exam_date' => '2026-05-10',
            'max_marks' => 100,
        ])->assertRedirect(route('teacher.exams.index'));

        $exam = Exam::where('title', 'Mid Semester Exam')->firstOrFail();

        $this->actingAs($teacher)->post(route('teacher.exams.results.store', $exam), [
            'results' => [
                [
                    'student_id' => $student->id,
                    'marks_obtained' => 87,
                    'remarks' => 'Strong performance.',
                ],
            ],
        ])->assertRedirect(route('teacher.exams.index'));

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'marks_obtained' => '87.00',
            'grade' => 'A',
        ]);

        $this->actingAs($teacher)
            ->patch(route('teacher.exams.publish', $exam))
            ->assertRedirect(route('teacher.exams.index'));

        $this->assertNotNull($exam->fresh()->published_at);
    }

    public function test_student_only_sees_published_results_for_enrolled_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Algebra',
        ]);

        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $publishedExam = Exam::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Published Exam',
            'exam_date' => '2026-05-10',
            'max_marks' => 100,
            'published_at' => now(),
        ]);
        $draftExam = Exam::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Draft Exam',
            'exam_date' => '2026-05-12',
            'max_marks' => 100,
        ]);

        ExamResult::create([
            'exam_id' => $publishedExam->id,
            'student_id' => $student->id,
            'marks_obtained' => 92,
            'grade' => 'A+',
        ]);
        ExamResult::create([
            'exam_id' => $draftExam->id,
            'student_id' => $student->id,
            'marks_obtained' => 50,
            'grade' => 'D',
        ]);

        $this->actingAs($student)
            ->get(route('student.results.index'))
            ->assertOk()
            ->assertSee('Published Exam')
            ->assertDontSee('Draft Exam');
    }

    public function test_teacher_cannot_schedule_exam_for_unassigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Grammar',
        ]);

        $this->actingAs($teacher)->post(route('teacher.exams.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Grammar Test',
            'exam_date' => '2026-05-10',
            'max_marks' => 50,
        ])->assertSessionHasErrors('class_room_id');

        $this->assertDatabaseMissing('exams', [
            'title' => 'Grammar Test',
        ]);
    }

    public function test_marks_cannot_exceed_exam_max_marks(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Computer',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Programming',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $exam = Exam::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'title' => 'Coding Test',
            'exam_date' => '2026-05-10',
            'max_marks' => 30,
        ]);

        $this->actingAs($teacher)->post(route('teacher.exams.results.store', $exam), [
            'results' => [
                [
                    'student_id' => $student->id,
                    'marks_obtained' => 35,
                    'remarks' => 'Too high.',
                ],
            ],
        ])->assertSessionHasErrors('results.0.marks_obtained');

        $this->assertDatabaseMissing('exam_results', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
        ]);
    }
}
