<?php

namespace App\Policies;

use App\Models\PropertyRequirement;
use App\Models\User;

class PropertyRequirementPolicy
{
    public function before(?User $user): ?bool
    {
        return $user?->hasRole(['super_admin', 'admin']) ? true : null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, PropertyRequirement $propertyRequirement): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PropertyRequirement $propertyRequirement): bool
    {
        return $user->id === $propertyRequirement->user_id;
    }

    public function delete(User $user, PropertyRequirement $propertyRequirement): bool
    {
        return $user->id === $propertyRequirement->user_id;
    }

    public function respond(User $user, PropertyRequirement $propertyRequirement): bool
    {
        return $user->hasRole(['owner', 'agent', 'developer']);
    }
}
