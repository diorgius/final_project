<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

// заполняемые поля
#[Fillable(['name'])]

class OfferTheme extends Model
{
    // используем мягкое удаление
    use SoftDeletes;

    /**
     * Связь: оферы - тема
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Offer, OfferTheme>
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'theme_id', 'id');
    }

}
