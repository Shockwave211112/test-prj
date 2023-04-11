<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DishFilter extends AbstractFilter
{
    public const NAME = 'name';
    public const COMPOSITION = 'composition';
    public const CALORIES = 'calories';
    public const PRICE = 'price';
    public const CATEGORY_ID = 'category_id';

    protected function getCallbacks(): array
    {
        return [
            self::NAME => [$this, 'name'],
            self::COMPOSITION => [$this, 'composition'],
            self::CALORIES => [$this, 'calories'],
            self::PRICE => [$this, 'price'],
            self::CATEGORY_ID => [$this, 'category_id']
        ];
    }
    public function name(Builder $builder, $value)
    {
        $builder->where('name', 'like', "%{$value}%");
    }
    public function calories(Builder $builder, $value)
    {
        $builder->where('calories', 'like', "%{$value}%");
    }
    public function composition(Builder $builder, $value)
    {
        $builder->where('composition', 'like', "%{$value}%");
    }
    public function price(Builder $builder, $value)
    {
        $builder->where('price', 'like', "%{$value}%");
    }
    public function category_id(Builder $builder, $value)
    {
        $builder->where('category_id', '=', $value);
    }
}
