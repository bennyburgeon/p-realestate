<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationSearchController extends Controller
{
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
}
