<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    public const IS_SUPERADMIN = 1;
    public const IS_ADMIN = 2;
    public const IS_WAITER = 3;
    public function users(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
