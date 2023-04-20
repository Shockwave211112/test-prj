<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class CategoryPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }
}
