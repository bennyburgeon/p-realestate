<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPropertyRequest;
use App\Http\Requests\Admin\RequestChangesRequest;
use App\Http\Requests\MarkPropertySoldRequest;
use App\Models\Property;
use App\Models\Status;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function show(Property $property): View
    {
        $property->load([
            'owner', 'location.parent', 'category', 'amenities', 'status',
            'statusHistories.actor', 'statusHistories.toStatus', 'statusHistories.fromStatus',
            'enquiries.status', 'enquiries.siteVisits',
        ]);

        return view('admin.properties.show', compact('property'));
    }

    public function approve(Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        $this->propertyService->approve($property, auth()->user());

        return back()->with('status', "\"{$property->title}\" approved and published.");
    }

    public function reject(RejectPropertyRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('reject', $property);

        $this->propertyService->reject($property, $request->user(), $request->validated('reason'), $request->validated('notes'));

        return back()->with('status', "\"{$property->title}\" rejected.");
    }

    public function requestChanges(RequestChangesRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('requestChanges', $property);

        $this->propertyService->requestChanges($property, $request->user(), $request->validated('reason'), $request->validated('notes'));

        return back()->with('status', 'Changes requested — owner notified.');
    }

    public function markSold(MarkPropertySoldRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('markAsSold', $property);

        $this->propertyService->markAsSold($property, $request->user(), $request->validated());

        return back()->with('status', 'Marked as sold.');
    }

    public function toggleFeatured(Property $property): RedirectResponse
    {
        $this->propertyService->toggleFeatured($property, ! $property->is_featured);

        return back()->with('status', $property->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    public function updateStatus(Request $request, Property $property): RedirectResponse
    {
        $request->validate(['status_id' => ['required', 'integer', 'exists:statuses,id']]);

        $this->propertyService->transitionStatus($property, $request->integer('status_id'), auth()->user());

        return back()->with('status', 'Status updated.');
    }

    public function toggleVerified(Property $property): RedirectResponse
    {
        $property->update(['is_verified' => ! $property->is_verified]);

        return back()->with('status', $property->is_verified ? 'Marked as verified.' : 'Verification removed.');
    }

    public function downloadDocument(Property $property, Media $media): Response
    {
        abort_unless($media->model_id === $property->id && $media->collection_name === 'documents', 404);

        return Storage::disk('local')->download($media->getPath());
    }
}
