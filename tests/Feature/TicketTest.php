<?php

namespace Tests\Feature;

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
        $ticket = Ticket::factory()->for($owner)->create();

        $this->actingAs($otherUser)->get(route('tickets.show', $ticket))->assertForbidden();
        $this->actingAs(User::factory()->technician()->create())
            ->get(route('tickets.show', $ticket))
            ->assertOk();
    }
}
