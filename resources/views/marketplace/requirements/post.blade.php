<x-layouts.marketplace title="Post a Property Requirement">
    <livewire:post-requirement-wizard :property-requirement="$propertyRequirement" :key="$propertyRequirement?->id ?? 'new'" />
</x-layouts.marketplace>
