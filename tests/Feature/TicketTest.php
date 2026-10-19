<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_ticket_with_anydesk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'No puedo abrir el sistema',
            'category' => 'Software',
            'description' => 'La aplicación muestra un error al iniciar.',
            'requires_anydesk' => '1',
            'anydesk_address' => '123 456 789',
        ]);

        $ticket = Ticket::firstOrFail();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertTrue($ticket->requires_anydesk);
        $this->assertSame('123 456 789', $ticket->anydesk_address);
        $this->assertSame($user->id, $ticket->user_id);
    }

    public function test_anydesk_address_is_required_when_requested(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'Necesito soporte remoto',
            'category' => 'Software',
            'description' => 'Detalle del problema.',
            'requires_anydesk' => '1',
        ])->assertSessionHasErrors('anydesk_address');

        $this->assertDatabaseEmpty('tickets');
    }

    public function test_users_only_see_their_own_tickets(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownTicket = Ticket::factory()->for($owner)->create([
            'subject' => 'Ticket visible del propietario',
        ]);
        $otherTicket = Ticket::factory()->for($otherUser)->create([
            'subject' => 'Ticket privado de otra persona',
        ]);

        $this->actingAs($owner)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ownTicket->subject)
            ->assertDontSee($otherTicket->subject)
            ->assertDontSee('Panel de control');

        $this->actingAs($otherUser)->get(route('tickets.show', $ownTicket))->assertForbidden();
        $this->actingAs(User::factory()->technician()->create())
            ->get(route('tickets.show', $ownTicket))
            ->assertOk();
    }

    public function test_technician_can_search_and_filter_tickets(): void
    {
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->create([
            'name' => 'María Solano',
            'email' => 'maria@example.com',
        ]);
        $matchingTicket = Ticket::factory()->for($requester)->create([
            'subject' => 'Problema con impresora de bodega',
            'category' => 'Impresoras',
            'status' => TicketStatus::InProgress,
        ]);
        $otherTicket = Ticket::factory()->create([
            'subject' => 'Acceso al correo',
            'category' => 'Correo',
            'status' => TicketStatus::New,
        ]);

        $this->actingAs($technician)
            ->get(route('tickets.index', [
                'q' => 'María',
                'status' => TicketStatus::InProgress->value,
                'category' => 'Impresoras',
            ]))
            ->assertOk()
            ->assertSee($matchingTicket->subject)
            ->assertDontSee($otherTicket->subject);
    }

    public function test_ticket_filters_do_not_expose_another_users_tickets(): void
    {
        $user = User::factory()->create();
        $otherTicket = Ticket::factory()->create([
            'subject' => 'Incidente privado identificable',
        ]);

        $this->actingAs($user)
            ->get(route('tickets.index', ['q' => (string) $otherTicket->id]))
            ->assertOk()
            ->assertDontSee($otherTicket->subject);
    }
}
