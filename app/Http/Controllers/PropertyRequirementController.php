<?php

namespace App\Http\Controllers;

use App\Models\PropertyRequirement;
use App\Services\PropertyMatchingService;
use App\Services\PropertyRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyRequirementController extends Controller
{
    public function __construct(
        private readonly PropertyRequirementService $requirementService,
        private readonly PropertyMatchingService $matchingService,
    ) {}

    public function create(): View
    {
        return view('marketplace.requirements.post', ['propertyRequirement' => null]);
    }

    public function edit(PropertyRequirement $propertyRequirement): View
    {
        $this->authorize('update', $propertyRequirement);

        return view('marketplace.requirements.post', ['propertyRequirement' => $propertyRequirement]);
    }

    public function mine(): View
    {
        $requirements = auth()->user()->propertyRequirements()
            ->withCount(['matches', 'responses'])
            ->latest()
            ->paginate(10);

        return view('marketplace.requirements.mine', ['requirements' => $requirements]);
    }

    public function show(PropertyRequirement $propertyRequirement): View
    {
        $this->authorize('view', $propertyRequirement);

        $propertyRequirement->load(['city', 'category', 'user']);

        $matches = $this->matchingService->findMatchesForRequirement($propertyRequirement)
            ->sortByDesc('score')
            ->load('property.location', 'property.category');

        $canSeeContact = auth()->check() && (
            auth()->id() === $propertyRequirement->user_id
            || auth()->user()->hasRole(['owner', 'agent', 'developer', 'admin', 'super_admin'])
        );

        $responses = $propertyRequirement->responses()->with(['responder', 'property'])->latest()->get();

        return view('marketplace.requirements.show', [
            'requirement' => $propertyRequirement,
            'matches' => $matches,
            'canSeeContact' => $canSeeContact,
            'responses' => $responses,
        ]);
    }

    public function pause(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('update', $propertyRequirement);
        $this->requirementService->pause($propertyRequirement);

        return back()->with('status', 'Requirement paused. It will not be matched with new properties until resumed.');
    }

    public function resume(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('update', $propertyRequirement);
        $this->requirementService->resume($propertyRequirement);

        return back()->with('status', 'Requirement resumed and is active again.');
    }

    public function close(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('update', $propertyRequirement);
        $this->requirementService->close($propertyRequirement);

        return back()->with('status', 'Requirement closed.');
    }

    public function fulfil(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('update', $propertyRequirement);
        $this->requirementService->markFulfilled($propertyRequirement);

        return back()->with('status', 'Marked as fulfilled. Glad we could help!');
    }

    public function renew(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('update', $propertyRequirement);
        $this->requirementService->renew($propertyRequirement);

        return back()->with('status', 'Requirement renewed for another 30 days.');
    }

    public function destroy(PropertyRequirement $propertyRequirement): RedirectResponse
    {
        $this->authorize('delete', $propertyRequirement);
        $propertyRequirement->delete();

        return redirect()->route('requirements.mine')->with('status', 'Requirement deleted.');
    }
}
