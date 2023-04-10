<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends AbstractFilter
{
    public const NUMBER = 'number';
    public const TOTAL_COST = 'total_cost';
    public const CLOSING_DATE = 'closing_date';

    protected function getCallbacks(): array
    {
        return [
            self::NUMBER => [$this, 'number'],
            self::TOTAL_COST => [$this, 'total_cost'],
            self::CLOSING_DATE => [$this, 'closing_date']
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
    public function closing_date(Builder $builder, $value)
    {
        $builder->where('closing_date', 'like', "%{$value}%");
    }
}
