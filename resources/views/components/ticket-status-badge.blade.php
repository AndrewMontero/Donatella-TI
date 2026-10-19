@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\TicketStatus::Pending => 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200',
        \App\Enums\TicketStatus::InProgress => 'border-blue-300 bg-blue-100 text-blue-800 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-200',
        \App\Enums\TicketStatus::Done => 'border-emerald-300 bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200',
    };
@endphp

<span {{ $attributes->class("inline-flex w-fit items-center rounded-full border px-3 py-1 text-xs font-semibold {$classes}") }}>
    {{ $status->label() }}
</span>
