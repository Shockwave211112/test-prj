<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dish extends Model
{
    use HasFactory;
    use Filterable;

    protected $table = 'dishes';
    protected $guarded = false;
    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'dish_orders', 'dish_id', 'order_id');
    }
}
