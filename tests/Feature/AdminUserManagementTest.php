<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_users_can_not_open_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_assign_technician_role_and_change_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'role' => UserRole::Technician->value,
            'is_active' => '1',
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertSame(UserRole::Technician, $user->role);
        $this->assertTrue(Hash::check('NuevaClave123!', $user->password));
    }

    public function test_self_service_password_reset_routes_are_disabled(): void
    {
        $this->assertFalse(app('router')->has('password.request'));
        $this->assertFalse(app('router')->has('password.update'));
    }
}
