@php
    $images = $property->getMedia('images');
    $isSale = $property->listing_type === \App\Models\Property::LISTING_SALE;
    $priceLabel = $isSale
        ? ($property->price ? '₹' . number_format((float) $property->price, 0) : 'Price on request')
        : ($property->rent_amount ? '₹' . number_format((float) $property->rent_amount, 0) . ' /month' : 'Rent on request');
    $isOwner = auth()->check() && auth()->id() === $property->user_id;
@endphp

<x-layouts.marketplace :title="$property->title" :description="Str::limit(strip_tags($property->description), 150)">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        <nav class="mb-4 flex items-center gap-1.5 text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-slate-600">Home</a> /
            <a href="{{ route('properties.index') }}" class="hover:text-slate-600">Properties</a> /
            <span class="text-slate-600">{{ Str::limit($property->title, 40) }}</span>
        </nav>

        {{-- Gallery --}}
        <div x-data="{ lightbox: false, active: 0, images: {{ $images->pluck('original_url')->values()->toJson() ?: '[]' }} }">
            <div class="grid grid-cols-4 gap-2 overflow-hidden rounded-2xl" style="grid-template-rows: repeat(2, minmax(0, 1fr));">
                @if ($images->isEmpty())
                    <img src="{{ asset('images/property-placeholder.svg') }}" alt="{{ $property->title }}" class="col-span-4 row-span-2 aspect-video w-full object-cover">
                @else
                    @foreach ($images->take(5) as $i => $media)
                        <button type="button" @click="lightbox = true; active = {{ $i }}"
                                class="{{ $i === 0 ? 'col-span-4 row-span-2 sm:col-span-2' : 'col-span-2 sm:col-span-1' }} aspect-video overflow-hidden bg-slate-100">
                            <img src="{{ $media->getUrl('large') ?: $media->getUrl() }}" alt="{{ $property->title }}" loading="lazy" class="h-full w-full object-cover transition hover:scale-105">
                        </button>
                    @endforeach
                @endif
            </div>

            {{-- Lightbox --}}
            <div x-show="lightbox" x-cloak x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-4"
                 @keydown.escape.window="lightbox = false">
                <button @click="lightbox = false" class="absolute right-5 top-5 text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                <button @click="active = (active - 1 + images.length) % images.length" class="absolute left-4 text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                </button>
                <img :src="images[active]" class="max-h-[85vh] max-w-4xl rounded-lg object-contain">
                <button @click="active = (active + 1) % images.length" class="absolute right-4 text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                </button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-3">
            {{-- Main --}}
            <div class="lg:col-span-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-slate-900 px-2.5 py-1 text-xs font-bold uppercase text-white">For {{ ucfirst($property->listing_type) }}</span>
                    <x-status-badge :status="$property->status" />
                    @if ($property->is_verified)
                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Verified
                        </span>
                    @endif
                </div>

                <h1 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $property->title }}</h1>
                <p class="mt-1 flex items-center gap-1.5 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" />
                    </svg>
                    {{ $property->address }}, {{ $property->location?->displayName() }}
                </p>

                <p class="mt-4 text-3xl font-extrabold text-emerald-700">{{ $priceLabel }}
                    @if ($property->is_negotiable)<span class="ml-1 text-sm font-medium text-slate-400">(Negotiable)</span>@endif
                </p>

                {{-- Key facts --}}
                <div class="mt-6 grid grid-cols-2 gap-4 rounded-2xl border border-slate-100 p-5 sm:grid-cols-4">
                    @foreach ([
                        ['Bedrooms', $property->bedrooms],
                        ['Bathrooms', $property->bathrooms],
                        ['Built-up Area', $property->built_up_area ? number_format((float) $property->built_up_area).' '.$property->area_unit : null],
                        ['Furnishing', $property->furnishing_status ? ucfirst(str_replace('_', ' ', $property->furnishing_status)) : null],
                        ['Floor', $property->floor_number ? "{$property->floor_number} of {$property->total_floors}" : null],
                        ['Facing', $property->facing ? ucfirst($property->facing) : null],
                        ['Possession', $property->possession_status ? ucfirst(str_replace('_', ' ', $property->possession_status)) : null],
                        ['Parking', $property->parking_details],
                    ] as [$label, $value])
                        @if ($value)
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-400">{{ $label }}</p>
                                <p class="mt-0.5 font-semibold text-slate-800">{{ $value }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if ($property->description)
                    <div class="mt-8">
                        <h2 class="text-lg font-bold text-slate-900">About this property</h2>
                        <p class="mt-2 whitespace-pre-line text-slate-600">{{ $property->description }}</p>
                    </div>
                @endif

                @if ($property->amenities->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="text-lg font-bold text-slate-900">Amenities</h2>
                        <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($property->amenities as $amenity)
                                <div class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                                    {{ $amenity->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($property->latitude && $property->longitude)
                    <div class="mt-8">
                        <h2 class="text-lg font-bold text-slate-900">Location</h2>
                        <div class="mt-3 overflow-hidden rounded-2xl border border-slate-100">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}&z=15&output=embed"
                                class="h-80 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                @endif

                @if ($isOwner && $matchingRequirements->isNotEmpty())
                    <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <h2 class="text-lg font-bold text-slate-900">Buyers/Tenants Looking For a Property Like This</h2>
                        <div class="mt-3 space-y-2">
                            @foreach ($matchingRequirements as $match)
                                <a href="{{ route('requirements.show', $match->requirement) }}" class="flex items-center justify-between rounded-xl bg-white p-3 shadow-sm">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $match->requirement->title }}</p>
                                        <p class="text-xs text-slate-400">Ref: {{ $match->requirement->reference_number }}</p>
                                    </div>
                                    <x-match-badge :band="$match->match_band" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                <div class="rounded-2xl border border-slate-100 p-5 shadow-sm" x-data="{ revealed: false }">
                    <p class="text-xs font-bold uppercase text-slate-400">Listed by</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $property->owner->name }}</p>

                    <div class="mt-4 space-y-2">
                        <button @click="revealed = true" x-show="!revealed"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                            Show Contact Number
                        </button>
                        <p x-show="revealed" x-cloak class="rounded-xl bg-emerald-50 py-2.5 text-center text-sm font-bold text-emerald-800">
                            {{ $property->contact_phone }}
                        </p>

                        <div class="grid grid-cols-2 gap-2">
                            @auth
                                <form method="POST" action="{{ route('favourites.toggle', $property) }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Save
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Save</a>
                            @endauth
                            <button type="button" onclick="navigator.share ? navigator.share({title: document.title, url: window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                                    class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Share
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 p-5 shadow-sm">
                    <h2 class="font-bold text-slate-900">Enquire About This Property</h2>
                    <form method="POST" action="{{ route('properties.enquire', $property) }}" class="mt-3 space-y-3">
                        @csrf
                        <div>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" placeholder="Your Name" required
                                   class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" placeholder="Mobile Number" required
                                   class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                        <div>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="Email (optional)"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="I'm interested in this property..."
                                      class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-amber-500 py-2.5 text-sm font-bold text-white hover:bg-amber-600">Send Enquiry</button>
                    </form>
                </div>

                <a href="{{ route('requirements.create') }}" class="block rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/50 p-5 text-center transition hover:border-emerald-300">
                    <p class="text-sm font-bold text-emerald-800">Not quite right?</p>
                    <p class="mt-1 text-xs text-emerald-700">Post your exact requirement instead</p>
                </a>
            </div>
        </div>

        {{-- Similar --}}
        @if ($similarProperties->isNotEmpty())
            <div class="mt-14">
                <h2 class="text-xl font-extrabold text-slate-900">Similar Properties</h2>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($similarProperties as $similar)
                        <x-property-card :property="$similar" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.marketplace>
