<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TeacherAssignmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_teacher_to_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Economics',
            'type' => 'optional',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.assign-teacher.store'), [
            'teacher_id' => $teacher->id,
            'class_id' => $classRoom->id,
        ]);

        $response->assertRedirect(route('admin.assign-teacher'));
        $this->assertTrue($classRoom->fresh()->teachers->contains($teacher));
    }

    public function test_admin_can_view_teacher_assignment_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.assign-teacher'));

        $response->assertOk();
        $response->assertSee('Teacher Assignments');
        $response->assertSee('Assign Teacher to Class');
    }

    public function test_duplicate_teacher_assignment_is_prevented(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Geography',
            'type' => 'optional',
        ]);

        $this->actingAs($admin)->post(route('admin.assign-teacher.store'), [
            'teacher_id' => $teacher->id,
            'class_id' => $classRoom->id,
        ]);

        $this->actingAs($admin)->post(route('admin.assign-teacher.store'), [
            'teacher_id' => $teacher->id,
            'class_id' => $classRoom->id,
        ]);

        $this->assertSame(1, DB::table('class_room_teacher')
            ->where('teacher_id', $teacher->id)
            ->where('class_room_id', $classRoom->id)
            ->count());
    }

    public function test_admin_can_remove_teacher_from_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Literature',
            'type' => 'optional',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);

        $response = $this->actingAs($admin)->delete(route('admin.assign-teacher.destroy'), [
            'teacher_id' => $teacher->id,
            'class_id' => $classRoom->id,
        ]);

        $response->assertRedirect(route('admin.assign-teacher'));
        $this->assertFalse($classRoom->fresh()->teachers->contains($teacher));
    }

    public function test_unauthorized_users_cannot_manage_teacher_assignments(): void
    {
        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Philosophy',
            'type' => 'optional',
        ]);

        $this->actingAs($teacherUser)
            ->get(route('admin.assign-teacher'))
            ->assertForbidden();

        $this->actingAs($teacherUser)
            ->post(route('admin.assign-teacher.store'), [
                'teacher_id' => $teacher->id,
                'class_id' => $classRoom->id,
            ])
            ->assertForbidden();

        $this->actingAs($teacherUser)
            ->delete(route('admin.assign-teacher.destroy'), [
                'teacher_id' => $teacher->id,
                'class_id' => $classRoom->id,
            ])
            ->assertForbidden();
    }
}
