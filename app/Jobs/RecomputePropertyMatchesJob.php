<?php

namespace App\Jobs;

use App\Models\Property;
use App\Models\PropertyRequirement;
use App\Services\PropertyMatchingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecomputePropertyMatchesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property|PropertyRequirement $subject,
    ) {}

    public function handle(PropertyMatchingService $matchingService): void
    {
        if ($this->subject instanceof PropertyRequirement) {
            $matchingService->findMatchesForRequirement($this->subject);
        } else {
            $matchingService->findMatchingRequirementsForProperty($this->subject);
        }
    }
}
