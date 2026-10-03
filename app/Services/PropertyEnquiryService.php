<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\SiteVisit;
use App\Models\Status;
use App\Models\User;
use App\Notifications\NewPropertyEnquiry;
use App\Notifications\SiteVisitScheduled;
use Illuminate\Validation\ValidationException;

class PropertyEnquiryService
{
    /**
     * @param  array{name: string, email?: ?string, phone: string, message?: ?string, source?: string}  $data
     */
    public function create(Property $property, array $data, ?User $user = null): Enquiry
    {
        $recentDuplicate = Enquiry::query()
            ->where('property_id', $property->id)
            ->where('phone', $data['phone'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($recentDuplicate) {
            throw ValidationException::withMessages([
                'phone' => 'You already sent an enquiry for this property a moment ago. Our team will get back to you shortly.',
            ]);
        }

        $enquiry = Enquiry::create([
            ...$data,
            'property_id' => $property->id,
            'user_id' => $user?->id,
            'status_id' => Status::ENQUIRY_NEW,
            'source' => $data['source'] ?? 'property_detail',
        ]);

        $enquiry->recordStatusChange(Status::ENQUIRY_NEW, null, $user);

        $property->owner->notify(new NewPropertyEnquiry($enquiry));

        return $enquiry;
    }

    /**
     * @param  array{scheduled_at: string, notes?: ?string}  $data
     */
    public function scheduleVisit(Enquiry $enquiry, User $actor, array $data): SiteVisit
    {
        $visit = SiteVisit::create([
            'enquiry_id' => $enquiry->id,
            'property_id' => $enquiry->property_id,
            'scheduled_by' => $actor->id,
            'scheduled_at' => $data['scheduled_at'],
            'notes' => $data['notes'] ?? null,
        ]);

        $from = $enquiry->status_id;
        $enquiry->update(['status_id' => Status::ENQUIRY_VISIT_SCHEDULED]);
        $enquiry->recordStatusChange(Status::ENQUIRY_VISIT_SCHEDULED, $from, $actor);

        $enquiry->user?->notify(new SiteVisitScheduled($visit));

        return $visit;
    }

    public function updateVisitStatus(SiteVisit $visit, string $status, ?string $notes = null): SiteVisit
    {
        $visit->update([
            'status' => $status,
            'notes' => $notes ?? $visit->notes,
        ]);

        return $visit;
    }
}
