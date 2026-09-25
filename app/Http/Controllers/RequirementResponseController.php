<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequirementResponseStoreRequest;
use App\Models\PropertyRequirement;
use App\Models\RequirementResponse;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;

class RequirementResponseController extends Controller
{
    public function __invoke(RequirementResponseStoreRequest $request, PropertyRequirement $propertyRequirement): RedirectResponse
    {
        RequirementResponse::updateOrCreate(
            [
                'property_requirement_id' => $propertyRequirement->id,
                'responder_id' => $request->user()->id,
                'property_id' => $request->validated('property_id'),
            ],
            [
                'status_id' => Status::RESPONSE_SENT,
                'message' => $request->validated('message'),
            ]
        );

        return back()->with('status', 'Your response has been sent to the requirement poster.');
    }
}
