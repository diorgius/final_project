<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

// заполняемые поля
#[Fillable(['offer_id', 'webmaster_id', 'ref_code'])]

class OfferSubscription extends Model
{
    // используем мягкое удаление
    use SoftDeletes;

    /**
     * Связь: оффер - подписки
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Offer, OfferSubscription>
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

    /**
     * Связь: подписки - вебмастер
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Offer>
     */
    public function webmaster()
    {
        return $this->belongsTo(User::class, 'webmaster_id', 'id');
    }

}
