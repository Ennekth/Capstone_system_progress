<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastSetting extends Model
{
    protected $fillable = [
        'scope',
        'category_id',
        'product_id',
        'sma_window_days',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}