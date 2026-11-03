<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\AuditsChanges;

class User extends Authenticatable
{
    use HasFactory, Notifiable, AuditsChanges;

    protected $fillable = [
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
        'suffix',
        'role',
        'is_active',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->lastname} {$this->suffix}");
    }
}