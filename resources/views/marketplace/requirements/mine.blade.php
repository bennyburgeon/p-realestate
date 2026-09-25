<x-layouts.marketplace title="My Requirements">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">My Requirements</h1>
                <p class="mt-1 text-sm text-slate-500">Everything you've posted, and how it's performing.</p>
            </div>
            <a href="{{ route('requirements.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-600">
                + Post New Requirement
            </a>
        </div>

        @if ($requirements->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                <p class="text-slate-600">You haven't posted any requirements yet.</p>
                <a href="{{ route('requirements.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-emerald-700 hover:underline">Post your first requirement &rarr;</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($requirements as $requirement)
                    <a href="{{ route('requirements.show', $requirement) }}" class="block rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <x-status-badge :status="$requirement->status" />
                                    <span class="text-xs font-mono text-slate-400">{{ $requirement->reference_number }}</span>
                                </div>
                                <h2 class="mt-2 font-bold text-slate-900">{{ $requirement->title }}</h2>
                                <p class="mt-1 text-sm text-slate-500">Want to {{ $requirement->intent }} &middot; {{ $requirement->city?->name ?? 'Any city' }}</p>
                            </div>
                            <div class="flex gap-4 text-center">
                                <div>
                                    <p class="text-lg font-extrabold text-emerald-700">{{ $requirement->matches_count }}</p>
                                    <p class="text-xs text-slate-400">Matches</p>
                                </div>
                                <div>
                                    <p class="text-lg font-extrabold text-slate-800">{{ $requirement->responses_count }}</p>
                                    <p class="text-xs text-slate-400">Responses</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $requirements->links() }}</div>
        @endif
    </div>
</x-layouts.marketplace>
