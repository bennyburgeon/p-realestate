@php
    $p = $property ?? null;
    $val = fn ($key, $default = null) => old($key, $p?->{$key} ?? $default);
    $selectedAmenities = old('amenity_ids', $p?->amenities->pluck('id')->all() ?? []);
@endphp

<div class="space-y-6">
    <div>
        <h2 class="font-bold text-slate-900">Basic Information</h2>
        <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Title</label>
                <input type="text" name="title" value="{{ $val('title') }}" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Listing Type</label>
                <select name="listing_type" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach (['sale' => 'For Sale', 'rent' => 'For Rent', 'lease' => 'For Lease'] as $value => $label)
                        <option value="{{ $value }}" @selected($val('listing_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('listing_type')" class="mt-1" />
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Category</label>
                <select name="property_category_id" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($val('property_category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('property_category_id')" class="mt-1" />
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">Locality</label>
                <select name="location_id" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select</option>
                    @foreach ($localities as $locality)
                        <option value="{{ $locality->id }}" @selected($val('location_id') === $locality->id)>{{ $locality->displayName() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('location_id')" class="mt-1" />
            </div>

            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Description</label>
                <textarea name="description" rows="4" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">{{ $val('description') }}</textarea>
            </div>
        </div>
    </div>

    <div>
        <h2 class="font-bold text-slate-900">Pricing</h2>
        <div class="mt-3 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Price (Sale)</label>
                <input type="number" name="price" value="{{ $val('price') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('price')" class="mt-1" />
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Rent/Lease Amount</label>
                <input type="number" name="rent_amount" value="{{ $val('rent_amount') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('rent_amount')" class="mt-1" />
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Security Deposit</label>
                <input type="number" name="security_deposit" value="{{ $val('security_deposit') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Maintenance</label>
                <input type="number" name="maintenance_amount" value="{{ $val('maintenance_amount') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>
        <label class="mt-3 flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_negotiable" value="1" @checked($val('is_negotiable')) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            Price is negotiable
        </label>
    </div>

    <div>
        <h2 class="font-bold text-slate-900">Specifications</h2>
        <div class="mt-3 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Built-up Area</label>
                <input type="number" name="built_up_area" value="{{ $val('built_up_area') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Carpet Area</label>
                <input type="number" name="carpet_area" value="{{ $val('carpet_area') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Bedrooms</label>
                <input type="number" name="bedrooms" value="{{ $val('bedrooms') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Bathrooms</label>
                <input type="number" name="bathrooms" value="{{ $val('bathrooms') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Floor / Total Floors</label>
                <div class="mt-1.5 flex gap-2">
                    <input type="number" name="floor_number" value="{{ $val('floor_number') }}" placeholder="Floor" class="w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <input type="number" name="total_floors" value="{{ $val('total_floors') }}" placeholder="Total" class="w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Furnishing</label>
                <select name="furnishing_status" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select</option>
                    <option value="unfurnished" @selected($val('furnishing_status') === 'unfurnished')>Unfurnished</option>
                    <option value="semi_furnished" @selected($val('furnishing_status') === 'semi_furnished')>Semi Furnished</option>
                    <option value="furnished" @selected($val('furnishing_status') === 'furnished')>Furnished</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Possession</label>
                <select name="possession_status" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select</option>
                    <option value="ready_to_move" @selected($val('possession_status') === 'ready_to_move')>Ready to Move</option>
                    <option value="under_construction" @selected($val('possession_status') === 'under_construction')>Under Construction</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Facing</label>
                <input type="text" name="facing" value="{{ $val('facing') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>
    </div>

    <div>
        <h2 class="font-bold text-slate-900">Amenities</h2>
        <div class="mt-3 grid grid-cols-2 gap-1.5 sm:grid-cols-4">
            @foreach ($amenities as $amenity)
                <label class="flex items-center gap-2 rounded px-1.5 py-1 text-sm text-slate-600 hover:bg-slate-50">
                    <input type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" @checked(in_array($amenity->id, $selectedAmenities)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    {{ $amenity->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="font-bold text-slate-900">Address & Contact</h2>
        <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Full Address</label>
                <input type="text" name="address" value="{{ $val('address') }}" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('address')" class="mt-1" />
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Landmark</label>
                <input type="text" name="landmark" value="{{ $val('landmark') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">PIN Code</label>
                <input type="text" name="pin_code" value="{{ $val('pin_code') }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Contact Phone</label>
                <input type="tel" name="contact_phone" value="{{ $val('contact_phone', auth()->user()->phone) }}" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Contact Email</label>
                <input type="email" name="contact_email" value="{{ $val('contact_email', auth()->user()->email) }}" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>
    </div>

    <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white hover:bg-emerald-700 sm:w-auto sm:px-8">
        {{ $p ? 'Save Changes' : 'Submit for Review' }}
    </button>
    @if (! $p)
        <p class="text-xs text-slate-400">Your listing will be reviewed by our team before it goes live.</p>
    @endif
</div>
