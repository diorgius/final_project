<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\OfferFactory;

// заполняемые поля
#[Fillable(['name', 'url', 'price', 'status', 'theme_id', 'advertiser_id'])]

class Offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    // используем мягкое удаление
    use HasFactory, SoftDeletes;
    
    /**
     * Устанавливливаем тип поля
     * @return array{price: string}
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2'
        ];
    }

    /**
     * Связь: тема - оффер
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<OfferTheme, Offer>
     */
    public function theme()
    {
        return $this->belongsTo(OfferTheme::class, 'theme_id', 'id');
    }

    /**
     * Связь: рекламодатель - офферы
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Offer>
     */
    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id', 'id');
    }

    /**
     * Связь: подписки - офферы
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<OfferSubscription, Offer>
     */
    public function subscribe()
    {
        return $this->hasMany(OfferSubscription::class, 'offer_id', 'id');
    }

    /**
     * Связь: клики - офферы
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<OfferClick, Offer>
    */
    public function click()
    {
        return $this->hasMany(OfferClick::class, 'offer_id', 'id');
    }
}
