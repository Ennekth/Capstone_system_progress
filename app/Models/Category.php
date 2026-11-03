<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditsChanges;

class Category extends Model
{
    use AuditsChanges;

    protected $fillable = ['name', 'type'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function getOrCreateDefault(): self
    {
        return static::firstOrCreate(
            ['name' => 'Uncategorized'],
            ['type' => 'product']
        );
    }
}