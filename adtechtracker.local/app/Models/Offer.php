<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'url', 'price', 'status', 'theme_id', 'advertiser_id'])]

class Offer extends Model
{

    /**
     * Summary of casts
     * @return array{price: string}
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2'
        ];
    }

    /**
     * Summary of theme
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<OfferTheme, Offer>
     */
    public function theme()
    {
        return $this->belongsTo(OfferTheme::class, 'theme_id', 'id');
    }

}
