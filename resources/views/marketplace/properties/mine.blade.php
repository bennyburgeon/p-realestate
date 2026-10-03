@php
    use App\Models\Status;

    $tabs = ['all' => 'All', 'drafts' => 'Drafts', 'pending' => 'Pending', 'live' => 'Live', 'sold' => 'Sold'];
@endphp

<x-layouts.marketplace title="My Properties">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">My Properties</h1>
                <p class="mt-1 text-sm text-slate-500">Track your listings from draft to sold.</p>
            </div>
            <a href="{{ route('properties.sell.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-800">
                + Sell a Property
            </a>
        </div>

        <div class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('properties.mine', ['tab' => $key]) }}"
                   class="shrink-0 rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === $key ? 'bg-white text-slate-900 shadow' : 'text-slate-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($properties->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                <p class="text-slate-600">No properties here yet.</p>
                <a href="{{ route('properties.sell.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-primary-700 hover:underline">Sell your first property &rarr;</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($properties as $property)
                    @php
                        $isDraft = $property->status_id === Status::PROPERTY_DRAFT;
                        $isPending = $property->status_id === Status::PROPERTY_PENDING_REVIEW;
                        $isChangesRequested = $property->status_id === Status::PROPERTY_CHANGES_REQUESTED;
                        $isLive = in_array($property->status_id, Status::publicPropertyStatuses());
                        $isSold = in_array($property->status_id, [Status::PROPERTY_SOLD, Status::PROPERTY_RENTED, Status::PROPERTY_LEASED]);
                    @endphp
                    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <x-status-badge :status="$property->status" />
                                </div>
                                <h2 class="mt-2 font-bold text-slate-900">{{ $property->title ?: 'Untitled draft' }}</h2>
                                <p class="mt-1 text-sm text-slate-500">{{ $property->location?->displayName() ?? 'No location set' }} &middot; {{ $property->displayPrice() ? '₹'.number_format((float) $property->displayPrice()) : 'Price not set' }}</p>
                                @if ($isChangesRequested)
                                    @php $latestReason = $property->statusHistories->first()?->reason; @endphp
                                    @if ($latestReason)
                                        <p class="mt-1 text-sm font-semibold text-amber-700">Reason: {{ $latestReason }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="flex gap-4 text-center">
                                <div>
                                    <p class="text-lg font-extrabold text-slate-800">{{ $property->views_count }}</p>
                                    <p class="text-xs text-slate-400">Views</p>
                                </div>
                                <div>
                                    <p class="text-lg font-extrabold text-primary-700">{{ $property->enquiries_count }}</p>
                                    <p class="text-xs text-slate-400">Enquiries</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                            @if ($isDraft)
                                <a href="{{ route('properties.sell.edit', $property) }}" class="rounded-lg bg-primary-900 px-3 py-1.5 text-xs font-bold text-white">Continue editing</a>
                                <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this draft?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-red-600">Delete</button>
                                </form>
                            @elseif ($isPending)
                                <a href="{{ route('properties.show', $property) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600">View</a>
                                <span class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">Awaiting admin review</span>
                            @elseif ($isChangesRequested)
                                <a href="{{ route('properties.sell.edit', $property) }}" class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-white">Edit &amp; Resubmit</a>
                            @elseif ($isLive)
                                <a href="{{ route('properties.show', $property) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600">View</a>
                                <a href="{{ route('properties.sell.edit', $property) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600">Edit</a>
                                <button type="button" onclick="document.getElementById('sold-form-{{ $property->id }}').classList.toggle('hidden')" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">Mark as Sold</button>
                            @elseif ($isSold)
                                <a href="{{ route('properties.show', $property) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600">View</a>
                            @endif
                        </div>

                        @if ($isLive)
                            <form id="sold-form-{{ $property->id }}" method="POST" action="{{ route('properties.mark-sold', $property) }}" class="mt-3 hidden space-y-2 rounded-xl bg-slate-50 p-3">
                                @csrf
                                <p class="text-xs font-semibold text-slate-600">Mark this property as sold? This will stop new enquiries and remove it from active sale results.</p>
                                <input type="number" name="sold_price" placeholder="Sold price (₹, private)" class="w-full rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                                <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">Confirm: Mark as Sold</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $properties->links() }}</div>
        @endif
    </div>
</x-layouts.marketplace>
