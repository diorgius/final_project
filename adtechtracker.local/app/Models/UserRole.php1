<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = [
        'role',
    ];

    /**
     * Пользователь роли
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<User, UserRole>
     */
    public function user()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

}
