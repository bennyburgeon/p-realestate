<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavouriteController extends Controller
{
    public function index(): View
    {
        $properties = Property::query()
            ->whereHas('favouritedBy', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['location', 'category'])
            ->paginate(12);

        return view('marketplace.favourites.index', ['properties' => $properties]);
    }

    public function toggle(Property $property): RedirectResponse
    {
        $favourite = Favourite::where('user_id', auth()->id())->where('property_id', $property->id)->first();

        if ($favourite) {
            $favourite->delete();
            $message = 'Removed from favourites.';
        } else {
            Favourite::create(['user_id' => auth()->id(), 'property_id' => $property->id]);
            $message = 'Added to favourites.';
        }

        return back()->with('status', $message);
    }
}
