<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DishFilter extends AbstractFilter
{
    public const NAME = 'name';
    public const COMPOSITION = 'composition';
    protected function getCallbacks(): array
    {
        return [
            self::NAME => [$this, 'name'],
            self::COMPOSITION => [$this, 'composition']
        ];
    }
    public function name(Builder $builder, $value)
    {
        $builder->where('name', 'like', "%{$value}%");
    }public function composition(Builder $builder, $value)
    {
        $builder->where('composition', 'like', "%{$value}%");
    }
}
