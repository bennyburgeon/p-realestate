<x-layouts.marketplace title="Sell Your Property">
    <livewire:sell-property-wizard :property="$property" :key="$property?->id ?? 'new'" />
</x-layouts.marketplace>
