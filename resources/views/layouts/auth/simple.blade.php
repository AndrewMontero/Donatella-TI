<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-brand-950 antialiased">
        <div aria-hidden="true" class="fixed inset-0 bg-[url('/images/donatella-login-background.png')] bg-cover bg-[position:32%_center] lg:bg-center"></div>
        <div aria-hidden="true" class="fixed inset-0 bg-brand-950/72 backdrop-blur-[1px] lg:bg-linear-to-r lg:from-brand-950/25 lg:via-brand-950/55 lg:to-brand-950/85"></div>

        <div class="relative z-10 flex min-h-svh items-start justify-center px-4 py-6 sm:items-center sm:px-8 sm:py-10 lg:justify-end lg:px-16 xl:px-28">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-brand-700/60 bg-white/95 shadow-2xl shadow-brand-950/50 backdrop-blur-sm dark:bg-brand-950/94">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 border-b border-brand-200 bg-brand-50 px-6 py-5 font-medium dark:border-brand-800" wire:navigate>
                    <x-app-logo-icon class="block h-auto w-36 sm:w-40" />
                    <span class="text-sm font-semibold tracking-wide text-brand-900">{{ config('app.name', 'Donatella TI') }}</span>
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
