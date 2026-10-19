<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_users_are_redirected_from_the_dashboard_to_their_tickets(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('tickets.index'));
    }

    public function test_technicians_and_admins_can_visit_the_dashboard(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertOk();
    }
}
