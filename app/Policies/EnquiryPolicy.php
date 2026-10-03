<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;

class EnquiryPolicy
{
    public function before(?User $user): ?bool
    {
        return $user?->hasRole(['super_admin', 'admin']) ? true : null;
    }

    public function manage(User $user, Enquiry $enquiry): bool
    {
        return $user->id === $enquiry->property->user_id;
    }
}
