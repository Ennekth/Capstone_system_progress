<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EoqSetting extends Model
{
    protected $fillable = [
        'default_ordering_cost',
        'holding_cost_percentage',
        'holding_cost_per_liter',
        'updated_by',
    ];
}
