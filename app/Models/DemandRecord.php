<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandRecord extends Model
{
    protected $fillable = [

        'product_id',
        'record_date',
        'quantity',
        'source',

    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
