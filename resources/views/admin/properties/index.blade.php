<x-layouts.admin title="Properties">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Search title..." class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All Statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" @selected(request('status') == $status->id)>{{ $status->label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Property</th>
                    <th class="px-4 py-3">Owner</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Flags</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($properties as $property)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('properties.show', $property) }}" target="_blank" class="font-semibold text-slate-800 hover:text-emerald-700">{{ Str::limit($property->title, 40) }}</a>
                            <p class="text-xs text-slate-400">{{ $property->location?->displayName() }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $property->owner->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ ucfirst($property->listing_type) }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$property->status" /></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                @if ($property->is_featured)<span class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-700">FEATURED</span>@endif
                                @if ($property->is_verified)<span class="rounded bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-700">VERIFIED</span>@endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1.5">
                                @if ($property->status_id === \App\Models\Status::PROPERTY_PENDING_REVIEW)
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-200">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}">
                                        @csrf
                                        <button class="rounded-lg bg-red-100 px-2.5 py-1.5 text-xs font-bold text-red-700 hover:bg-red-200">Reject</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.properties.toggle-featured', $property) }}">
                                    @csrf
                                    <button class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">{{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.properties.toggle-verified', $property) }}">
                                    @csrf
                                    <button class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">{{ $property->is_verified ? 'Unverify' : 'Verify' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $properties->links() }}</div>
</x-layouts.admin>
