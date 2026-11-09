{{--
    Searchable select enhanced by the shared Select2 initialiser (resources/js/select2.js).

    Usage:
        <x-select id="city" name="city" placeholder="Any city">…<option>s…</x-select>
        <x-select wire:model.live="city_id" placeholder="Search for a city" accent="primary">…</x-select>
        <x-select name="ids[]" multiple placeholder="Choose areas" />
        <x-select name="location_id" :ajax-url="route('locations.options')" depends-on="#city" />

    The wrapper <div> keeps Select2's generated container as the select's last sibling so
    Livewire's DOM morphing can preserve it; margin utilities passed via `wrapper-class`.
--}}
@props([
    'placeholder' => null,
    'error' => null,
    'invalid' => null,
    'ajaxUrl' => null,
    'ajaxParams' => [],
    'dependsOn' => null,
    'dependsParam' => 'parent',
    'searchable' => true,
    'allowClear' => true,
    'accent' => null,
    'size' => null,
    'noResults' => null,
    'wrapperClass' => 'mt-1.5',
])

@php
    $isMultiple = (bool) $attributes->get('multiple', false);

    $errorKey = $error ?? (string) str((string) ($attributes->wire('model')->value() ?: $attributes->get('name', '')))
        ->replaceEnd('[]', '')
        ->replace(['[', ']'], ['.', '']);

    $hasError = $invalid ?? ($errorKey !== '' && isset($errors) && $errors->has($errorKey));
@endphp

<div {{ $attributes->only('wire:key')->merge(['class' => trim($wrapperClass)]) }}>
    <select
        data-searchable-select
        {{ $attributes->except(['wire:key', 'class'])->merge([
            'data-select2-placeholder' => $placeholder,
            'data-select2-ajax-url' => $ajaxUrl,
            'data-select2-params' => $ajaxParams ? json_encode($ajaxParams) : null,
            'data-select2-depends-on' => $dependsOn,
            'data-select2-depends-param' => $dependsOn ? $dependsParam : null,
            'data-select2-search' => $searchable ? null : 'false',
            'data-select2-allow-clear' => $allowClear ? null : 'false',
            'data-select2-accent' => $accent,
            'data-select2-size' => $size,
            'data-select2-no-results' => $noResults,
            'aria-invalid' => $hasError ? 'true' : null,
        ]) }}
        @class([
            'w-full rounded-lg focus:ring-1',
            $attributes->get('class'),
            'border-red-400 focus:border-red-500 focus:ring-red-500' => $hasError,
            'border-slate-200' => ! $hasError,
            'focus:border-primary-500 focus:ring-primary-500' => ! $hasError && $accent === 'primary',
            'focus:border-emerald-500 focus:ring-emerald-500' => ! $hasError && $accent !== 'primary',
        ])
    >
        @if ($placeholder !== null && ! $isMultiple)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
</div>
