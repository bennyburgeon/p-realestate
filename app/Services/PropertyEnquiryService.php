<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\Status;
use App\Models\User;
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

        return Enquiry::create([
            ...$data,
            'property_id' => $property->id,
            'user_id' => $user?->id,
            'status_id' => Status::ENQUIRY_NEW,
            'source' => $data['source'] ?? 'property_detail',
        ]);
    }
}
