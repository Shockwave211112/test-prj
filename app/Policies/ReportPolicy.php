<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{

    public function view(User $user, User $model): bool
    {
        return in_array($user->role_id, [1, 2]);
    }
}
