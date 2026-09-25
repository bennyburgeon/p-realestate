@props(['property'])

@php
    $isSale = $property->listing_type === \App\Models\Property::LISTING_SALE;
    $amount = $isSale ? $property->price : $property->rent_amount;
    $priceLabel = $amount
        ? '₹' . number_format((float) $amount, 0) . ($isSale ? '' : ' /mo')
        : 'Price on request';
    $image = $property->getFirstMediaUrl('images', 'thumb') ?: $property->getFirstMediaUrl('images') ?: asset('images/property-placeholder.svg');
    $isFavourited = auth()->check() && $property->relationLoaded('favouritedBy')
        ? $property->favouritedBy->contains('user_id', auth()->id())
        : false;
@endphp

<a href="{{ route('properties.show', $property) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        <img src="{{ $image }}" alt="{{ $property->title }}" loading="lazy"
             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">

        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            <span class="rounded-full bg-slate-900/80 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-white">
                For {{ ucfirst($property->listing_type) }}
            </span>
            @if ($property->is_featured)
                <span class="rounded-full bg-amber-500 px-2.5 py-1 text-xs font-bold text-white">Featured</span>
            @endif
        </div>

        @auth
            <button type="button"
                    onclick="event.preventDefault(); this.closest('a').querySelector('form').requestSubmit();"
                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-rose-500 shadow transition hover:bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="{{ $isFavourited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.8C.8 7.9 2.6 5 5.6 5c1.7 0 3 .9 3.9 2.2C10.4 5.9 11.7 5 13.4 5c3 0 4.8 2.9 3.1 6.2C14 15.65 12 20 12 20Z" />
                </svg>
            </button>
            <form method="POST" action="{{ route('favourites.toggle', $property) }}" class="hidden">
                @csrf
            </form>
        @endauth
    </div>

    <div class="flex flex-1 flex-col gap-2 p-4">
        <div class="flex items-center justify-between gap-2">
            <p class="text-lg font-extrabold text-slate-900">{{ $priceLabel }}</p>
            <x-status-badge :status="$property->status" />
        </div>

        <h3 class="line-clamp-1 font-semibold text-slate-800 group-hover:text-emerald-700">{{ $property->title }}</h3>

        <p class="flex items-center gap-1 text-sm text-slate-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Z" />
                <circle cx="12" cy="10" r="2.5" />
            </svg>
            <span class="line-clamp-1">{{ $property->location?->displayName() }}</span>
        </p>

        <div class="mt-auto flex items-center gap-3 border-t border-slate-100 pt-3 text-xs font-medium text-slate-500">
            @if ($property->bedrooms)
                <span>{{ $property->bedrooms }} Bed</span>
            @endif
            @if ($property->bathrooms)
                <span>{{ $property->bathrooms }} Bath</span>
            @endif
            @if ($property->built_up_area)
                <span>{{ number_format((float) $property->built_up_area) }} {{ $property->area_unit }}</span>
            @endif
        </div>
    </div>
</a>
