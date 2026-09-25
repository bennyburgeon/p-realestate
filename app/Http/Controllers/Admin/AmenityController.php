<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(): View
    {
        return view('admin.amenities.index', [
            'amenities' => Amenity::withCount('properties')->orderBy('category')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
        ]);

        Amenity::create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
        ]);

        return back()->with('status', 'Amenity added.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $amenity->properties()->detach();
        $amenity->delete();

        return back()->with('status', 'Amenity removed.');
    }
}
