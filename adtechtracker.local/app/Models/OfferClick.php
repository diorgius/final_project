<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

// заполняемые поля
#[Fillable([
    'offer_id', 
    'advertiser_id', 
    'webmaster_id', 
    'subscription_id', 
    'ref_code', 
    'price', 
    'advertiser_cost', 
    'webmaster_income', 
    'system_commission', 
    'ip', 
    'user_agent'
])]

class OfferClick extends Model
{

    /**
     * Устанавливливаем тип полей
     * @return array{advertiser_cost: string, system_commission: string, webmaster_income: string}
     */
    protected function casts(): array
    {
        return [
            'advertiser_cost' => 'decimal:2',
            'webmaster_income' => 'decimal:2',
            'system_commission' => 'decimal:2'
        ];
    }

    /**
     * Связь: оффер - клики
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Offer, OfferClick>
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

}
