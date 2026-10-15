<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderingCost extends Model
{
    protected $fillable = ['product_id', 'ordering_cost', 'holding_cost_override', 'set_by'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}