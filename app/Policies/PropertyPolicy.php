<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function before(?User $user): ?bool
    {
        return $user?->hasRole(['super_admin', 'admin']) ? true : null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Property $property): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'agent', 'developer']);
    }

    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->user_id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->user_id;
    }

    public function restore(User $user, Property $property): bool
    {
        return $user->id === $property->user_id;
    }

    public function forceDelete(User $user, Property $property): bool
    {
        return false;
    }
}
