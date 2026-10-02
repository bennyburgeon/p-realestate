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

<a href="{{ route('properties.show', $property) }}" class="group relative flex flex-col overflow-hidden rounded-2xl bg-neutral-900">
    <div class="relative aspect-[4/5] overflow-hidden sm:aspect-[4/3]">
        <img src="{{ $image }}" alt="{{ $property->title }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.03]">

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-neutral-900/90 via-neutral-900/10 to-transparent"></div>

        <div class="absolute left-4 top-4 flex flex-wrap gap-1.5">
            <span class="rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-primary-800 backdrop-blur">
                For {{ ucfirst($property->listing_type) }}
            </span>
            @if ($property->is_featured)
                <span class="rounded-full bg-secondary-500 px-2.5 py-1 text-[11px] font-bold text-primary-900">Featured</span>
            @endif
        </div>

        @auth
            <button type="button"
                    onclick="event.preventDefault(); this.closest('a').querySelector('form').requestSubmit();"
                    class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-rose-500 shadow transition hover:scale-110 hover:bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="{{ $isFavourited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.8C.8 7.9 2.6 5 5.6 5c1.7 0 3 .9 3.9 2.2C10.4 5.9 11.7 5 13.4 5c3 0 4.8 2.9 3.1 6.2C14 15.65 12 20 12 20Z" />
                </svg>
            </button>
            <form method="POST" action="{{ route('favourites.toggle', $property) }}" class="hidden">
                @csrf
            </form>
        @endauth

        <div class="absolute inset-x-0 bottom-0 p-4">
            <p class="font-display text-xl font-bold text-white">{{ $priceLabel }}</p>
            <h3 class="mt-0.5 line-clamp-1 text-sm font-semibold text-white/90">{{ $property->title }}</h3>
            <p class="mt-1 flex items-center gap-1 text-xs text-white/70">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Z" />
                    <circle cx="12" cy="10" r="2.5" />
                </svg>
                <span class="line-clamp-1">{{ $property->location?->displayName() }}</span>
            </p>

            <div class="mt-0 grid max-h-0 grid-rows-[0fr] overflow-hidden text-xs font-medium text-white/80 transition-all duration-300 ease-out group-hover:mt-3 group-hover:grid-rows-[1fr] group-hover:max-h-20">
                <div class="flex items-center gap-3 overflow-hidden border-t border-white/15 pt-3">
                    @if ($property->bedrooms)
                        <span>{{ $property->bedrooms }} Bed</span>
                    @endif
                    @if ($property->bathrooms)
                        <span>{{ $property->bathrooms }} Bath</span>
                    @endif
                    @if ($property->built_up_area)
                        <span>{{ number_format((float) $property->built_up_area) }} {{ $property->area_unit }}</span>
                    @endif
                    <span class="ml-auto inline-flex items-center gap-1 font-bold text-secondary-400">
                        View
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
</a>
