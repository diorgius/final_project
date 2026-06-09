<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// заполняемые поля
#[Fillable(['name'])]

/**
 * Связь: оферы - тема, один ко многим
 */
class OfferTheme extends Model
{
    public function offers()
    {
        return $this->hasMany(Offer::class, 'theme_id', 'id');
    }

}
