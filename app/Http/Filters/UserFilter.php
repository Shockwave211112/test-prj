<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends AbstractFilter
{
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const ROLE_ID = 'role_id';

    protected function getCallbacks(): array
    {
        return [
            self::NAME => [$this, 'name'],
            self::EMAIL => [$this, 'email'],
            self::ROLE_ID => [$this, 'role_id']
        ];
    }
    public function name(Builder $builder, $value)
    {
        dd(11);
        $builder->where('name', 'like', "%{$value}%");
    }
    public function email(Builder $builder, $value)
    {
        $builder->where('email', 'like', "%{$value}%");
    }
    public function role_id(Builder $builder, $value)
    {
        $builder->where('role_id', 'like', $value);
    }

}
