<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\TPivotModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// заполняемые поля
#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    // /**
    //  * Связь: офферы - вебмастера
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Offer, User>
    //  */
    // public function offersWebmasters()
    // {
    //     return $this->belongsToMany(Offer::class, 'webmaster_id', 'id');
    // }

    /**
     * Связь: подписки - вебмастер
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<OfferSubscription, User>
     */
    public function subscriptions()
    {
        return $this->hasMany(OfferSubscription::class, 'webmaster_id', 'id');
    }

}