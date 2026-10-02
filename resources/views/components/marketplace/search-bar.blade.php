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
    class="mx-auto max-w-4xl rounded-3xl border border-slate-100 bg-white p-4 shadow-2xl shadow-slate-900/20 sm:p-6"
>
    <div class="inline-flex gap-1 rounded-xl bg-slate-100 p-1">
        <button type="button" @click="intent = 'buy'"
                :class="intent === 'buy' ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-700'"
                class="rounded-lg px-5 py-2 text-sm font-bold transition">Buy</button>
        <button type="button" @click="intent = 'rent'"
                :class="intent === 'rent' ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-700'"
                class="rounded-lg px-5 py-2 text-sm font-bold transition">Rent</button>
    </div>

    <form method="GET" action="{{ route('properties.index') }}" class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-12 lg:items-stretch lg:gap-2">
        <input type="hidden" name="intent" :value="intent">
        <input type="hidden" name="city" :value="citySlug">
        <input type="hidden" name="locality" :value="localitySlug">

        <div class="relative lg:col-span-4" @click.outside="open = false">
            <div class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-3 focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" stroke-linecap="round" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
                </svg>
                <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="if (results.length) open = true"
                       placeholder="City, locality or area" autocomplete="off"
                       class="w-full border-0 p-0 text-sm placeholder:text-slate-400 focus:ring-0">
            </div>

            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute z-20 mt-1.5 w-full overflow-hidden rounded-xl border border-slate-100 bg-white py-1 text-left shadow-xl">
                <template x-for="item in results" :key="item.id">
                    <button type="button" @click="select(item)" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                        <span class="text-slate-400">📍</span>
                        <span x-text="item.display"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="lg:col-span-3">
            <select name="category" class="h-full w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Property Type</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2 lg:col-span-3">
            <select name="price_min" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Min &#8377;</option>
                <option value="1000000">&#8377;10L</option>
                <option value="2500000">&#8377;25L</option>
                <option value="5000000">&#8377;50L</option>
                <option value="10000000">&#8377;1Cr</option>
            </select>
            <select name="price_max" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Max &#8377;</option>
                <option value="2500000">&#8377;25L</option>
                <option value="5000000">&#8377;50L</option>
                <option value="10000000">&#8377;1Cr</option>
                <option value="25000000">&#8377;2.5Cr</option>
            </select>
        </div>

        <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-primary-700 lg:col-span-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7" stroke-linecap="round" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
            </svg>
            Search
        </button>
    </form>

    <p class="mt-4 flex items-center justify-center gap-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
        <span>Secure</span>&bull;<span>Simple</span>&bull;<span>Fast</span>
    </p>
</div>
