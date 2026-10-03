@php
    $steps = ['Type', 'Location', 'Details', 'Price', 'Photos', 'Owner', 'Description', 'Preview'];
@endphp

<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="text-center">
        <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Sell Your Property</h1>
        <p class="mt-2 text-slate-500">List your property on BHKnow and connect with interested buyers.</p>
    </div>

    {{-- Progress indicator --}}
    <div class="mt-8 flex items-center justify-between overflow-x-auto pb-1">
        @foreach ($steps as $i => $label)
            @php $n = $i + 1; @endphp
            <div class="flex flex-1 items-center">
                <div class="flex flex-col items-center">
                    <button type="button" wire:click="goToStep({{ $n }})"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold transition
                                   {{ $step === $n ? 'bg-primary-600 text-white' : ($step > $n ? 'bg-primary-100 text-primary-700' : 'bg-slate-100 text-slate-400') }}">
                        @if ($step > $n)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                        @else
                            {{ $n }}
                        @endif
                    </button>
                    <span class="mt-1 hidden text-[11px] font-semibold {{ $step === $n ? 'text-slate-900' : 'text-slate-400' }} sm:block">{{ $label }}</span>
                </div>
                @if (! $loop->last)
                    <div class="mx-1 h-0.5 flex-1 {{ $step > $n ? 'bg-primary-400' : 'bg-slate-100' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">

        {{-- Step 1: Purpose / Type --}}
        @if ($step === 1)
            <div class="space-y-5">
                <div>
                    <label class="text-sm font-semibold text-slate-700">What would you like to do?</label>
                    <div class="mt-2 grid grid-cols-3 gap-2">
                        @foreach (['sale' => 'Sell', 'rent' => 'Rent Out', 'lease' => 'Lease Out'] as $value => $label)
                            <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700">
                                <input type="radio" wire:model="listing_type" value="{{ $value }}" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1.5 text-xs text-slate-400">List your property on BHKnow and connect with interested buyers.</p>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Property nature</label>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach (['residential' => 'Residential', 'commercial' => 'Commercial'] as $value => $label)
                            <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700">
                                <input type="radio" wire:model.live="property_nature" value="{{ $value }}" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700" for="property_category_id">Property type</label>
                    <select id="property_category_id" wire:model="property_category_id" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Select a property type</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('property_category_id')" class="mt-1" />
                </div>
            </div>
        @endif

        {{-- Step 2: Location --}}
        @if ($step === 2)
            <div class="space-y-5">
                <div>
                    <label class="text-sm font-semibold text-slate-700" for="location_id">City / Locality</label>
                    <select id="location_id" wire:model="location_id" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Select a city</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('location_id')" class="mt-1" />
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700" for="address">Address</label>
                    <textarea id="address" wire:model="address" rows="2" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="landmark">Landmark</label>
                        <input type="text" id="landmark" wire:model="landmark" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="pin_code">PIN code</label>
                        <input type="text" id="pin_code" wire:model="pin_code" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-data="{
                    locate() {
                        navigator.geolocation?.getCurrentPosition(
                            (pos) => $wire.useMyLocation(pos.coords.latitude, pos.coords.longitude)
                        );
                    }
                }">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="latitude">Latitude</label>
                        <input type="text" id="latitude" wire:model="latitude" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="longitude">Longitude</label>
                        <input type="text" id="longitude" wire:model="longitude" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <button type="button" @click="locate" class="col-span-2 flex items-center gap-1.5 text-sm font-semibold text-primary-700 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2M2 12h2m16 0h2M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>
                        Use my current location
                    </button>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model="address_visibility" class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Show my exact address publicly</span>
                            <span class="block text-sm text-slate-500">Turn this off to show only the locality/area to buyers — your exact address stays private until you choose to share it.</span>
                        </span>
                    </label>
                </div>
            </div>
        @endif

        {{-- Step 3: Details (type-conditional) --}}
        @if ($step === 3)
            <div class="space-y-5">
                @if (! $this->isLandCategory())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Bedrooms</label>
                            <input type="number" wire:model="bedrooms" min="0" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Bathrooms</label>
                            <input type="number" wire:model="bathrooms" min="0" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Balconies</label>
                            <input type="number" wire:model="balconies" min="0" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Floor</label>
                            <input type="number" wire:model="floor_number" min="0" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Furnishing</label>
                        <select wire:model="furnishing_status" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Select</option>
                            <option value="unfurnished">Unfurnished</option>
                            <option value="semi_furnished">Semi Furnished</option>
                            <option value="furnished">Furnished</option>
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Built-up area ({{ $area_unit }})</label>
                        <input type="number" wire:model.live="built_up_area" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    @if ($this->isLandCategory())
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Plot area ({{ $area_unit }})</label>
                            <input type="number" wire:model="plot_area" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    @else
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Carpet area ({{ $area_unit }})</label>
                            <input type="number" wire:model="carpet_area" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Facing</label>
                        <input type="text" wire:model="facing" placeholder="e.g. East facing" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    @if (! $this->isLandCategory())
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Parking</label>
                            <input type="text" wire:model="parking_details" placeholder="e.g. 1 covered" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Possession</label>
                        <select wire:model="possession_status" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Select</option>
                            <option value="ready_to_move">Ready to Move</option>
                            <option value="under_construction">Under Construction</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Availability date</label>
                        <input type="date" wire:model="availability_date" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Amenities</label>
                    <div class="mt-1.5 grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                        @foreach ($amenitiesList as $amenity)
                            <label class="flex items-center gap-2 rounded px-1.5 py-1 text-sm text-slate-600 hover:bg-slate-50">
                                <input type="checkbox" wire:model="amenity_ids" value="{{ $amenity->id }}" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                {{ $amenity->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 4: Price --}}
        @if ($step === 4)
            <div class="space-y-5">
                @if ($listing_type === 'sale')
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Expected sale price (₹)</label>
                        <input type="number" wire:model.live="price" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <x-input-error :messages="$errors->get('price')" class="mt-1" />
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Monthly rent (₹)</label>
                            <input type="number" wire:model="rent_amount" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                            <x-input-error :messages="$errors->get('rent_amount')" class="mt-1" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Security deposit (₹)</label>
                            <input type="number" wire:model="security_deposit" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Maintenance (₹/month)</label>
                        <input type="number" wire:model="maintenance_amount" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                @endif

                <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="checkbox" wire:model="is_negotiable" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Price is negotiable
                </label>

                @if ($price_per_sqft)
                    <p class="text-sm text-slate-500">Suggested: <span class="font-semibold text-slate-700">₹{{ $price_per_sqft }}/sqft</span> (editable below)</p>
                @endif
                <div>
                    <label class="text-sm font-semibold text-slate-700">Price per sq.ft. (₹)</label>
                    <input type="number" wire:model="price_per_sqft" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <label class="text-sm font-semibold text-amber-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm1-10V7a5 5 0 0 1 10 0v2"/></svg>
                        Minimum acceptable price (₹) — private
                    </label>
                    <input type="number" wire:model="min_acceptable_price" class="mt-1.5 w-full rounded-lg border-amber-200 focus:border-amber-500 focus:ring-amber-500">
                    <p class="mt-1 text-xs text-amber-700">Only visible to you and the BHKnow team — never shown to buyers.</p>
                </div>
            </div>
        @endif

        {{-- Step 5: Photos + Documents --}}
        @if ($step === 5)
            <div class="space-y-6">
                @if ($propertyId)
                    <p class="text-sm text-slate-500">This listing already has photos. Upload more below if you'd like to add any, or continue.</p>
                @endif

                <div>
                    <label class="text-sm font-semibold text-slate-700">Property photos (minimum 3)</label>
                    <input type="file" wire:model="photos" multiple accept="image/*" class="mt-1.5 block w-full text-sm">
                    <x-input-error :messages="$errors->get('photos')" class="mt-1" />
                    <x-input-error :messages="$errors->get('photos.*')" class="mt-1" />

                    <div wire:loading wire:target="photos" class="mt-2 text-sm text-slate-400">Uploading…</div>

                    @if (count($photos))
                        <div class="mt-3 grid grid-cols-3 gap-3 sm:grid-cols-4">
                            @foreach ($photos as $index => $photo)
                                <div class="relative overflow-hidden rounded-lg border {{ $index === $coverIndex ? 'border-primary-600 ring-2 ring-primary-200' : 'border-slate-200' }}">
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-24 w-full object-cover">
                                    <div class="flex items-center justify-between bg-white/90 px-1.5 py-1 text-[11px]">
                                        <button type="button" wire:click="moveCoverTo({{ $index }})" class="font-semibold {{ $index === $coverIndex ? 'text-primary-700' : 'text-slate-500' }}">
                                            {{ $index === $coverIndex ? 'Cover' : 'Set cover' }}
                                        </button>
                                        <button type="button" wire:click="removePhoto({{ $index }})" class="text-red-500">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Documents (optional)</label>
                    <p class="text-xs text-slate-400">Ownership proof, title deed, tax receipts, etc. Kept private — never shown publicly.</p>
                    <input type="file" wire:model="documents" multiple accept=".pdf,image/*" class="mt-1.5 block w-full text-sm">
                    <x-input-error :messages="$errors->get('documents.*')" class="mt-1" />

                    @if (count($documents))
                        <ul class="mt-2 space-y-1 text-sm text-slate-600">
                            @foreach ($documents as $index => $doc)
                                <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                    {{ $doc->getClientOriginalName() }}
                                    <button type="button" wire:click="removeDocument({{ $index }})" class="text-red-500">Remove</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

        {{-- Step 6: Owner / Agent --}}
        @if ($step === 6)
            <div class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Mobile number</label>
                        <input type="tel" wire:model="contact_phone" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Email (optional)</label>
                        <input type="email" wire:model="contact_email" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700">Preferred contact method</label>
                    <select wire:model="contact_preference" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                        <option value="phone">Phone Call</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="email">Email</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" wire:model.live="isAgentListing" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    I'm listing this property as an Agent / Broker
                </label>

                @if ($isAgentListing)
                    <div class="space-y-4 rounded-xl bg-slate-50 p-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Agent name</label>
                                <input type="text" wire:model="agent_name" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                                <x-input-error :messages="$errors->get('agent_name')" class="mt-1" />
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Agent phone</label>
                                <input type="tel" wire:model="agent_phone" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                                <x-input-error :messages="$errors->get('agent_phone')" class="mt-1" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Agency name</label>
                                <input type="text" wire:model="agency_name" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">License / registration no.</label>
                                <input type="text" wire:model="agent_license_number" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Step 7: Description --}}
        @if ($step === 7)
            <div class="space-y-5">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Listing title</label>
                    <input type="text" wire:model="title" placeholder="e.g. Sunny 3 BHK apartment with lake view" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea wire:model="description" rows="6" placeholder="Overview, key highlights, nearby landmarks…" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-primary-500 focus:ring-primary-500"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
            </div>
        @endif

        {{-- Step 8: Preview --}}
        @if ($step === 8)
            <div class="space-y-5">
                @if (count($photos))
                    <div class="aspect-[16/9] overflow-hidden rounded-xl bg-slate-100">
                        <img src="{{ $photos[$coverIndex]->temporaryUrl() }}" class="h-full w-full object-cover">
                    </div>
                @endif

                <div>
                    <p class="text-2xl font-extrabold text-slate-900">
                        @if ($listing_type === 'sale')
                            ₹{{ number_format((float) ($price ?: 0)) }}
                        @else
                            ₹{{ number_format((float) ($rent_amount ?: 0)) }}/mo
                        @endif
                    </p>
                    <p class="text-slate-600">{{ $title }}</p>
                    <p class="text-sm text-slate-500">📍 {{ $address }}</p>
                </div>

                <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                    @if ($bedrooms) <span>🛏 {{ $bedrooms }} Bed</span> @endif
                    @if ($bathrooms) <span>🛁 {{ $bathrooms }} Bath</span> @endif
                    @if ($built_up_area) <span>📐 {{ $built_up_area }} {{ $area_unit }}</span> @endif
                </div>

                @if (count($amenity_ids))
                    <p class="text-sm text-slate-500">{{ count($amenity_ids) }} amenities selected</p>
                @endif

                <p class="text-sm text-slate-600">{{ $description }}</p>

                <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    {{ $isAgentListing ? 'Listed by Agent' : 'Listed by Owner' }} &middot; {{ $contact_phone }}
                </div>

                <p class="text-xs text-slate-400">By submitting, your property will be reviewed by the BHKnow team before it goes live. You'll be notified once it's approved.</p>
            </div>
        @endif

        {{-- Navigation --}}
        <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
            <button type="button" wire:click="previousStep" @if ($step === 1) disabled @endif
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-0">
                &larr; Back
            </button>

            @if ($step < count($steps))
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                        class="rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700">
                    Continue &rarr;
                </button>
            @else
                <button type="button" wire:click="submit" wire:loading.attr="disabled"
                        class="rounded-xl bg-secondary-500 px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary-600">
                    Submit for Approval
                </button>
            @endif
        </div>
    </div>
</div>
