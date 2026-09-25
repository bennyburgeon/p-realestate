<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\RequirementResponse;
use App\Services\PropertyMatchingService;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardIndex extends Component
{
    use WithPagination;

    public string $tab = 'properties';

    public function mount(): void
    {
        $user = auth()->user();
        $this->tab = $user->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin']) ? 'properties' : 'requirements';
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render(PropertyMatchingService $matchingService)
    {
        $user = auth()->user();
        $isLister = $user->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin']);

        $properties = $isLister && $this->tab === 'properties'
            ? $user->properties()->with(['location', 'category', 'status'])->latest()->paginate(8, pageName: 'properties-page')
            : null;

        $requirements = $this->tab === 'requirements'
            ? $user->propertyRequirements()->with('status')->withCount(['matches', 'responses'])->latest()->paginate(8, pageName: 'requirements-page')
            : null;

        $favourites = $this->tab === 'favourites'
            ? Property::query()->whereHas('favouritedBy', fn ($q) => $q->where('user_id', $user->id))->with(['location', 'category', 'status'])->paginate(8, pageName: 'favourites-page')
            : null;

        $myResponses = $isLister && $this->tab === 'my-responses'
            ? RequirementResponse::where('responder_id', $user->id)->with(['requirement', 'property', 'status'])->latest()->paginate(8, pageName: 'responses-page')
            : null;

        $matchingRequirements = collect();
        if ($isLister && $this->tab === 'matches-for-me') {
            foreach ($user->properties()->live()->get() as $property) {
                $matchingRequirements = $matchingRequirements->concat(
                    $matchingService->findMatchingRequirementsForProperty($property)->load('requirement')
                );
            }
            $matchingRequirements = $matchingRequirements->sortByDesc('score')->unique('property_requirement_id')->take(20);
        }

        return view('livewire.dashboard-index', [
            'isLister' => $isLister,
            'properties' => $properties,
            'requirements' => $requirements,
            'favourites' => $favourites,
            'myResponses' => $myResponses,
            'matchingRequirements' => $matchingRequirements,
        ]);
    }
}
