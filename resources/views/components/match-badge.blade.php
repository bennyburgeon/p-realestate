@props(['band'])

@php
    $styles = [
        'excellent' => ['label' => 'Excellent Match', 'class' => 'bg-emerald-600 text-white'],
        'good' => ['label' => 'Good Match', 'class' => 'bg-sky-600 text-white'],
        'possible' => ['label' => 'Possible Match', 'class' => 'bg-slate-500 text-white'],
    ];
    $style = $styles[$band] ?? $styles['possible'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold {$style['class']}"]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2 14.6 8.8 22 9.3 16.5 14 18.2 21 12 17.3 5.8 21 7.5 14 2 9.3 9.4 8.8Z" />
    </svg>
    {{ $style['label'] }}
</span>
