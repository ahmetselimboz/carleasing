<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FleetManagementPolicy
{
    public function before(?User $user, string $ability): ?bool
    {
        if ($user === null || ! $user->active) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->canView($user);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->canView($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->canManage($user);
    }

    private function canView(User $user): bool
    {
        return $user->is_super_admin
            || $user->role === User::ROLE_ADMIN
            || $user->role === User::ROLE_CUSTOMER_SERVICE;
    }

    private function canManage(User $user): bool
    {
        return $user->is_super_admin || $user->role === User::ROLE_ADMIN;
    }
}
