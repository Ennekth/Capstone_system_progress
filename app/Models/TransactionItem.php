<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'selling_price',
        'quantity',
        'subtotal',
        'item_discount_type',
        'item_discount_value',
        'item_discount_scope',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'item_discount_value' => 'decimal:2', // Add this cast
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}