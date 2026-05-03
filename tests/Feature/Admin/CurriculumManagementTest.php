<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_curriculum_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.curriculum'));

        $response->assertOk();
        $response->assertSee('Curriculum Management');
    }

    public function test_non_admin_users_cannot_access_the_curriculum_management_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('admin.curriculum'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_class_subject_and_topic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.class-rooms.store'), [
                'name' => 'History',
                'type' => 'optional',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $classRoom = ClassRoom::where('name', 'History')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.subjects.store'), [
                'class_room_id' => $classRoom->id,
                'name' => 'World History',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $subject = Subject::where('name', 'World History')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.topics.store'), [
                'subject_id' => $subject->id,
                'title' => 'Ancient Civilizations',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $this->assertDatabaseHas('class_rooms', [
            'name' => 'History',
            'type' => 'optional',
        ]);

        $this->assertDatabaseHas('subjects', [
            'class_room_id' => $classRoom->id,
            'name' => 'World History',
        ]);

        $this->assertDatabaseHas('topics', [
            'subject_id' => $subject->id,
            'title' => 'Ancient Civilizations',
        ]);
    }

    public function test_admin_can_update_curriculum_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'compulsory',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Physics',
        ]);
        $topic = Topic::create([
            'subject_id' => $subject->id,
            'title' => 'Motion',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.class-rooms.update', $classRoom), [
                'name' => 'Advanced Science',
                'type' => 'optional',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $this->actingAs($admin)
            ->patch(route('admin.subjects.update', $subject), [
                'class_room_id' => $classRoom->id,
                'name' => 'Applied Physics',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $this->actingAs($admin)
            ->patch(route('admin.topics.update', $topic), [
                'subject_id' => $subject->id,
                'title' => 'Forces and Motion',
            ])
            ->assertRedirect(route('admin.curriculum'));

        $this->assertDatabaseHas('class_rooms', [
            'id' => $classRoom->id,
            'name' => 'Advanced Science',
            'type' => 'optional',
        ]);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Applied Physics',
        ]);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'title' => 'Forces and Motion',
        ]);
    }

    public function test_admin_can_delete_curriculum_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $classRoom = ClassRoom::create([
            'name' => 'Commerce',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Accounting',
        ]);
        $topic = Topic::create([
            'subject_id' => $subject->id,
            'title' => 'Ledger Basics',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.topics.destroy', $topic))
            ->assertRedirect(route('admin.curriculum'));

        $this->actingAs($admin)
            ->delete(route('admin.subjects.destroy', $subject))
            ->assertRedirect(route('admin.curriculum'));

        $this->actingAs($admin)
            ->delete(route('admin.class-rooms.destroy', $classRoom))
            ->assertRedirect(route('admin.curriculum'));

        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseMissing('class_rooms', ['id' => $classRoom->id]);
    }
}
