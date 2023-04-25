<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user): bool
    {
        return in_array($user->role, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [Role::IS_SUPERADMIN, Role::IS_ADMIN, Role::IS_WAITER]);
    }

    public function update(User $user): bool
    {
       return in_array($user->role, [Role::IS_SUPERADMIN, Role::IS_ADMIN, Role::IS_WAITER]);
    }

    public function delete(User $user): bool
    {
        return in_array($user->role, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }
}
