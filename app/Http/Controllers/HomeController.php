<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyRequirement;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $baseQuery = Property::query()->live()->with(['location', 'category']);

        return view('marketplace.home', [
            'featuredProperties' => (clone $baseQuery)->where('is_featured', true)->latest('published_at')->take(6)->get(),
            'newProperties' => (clone $baseQuery)->latest('published_at')->take(8)->get(),
            'saleProperties' => (clone $baseQuery)->where('listing_type', Property::LISTING_SALE)->latest('published_at')->take(8)->get(),
            'rentProperties' => (clone $baseQuery)->where('listing_type', Property::LISTING_RENT)->latest('published_at')->take(8)->get(),
            'recentRequirements' => PropertyRequirement::query()
                ->active()
                ->with('city')
                ->latest()
                ->take(6)
                ->get(),
            'categories' => PropertyCategory::orderBy('sort_order')->get(),
            'popularCities' => Location::where('type', Location::TYPE_CITY)->where('is_popular', true)->orderBy('name')->get(),
        ]);
    }
}
