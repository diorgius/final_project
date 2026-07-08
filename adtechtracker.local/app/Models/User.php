<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\TPivotModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// заполняемые поля
#[Fillable([
        'name', 
        'email', 
        'password', 
        'role', 
        'status', 
        'locale',
    ])]
#[Hidden([
        'password', 
        'remember_token',
    ])]

// class User extends Authenticatable
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Связь: офферы - рекламодатель
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Offer, User>
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'advertiser_id', 'id');
    }

    /**
     * Связь: подписки - вебмастер
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<OfferSubscription, User>
     */
    public function subscriptions()
    {
        return $this->hasMany(OfferSubscription::class, 'webmaster_id', 'id');
    }

}