<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\OfferThemeFactory;

// заполняемые поля
#[Fillable(['name',])]

/**
 * Summary of OfferTheme
 */
class OfferTheme extends Model
{
    /** @use HasFactory<OfferFactory> */
    
    // используем мягкое удаление
    use HasFactory, SoftDeletes;
    
    /**
     * Связь: оферы - тема
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Offer, OfferTheme>
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'theme_id', 'id');
    }

}
