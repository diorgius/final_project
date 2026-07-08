<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// заполняемые поля
#[Fillable([
        'offer_id', 
        'webmaster_id', 
        'subscription_id', 
        'ref_code',
        'target_url', 
        'status', 
        'reason', 
        'ip', 
        'user_agent',
    ])]

class OfferAccessLog extends Model
{

}
