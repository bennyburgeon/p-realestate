<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyRequirement;
use App\Models\Status;
use App\Services\PropertyRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequirementController extends Controller
{
    public function __construct(private readonly PropertyRequirementService $requirementService) {}

    public function index(Request $request): View
    {
        $requirements = PropertyRequirement::query()
            ->with(['user', 'city', 'status'])
            ->withCount(['matches', 'responses'])
            ->when($request->filled('status'), fn ($q) => $q->where('status_id', $request->integer('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = Status::where('group', 'requirement')->orderBy('sort_order')->get();

        return view('admin.requirements.index', compact('requirements', 'statuses'));
    }

    public function close(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->requirementService->close($propertyRequirement);

        return back()->with('status', 'Requirement closed.');
    }
}
