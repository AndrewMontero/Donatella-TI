<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-start px-4 py-6 sm:justify-center sm:px-6 sm:py-10">
            <div class="flex w-full max-w-sm flex-col gap-3">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="mb-1 block w-32 max-w-full overflow-hidden rounded-lg bg-white sm:w-40">
                        <x-app-logo-icon class="block h-auto w-full" />
                    </span>
                    <span class="text-lg font-semibold">{{ config('app.name', 'Donatella TI') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
