<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnquiryStoreRequest;
use App\Models\Property;
use App\Services\PropertyEnquiryService;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function __invoke(EnquiryStoreRequest $request, Property $property, PropertyEnquiryService $enquiryService): RedirectResponse
    {
        $enquiryService->create($property, $request->validated(), $request->user());

        return back()->with('status', 'Your enquiry has been sent to the owner/agent. They will contact you shortly.');
    }
}
