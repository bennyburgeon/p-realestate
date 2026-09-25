<x-layouts.marketplace title="List a Property">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900">List Your Property</h1>
        <p class="mt-1 text-sm text-slate-500">Provide accurate details to help the right buyers and tenants find it.</p>

        <form method="POST" action="{{ route('properties.store') }}" class="mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @include('marketplace.properties._form', ['property' => null])
        </form>
    </div>
</x-layouts.marketplace>
