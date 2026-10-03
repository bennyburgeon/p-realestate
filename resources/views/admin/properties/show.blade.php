@php
    use App\Models\Status;
@endphp

<x-layouts.admin :title="$property->title">
    <div x-data="{ tab: 'overview', showReject: false, showChanges: false, showSold: false }">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-900">{{ $property->title }}</h1>
                    <x-status-badge :status="$property->status" />
                </div>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $property->location?->displayName() }} &middot; Owner: {{ $property->owner->name }}
                    &middot; Created {{ $property->created_at->format('d M Y') }}
                    &middot; Updated {{ $property->updated_at->format('d M Y') }}
                </p>
            </div>
            <a href="{{ route('properties.show', $property) }}" target="_blank" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                View public page &nearr;
            </a>
        </div>

        {{-- Overview / actions --}}
        <div class="mb-6 flex flex-wrap gap-2 rounded-2xl border border-slate-100 bg-white p-4">
            @if (in_array($property->status_id, [Status::PROPERTY_PENDING_REVIEW, Status::PROPERTY_CHANGES_REQUESTED]))
                <form method="POST" action="{{ route('admin.properties.approve', $property) }}">
                    @csrf
                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Approve</button>
                </form>
                <button type="button" @click="showChanges = true" class="rounded-lg bg-amber-100 px-4 py-2 text-sm font-bold text-amber-700 hover:bg-amber-200">Request Changes</button>
                <button type="button" @click="showReject = true" class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-200">Reject</button>
            @endif

            @if (in_array($property->status_id, Status::publicPropertyStatuses()))
                <button type="button" @click="showSold = true" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">Mark as Sold</button>
            @endif

            <form method="POST" action="{{ route('admin.properties.toggle-featured', $property) }}">
                @csrf
                <button class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">{{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.properties.toggle-verified', $property) }}">
                @csrf
                <button class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">{{ $property->is_verified ? 'Unverify' : 'Verify' }}</button>
            </form>
        </div>

        {{-- Reject modal --}}
        <div x-show="showReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6" @click.outside="showReject = false">
                <h3 class="font-bold text-slate-900">Reject Property</h3>
                <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Reason</label>
                        <input type="text" name="reason" required class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showReject = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button>
                        <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Reject Property</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Request changes modal --}}
        <div x-show="showChanges" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6" @click.outside="showChanges = false">
                <h3 class="font-bold text-slate-900">Request Changes</h3>
                <form method="POST" action="{{ route('admin.properties.request-changes', $property) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-slate-700">What needs to change?</label>
                        <input type="text" name="reason" required placeholder="e.g. Please upload ownership document" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showChanges = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button>
                        <button class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-white hover:bg-amber-600">Send Request</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mark sold modal --}}
        <div x-show="showSold" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6" @click.outside="showSold = false">
                <h3 class="font-bold text-slate-900">Mark this property as sold?</h3>
                <p class="mt-1 text-sm text-slate-500">This will stop new enquiries and remove it from active results.</p>
                <form method="POST" action="{{ route('admin.properties.mark-sold', $property) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Sold price (₹) — private</label>
                        <input type="number" name="sold_price" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Buyer source</label>
                        <select name="buyer_source" class="mt-1.5 w-full rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="platform_enquiry">Platform enquiry</option>
                            <option value="external">External</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showSold = false" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button>
                        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">Mark as Sold</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 overflow-x-auto border-b border-slate-200">
            @foreach (['overview' => 'Overview', 'details' => 'Details', 'photos' => 'Photos', 'documents' => 'Documents', 'owner' => 'Owner', 'agent' => 'Agent', 'enquiries' => 'Enquiries', 'history' => 'History'] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        class="shrink-0 border-b-2 px-4 py-2.5 text-sm font-semibold"
                        :class="tab === '{{ $key }}' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="mt-5 rounded-2xl border border-slate-100 bg-white p-6">
            <div x-show="tab === 'overview'">
                <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                    <div><dt class="text-slate-400">Price</dt><dd class="font-semibold text-slate-800">{{ $property->displayPrice() ? '₹'.number_format((float) $property->displayPrice()) : '—' }}</dd></div>
                    <div><dt class="text-slate-400">Type</dt><dd class="font-semibold text-slate-800">{{ ucfirst($property->listing_type) }} &middot; {{ $property->category?->name }}</dd></div>
                    <div><dt class="text-slate-400">Location</dt><dd class="font-semibold text-slate-800">{{ $property->location?->displayName() }}</dd></div>
                    <div><dt class="text-slate-400">Views</dt><dd class="font-semibold text-slate-800">{{ $property->views_count }}</dd></div>
                    <div><dt class="text-slate-400">Enquiries</dt><dd class="font-semibold text-slate-800">{{ $property->enquiries->count() }}</dd></div>
                    <div><dt class="text-slate-400">Address visibility</dt><dd class="font-semibold text-slate-800">{{ $property->address_visibility ? 'Public' : 'Approximate only' }}</dd></div>
                </dl>
            </div>

            <div x-show="tab === 'details'" class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                @foreach ([
                    'Bedrooms' => $property->bedrooms, 'Bathrooms' => $property->bathrooms, 'Balconies' => $property->balconies,
                    'Built-up area' => $property->built_up_area ? "{$property->built_up_area} {$property->area_unit}" : null,
                    'Carpet area' => $property->carpet_area ? "{$property->carpet_area} {$property->area_unit}" : null,
                    'Plot area' => $property->plot_area ? "{$property->plot_area} {$property->area_unit}" : null,
                    'Furnishing' => $property->furnishing_status, 'Facing' => $property->facing, 'Parking' => $property->parking_details,
                    'Floor' => $property->floor_number, 'Possession' => $property->possession_status,
                    'Min. acceptable price (private)' => $property->min_acceptable_price ? '₹'.number_format((float) $property->min_acceptable_price) : null,
                    'Price per sqft' => $property->price_per_sqft ? '₹'.number_format((float) $property->price_per_sqft) : null,
                ] as $label => $value)
                    @if ($value)
                        <div><dt class="text-slate-400">{{ $label }}</dt><dd class="font-semibold text-slate-800">{{ $value }}</dd></div>
                    @endif
                @endforeach
                <div class="col-span-full">
                    <dt class="text-slate-400">Description</dt>
                    <dd class="mt-1 whitespace-pre-line text-slate-700">{{ $property->description }}</dd>
                </div>
                <div class="col-span-full">
                    <dt class="text-slate-400">Amenities</dt>
                    <dd class="mt-1 text-slate-700">{{ $property->amenities->pluck('name')->join(', ') ?: '—' }}</dd>
                </div>
            </div>

            <div x-show="tab === 'photos'" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @forelse ($property->getMedia('images') as $media)
                    <div class="relative overflow-hidden rounded-lg border {{ $media->getCustomProperty('is_cover') ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-slate-200' }}">
                        <img src="{{ $media->getUrl('thumb') ?: $media->getUrl() }}" class="h-28 w-full object-cover">
                        @if ($media->getCustomProperty('is_cover'))
                            <span class="absolute left-1 top-1 rounded bg-emerald-600 px-1.5 py-0.5 text-[10px] font-bold text-white">Cover</span>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full text-sm text-slate-400">No photos uploaded.</p>
                @endforelse
            </div>

            <div x-show="tab === 'documents'" class="space-y-2">
                @forelse ($property->getMedia('documents') as $media)
                    <a href="{{ route('admin.properties.documents.download', [$property, $media]) }}" class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        {{ $media->file_name }}
                        <span class="text-xs font-normal text-slate-400">Download</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">No documents uploaded.</p>
                @endforelse
            </div>

            <div x-show="tab === 'owner'" class="text-sm">
                <p class="font-semibold text-slate-800">{{ $property->owner->name }}</p>
                <p class="text-slate-500">{{ $property->owner->email ?? '—' }} &middot; {{ $property->owner->phone ?? '—' }}</p>
                <p class="mt-1 text-slate-500">Preferred contact: {{ ucfirst($property->contact_preference ?? 'phone') }} &middot; {{ $property->contact_phone }}</p>
            </div>

            <div x-show="tab === 'agent'" class="text-sm">
                @if ($property->agent_name)
                    <p class="font-semibold text-slate-800">{{ $property->agent_name }} @if ($property->agency_name) &middot; {{ $property->agency_name }} @endif</p>
                    <p class="text-slate-500">{{ $property->agent_phone }} &middot; {{ $property->agent_email ?? '—' }}</p>
                    @if ($property->agent_license_number)
                        <p class="mt-1 text-slate-500">License: {{ $property->agent_license_number }}</p>
                    @endif
                @else
                    <p class="text-slate-400">Listed by owner directly — no agent on this listing.</p>
                @endif
            </div>

            <div x-show="tab === 'enquiries'" class="space-y-3">
                @forelse ($property->enquiries as $enquiry)
                    <div class="rounded-lg border border-slate-200 p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-800">{{ $enquiry->name }}</p>
                            <x-status-badge :status="$enquiry->status" />
                        </div>
                        <p class="text-slate-500">{{ $enquiry->phone }} &middot; {{ $enquiry->created_at->format('d M Y, g:ia') }}</p>
                        @if ($enquiry->message)
                            <p class="mt-1 text-slate-600">{{ $enquiry->message }}</p>
                        @endif
                        @foreach ($enquiry->siteVisits as $visit)
                            <p class="mt-1 text-xs font-semibold text-sky-700">Visit {{ $visit->status }}: {{ $visit->scheduled_at->format('d M Y, g:ia') }}</p>
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No enquiries yet.</p>
                @endforelse
            </div>

            <div x-show="tab === 'history'" class="space-y-3">
                @forelse ($property->statusHistories as $entry)
                    <div class="border-l-2 border-slate-200 pl-3 text-sm">
                        <p class="font-semibold text-slate-800">
                            {{ $entry->fromStatus?->label ?? 'Created' }} &rarr; {{ $entry->toStatus->label }}
                            <span class="font-normal text-slate-400">by {{ $entry->actor?->name ?? 'System' }}</span>
                        </p>
                        <p class="text-xs text-slate-400">{{ $entry->created_at->format('d M Y, g:ia') }}</p>
                        @if ($entry->reason)
                            <p class="mt-1 text-slate-600">Reason: {{ $entry->reason }}</p>
                        @endif
                        @if ($entry->notes)
                            <p class="text-slate-500">{{ $entry->notes }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No history yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
