<x-layouts.admin title="Property Requirements">
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
                    <th class="px-4 py-3">Requirement</th>
                    <th class="px-4 py-3">Posted By</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Matches</th>
                    <th class="px-4 py-3">Responses</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($requirements as $requirement)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('requirements.show', $requirement) }}" target="_blank" class="font-semibold text-slate-800 hover:text-emerald-700">{{ Str::limit($requirement->title, 40) }}</a>
                            <p class="text-xs font-mono text-slate-400">{{ $requirement->reference_number }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $requirement->user->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$requirement->status" /></td>
                        <td class="px-4 py-3 text-slate-600">{{ $requirement->matches_count }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $requirement->responses_count }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($requirement->status_id === \App\Models\Status::REQUIREMENT_ACTIVE)
                                <form method="POST" action="{{ route('admin.requirements.close', $requirement) }}">
                                    @csrf
                                    <button class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Close</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $requirements->links() }}</div>
</x-layouts.admin>
