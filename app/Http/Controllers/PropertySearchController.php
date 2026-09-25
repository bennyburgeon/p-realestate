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
            'cities' => Location::where('type', Location::TYPE_CITY)->orderBy('name')->get(),
        ]);
    }
}
