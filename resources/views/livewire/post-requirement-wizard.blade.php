@php
    $steps = ['Basics', 'Preferences', 'Contact', 'Preview'];
@endphp

<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Post Your Property Requirement</h1>
            <p class="mt-2 text-slate-500">Tell us what you're looking for &mdash; owners and agents with a match will reach out to you.</p>
        </div>

        {{-- Progress indicator --}}
        <div class="mt-8 flex items-center justify-between">
            @foreach ($steps as $i => $label)
                @php $n = $i + 1; @endphp
                <div class="flex flex-1 items-center">
                    <div class="flex flex-col items-center">
                        <button type="button" wire:click="goToStep({{ $n }})"
                                class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition
                                       {{ $step === $n ? 'bg-emerald-600 text-white' : ($step > $n ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400') }}">
                            @if ($step > $n)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" /></svg>
                            @else
                                {{ $n }}
                            @endif
                        </button>
                        <span class="mt-1.5 hidden text-xs font-semibold {{ $step === $n ? 'text-slate-900' : 'text-slate-400' }} sm:block">{{ $label }}</span>
                    </div>
                    @if (! $loop->last)
                        <div class="mx-1 h-0.5 flex-1 {{ $step > $n ? 'bg-emerald-400' : 'bg-slate-100' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">

            {{-- Step 1: Basics --}}
            @if ($step === 1)
                <div class="space-y-5">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">I want to</label>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            @foreach (['buy' => 'Buy', 'rent' => 'Rent', 'lease' => 'Lease'] as $value => $label)
                                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                    <input type="radio" wire:model="intent" value="{{ $value }}" class="sr-only">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Property type</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach (['residential' => 'Residential', 'commercial' => 'Commercial'] as $value => $label)
                                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                    <input type="radio" wire:model.live="property_nature" value="{{ $value }}" class="sr-only">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="title">Requirement title</label>
                        <input type="text" id="title" wire:model="title" placeholder="e.g. 2 BHK apartment for rent near Koramangala"
                               class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="property_category_id">Category</label>
                        <select id="property_category_id" wire:model="property_category_id" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('property_category_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700" for="city_id">City</label>
                            <select id="city_id" wire:model.live="city_id" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Select a city</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('city_id')" class="mt-1" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Preferred areas</label>
                            <div class="mt-1.5 max-h-32 space-y-1 overflow-y-auto rounded-lg border border-slate-200 p-2">
                                @forelse ($localities as $locality)
                                    <label class="flex items-center gap-2 rounded px-1.5 py-1 text-sm text-slate-600 hover:bg-slate-50">
                                        <input type="checkbox" wire:model="preferred_locality_ids" value="{{ $locality->id }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        {{ $locality->name }}
                                    </label>
                                @empty
                                    <p class="px-1.5 py-1 text-xs text-slate-400">Select a city to see areas</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Budget Min (₹)</label>
                            <input type="number" wire:model="budget_min" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('budget_min')" class="mt-1" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Budget Max (₹)</label>
                            <input type="number" wire:model="budget_max" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('budget_max')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Area Min ({{ $area_unit }})</label>
                            <input type="number" wire:model="area_min" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('area_min')" class="mt-1" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Area Max ({{ $area_unit }})</label>
                            <input type="number" wire:model="area_max" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('area_max')" class="mt-1" />
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step 2: Preferences --}}
            @if ($step === 2)
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Bedrooms</label>
                            <select wire:model="bedrooms" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Any</option>
                                @foreach ([1, 2, 3, 4, 5] as $n)
                                    <option value="{{ $n }}">{{ $n }}+</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Bathrooms</label>
                            <select wire:model="bathrooms" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Any</option>
                                @foreach ([1, 2, 3, 4] as $n)
                                    <option value="{{ $n }}">{{ $n }}+</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Furnishing</label>
                        <select wire:model="furnishing_status" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Any</option>
                            <option value="unfurnished">Unfurnished</option>
                            <option value="semi_furnished">Semi Furnished</option>
                            <option value="furnished">Furnished</option>
                        </select>
                    </div>

                    <button type="button" wire:click="$toggle('showAdvanced')" class="flex items-center gap-1.5 text-sm font-bold text-emerald-700">
                        {{ $showAdvanced ? 'Hide' : 'Show' }} more preferences
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition {{ $showAdvanced ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>

                    @if ($showAdvanced)
                        <div class="space-y-5 rounded-xl bg-slate-50 p-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input type="checkbox" wire:model="parking_required" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Parking required
                            </label>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Floor preference</label>
                                    <input type="text" wire:model="floor_preference" placeholder="e.g. Not ground floor" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Facing preference</label>
                                    <input type="text" wire:model="facing_preference" placeholder="e.g. East facing" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Property age</label>
                                    <select wire:model="property_age" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">Any</option>
                                        <option value="new">New / Under Construction</option>
                                        <option value="0-5">0-5 years</option>
                                        <option value="5-10">5-10 years</option>
                                        <option value="10+">10+ years</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Possession</label>
                                    <select wire:model="possession_requirement" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">Any</option>
                                        <option value="ready_to_move">Ready to Move</option>
                                        <option value="under_construction">Under Construction</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-700">Preferred move-in date</label>
                                <input type="date" wire:model="move_in_date" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <x-input-error :messages="$errors->get('move_in_date')" class="mt-1" />
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-700">Amenities</label>
                                <div class="mt-1.5 grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                                    @foreach ($amenitiesList as $amenity)
                                        <label class="flex items-center gap-2 rounded px-1.5 py-1 text-sm text-slate-600 hover:bg-white">
                                            <input type="checkbox" wire:model="amenity_ids" value="{{ $amenity->id }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            {{ $amenity->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-700">Special requirements</label>
                                <textarea wire:model="special_requirements" rows="2" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-700">Additional notes</label>
                                <textarea wire:model="additional_notes" rows="2" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Step 3: Contact --}}
            @if ($step === 3)
                <div class="space-y-5">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Full name</label>
                        <input type="text" wire:model="contact_name" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                        <x-input-error :messages="$errors->get('contact_name')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Mobile number</label>
                            <input type="tel" wire:model="contact_phone" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Email (optional)</label>
                            <input type="email" wire:model="contact_email" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <x-input-error :messages="$errors->get('contact_email')" class="mt-1" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Preferred contact method</label>
                            <select wire:model="preferred_contact_method" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="phone">Phone Call</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Preferred time</label>
                            <input type="text" wire:model="preferred_contact_time" placeholder="e.g. Evenings after 6pm" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step 4: Preview --}}
            @if ($step === 4)
                <div class="space-y-5">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Basics</h3>
                            <button type="button" wire:click="goToStep(1)" class="text-xs font-semibold text-emerald-700 hover:underline">Edit</button>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $title }}</p>
                        <p class="text-sm text-slate-500">
                            Want to {{ $intent }} &middot; {{ ucfirst($property_nature) }}
                            @if ($budget_min || $budget_max)
                                &middot; ₹{{ number_format((float) ($budget_min ?: 0)) }} - ₹{{ number_format((float) ($budget_max ?: 0)) }}
                            @endif
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Preferences</h3>
                            <button type="button" wire:click="goToStep(2)" class="text-xs font-semibold text-emerald-700 hover:underline">Edit</button>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">
                            {{ $bedrooms ? $bedrooms.'+ BHK' : 'Any BHK' }}
                            @if ($furnishing_status) &middot; {{ ucfirst(str_replace('_', ' ', $furnishing_status)) }} @endif
                            @if ($parking_required) &middot; Parking required @endif
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Contact</h3>
                            <button type="button" wire:click="goToStep(3)" class="text-xs font-semibold text-emerald-700 hover:underline">Edit</button>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $contact_name }} &middot; {{ $contact_phone }}</p>
                    </div>

                    <p class="text-xs text-slate-400">By submitting, you agree to be contacted by owners and agents about matching properties.</p>
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
                            class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                        Continue &rarr;
                    </button>
                @else
                    <button type="button" wire:click="submit" wire:loading.attr="disabled"
                            class="rounded-xl bg-amber-500 px-6 py-2.5 text-sm font-bold text-white hover:bg-amber-600">
                        Submit Requirement
                    </button>
                @endif
            </div>
        </div>
    </div>
