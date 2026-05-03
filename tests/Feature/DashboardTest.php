<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_visit_the_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_authenticated_users_are_redirected_to_their_dashboard_from_home(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertRedirect(route('student.dashboard'));
    }
}
