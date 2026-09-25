<x-layouts.admin title="Locations">
    <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-5">
        <h2 class="font-bold text-slate-900">Add City or Locality</h2>
        <form method="POST" action="{{ route('admin.locations.store') }}" class="mt-3 flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-slate-500">Type</label>
                <select name="type" class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="city">City</option>
                    <option value="locality">Locality</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Name</label>
                <input type="text" name="name" required class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Parent City (for locality)</label>
                <select name="parent_id" class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">&mdash;</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">State</label>
                <input type="text" name="state" class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Add</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr><th class="px-4 py-3">City</th><th class="px-4 py-3">Localities</th><th class="px-4 py-3">Properties</th><th class="px-4 py-3">Popular</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($cities as $city)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $city->name }} <span class="text-xs text-slate-400">{{ $city->state }}</span></td>
                        <td class="px-4 py-3 text-slate-600">{{ $city->localities_count }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $city->properties_count }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.locations.toggle-featured', $city) }}">
                                @csrf
                                <button class="rounded-full px-2 py-1 text-xs font-bold {{ $city->is_popular ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $city->is_popular ? 'Popular' : 'Mark Popular' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.locations.destroy', $city) }}" onsubmit="return confirm('Delete this location?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
