<x-layouts.marketplace :title="'Edit ' . $property->title">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900">Edit Property</h1>
        <p class="mt-1 text-sm text-slate-500">Update details for {{ $property->title }}.</p>

        <form method="POST" action="{{ route('properties.update', $property) }}" class="mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')
            @include('marketplace.properties._form', ['property' => $property])
        </form>
    </div>
</x-layouts.marketplace>
