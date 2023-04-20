<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DishPolicy
{
    public function view(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    public function update(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    public function delete(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }
}
