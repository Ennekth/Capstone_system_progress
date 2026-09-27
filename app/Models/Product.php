<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'product_description',
        'purchase_cost',
        'selling_price',
        'quantity',
        'reorder_point',
        'category_id',
        'approx_volume',
        'product_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isLowStock(): bool
    {
        if (is_null($this->reorder_point)) {
            return false;
        }

        return $this->quantity <= $this->reorder_point;
    }

    public function demandRecords()
    {
        return $this->hasMany(DemandRecord::class);
    }
}