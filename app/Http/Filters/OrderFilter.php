<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends AbstractFilter
{
    public const NUMBER = 'number';
    public const TOTAL_COST = 'total_cost';
    public const CLOSED_AT = 'closed_at';

    protected function getCallbacks(): array
    {
        return [
            self::NUMBER => [$this, 'number'],
            self::TOTAL_COST => [$this, 'total_cost'],
            self::CLOSED_AT => [$this, 'closed_at']
        ];
    }
    public function number(Builder $builder, $value)
    {
        $builder->where('number', 'like', "%{$value}%");
    }
    public function total_cost(Builder $builder, $value)
    {
        $builder->where('total_cost', 'like', $value);
    }
    public function closed_at(Builder $builder, $value)
    {
        $builder->where('closed_at', 'like', "%{$value}%");
    }
}
