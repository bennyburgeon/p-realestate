<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => PropertyCategory::withCount('properties')->orderBy('nature')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nature' => ['required', 'in:residential,commercial'],
        ]);

        PropertyCategory::create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
        ]);

        return back()->with('status', 'Category added.');
    }

    public function destroy(PropertyCategory $category): RedirectResponse
    {
        if ($category->properties()->exists()) {
            return back()->with('status', 'Cannot delete a category that has properties assigned to it.');
        }

        $category->delete();

        return back()->with('status', 'Category removed.');
    }
}
