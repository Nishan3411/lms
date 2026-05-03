<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_announcement_to_students(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'audience' => 'students',
            'title' => 'Holiday Notice',
            'body' => 'School will remain closed tomorrow.',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'created_by' => $admin->id,
            'audience' => 'students',
            'title' => 'Holiday Notice',
        ]);
    }

    public function test_teacher_can_publish_announcement_to_assigned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);

        $this->actingAs($teacher)->post(route('teacher.announcements.store'), [
            'class_room_id' => $classRoom->id,
            'audience' => 'students',
            'title' => 'Bring Notebook',
            'body' => 'Bring your algebra notebook tomorrow.',
        ])->assertRedirect(route('teacher.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'created_by' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'title' => 'Bring Notebook',
        ]);
    }

    public function test_student_sees_only_relevant_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);
        $otherClass = ClassRoom::create([
            'name' => 'Commerce',
            'type' => 'optional',
        ]);

        $classRoom->students()->syncWithoutDetaching([$student->id]);

        Announcement::create([
            'created_by' => $admin->id,
            'class_room_id' => $classRoom->id,
            'audience' => 'students',
            'title' => 'Science Lab',
            'body' => 'Lab tomorrow.',
            'published_at' => now(),
        ]);
        Announcement::create([
            'created_by' => $admin->id,
            'class_room_id' => $otherClass->id,
            'audience' => 'students',
            'title' => 'Commerce Notice',
            'body' => 'Not for this student.',
            'published_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.announcements.index'))
            ->assertOk()
            ->assertSee('Science Lab')
            ->assertDontSee('Commerce Notice');
    }

    public function test_parent_sees_announcements_for_linked_children(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);

        $classRoom->students()->syncWithoutDetaching([$student->id]);
        $parent->children()->syncWithoutDetaching([$student->id]);

        Announcement::create([
            'created_by' => $admin->id,
            'class_room_id' => $classRoom->id,
            'audience' => 'parents',
            'title' => 'Parent Meeting',
            'body' => 'Meeting on Friday.',
            'published_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get(route('parent.announcements.index'))
            ->assertOk()
            ->assertSee('Parent Meeting');
    }
}
