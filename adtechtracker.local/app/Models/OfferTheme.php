<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable(['name'])]

/**
 * Summary of OfferTheme
 */
class OfferTheme extends Model
{
    public function offers()
    {
        return $this->hasMany(Offer::class, 'theme_id', 'id');
    }

}
