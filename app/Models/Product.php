<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_description',
        'purchase_cost',
        'selling_price',
        'store_quantity',
        'warehouse_quantity',
        'reorder_point',
        'product_image',
        'category_id',
        'approx_volume',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function demandRecords()
    {
        return $this->hasMany(DemandRecord::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        if (is_null($this->reorder_point)) {
            return false;
        }

        return $this->store_quantity <= $this->reorder_point;
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->store_quantity + $this->warehouse_quantity;
    }

    public function orderingCosts()
    {
        return $this->hasMany(ProductOrderingCost::class);
    }

    public function latestOrderingCost()
    {
        return $this->hasOne(ProductOrderingCost::class)->latestOfMany();
    }
}