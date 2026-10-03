<x-layouts.marketplace :title="'Buy, Rent & Find Your Next Property'" :floating-nav="true">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-primary-900">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_10%,rgba(232,179,78,0.16),transparent_40%),radial-gradient(circle_at_90%_30%,rgba(136,166,199,0.18),transparent_45%)]"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-28 sm:px-6 sm:pt-36 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-8">
                {{-- Editorial copy --}}
                <div class="lg:col-span-6">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-secondary-300">
                        Buy &middot; Rent &middot; Sell &middot; Find
                    </span>

                    <h1 class="mt-6 font-display text-4xl font-bold leading-[1.05] tracking-tight text-white sm:text-6xl">
                        Find somewhere worth coming home to.
                    </h1>

                    <p class="mt-5 max-w-md text-base text-primary-100/80 sm:text-lg">
                        Search verified listings across the city, or tell us what you need and let owners and agents come to you.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('requirements.create') }}"
                           class="inline-flex items-center gap-2 rounded-full bg-secondary-500 px-6 py-3.5 text-base font-bold text-primary-900 shadow-lg shadow-secondary-500/20 transition hover:bg-secondary-400">
                            Post Your Requirement
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                        </a>
                        <span class="text-sm font-medium text-primary-200/60">or search below &darr;</span>
                    </div>

                    <div class="mt-12 flex items-center gap-8 text-sm text-primary-200/70">
                        <div>
                            <p class="font-display text-2xl font-bold text-white">10K+</p>
                            <p class="mt-0.5">Listings</p>
                        </div>
                        <div>
                            <p class="font-display text-2xl font-bold text-white">50+</p>
                            <p class="mt-0.5">Cities</p>
                        </div>
                        <div>
                            <p class="font-display text-2xl font-bold text-white">4.8<span class="text-secondary-400">&#9733;</span></p>
                            <p class="mt-0.5">User rated</p>
                        </div>
                    </div>
                </div>

                {{-- Layered visual composition (abstract — no stock photography in the data set yet) --}}
                <div class="relative lg:col-span-6">
                    <div class="relative aspect-[5/4] overflow-hidden rounded-[2rem] bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 shadow-2xl shadow-black/40">
                        <div class="absolute inset-0 opacity-[0.08] [background-image:radial-gradient(circle,white_1px,transparent_1px)] [background-size:22px_22px]"></div>

                        <svg class="absolute inset-x-0 bottom-0 h-2/3 w-full text-primary-900/70" viewBox="0 0 400 220" preserveAspectRatio="xMidYMax slice" fill="currentColor">
                            <rect x="20" y="90" width="55" height="130" rx="4" />
                            <rect x="85" y="50" width="70" height="170" rx="4" />
                            <rect x="165" y="110" width="50" height="110" rx="4" />
                            <rect x="225" y="30" width="80" height="190" rx="4" />
                            <rect x="315" y="75" width="60" height="145" rx="4" />
                        </svg>

                        <div class="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                            Kakkanad, Kochi
                        </div>

                        <div class="absolute bottom-6 left-6 rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-md">
                            <p class="font-display text-lg font-bold text-white">&#8377;52 L</p>
                            <p class="text-xs text-primary-100/80">2 BHK &middot; Ready to move</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search card — overlaps the hero's bottom edge --}}
        <div class="relative z-10 mx-auto -mt-8 max-w-5xl px-4 sm:-mt-10 sm:px-6 lg:px-8">
            <x-marketplace.search-bar :categories="$categories" />
        </div>

        <div class="h-10 sm:h-14"></div>
    </section>

    {{-- Explore cities — editorial, overlapping cards --}}
    @if ($popularCities->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">Explore by city</h2>
                <a href="{{ route('properties.index') }}" class="hidden text-sm font-semibold text-primary-700 hover:underline sm:inline">All cities &rarr;</a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($popularCities->take(6) as $i => $city)
                    <a href="{{ route('properties.index', ['city' => $city->slug]) }}"
                       class="group relative flex aspect-[4/3] flex-col justify-end overflow-hidden rounded-2xl bg-primary-800 p-5 {{ $i === 0 ? 'sm:col-span-2 sm:aspect-[21/10] lg:col-span-1 lg:aspect-[4/3]' : '' }}">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 to-primary-900 opacity-90 transition group-hover:opacity-100"></div>
                        <div class="absolute inset-0 opacity-[0.1] [background-image:radial-gradient(circle,white_1px,transparent_1px)] [background-size:18px_18px]"></div>
                        <div class="relative">
                            <h3 class="font-display text-xl font-bold text-white">{{ $city->name }}</h3>
                            <p class="mt-1 text-sm text-white/70">{{ number_format($city->properties_count) }} properties</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Property categories --}}
    <section class="bg-neutral-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">Find your kind of space</h2>
            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($categories->take(6) as $category)
                    <a href="{{ route('properties.index', ['category' => $category->slug]) }}"
                       class="group flex aspect-square flex-col items-center justify-center gap-2 rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-neutral-100 transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-50 text-primary-700 transition group-hover:bg-primary-700 group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9" /></svg>
                        </span>
                        <span class="text-sm font-semibold text-primary-900">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured properties --}}
    @if ($featuredProperties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">Featured Properties</h2>
                    <p class="mt-1 text-sm text-neutral-500">Hand-picked listings worth a look</p>
                </div>
                <a href="{{ route('properties.index', ['featured_only' => 1]) }}" class="text-sm font-semibold text-primary-700 hover:underline">View all &rarr;</a>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Requirement CTA — signature section --}}
    <section class="relative overflow-hidden bg-primary-900 py-20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(232,179,78,0.14),transparent_45%)]"></div>
        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">
                Can't find what you're looking for?
            </h2>
            <p class="mt-4 text-lg text-primary-100/80">
                Tell BHKnow what you need — owners and agents with a match will reach out to you directly.
            </p>
            <a href="{{ route('requirements.create') }}"
               class="mt-8 inline-flex items-center gap-2 rounded-full bg-secondary-500 px-7 py-3.5 text-base font-bold text-primary-900 shadow-lg shadow-secondary-500/20 transition hover:bg-secondary-400">
                Post your requirement
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </div>
    </section>

    {{-- New properties --}}
    @if ($newProperties->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">Newly Added</h2>
                <p class="mt-1 text-sm text-neutral-500">Fresh listings from owners and agents</p>
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($newProperties->take(4) as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Sale / Rent split --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
            <div>
                <div class="mb-5 flex items-end justify-between">
                    <h2 class="font-display text-xl font-bold text-primary-900">For Sale</h2>
                    <a href="{{ route('properties.index', ['intent' => 'buy']) }}" class="text-sm font-semibold text-primary-700 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse ($saleProperties->take(4) as $property)
                        <x-property-card :property="$property" />
                    @empty
                        <p class="text-sm text-neutral-500">No sale listings yet.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <div class="mb-5 flex items-end justify-between">
                    <h2 class="font-display text-xl font-bold text-primary-900">For Rent</h2>
                    <a href="{{ route('properties.index', ['intent' => 'rent']) }}" class="text-sm font-semibold text-primary-700 hover:underline">See all</a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @forelse ($rentProperties->take(4) as $property)
                        <x-property-card :property="$property" />
                    @empty
                        <p class="text-sm text-neutral-500">No rental listings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Sell Your Property — signature section --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-neutral-100 bg-gradient-to-br from-white via-neutral-50 to-primary-50 p-10 shadow-sm sm:p-14">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-secondary-200/40 blur-3xl"></div>
            <div class="relative grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-secondary-600">Sell Your Property</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-primary-900 sm:text-4xl">
                        Have a property to sell?
                    </h2>
                    <p class="mt-4 text-lg text-neutral-600">
                        Put it in front of genuine buyers searching on BHKnow.
                    </p>
                    <a href="{{ route('properties.sell.create') }}"
                       class="mt-8 inline-flex items-center gap-2 rounded-full bg-primary-900 px-7 py-3.5 text-base font-bold text-white shadow-lg shadow-primary-900/20 transition hover:bg-primary-800">
                        Sell Your Property
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </a>
                    <p class="mt-4 text-sm font-semibold text-neutral-500">Free listing &bull; Professional property presentation &bull; Buyer enquiries</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                        <p class="text-2xl font-bold text-primary-900">8 steps</p>
                        <p class="mt-1 text-sm text-neutral-500">Guided listing wizard — no clunky forms</p>
                    </div>
                    <div class="rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                        <p class="text-2xl font-bold text-primary-900">Verified</p>
                        <p class="mt-1 text-sm text-neutral-500">Every listing reviewed before it goes live</p>
                    </div>
                    <div class="col-span-2 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold text-neutral-700">Owners &amp; agents welcome</p>
                        <p class="mt-1 text-sm text-neutral-500">List as a property owner or as a licensed agent/broker — buyers always know who they're talking to.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Recently posted requirements --}}
    @if ($recentRequirements->isNotEmpty())
        <section class="bg-neutral-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">Buyers looking right now</h2>
                    <p class="mt-1 text-sm text-neutral-500">Recently posted requirements from verified buyers and tenants</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($recentRequirements as $requirement)
                        <a href="{{ route('requirements.show', $requirement) }}" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-100 transition hover:shadow-md">
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-bold uppercase text-neutral-600">
                                Want to {{ $requirement->intent }}
                            </span>
                            <h3 class="mt-3 line-clamp-2 font-semibold text-primary-900">{{ $requirement->title }}</h3>
                            <p class="mt-2 text-sm text-neutral-500">{{ $requirement->city?->name ?? 'Any city' }}</p>
                            @if ($requirement->budget_min || $requirement->budget_max)
                                <p class="mt-1 text-sm font-semibold text-primary-700">
                                    ₹{{ number_format((float) ($requirement->budget_min ?? 0)) }} &ndash; ₹{{ number_format((float) ($requirement->budget_max ?? 0)) }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="font-display text-2xl font-bold text-primary-900 sm:text-3xl">How BHKnow works</h2>
            <p class="mt-2 text-neutral-500">Whether you're searching or listing, getting started takes minutes.</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-10 sm:grid-cols-3">
            @foreach ([
                ['title' => 'Search or post', 'body' => 'Browse verified listings, or post exactly what you need in a simple guided form.'],
                ['title' => 'Get matched', 'body' => 'Our matching engine scores properties and requirements against each other automatically.'],
                ['title' => 'Connect directly', 'body' => 'Talk to owners, agents or interested buyers and schedule a visit.'],
            ] as $i => $step)
                <div class="text-center">
                    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full border-2 border-secondary-400 font-display text-lg font-bold text-primary-900">
                        {{ $i + 1 }}
                    </div>
                    <h3 class="mt-4 font-display font-bold text-primary-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm text-neutral-500">{{ $step['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Trust --}}
    <section class="bg-primary-900 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="max-w-xl font-display text-2xl font-bold text-white sm:text-3xl">Real properties. Real people. Real conversations.</h2>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    'Verified Listings' => 'Every listing goes through an admin review before going live.',
                    'Smart Matching' => 'Requirements are automatically scored against live properties.',
                    'Direct Contact' => 'Talk to owners and agents directly — no unnecessary middlemen.',
                    'Free to Post' => 'Posting a requirement or a listing costs nothing.',
                ] as $title => $body)
                    <div class="border-t border-white/10 pt-5">
                        <h3 class="font-display font-bold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-primary-200/70">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
        <h2 class="text-center font-display text-2xl font-bold text-primary-900 sm:text-3xl">Frequently asked questions</h2>
        <div class="mt-8 space-y-3">
            @foreach ([
                'Is it free to post a property requirement?' => 'Yes, posting a requirement is completely free for buyers and tenants.',
                'How does the matching score work?' => 'We compare your requirement against live properties on location, budget, area, category and more, then label results as Excellent, Good or Possible matches. It is an estimate, not a guarantee.',
                'Can I edit my requirement after posting it?' => 'Yes, you can edit, pause, close, renew or mark your requirement as fulfilled at any time from your dashboard.',
                'Are listed properties verified?' => 'Listings go through an admin review step before appearing publicly, and verified owners/agents carry a verification badge.',
            ] as $question => $answer)
                <details class="group rounded-2xl bg-neutral-50 p-4 open:shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-primary-900">
                        {{ $question }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-neutral-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm text-neutral-500">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </section>

</x-layouts.marketplace>
