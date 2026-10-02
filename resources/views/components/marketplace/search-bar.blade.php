@props(['categories'])

<div
    x-data="{
        intent: 'buy',
        query: '',
        citySlug: '',
        localitySlug: '',
        results: [],
        open: false,
        async search() {
            if (this.query.length < 2) { this.results = []; this.open = false; return; }
            const res = await fetch(`{{ route('locations.search') }}?q=${encodeURIComponent(this.query)}`);
            this.results = await res.json();
            this.open = this.results.length > 0;
        },
        select(item) {
            this.query = item.display;
            this.citySlug = item.type === 'city' ? item.slug : '';
            this.localitySlug = item.type === 'locality' ? item.slug : '';
            this.open = false;
        },
    }"
    class="mx-auto mt-10 max-w-4xl rounded-2xl bg-white p-3 shadow-2xl sm:p-4"
>
    <div class="flex gap-1 rounded-xl bg-slate-100 p-1">
        <button type="button" @click="intent = 'buy'"
                :class="intent === 'buy' ? 'bg-white shadow text-slate-900' : 'text-slate-500'"
                class="flex-1 rounded-lg px-4 py-2 text-sm font-bold transition">Buy</button>
        <button type="button" @click="intent = 'rent'"
                :class="intent === 'rent' ? 'bg-white shadow text-slate-900' : 'text-slate-500'"
                class="flex-1 rounded-lg px-4 py-2 text-sm font-bold transition">Rent</button>
    </div>

    <form method="GET" action="{{ route('properties.index') }}" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-5">
        <input type="hidden" name="intent" :value="intent">
        <input type="hidden" name="city" :value="citySlug">
        <input type="hidden" name="locality" :value="localitySlug">

        <div class="relative col-span-1 sm:col-span-2" @click.outside="open = false">
            <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="if (results.length) open = true"
                   placeholder="Search city, locality or area" autocomplete="off"
                   class="w-full rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">

            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-slate-100 bg-white py-1 text-left shadow-xl">
                <template x-for="item in results" :key="item.id">
                    <button type="button" @click="select(item)" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                        <span>📍</span>
                        <span x-text="item.display"></span>
                    </button>
                </template>
            </div>
        </div>

        <select name="category" class="col-span-1 rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Property Type</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="price_min" class="col-span-1 rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Min Budget</option>
            <option value="1000000">₹10L</option>
            <option value="2500000">₹25L</option>
            <option value="5000000">₹50L</option>
            <option value="10000000">₹1Cr</option>
        </select>

        <div class="col-span-1 grid grid-cols-2 gap-2 sm:col-span-1">
            <select name="price_max" class="rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Max Budget</option>
                <option value="2500000">₹25L</option>
                <option value="5000000">₹50L</option>
                <option value="10000000">₹1Cr</option>
                <option value="25000000">₹2.5Cr</option>
            </select>

            <button type="submit" class="flex items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" stroke-linecap="round" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
                </svg>
            </button>
        </div>
    </form>
</div>
