<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\ScheduleEntry;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_schedule_entry(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
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

        $this->actingAs($admin)->post(route('admin.timetable.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Monday',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'location' => 'Room 101',
        ])->assertRedirect(route('admin.timetable.index'));

        $this->assertDatabaseHas('schedule_entries', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Monday',
            'location' => 'Room 101',
        ]);
    }

    public function test_teacher_must_be_assigned_to_selected_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Physics',
        ]);

        $this->actingAs($admin)->post(route('admin.timetable.store'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Tuesday',
            'starts_at' => '10:00',
            'ends_at' => '11:00',
        ])->assertSessionHasErrors('teacher_id');

        $this->assertDatabaseCount('schedule_entries', 0);
    }

    public function test_teacher_and_student_can_view_relevant_timetable_entries(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Grammar',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $classRoom->students()->syncWithoutDetaching([$student->id]);

        ScheduleEntry::create([
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Wednesday',
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'location' => 'Room 202',
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.timetable.index'))
            ->assertOk()
            ->assertSee('Grammar')
            ->assertSee('Room 202');

        $this->actingAs($student)
            ->get(route('student.timetable.index'))
            ->assertOk()
            ->assertSee('Grammar')
            ->assertSee('Room 202');
    }
}
