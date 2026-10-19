<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_audited_without_storing_the_password(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Ana López',
            'email' => 'ana@example.com',
            'password' => 'ClaveSegura123!',
            'password_confirmation' => 'ClaveSegura123!',
        ])->assertSessionHasNoErrors();

        $log = AuditLog::where('action', 'user.registered')->firstOrFail();

        $this->assertSame(['email' => 'ana@example.com'], $log->changes);
        $this->assertStringNotContainsString('ClaveSegura123!', json_encode($log->toArray()));
    }

    public function test_admin_user_changes_are_audited_without_storing_the_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'role' => UserRole::Technician->value,
            'is_active' => '1',
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
        ])->assertSessionHasNoErrors();

        $log = AuditLog::where('action', 'user.updated')->firstOrFail();

        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame(UserRole::Technician->value, $log->changes['role']['to']);
        $this->assertTrue($log->changes['password_changed']);
        $this->assertStringNotContainsString('NuevaClave123!', json_encode($log->toArray()));
    }

    public function test_ticket_status_changes_are_audited(): void
    {
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($technician)->put(route('tickets.update', $ticket), [
            'status' => TicketStatus::InProgress->value,
        ])->assertSessionHasNoErrors();

        $log = AuditLog::where('action', 'ticket.status_updated')->firstOrFail();

        $this->assertSame($technician->id, $log->actor_id);
        $this->assertSame(TicketStatus::InProgress->value, $log->changes['status']['to']);
    }

    public function test_regular_users_can_not_view_the_audit_log(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_the_audit_log(): void
    {
        $admin = User::factory()->admin()->create();
        AuditLog::record($admin, 'user.updated', $admin, ['password_changed' => true]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('Historial administrativo')
            ->assertSee('Usuario actualizado');
    }
}
