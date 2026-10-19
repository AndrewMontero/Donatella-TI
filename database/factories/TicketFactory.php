<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => fake()->sentence(5),
            'category' => fake()->randomElement(['Hardware', 'Software', 'Red', 'Accesos', 'Correo', 'Impresoras', 'Otro']),
            'description' => fake()->paragraph(),
            'status' => TicketStatus::Pending,
            'requires_anydesk' => false,
            'anydesk_address' => null,
        ];
    }
}
