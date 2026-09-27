<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionItem;
use App\Models\User;
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'transaction_type',
        'total_amount',
        'amount_paid',
        'amount_change',
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
