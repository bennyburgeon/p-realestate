<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('admin.locations.index', [
            'cities' => Location::where('type', Location::TYPE_CITY)
                ->withCount(['localities', 'properties'])
                ->orderByRaw('district is null')
                ->orderBy('district')
                ->orderBy('name')
                ->paginate(50)
                ->withQueryString(),
            'allCities' => Location::where('type', Location::TYPE_CITY)
                ->orderByRaw('district is null')
                ->orderBy('district')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:city,locality'],
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:locations,id', 'required_if:type,locality'],
            'state' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $parent = $data['type'] === Location::TYPE_LOCALITY ? Location::find($data['parent_id']) : null;

        Location::create([
            ...$data,
            'parent_id' => $parent?->id,
            // A locality sits in its city's region unless told otherwise.
            'state' => $data['state'] ?? $parent?->state,
            'district' => $data['district'] ?? $parent?->district,
            'country' => $data['country'] ?? $parent?->country,
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
        ]);

        return back()->with('status', 'Location added.');
    }

    public function toggleFeatured(Location $location): RedirectResponse
    {
        $location->update(['is_popular' => ! $location->is_popular]);

        return back()->with('status', $location->is_popular ? 'Marked as popular.' : 'Removed from popular.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->properties()->exists() || $location->localities()->exists()) {
            return back()->with('status', 'Cannot delete a location that is in use.');
        }

        $location->delete();

        return back()->with('status', 'Location removed.');
    }
}
