<?php
namespace App\Policies;
use App\Models\RentalRequest;
use App\Models\User;

class RentalRequestPolicy
{
    public function view(User $user, RentalRequest $request): bool
    {
        return $user->id === $request->user_id
            || $user->hasPermission('rental-requests.view')
            || ($request->space && $request->space->investor_id === $user->id);
    }
    public function approve(User $user, RentalRequest $request): bool
    {
        return $user->hasPermission('rental-requests.approve')
            || ($request->space && $request->space->investor_id === $user->id);
    }
    public function reject(User $user, RentalRequest $request): bool
    {
        return $this->approve($user, $request);
    }
}
