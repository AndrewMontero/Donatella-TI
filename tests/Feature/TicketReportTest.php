<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_support_staff_can_access_ticket_reports(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reports.tickets.index'))
            ->assertForbidden();

        $this->actingAs(User::factory()->technician()->create())
            ->get(route('reports.tickets.index'))
            ->assertOk()
            ->assertSee('Reporte mensual de tickets');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('reports.tickets.index'))
            ->assertOk();
    }

    public function test_support_staff_can_download_general_and_collaborator_reports(): void
    {
        $technician = User::factory()->technician()->create();
        $collaborator = User::factory()->create(['name' => 'Ana Reportes']);
        Ticket::factory()->for($collaborator)->create([
            'subject' => 'Ticket del reporte mensual',
            'created_at' => '2026-10-12 09:30:00',
        ]);

        $this->actingAs($technician)
            ->get(route('reports.tickets.pdf', ['month' => '2026-10']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($technician)
            ->get(route('reports.tickets.pdf', ['month' => '2026-10', 'user_id' => $collaborator->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_report_requires_a_valid_month_and_user(): void
    {
        $technician = User::factory()->technician()->create();

        $this->actingAs($technician)
            ->get(route('reports.tickets.pdf', ['month' => 'octubre', 'user_id' => 999]))
            ->assertSessionHasErrors(['month', 'user_id']);
    }
}
