<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="flex min-h-svh items-start justify-center px-4 py-6 sm:items-center sm:px-6 sm:py-10">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xl shadow-zinc-950/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/30">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 border-b border-zinc-200 bg-white px-6 py-5 font-medium dark:border-zinc-800" wire:navigate>
                    <x-app-logo-icon class="block h-auto w-36 sm:w-40" />
                    <span class="text-sm font-semibold tracking-wide text-zinc-800">{{ config('app.name', 'Donatella TI') }}</span>
                </a>
                <div class="flex flex-col gap-6 px-5 py-6 sm:px-8 sm:py-8">
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
