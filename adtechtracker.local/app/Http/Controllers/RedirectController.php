<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferSubscription;
use App\Models\OfferAccessLog;
use App\Models\OfferClick;
use App\Models\Commission;

class RedirectController extends Controller
{
    
    /**
     * Summary of handle
     * @param string $ref
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle(string $ref)
    {
        // получаем подписку
        $subscription = OfferSubscription::withTrashed()->where('ref_code', $ref)->first();

        // если вообще нет такой подписки
        if (!$subscription) {
            OfferAccessLog::create([
                'offer_id' => null,
                'webmaster_id' => auth()->id(),
                'ref_code' => $ref,
                'target_url' => null,
                'status' => 'rejected',
                'reason' => 'invalid_ref',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            abort(404, 'Запрашиваемая страница не найдена');
        }

        // получаем оффер и вебмастера
        $offer = $subscription->offer;
        $webmaster = $subscription->webmaster;
        
        // если подписка существует, но вебмастер отписался
        if ($subscription->trashed()) {
            OfferAccessLog::create([
                'offer_id' => $subscription->offer_id,
                'webmaster_id' => $subscription->webmaster_id,
                'subscription_id' => $subscription->id,
                'ref_code' => $ref,
                'target_url' => $offer->url,
                'status' => 'rejected',
                'reason' => 'subscription_inactive',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            abort(404, 'Запрашиваемая страница не найдена');
        }

        // если оффер отключён
        if (!$offer || $offer->status !== 1) {
            OfferAccessLog::create([
                'offer_id' => $offer?->id,
                'webmaster_id' => $webmaster?->id,
                'subscription_id' => $subscription->id,
                'ref_code' => $ref,
                'target_url' => $offer->url,
                'status' => 'rejected',
                'reason' => 'inactive_offer',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            abort(404, 'Запрашиваемая страница не найдена');
        }

        // логируем успешный доступ
        OfferAccessLog::create([
            'offer_id' => $offer->id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $ref,
            'target_url' => $offer->url,
            'status' => 'allowed',
            'reason' => null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // получаем коммиссию
        $commission = Commission::get('commission')->value('commission');

        // записываем клик в БД
        OfferClick::create([
            'offer_id' => $offer->id,
            'advertiser_id' => $offer->advertiser_id,
            'webmaster_id' => $webmaster->id,
            'subscription_id' => $subscription->id,
            'ref_code' => $ref,
            'target_url' => $offer->url,
            'advertiser_cost' => $offer->price,
            'webmaster_income' => round($offer->price * ((100 - $commission) / 100), 2),
            'system_commission' => round($offer->price * ($commission / 100), 2),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->away($offer->url);
    }
}
