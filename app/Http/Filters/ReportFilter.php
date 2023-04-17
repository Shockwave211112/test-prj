<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ReportFilter extends AbstractFilter
{
    public const TOTAL_COUNT = 'total_count';
    public const TOTAL_PROFIT = 'total_profit';
    public const CREATED_AT = 'created_at';

    protected function getCallbacks(): array
    {
        return [
            self::TOTAL_COUNT => [$this, 'total_orders'],
            self::TOTAL_PROFIT => [$this, 'total_profit'],
            self::CREATED_AT => [$this, 'created_at']
        ];
    }
    public function total_orders(Builder $builder, $value)
    {
        $builder->where('total_orders', '=', $value);
    }
    public function total_profit(Builder $builder, $value)
    {
        $builder->where('total_profit', '=', $value);
    }
    public function created_at(Builder $builder, $value)
    {
        $builder->where('created_at', 'like', "%{$value}%");
    }
    public function is_closed(Builder $builder, $value)
    {
        $builder->where('is_closed', 'like', "%{$value}%");
    }
}
