<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Status;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertyService $propertyService) {}

    public function index(Request $request): View
    {
        $properties = Property::query()
            ->with(['owner', 'location', 'category', 'status'])
            ->when($request->filled('status'), fn ($q) => $q->where('status_id', $request->integer('status')))
            ->when($request->filled('keyword'), fn ($q) => $q->where('title', 'like', '%'.$request->string('keyword').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = Status::where('group', 'property')->orderBy('sort_order')->get();

        return view('admin.properties.index', compact('properties', 'statuses'));
    }

    public function approve(Property $property): RedirectResponse
    {
        $this->propertyService->approve($property);

        return back()->with('status', "\"{$property->title}\" approved and published.");
    }

    public function reject(Property $property): RedirectResponse
    {
        $this->propertyService->reject($property);

        return back()->with('status', "\"{$property->title}\" rejected.");
    }

    public function toggleFeatured(Property $property): RedirectResponse
    {
        $this->propertyService->toggleFeatured($property, ! $property->is_featured);

        return back()->with('status', $property->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    public function updateStatus(Request $request, Property $property): RedirectResponse
    {
        $request->validate(['status_id' => ['required', 'integer', 'exists:statuses,id']]);

        $this->propertyService->transitionStatus($property, $request->integer('status_id'));

        return back()->with('status', 'Status updated.');
    }

    public function toggleVerified(Property $property): RedirectResponse
    {
        $property->update(['is_verified' => ! $property->is_verified]);

        return back()->with('status', $property->is_verified ? 'Marked as verified.' : 'Verification removed.');
    }
}
