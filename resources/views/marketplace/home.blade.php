<x-layouts.marketplace :title="'Buy, Rent & Find Your Next Property'">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-slate-900">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.35),transparent_55%)]"></div>
        <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 sm:pb-20 sm:pt-20 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                    Can't find what you need? Tell us instead.
                </span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-5xl">
                    Find a place to call home &mdash; or let it find you
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-base text-slate-300 sm:text-lg">
                    Search thousands of properties for sale and rent, or post exactly what you're looking for and let verified owners and agents come to you.
                </p>

                <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('requirements.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-amber-500/30 transition hover:bg-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                        </svg>
                        Post Your Property Requirement
                    </a>
                    <span class="text-sm text-slate-400">It takes less than 2 minutes &middot; free</span>
                </div>
            </div>

            {{-- Search card --}}
            <x-marketplace.search-bar :categories="$categories" />
        </div>
    </section>

    {{-- Explore by --}}
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <h2 class="text-center text-sm font-bold uppercase tracking-wide text-slate-400">Explore by</h2>
        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ([
                ['label' => 'Buy', 'icon' => '🏠', 'params' => ['intent' => 'buy']],
                ['label' => 'Rent', 'icon' => '🏢', 'params' => ['intent' => 'rent']],
                ['label' => 'Popular Locations', 'icon' => '📍', 'params' => []],
                ['label' => 'Affordable Homes', 'icon' => '💰', 'params' => ['price_max' => 5000000]],
                ['label' => 'Premium Properties', 'icon' => '✨', 'params' => ['price_min' => 10000000]],
                ['label' => 'New Listings', 'icon' => '🏘️', 'params' => []],
            ] as $card)
                <a href="{{ route('properties.index', $card['params']) }}"
                   class="flex flex-col items-center gap-2 rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md">
                    <span class="text-2xl">{{ $card['icon'] }}</span>
                    <span class="text-sm font-semibold text-slate-700">{{ $card['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured properties --}}
    @if ($featuredProperties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Featured Properties</h2>
                    <p class="mt-1 text-sm text-slate-500">Hand-picked listings worth a look</p>
                </div>
                <a href="{{ route('properties.index', ['featured_only' => 1]) }}" class="text-sm font-semibold text-emerald-700 hover:underline">View all &rarr;</a>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Requirement CTA banner --}}
    <section class="bg-emerald-600">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-10 text-center sm:flex-row sm:px-6 sm:text-left lg:px-8">
            <div>
                <h2 class="text-xl font-extrabold text-white sm:text-2xl">Not finding the right property?</h2>
                <p class="mt-1 text-emerald-50">Post what you need &mdash; owners and agents with a match will reach out to you directly.</p>
            </div>
            <a href="{{ route('requirements.create') }}" class="shrink-0 rounded-xl bg-white px-6 py-3 text-sm font-bold text-emerald-700 shadow transition hover:bg-emerald-50">
                Post a Requirement
            </a>
        </div>
    </section>

    {{-- New properties --}}
    @if ($newProperties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-extrabold text-slate-900">Newly Added</h2>
                <p class="mt-1 text-sm text-slate-500">Fresh listings from owners and agents</p>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($newProperties->take(4) as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Sale / Rent split --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
            <div>
                <div class="mb-5 flex items-end justify-between">
                    <h2 class="text-xl font-extrabold text-slate-900">For Sale</h2>
                    <a href="{{ route('properties.index', ['intent' => 'buy']) }}" class="text-sm font-semibold text-emerald-700 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse ($saleProperties->take(4) as $property)
                        <x-property-card :property="$property" />
                    @empty
                        <p class="text-sm text-slate-500">No sale listings yet.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <div class="mb-5 flex items-end justify-between">
                    <h2 class="text-xl font-extrabold text-slate-900">For Rent</h2>
                    <a href="{{ route('properties.index', ['intent' => 'rent']) }}" class="text-sm font-semibold text-emerald-700 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse ($rentProperties->take(4) as $property)
                        <x-property-card :property="$property" />
                    @empty
                        <p class="text-sm text-slate-500">No rental listings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Recently posted requirements --}}
    @if ($recentRequirements->isNotEmpty())
        <section class="bg-slate-50 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">Recently Posted Requirements</h2>
                        <p class="mt-1 text-sm text-slate-500">Buyers and tenants actively looking right now</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($recentRequirements as $requirement)
                        <a href="{{ route('requirements.show', $requirement) }}" class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold uppercase text-slate-600">
                                Want to {{ $requirement->intent }}
                            </span>
                            <h3 class="mt-3 line-clamp-2 font-semibold text-slate-800">{{ $requirement->title }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $requirement->city?->name ?? 'Any city' }}</p>
                            @if ($requirement->budget_min || $requirement->budget_max)
                                <p class="mt-1 text-sm font-semibold text-emerald-700">
                                    ₹{{ number_format((float) ($requirement->budget_min ?? 0)) }} &ndash; ₹{{ number_format((float) ($requirement->budget_max ?? 0)) }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
                <div class="mt-6 rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/50 p-5 text-center">
                    <p class="text-sm font-medium text-emerald-800">Have a similar need? Get discovered by owners and agents in minutes.</p>
                    <a href="{{ route('requirements.create') }}" class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-emerald-700 hover:underline">
                        Post your requirement &rarr;
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-2xl font-extrabold text-slate-900">How BHKnow Works</h2>
            <p class="mt-2 text-slate-500">Whether you're searching or listing, getting started takes minutes.</p>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ([
                ['title' => 'Search or Post', 'body' => 'Browse verified listings, or post exactly what you need in a simple guided form.', 'icon' => 'search'],
                ['title' => 'Get Matched', 'body' => 'Our matching engine scores properties and requirements against each other automatically.', 'icon' => 'match'],
                ['title' => 'Connect Directly', 'body' => 'Chat with owners, agents or interested buyers/tenants and schedule a visit.', 'icon' => 'connect'],
            ] as $i => $step)
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-extrabold">
                        {{ $i + 1 }}
                    </div>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ $step['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Benefits --}}
    <section class="bg-slate-900 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-white text-center">Why Choose BHKnow</h2>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    'Verified Listings' => 'Every listing goes through an admin review before going live.',
                    'Smart Matching' => 'Requirements are automatically scored against live properties.',
                    'Direct Contact' => 'Talk to owners and agents directly — no unnecessary middlemen.',
                    'Free to Post' => 'Posting a requirement or a listing costs nothing.',
                ] as $title => $body)
                    <div class="rounded-2xl bg-white/5 p-6">
                        <h3 class="font-bold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-slate-400">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="text-center text-2xl font-extrabold text-slate-900">Frequently Asked Questions</h2>
        <div class="mt-8 space-y-3">
            @foreach ([
                'Is it free to post a property requirement?' => 'Yes, posting a requirement is completely free for buyers and tenants.',
                'How does the matching score work?' => 'We compare your requirement against live properties on location, budget, area, category and more, then label results as Excellent, Good or Possible matches. It is an estimate, not a guarantee.',
                'Can I edit my requirement after posting it?' => 'Yes, you can edit, pause, close, renew or mark your requirement as fulfilled at any time from your dashboard.',
                'Are listed properties verified?' => 'Listings go through an admin review step before appearing publicly, and verified owners/agents carry a verification badge.',
            ] as $question => $answer)
                <details class="group rounded-xl border border-slate-100 bg-white p-4 open:shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-800">
                        {{ $question }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm text-slate-500">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </section>

</x-layouts.marketplace>
