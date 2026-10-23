<button
    type="button"
    class="appearance-toggle inline-flex size-10 items-center justify-center rounded-full transition duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-pineapple-orange focus:ring-offset-2 dark:focus:ring-offset-zinc-800"
    x-data
    x-on:click="$flux.appearance = $flux.dark ? 'light' : 'dark'"
    x-bind:aria-label="$flux.dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    x-bind:title="$flux.dark ? 'Modo claro' : 'Modo oscuro'"
>
    <svg x-cloak x-show="! $flux.dark" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3V2m0 20v-1m9-9h1M2 12h1m15.36-6.36.71-.71M4.93 19.07l.71-.71m12.72 0 .71.71M4.93 4.93l.71.71M17 12a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
    </svg>
    <svg x-cloak x-show="$flux.dark" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15.15A9 9 0 1 1 8.85 3 7 7 0 0 0 21 15.15Z" />
    </svg>
</button>
