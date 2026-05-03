<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enroll_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Robotics',
            'type' => 'optional',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.enroll-student'), [
            'user_id' => $student->id,
            'class_room_id' => $classRoom->id,
        ]);

        $response->assertRedirect(route('admin.enrollment'));

        $this->assertTrue(
            $student->fresh()->enrolledClasses->contains($classRoom)
        );
    }

    public function test_admin_can_link_parent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)->post(route('admin.link-parent'), [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('admin.enrollment'));

        $this->assertTrue(
            $parent->fresh()->children->contains($student)
        );
    }

    public function test_admin_can_remove_student_from_optional_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Photography',
            'type' => 'optional',
        ]);

        $student->enrolledClasses()->syncWithoutDetaching([$classRoom->id]);

        $response = $this->actingAs($admin)->delete(route('admin.enroll-student.destroy'), [
            'user_id' => $student->id,
            'class_room_id' => $classRoom->id,
        ]);

        $response->assertRedirect(route('admin.enrollment'));

        $this->assertFalse(
            $student->fresh()->enrolledClasses->contains($classRoom)
        );
    }

    public function test_admin_cannot_remove_student_from_compulsory_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $compulsoryClass = ClassRoom::create([
            'name' => 'Core Mathematics',
            'type' => 'compulsory',
        ]);
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)->delete(route('admin.enroll-student.destroy'), [
            'user_id' => $student->id,
            'class_room_id' => $compulsoryClass->id,
        ]);

        $response->assertRedirect(route('admin.enrollment'));
        $response->assertSessionHas('error');

        $this->assertTrue(
            $student->fresh()->enrolledClasses->contains($compulsoryClass)
        );
    }

    public function test_admin_can_unlink_parent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);

        $parent->children()->syncWithoutDetaching([$student->id]);

        $response = $this->actingAs($admin)->delete(route('admin.link-parent.destroy'), [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('admin.enrollment'));

        $this->assertFalse(
            $parent->fresh()->children->contains($student)
        );
    }

    public function test_duplicate_entries_are_prevented(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parent = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Drama',
            'type' => 'optional',
        ]);

        $this->actingAs($admin)->post(route('admin.enroll-student'), [
            'user_id' => $student->id,
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($admin)->post(route('admin.enroll-student'), [
            'user_id' => $student->id,
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($admin)->post(route('admin.link-parent'), [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        $this->actingAs($admin)->post(route('admin.link-parent'), [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);

        $this->assertSame(1, DB::table('class_room_user')
            ->where('user_id', $student->id)
            ->where('class_room_id', $classRoom->id)
            ->count());

        $this->assertSame(1, DB::table('parent_student')
            ->where('parent_id', $parent->id)
            ->where('student_id', $student->id)
            ->count());
    }

    public function test_unauthorized_users_cannot_access_enrollment_management(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $parent = User::factory()->create(['role' => 'parent']);
        $classRoom = ClassRoom::create([
            'name' => 'Music',
            'type' => 'optional',
        ]);

        $this->actingAs($teacher)
            ->get(route('admin.enrollment'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('admin.enroll-student'), [
                'user_id' => $student->id,
                'class_room_id' => $classRoom->id,
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('admin.link-parent'), [
                'parent_id' => $parent->id,
                'student_id' => $student->id,
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->delete(route('admin.enroll-student.destroy'), [
                'user_id' => $student->id,
                'class_room_id' => $classRoom->id,
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->delete(route('admin.link-parent.destroy'), [
                'parent_id' => $parent->id,
                'student_id' => $student->id,
            ])
            ->assertForbidden();
    }
}
