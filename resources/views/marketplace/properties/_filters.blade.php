<form method="GET" class="space-y-5">
    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Looking to</label>
        <div class="mt-2 grid grid-cols-3 gap-1.5">
            @foreach (['buy' => 'Buy', 'rent' => 'Rent', 'lease' => 'Lease'] as $value => $label)
                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 py-2 text-xs font-semibold text-slate-600 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                    <input type="radio" name="intent" value="{{ $value }}" class="sr-only" @checked(($filters['intent'] ?? null) === $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500" for="keyword-{{ $suffix }}">Keyword</label>
        <input type="text" id="keyword-{{ $suffix }}" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Area, landmark..."
               class="mt-1.5 w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500" for="city-{{ $suffix }}">City</label>
        <select id="city-{{ $suffix }}" name="city" class="mt-1.5 w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Any City</option>
            @foreach ($cities as $city)
                <option value="{{ $city->slug }}" @selected(($filters['city'] ?? null) === $city->slug)>{{ $city->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500" for="category-{{ $suffix }}">Property Type</label>
        <select id="category-{{ $suffix }}" name="category" class="mt-1.5 w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Any Type</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(($filters['category'] ?? null) === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Budget</label>
        <div class="mt-1.5 grid grid-cols-2 gap-2">
            <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}" placeholder="Min" class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" placeholder="Max" class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Bedrooms</label>
        <div class="mt-1.5 flex flex-wrap gap-1.5">
            @foreach ([1, 2, 3, 4, 5] as $bhk)
                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                    <input type="radio" name="bedrooms" value="{{ $bhk }}" class="sr-only" @checked((int) ($filters['bedrooms'] ?? 0) === $bhk)>
                    {{ $bhk }}+
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-slate-500" for="furnishing-{{ $suffix }}">Furnishing</label>
        <select id="furnishing-{{ $suffix }}" name="furnishing" class="mt-1.5 w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">Any</option>
            <option value="unfurnished" @selected(($filters['furnishing'] ?? null) === 'unfurnished')>Unfurnished</option>
            <option value="semi_furnished" @selected(($filters['furnishing'] ?? null) === 'semi_furnished')>Semi Furnished</option>
            <option value="furnished" @selected(($filters['furnishing'] ?? null) === 'furnished')>Furnished</option>
        </select>
    </div>

    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
        <input type="checkbox" name="verified_only" value="1" @checked($filters['verified_only'] ?? false) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        Verified listings only
    </label>

    <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-bold text-white hover:bg-slate-800">Apply Filters</button>
</form>
