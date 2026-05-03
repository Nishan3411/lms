<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_mark_attendance_for_an_assigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $studentOne = User::factory()->create(['role' => 'student', 'name' => 'Student One']);
        $studentTwo = User::factory()->create(['role' => 'student', 'name' => 'Student Two']);
        $classRoom = ClassRoom::create([
            'name' => 'Physics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Mechanics',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$studentOne->id, $studentTwo->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => '2026-04-10',
            'records' => [
                ['student_id' => $studentOne->id, 'status' => 'present'],
                ['student_id' => $studentTwo->id, 'status' => 'late'],
            ],
        ]);

        $response->assertRedirect(route('teacher.attendance.index'));

        $attendance = Attendance::where('class_room_id', $classRoom->id)
            ->where('subject_id', $subject->id)
            ->whereDate('date', '2026-04-10')
            ->firstOrFail();

        $this->assertDatabaseHas('attendance_records', [
            'attendance_id' => $attendance->id,
            'student_id' => $studentOne->id,
            'status' => 'present',
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'attendance_id' => $attendance->id,
            'student_id' => $studentTwo->id,
            'status' => 'late',
        ]);
    }

    public function test_duplicate_attendance_for_the_same_class_and_date_is_prevented(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Chemistry',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Organic Chemistry',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        Attendance::create([
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => '2026-04-10',
        ]);

        $response = $this->actingAs($teacher)
            ->from(route('teacher.attendance.index'))
            ->post(route('teacher.attendance.store'), [
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'date' => '2026-04-10',
                'records' => [
                    ['student_id' => $student->id, 'status' => 'present'],
                ],
            ]);

        $response->assertRedirect(route('teacher.attendance.mark', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => '2026-04-10',
        ]));
        $response->assertSessionHasErrors('date');

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('attendance_records', 0);
    }

    public function test_students_can_view_their_marked_attendance_history(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Ravi Student']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Algebra',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => '2026-04-12',
            'records' => [
                ['student_id' => $student->id, 'status' => 'present'],
            ],
        ]);

        $student->refresh();

        $this->assertSame(1, $student->attendanceRecords()->count());

        $this->actingAs($student)
            ->get(route('student.attendance.index'))
            ->assertOk()
            ->assertSee('Algebra')
            ->assertSee('Mathematics')
            ->assertSee('present');
    }

    public function test_non_teachers_cannot_mark_attendance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'History',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Ancient History',
        ]);

        $classRoom->students()->syncWithoutDetaching([$student->id]);

        $this->actingAs($admin)
            ->get(route('teacher.attendance.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('teacher.attendance.store'), [
                'class_room_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'date' => '2026-04-10',
                'records' => [
                    ['student_id' => $student->id, 'status' => 'present'],
                ],
            ])
            ->assertForbidden();
    }
}
