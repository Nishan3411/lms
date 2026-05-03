<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response->assertOk();
        $response->assertSee('User Management');
    }

    public function test_admin_can_create_users_of_any_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Principal Admin',
            'email' => 'principal@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'name' => 'Principal Admin',
            'email' => 'principal@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_update_existing_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher', 'email' => 'teacher1@example.com']);

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $teacher), [
            'name' => 'Updated Teacher',
            'email' => 'updated.teacher@example.com',
            'role' => 'teacher',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('admin.users'));

        $teacher->refresh();

        $this->assertSame('Updated Teacher', $teacher->name);
        $this->assertSame('updated.teacher@example.com', $teacher->email);
        $this->assertTrue(Hash::check('new-password', $teacher->password));
    }

    public function test_admin_can_delete_user_but_not_last_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $student))
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseMissing('users', ['id' => $student->id]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_users_cannot_manage_users(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $target = User::factory()->create(['role' => 'student']);

        $this->actingAs($teacher)
            ->get(route('admin.users'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('admin.users.store'), [
                'name' => 'Blocked User',
                'email' => 'blocked@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'student',
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->patch(route('admin.users.update', $target), [
                'name' => 'Blocked Update',
                'email' => 'blocked.update@example.com',
                'role' => 'student',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->delete(route('admin.users.destroy', $target))
            ->assertForbidden();
    }
}
