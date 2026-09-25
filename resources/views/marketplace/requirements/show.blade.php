@php
    $isOwner = auth()->check() && auth()->id() === $requirement->user_id;
    $canRespond = auth()->check() && ! $isOwner && auth()->user()->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin']);
@endphp

<x-layouts.marketplace :title="$requirement->title">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$requirement->status" />
                        <span class="text-xs font-mono text-slate-400">Ref: {{ $requirement->reference_number }}</span>
                    </div>
                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900">{{ $requirement->title }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Want to <strong>{{ $requirement->intent }}</strong> a {{ str_replace('_', ' ', $requirement->property_nature) }} property
                        in {{ $requirement->city?->name ?? 'any city' }}
                    </p>
                </div>

                @if ($isOwner && $requirement->isEditable())
                    <a href="{{ route('requirements.edit', $requirement) }}" class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                @endif
            </div>

            @if ($isOwner)
                <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                    @if ($requirement->status_id === \App\Models\Status::REQUIREMENT_ACTIVE)
                        <form method="POST" action="{{ route('requirements.pause', $requirement) }}">
                            @csrf
                            <button class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">Pause</button>
                        </form>
                    @elseif (in_array($requirement->status_id, [\App\Models\Status::REQUIREMENT_PAUSED, \App\Models\Status::REQUIREMENT_EXPIRED]))
                        <form method="POST" action="{{ route('requirements.resume', $requirement) }}">
                            @csrf
                            <button class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200">Resume</button>
                        </form>
                    @endif

                    @if ($requirement->isEditable())
                        <form method="POST" action="{{ route('requirements.fulfil', $requirement) }}">
                            @csrf
                            <button class="rounded-lg bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-200">Mark as Fulfilled</button>
                        </form>
                        <form method="POST" action="{{ route('requirements.close', $requirement) }}">
                            @csrf
                            <button class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">Close</button>
                        </form>
                    @endif

                    @if ($requirement->status_id === \App\Models\Status::REQUIREMENT_EXPIRED)
                        <form method="POST" action="{{ route('requirements.renew', $requirement) }}">
                            @csrf
                            <button class="rounded-lg bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200">Renew for 30 Days</button>
                        </form>
                    @endif

                    <button type="button" onclick="navigator.share ? navigator.share({title: document.title, url: window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Share</button>

                    <form method="POST" action="{{ route('requirements.destroy', $requirement) }}" onsubmit="return confirm('Delete this requirement permanently?')" class="ml-auto">
                        @csrf @method('DELETE')
                        <button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-100 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-400">Budget</p>
                <p class="mt-1 font-bold text-slate-900">
                    @if ($requirement->budget_min || $requirement->budget_max)
                        ₹{{ number_format((float) ($requirement->budget_min ?? 0)) }} &ndash; ₹{{ number_format((float) ($requirement->budget_max ?? 0)) }}
                    @else
                        Flexible
                    @endif
                </p>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-400">Area</p>
                <p class="mt-1 font-bold text-slate-900">
                    @if ($requirement->area_min || $requirement->area_max)
                        {{ number_format((float) ($requirement->area_min ?? 0)) }} &ndash; {{ number_format((float) ($requirement->area_max ?? 0)) }} {{ $requirement->area_unit }}
                    @else
                        Flexible
                    @endif
                </p>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-400">Bedrooms</p>
                <p class="mt-1 font-bold text-slate-900">{{ $requirement->bedrooms ? $requirement->bedrooms.'+' : 'Any' }}</p>
            </div>
        </div>

        @if ($requirement->additional_notes)
            <div class="mt-6 rounded-2xl border border-slate-100 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-400">Additional Notes</p>
                <p class="mt-1 text-sm text-slate-700">{{ $requirement->additional_notes }}</p>
            </div>
        @endif

        @if ($canSeeContact)
            <div class="mt-6 rounded-2xl border border-slate-100 bg-white p-5">
                <p class="text-xs font-bold uppercase text-slate-400">Contact</p>
                <p class="mt-1 text-sm text-slate-700">{{ $requirement->contact_name }} &middot; {{ $requirement->contact_phone }} @if($requirement->contact_email) &middot; {{ $requirement->contact_email }} @endif</p>
            </div>
        @endif

        {{-- Matching properties --}}
        <div class="mt-10">
            <h2 class="text-xl font-extrabold text-slate-900">{{ $matches->count() }} Matching {{ Str::plural('Property', $matches->count()) }}</h2>
            <p class="mt-1 text-sm text-slate-500">Ranked by how well they fit this requirement. Matches are estimates, not guarantees.</p>

            @if ($matches->isEmpty())
                <div class="mt-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    No matching properties yet &mdash; we'll keep checking as new listings are added.
                </div>
            @else
                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($matches as $match)
                        <div class="relative">
                            <div class="absolute left-3 top-3 z-10"><x-match-badge :band="$match->match_band" /></div>
                            <x-property-card :property="$match->property" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Responses --}}
        @if ($isOwner && $responses->isNotEmpty())
            <div class="mt-10">
                <h2 class="text-xl font-extrabold text-slate-900">{{ $responses->count() }} {{ Str::plural('Response', $responses->count()) }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($responses as $response)
                        <div class="rounded-2xl border border-slate-100 bg-white p-4">
                            <p class="font-semibold text-slate-800">{{ $response->responder->name }}</p>
                            @if ($response->property)
                                <a href="{{ route('properties.show', $response->property) }}" class="text-sm text-emerald-700 hover:underline">{{ $response->property->title }}</a>
                            @endif
                            <p class="mt-1 text-sm text-slate-600">{{ $response->message }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($canRespond)
            <div class="mt-10 rounded-2xl border border-slate-100 bg-white p-5">
                <h2 class="font-bold text-slate-900">Respond to This Requirement</h2>
                <form method="POST" action="{{ route('requirements.respond', $requirement) }}" class="mt-3 space-y-3">
                    @csrf
                    <select name="property_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Link one of my properties (optional)</option>
                        @foreach (auth()->user()->properties()->live()->get() as $ownProperty)
                            <option value="{{ $ownProperty->id }}">{{ $ownProperty->title }}</option>
                        @endforeach
                    </select>
                    <textarea name="message" rows="3" required placeholder="Introduce yourself and how you can help..."
                              class="w-full rounded-lg border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Send Response</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.marketplace>
