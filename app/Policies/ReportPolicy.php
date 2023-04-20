<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class ReportPolicy
{

    public function view(User $user): bool
    {
        return in_array($user->role_id, [Role::IS_SUPERADMIN, Role::IS_ADMIN]);
    }
}
