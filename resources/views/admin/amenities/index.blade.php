<x-layouts.admin title="Amenities">
    <div class="mb-6 rounded-2xl border border-slate-100 bg-white p-5">
        <h2 class="font-bold text-slate-900">Add Amenity</h2>
        <form method="POST" action="{{ route('admin.amenities.store') }}" class="mt-3 flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-slate-500">Name</label>
                <input type="text" name="name" required class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Category</label>
                <input type="text" name="category" placeholder="e.g. lifestyle" class="mt-1 rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Add</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Properties</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($amenities as $amenity)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $amenity->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $amenity->category }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $amenity->properties_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}" onsubmit="return confirm('Delete this amenity?')">
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
