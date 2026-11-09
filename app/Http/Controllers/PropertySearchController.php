<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySearchRequest;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\View\View;

class PropertySearchController extends Controller
{
    public function __invoke(PropertySearchRequest $request): View
    {
        $filters = $request->validated();

        $sort = $filters['sort'] ?? 'newest';

        $properties = Property::query()
            ->live()
            ->with(['location', 'category'])
            ->filterFromRequest($filters)
            ->when($sort === 'newest', fn ($q) => $q->latest('published_at'))
            ->when($sort === 'price_low', fn ($q) => $q->orderByRaw('COALESCE(price, rent_amount) asc'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByRaw('COALESCE(price, rent_amount) desc'))
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.properties.index', [
            'properties' => $properties,
            'filters' => $filters,
            'categories' => PropertyCategory::orderBy('sort_order')->get(),
            'cities' => Location::query()->cities()->orderedForPicker()->get(),
            'selectedLocality' => $this->selectedLocality($filters),
        ]);
    }

    /**
     * Resolve the active locality filter (a slug, scoped to the city filter when present)
     * so the locality picker can render it as the selected option.
     *
     * @param  array<string, mixed>  $filters
     */
    private function selectedLocality(array $filters): ?Location
    {
        if (empty($filters['locality'])) {
            return null;
        }

        return Location::query()
            ->where('type', Location::TYPE_LOCALITY)
            ->where('slug', $filters['locality'])
            ->when(! empty($filters['city']), fn ($query) => $query->whereHas('parent', fn ($parent) => $parent->where('slug', $filters['city'])))
            ->first();
    }
}
