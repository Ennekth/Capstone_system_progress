<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditsChanges;

class Category extends Model
{
    use AuditsChanges;

    protected $fillable = [
        'name', 
        'type', 
        'code', 
        'status', 
        'parent_id', 
        'attachment_path'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}