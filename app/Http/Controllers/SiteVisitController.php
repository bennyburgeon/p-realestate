<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleSiteVisitRequest;
use App\Http\Requests\UpdateSiteVisitStatusRequest;
use App\Models\Enquiry;
use App\Models\SiteVisit;
use App\Services\PropertyEnquiryService;
use Illuminate\Http\RedirectResponse;

class SiteVisitController extends Controller
{
    public function __construct(private readonly PropertyEnquiryService $enquiryService) {}

    public function schedule(ScheduleSiteVisitRequest $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('manage', $enquiry);

        $this->enquiryService->scheduleVisit($enquiry, $request->user(), $request->validated());

        return back()->with('status', 'Site visit scheduled.');
    }

    public function updateStatus(UpdateSiteVisitStatusRequest $request, SiteVisit $siteVisit): RedirectResponse
    {
        $this->authorize('manage', $siteVisit->enquiry);

        $this->enquiryService->updateVisitStatus($siteVisit, $request->validated('status'), $request->validated('notes'));

        return back()->with('status', 'Visit updated.');
    }
}
