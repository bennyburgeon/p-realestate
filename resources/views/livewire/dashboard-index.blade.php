<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Welcome back, {{ Str::before(auth()->user()->name, ' ') }}</h1>
                <p class="mt-1 text-sm text-slate-500">Manage your listings, requirements and saved properties.</p>
            </div>
            <div class="flex gap-2">
                @if ($isLister)
                    <a href="{{ route('properties.create') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">+ Add Property</a>
                @endif
                <a href="{{ route('requirements.create') }}" class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-600">+ Post Requirement</a>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1">
            @if ($isLister)
                <button wire:click="setTab('properties')" class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'properties' ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">My Properties</button>
            @endif
            <button wire:click="setTab('requirements')" class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'requirements' ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">My Requirements</button>
            <button wire:click="setTab('favourites')" class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'favourites' ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">Favourites</button>
            @if ($isLister)
                <button wire:click="setTab('my-responses')" class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'my-responses' ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">Responses Sent</button>
                <button wire:click="setTab('matches-for-me')" class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'matches-for-me' ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">Matching Requirements</button>
            @endif
        </div>

        <div class="mt-6">
            {{-- Properties --}}
            @if ($tab === 'properties' && $properties)
                @if ($properties->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <p class="text-slate-600">You haven't listed any properties yet.</p>
                        <a href="{{ route('properties.create') }}" class="mt-3 inline-flex text-sm font-bold text-emerald-700 hover:underline">List your first property &rarr;</a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($properties as $property)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-white p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $property->getFirstMediaUrl('images', 'thumb') ?: asset('images/property-placeholder.svg') }}" class="h-16 w-20 rounded-lg object-cover">
                                    <div>
                                        <a href="{{ route('properties.show', $property) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ $property->title }}</a>
                                        <p class="text-xs text-slate-400">{{ $property->location?->displayName() }}</p>
                                        <x-status-badge :status="$property->status" class="mt-1" />
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('properties.edit', $property) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this property?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $properties->links() }}</div>
                @endif
            @endif

            {{-- Requirements --}}
            @if ($tab === 'requirements' && $requirements)
                @if ($requirements->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <p class="text-slate-600">You haven't posted any requirements yet.</p>
                        <a href="{{ route('requirements.create') }}" class="mt-3 inline-flex text-sm font-bold text-emerald-700 hover:underline">Post your first requirement &rarr;</a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($requirements as $requirement)
                            <a href="{{ route('requirements.show', $requirement) }}" class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-white p-4 hover:shadow-md">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-status-badge :status="$requirement->status" />
                                        <span class="text-xs font-mono text-slate-400">{{ $requirement->reference_number }}</span>
                                    </div>
                                    <p class="mt-1 font-semibold text-slate-800">{{ $requirement->title }}</p>
                                </div>
                                <div class="flex gap-4 text-center text-xs text-slate-400">
                                    <div><span class="block text-base font-extrabold text-emerald-700">{{ $requirement->matches_count }}</span>Matches</div>
                                    <div><span class="block text-base font-extrabold text-slate-800">{{ $requirement->responses_count }}</span>Responses</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $requirements->links() }}</div>
                @endif
            @endif

            {{-- Favourites --}}
            @if ($tab === 'favourites' && $favourites)
                @if ($favourites->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <p class="text-slate-600">No saved properties yet.</p>
                        <a href="{{ route('properties.index') }}" class="mt-3 inline-flex text-sm font-bold text-emerald-700 hover:underline">Browse properties &rarr;</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($favourites as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $favourites->links() }}</div>
                @endif
            @endif

            {{-- Responses sent --}}
            @if ($tab === 'my-responses' && $myResponses)
                @if ($myResponses->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <p class="text-slate-600">You haven't responded to any requirements yet.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($myResponses as $response)
                            <div class="rounded-2xl border border-slate-100 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('requirements.show', $response->requirement) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ $response->requirement->title }}</a>
                                    <x-status-badge :status="$response->status" />
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $response->message }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $myResponses->links() }}</div>
                @endif
            @endif

            {{-- Matches for me --}}
            @if ($tab === 'matches-for-me')
                @if ($matchingRequirements->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                        <p class="text-slate-600">No active requirements currently match your live properties.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($matchingRequirements as $match)
                            <a href="{{ route('requirements.show', $match->requirement) }}" class="rounded-2xl border border-slate-100 bg-white p-4 hover:shadow-md">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-mono text-slate-400">{{ $match->requirement->reference_number }}</span>
                                    <x-match-badge :band="$match->match_band" />
                                </div>
                                <p class="mt-2 font-semibold text-slate-800">{{ $match->requirement->title }}</p>
                                <p class="text-sm text-slate-500">Budget: ₹{{ number_format((float) ($match->requirement->budget_min ?? 0)) }} - ₹{{ number_format((float) ($match->requirement->budget_max ?? 0)) }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
