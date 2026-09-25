<x-layouts.admin title="Enquiries">
    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" @selected(request('status') == $status->id)>{{ $status->label }}</option>
            @endforeach
        </select>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Property</th>
                    <th class="px-4 py-3">From</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($enquiries as $enquiry)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('properties.show', $enquiry->property) }}" target="_blank" class="font-semibold text-slate-800 hover:text-emerald-700">{{ Str::limit($enquiry->property->title, 30) }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $enquiry->name }}<br><span class="text-xs text-slate-400">{{ $enquiry->phone }}</span></td>
                        <td class="px-4 py-3 text-slate-600">{{ Str::limit($enquiry->message, 60) }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$enquiry->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.enquiries.status', $enquiry) }}" class="flex justify-end gap-1.5">
                                @csrf
                                <select name="status_id" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" @selected($enquiry->status_id === $status->id)>{{ $status->label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $enquiries->links() }}</div>
</x-layouts.admin>
