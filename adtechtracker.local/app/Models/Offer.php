<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// заполняемые поля
#[Fillable(['name', 'url', 'price', 'status', 'theme_id', 'advertiser_id'])]

class Offer extends Model
{
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
     * Связь: тема - офферы, многие к одному
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<OfferTheme, Offer>
     */
    public function theme()
    {
        return $this->belongsTo(OfferTheme::class, 'theme_id', 'id');
    }

    /**
     * Связь: рекламодатель - офферы, многие в одному
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Offer>
     */
    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id', 'id');
    }
}
