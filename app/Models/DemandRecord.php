<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandRecord extends Model
{
    use HasFactory;

    protected $fillable = [
    'product_id',
    'transaction_id',
    'record_date',
    'quantity',
    'source',
];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    protected $casts = [
        'record_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}