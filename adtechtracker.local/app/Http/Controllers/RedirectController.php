<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferSubscription;
use App\Models\OfferAccessLog;
use App\Models\OfferClick;


class RedirectController extends Controller
{
    
    public function handle(string $ref)
    {
        $subscription = OfferSubscription::where('ref_code', $ref)->first();

        // если вообще нет такой ссылки
        if (!$subscription) {
            OfferAccessLog::create([
                'offer_id' => null,
                'webmaster_id' => null,
                'ref_code' => $ref,
                'status' => 'rejected',
                'reason' => 'invalid_ref',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            abort(404, 'Запрашиваемая страница не найдена');
        }

        $offer = $subscription->offer;
        $webmaster = $subscription->webmaster;

        // проверка доступа
        $allowed = $offer && $offer->status === 1;

        // логируем доступ
        OfferAccessLog::create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $ref,
            'status' => $allowed ? 'allowed' : 'rejected',
            'reason' => $allowed ? null : 'no_subscription',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        if (!$allowed) {
            // return redirect('/'); // или страница "доступ запрещён"
            abort(403, 'Доступ запрещен');
        }

        

        // 💰 считаем деньги
        OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $offer->advertiser_id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $ref,
            'price' => $offer->price,
            'advertiser_cost' => $offer->price,
            'webmaster_income' => $offer->price * 0.7,
            'system_commission' => $offer->price * 0.3,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect($offer->url);
    }
}
