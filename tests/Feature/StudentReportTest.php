<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_personal_performance_report(): void
    {
        [$teacher, $student, $classRoom, $subject] = $this->createAcademicContext();

        AttendanceRecord::create([
            'attendance_id' => Attendance::create([
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'date' => '2026-04-10',
            ])->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);

        ExamResult::create([
            'exam_id' => Exam::create([
                'teacher_id' => $teacher->id,
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'title' => 'Physics Midterm',
                'exam_date' => '2026-04-12',
                'max_marks' => 100,
                'published_at' => now(),
            ])->id,
            'student_id' => $student->id,
            'marks_obtained' => 88,
            'grade' => 'A',
            'remarks' => 'Strong understanding.',
        ]);

        AssignmentSubmission::create([
            'assignment_id' => Assignment::create([
                'teacher_id' => $teacher->id,
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'title' => 'Wave Motion Task',
                'description' => 'Complete the worksheet.',
                'max_marks' => 20,
                'due_at' => '2026-04-11 17:00:00',
                'disk' => 'local',
            ])->id,
            'student_id' => $student->id,
            'status' => 'graded',
            'marks_obtained' => 17,
            'teacher_feedback' => 'Well explained.',
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('student.report.show'));

        $response->assertOk();
        $response->assertSee('My Performance Report');
        $response->assertSee($student->name);
        $response->assertSee('Physics Midterm');
        $response->assertSee('Wave Motion Task');
    }

    public function test_parent_can_view_reports_for_linked_children_only(): void
    {
        $parent = User::factory()->create(['role' => 'parent', 'name' => 'Parent Kiran']);
        [, $linkedStudent] = $this->createAcademicContext(studentName: 'Student Linked');
        [, $unlinkedStudent] = $this->createAcademicContext(studentName: 'Student Hidden');

        $parent->children()->syncWithoutDetaching([$linkedStudent->id]);

        $response = $this->actingAs($parent)->get(route('parent.student-reports.index'));

        $response->assertOk();
        $response->assertSee('Student Linked');
        $response->assertDontSee('Student Hidden');
    }

    public function test_teacher_can_view_assigned_student_report_but_not_unassigned_student_report(): void
    {
        [$teacher, $student, $classRoom] = $this->createAcademicContext();

        $response = $this->actingAs($teacher)->get(route('teacher.student-reports.index', [
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
        ]));

        $response->assertOk();
        $response->assertSee($student->name);

        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        [, $otherStudent, $otherClassRoom] = $this->createAcademicContext(teacher: $otherTeacher, studentName: 'Student Outsider');

        $forbidden = $this->actingAs($teacher)->get(route('teacher.student-reports.index', [
            'class_room_id' => $otherClassRoom->id,
            'student_id' => $otherStudent->id,
        ]));

        $forbidden->assertForbidden();
    }

    private function createAcademicContext(
        ?User $teacher = null,
        string $studentName = 'Student Report User'
    ): array {
        $teacher ??= User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student', 'name' => $studentName]);

        $classRoom = ClassRoom::create([
            'name' => 'Advanced Science',
            'type' => 'optional',
        ]);

        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Physics',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        return [$teacher, $student, $classRoom, $subject];
    }
}
