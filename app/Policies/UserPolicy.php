<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (! $user->active) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->is_super_admin
            || $user->role === User::ROLE_ADMIN
            || $user->role === User::ROLE_CUSTOMER_SERVICE;
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->role === User::ROLE_ADMIN;
    }

    public function update(User $user, User $model): bool
    {
        if ($model->is_super_admin) {
            return false;
        }

        if ($user->is_super_admin) {
            return true;
        }

        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        if ($user->role === User::ROLE_CUSTOMER_SERVICE) {
            return $model->role === User::ROLE_CUSTOMER_SERVICE;
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->is_super_admin) {
            return false;
        }

        if ($user->id === $model->id) {
            return false;
        }

        if ($user->is_super_admin) {
            return true;
        }

        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        if ($user->role === User::ROLE_CUSTOMER_SERVICE) {
            return $model->role === User::ROLE_CUSTOMER_SERVICE;
        }

        return false;
    }
}
