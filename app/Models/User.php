<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;
    use Filterable;

    protected $table = 'users';
    protected $guarded = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'pin_code',
        'role_id'
    ];
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
