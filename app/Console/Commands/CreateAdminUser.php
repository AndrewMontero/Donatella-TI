<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-admin {email} {--name=Administrador}')]
#[Description('Create or promote the local administrator account')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->option('name');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! is_string($name)) {
            $this->error('Debe indicar un correo válido y un nombre para el administrador.');

            return self::FAILURE;
        }

        $email = mb_strtolower(trim($email));

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'role' => UserRole::Admin,
                'is_active' => true,
            ]);

            $this->info("Usuario promovido a administrador: {$user->email}");

            return self::SUCCESS;
        }

        $password = $this->secret('Contraseña del administrador (mínimo 8 caracteres)');
        $confirmation = $this->secret('Confirme la contraseña');

        if (! is_string($password) || strlen($password) < 8 || $password !== $confirmation) {
            $this->error('Las contraseñas deben coincidir y contener al menos 8 caracteres.');

            return self::FAILURE;
        }

        $user = User::create([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->info("Administrador listo: {$user->email}");

        return self::SUCCESS;
    }
}
