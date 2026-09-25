<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Services\PropertyMatchingService;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyMatchingService $matchingService,
    ) {}

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load(['location.parent', 'category', 'owner', 'amenities']);
        $property->increment('views_count');

        $similar = Property::query()
            ->live()
            ->where('id', '!=', $property->id)
            ->where('property_category_id', $property->property_category_id)
            ->where('location_id', $property->location_id)
            ->take(4)
            ->get();

        $matchingRequirements = auth()->check() && auth()->user()->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin'])
            ? $this->matchingService->findMatchingRequirementsForProperty($property)
                ->sortByDesc('score')
                ->take(5)
                ->load('requirement')
            : collect();

        return view('marketplace.properties.show', [
            'property' => $property,
            'similarProperties' => $similar,
            'matchingRequirements' => $matchingRequirements,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('marketplace.properties.create', [
            'categories' => PropertyCategory::orderBy('sort_order')->get(),
            'localities' => Location::where('type', Location::TYPE_LOCALITY)->orderBy('name')->get(),
            'amenities' => Amenity::orderBy('sort_order')->get(),
        ]);
    }

    public function store(PropertyStoreRequest $request): RedirectResponse
    {
        $property = $this->propertyService->create($request->user(), $request->validated());
        $this->propertyService->submitForReview($property);

        return redirect()->route('properties.show', $property)
            ->with('status', 'Your property has been submitted and is pending admin review.');
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        return view('marketplace.properties.edit', [
            'property' => $property->load('amenities'),
            'categories' => PropertyCategory::orderBy('sort_order')->get(),
            'localities' => Location::where('type', Location::TYPE_LOCALITY)->orderBy('name')->get(),
            'amenities' => Amenity::orderBy('sort_order')->get(),
        ]);
    }

    public function update(PropertyUpdateRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $this->propertyService->update($property, $request->validated());

        return redirect()->route('properties.show', $property)->with('status', 'Property updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return redirect()->route('dashboard')->with('status', 'Property removed.');
    }
}
