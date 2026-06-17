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

}
