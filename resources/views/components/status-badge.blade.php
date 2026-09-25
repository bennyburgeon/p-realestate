@props(['status'])

@php
    $colors = [
        'green' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'red' => 'bg-rose-100 text-rose-700',
        'blue' => 'bg-sky-100 text-sky-700',
        'gray' => 'bg-slate-100 text-slate-600',
    ];
    $classes = $colors[$status?->color] ?? $colors['gray'];
@endphp

@if ($status)
    <span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold $classes"]) }}>
        {{ $status->label }}
    </span>
@endif
