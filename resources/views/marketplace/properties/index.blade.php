@php
    $sortOptions = ['newest' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low'];
@endphp

<x-layouts.marketplace title="Search Properties">
    <div x-data="{ filtersOpen: false }" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">
                    {{ $properties->total() }} {{ Str::plural('property', $properties->total()) }} found
                </h1>
                @if (! empty(array_filter($filters)))
                    <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Clear all filters</a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button @click="filtersOpen = true" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4" />
                    </svg>
                    Filters
                </button>

                <form method="GET" class="hidden sm:block">
                    @foreach ($filters as $key => $value)
                        @if ($key !== 'sort' && $value !== null)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">
            {{-- Filters: persistent desktop sidebar --}}
            <aside class="hidden lg:block lg:rounded-2xl lg:border lg:border-slate-100 lg:bg-white lg:p-5 lg:shadow-sm">
                @include('marketplace.properties._filters', ['suffix' => 'desktop'])
            </aside>

            {{-- Filters: mobile drawer --}}
            <div x-show="filtersOpen" x-cloak x-transition
                 class="fixed inset-0 z-50 overflow-y-auto bg-white p-4 lg:hidden">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Filters</h2>
                    <button @click="filtersOpen = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @include('marketplace.properties._filters', ['suffix' => 'mobile'])
            </div>

            {{-- Results --}}
            <div>
                @if ($properties->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <h2 class="text-lg font-bold text-slate-900">No properties match your search</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                            Try widening your filters &mdash; or post a requirement and let owners and agents with a match come to you.
                        </p>
                        <a href="{{ route('requirements.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-amber-600">
                            Post Your Requirement Instead
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.marketplace>
