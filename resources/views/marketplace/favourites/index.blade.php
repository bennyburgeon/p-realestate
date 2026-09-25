<x-layouts.marketplace title="My Favourites">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900">My Favourites</h1>
        <p class="mt-1 text-sm text-slate-500">Properties you've saved for later.</p>

        @if ($properties->isEmpty())
            <div class="mt-8 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                <p class="text-slate-600">You haven't saved any properties yet.</p>
                <a href="{{ route('properties.index') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-emerald-700 hover:underline">Browse properties &rarr;</a>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
            <div class="mt-8">{{ $properties->links() }}</div>
        @endif
    </div>
</x-layouts.marketplace>
