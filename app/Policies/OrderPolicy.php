<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, User $model): bool
    {
        return in_array($user->role_id, [1, 2]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role_id, [1, 2, 3]);
    }

    public function update(User $user, User $model): bool
    {
        return in_array($user->role_id, [1, 2, 3]);
    }

    public function delete(User $user, User $model): bool
    {
        return in_array($user->role_id, [1, 2]);
    }
    public function restore(User $user, User $model): bool
    {
        return in_array($user->role_id, [1]);
    }
}
