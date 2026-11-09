<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationSearchController extends Controller
{
    private const int OPTIONS_PER_PAGE = 20;

    public function index(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));

        if (mb_strlen($keyword) < 2) {
            return response()->json([]);
        }

        $locations = Location::query()
            ->with('parent')
            ->where('name', 'like', "{$keyword}%")
            ->orderByRaw("CASE WHEN type = 'city' THEN 0 ELSE 1 END")
            ->orderBy('is_popular', 'desc')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json($locations->map(fn (Location $location) => [
            'id' => $location->id,
            'slug' => $location->slug,
            'name' => $location->name,
            'type' => $location->type,
            'display' => $location->displayName(),
            'parent_slug' => $location->type === Location::TYPE_LOCALITY ? $location->parent?->slug : null,
        ]));
    }

    /**
     * Paginated location options in Select2's AJAX format.
     *
     * Query parameters:
     *  - q:      search term (prefix match on the name; optional)
     *  - type:   city | locality (optional; both when omitted)
     *  - parent: id or slug of a city to restrict localities to (optional)
     *  - key:    id | slug — which column is used as the option value (default: id)
     *  - page:   1-based page number
     */
    public function options(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:'.Location::TYPE_CITY.','.Location::TYPE_LOCALITY],
            'parent' => ['nullable', 'string', 'max:191'],
            'key' => ['nullable', 'in:id,slug'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $keyword = trim($validated['q'] ?? '');
        $valueKey = $validated['key'] ?? 'id';
        $parentReference = $validated['parent'] ?? null;

        $locations = Location::query()
            ->with('parent')
            ->when($validated['type'] ?? null, fn ($query, string $type) => $query->where('type', $type))
            ->when($parentReference, function ($query, string $parentReference) {
                $query->whereHas('parent', fn ($parent) => $parent
                    ->where('id', $parentReference)
                    ->orWhere('slug', $parentReference));
            })
            ->when($keyword !== '', fn ($query) => $query->where('name', 'like', "{$keyword}%"))
            ->orderByRaw("CASE WHEN type = 'city' THEN 0 ELSE 1 END")
            ->orderBy('is_popular', 'desc')
            ->orderBy('name')
            ->simplePaginate(self::OPTIONS_PER_PAGE, ['*'], 'page', $validated['page'] ?? 1);

        return response()->json([
            'results' => $locations->getCollection()->map(fn (Location $location) => [
                'id' => $location->{$valueKey},
                'text' => $location->displayName(),
                'meta' => $location->regionLabel(),
                'type' => $location->type,
            ])->values(),
            'pagination' => ['more' => $locations->hasMorePages()],
        ]);
    }
}
