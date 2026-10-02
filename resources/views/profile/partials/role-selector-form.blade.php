@php
    $roleOptions = [
        'buyer_tenant' => ['label' => 'Buyer / Tenant', 'description' => "I'm looking to buy or rent a property"],
        'owner' => ['label' => 'Property Owner', 'description' => 'I want to list my property for sale or rent'],
        'agent' => ['label' => 'Agent / Broker', 'description' => 'I help clients buy, rent, or sell properties'],
    ];
    $userRoles = $user->getRoleNames();
@endphp

<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">I am a:</h2>
        <p class="mt-1 text-sm text-slate-500">Select every option that applies — you can change this anytime.</p>
    </header>

    <form method="post" action="{{ route('profile.roles.update') }}" class="mt-5 space-y-3">
        @csrf
        @method('patch')

        @foreach ($roleOptions as $value => $option)
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3.5 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                <input type="checkbox" name="roles[]" value="{{ $value }}" class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                       @checked($userRoles->contains($value))>
                <span>
                    <span class="block text-sm font-semibold text-slate-900">{{ $option['label'] }}</span>
                    <span class="block text-sm text-slate-500">{{ $option['description'] }}</span>
                </span>
            </label>
        @endforeach

        <x-input-error :messages="$errors->get('roles')" class="mt-1" />

        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">
                Save Profile
            </button>

            @if (session('roles_status') === 'roles-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-slate-500">
                    Saved.
                </p>
            @endif
        </div>
    </form>
</section>
