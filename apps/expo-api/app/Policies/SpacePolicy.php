<?php
namespace App\Policies;
use App\Models\Space;
use App\Models\User;

class SpacePolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Space $space): bool { return true; }
    public function create(User $user): bool { return $user->hasPermission('spaces.create'); }
    public function update(User $user, Space $space): bool
    {
        return $user->hasPermission('spaces.update') || $space->investor_id === $user->id;
    }
    public function delete(User $user, Space $space): bool { return $user->hasPermission('spaces.delete'); }
}
