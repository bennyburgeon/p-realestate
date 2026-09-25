<x-layouts.admin title="Dashboard">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ([
            ['label' => 'Total Properties', 'value' => $stats['total_properties']],
            ['label' => 'Published', 'value' => $stats['published_properties']],
            ['label' => 'Pending Approval', 'value' => $stats['pending_approvals'], 'highlight' => true],
            ['label' => 'Sold', 'value' => $stats['sold_properties']],
            ['label' => 'Rented', 'value' => $stats['rented_properties']],
            ['label' => 'Total Users', 'value' => $stats['total_users']],
            ['label' => 'New Users (7d)', 'value' => $stats['new_users_7d']],
            ['label' => 'Active Requirements', 'value' => $stats['active_requirements']],
            ['label' => 'Unresolved Enquiries', 'value' => $stats['unresolved_enquiries'], 'highlight' => true],
        ] as $card)
            <div class="rounded-2xl border border-slate-100 bg-white p-5 {{ ($card['highlight'] ?? false) && $card['value'] > 0 ? 'ring-2 ring-amber-300' : '' }}">
                <p class="text-2xl font-extrabold text-slate-900">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs font-semibold uppercase text-slate-400">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <h2 class="font-bold text-slate-900">Property Category Distribution</h2>
            <div class="mt-3 space-y-2">
                @foreach ($categoryDistribution as $category)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">{{ $category->name }}</span>
                        <span class="font-bold text-slate-900">{{ $category->properties_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <h2 class="font-bold text-slate-900">Top Locations</h2>
            <div class="mt-3 space-y-2">
                @foreach ($topLocations as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">{{ $row->location?->displayName() ?? 'Unknown' }}</span>
                        <span class="font-bold text-slate-900">{{ $row->total }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Recent Property Submissions</h2>
                <a href="{{ route('admin.properties.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">View all</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse ($recentProperties as $property)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">{{ Str::limit($property->title, 35) }}</p>
                            <p class="text-xs text-slate-400">{{ $property->owner->name }}</p>
                        </div>
                        <x-status-badge :status="$property->status" />
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No properties yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Recent Requirement Submissions</h2>
                <a href="{{ route('admin.requirements.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">View all</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse ($recentRequirements as $requirement)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">{{ Str::limit($requirement->title, 35) }}</p>
                            <p class="text-xs text-slate-400">{{ $requirement->user->name }}</p>
                        </div>
                        <x-status-badge :status="$requirement->status" />
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No requirements yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
