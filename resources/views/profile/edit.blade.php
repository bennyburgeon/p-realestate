<x-layouts.marketplace title="My Profile">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900">My Profile</h1>
        <p class="mt-1 text-sm text-slate-500">Manage your details and how you use BHKnow.</p>

        <div class="mt-8 space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                @include('profile.partials.role-selector-form')
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            @if ($user->password)
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            @endif

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-layouts.marketplace>
