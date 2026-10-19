<?php

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perfil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated)->save();

        Flux::toast(variant: 'success', text: 'Perfil actualizado.');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout heading="Perfil" subheading="Actualice su nombre y correo electrónico">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" label="Nombre" type="text" required autofocus autocomplete="name" />
            <flux:input wire:model="email" label="Correo electrónico" type="email" required autocomplete="email" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">Guardar</flux:button>
            </div>
        </form>

        <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
            Por seguridad, únicamente el administrador puede cambiar contraseñas y roles.
        </div>
    </x-pages::settings.layout>
</section>
