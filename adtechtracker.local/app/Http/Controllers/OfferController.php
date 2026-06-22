<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\OfferSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\OfferTheme;
use App\Models\Offer;
use App\Models\User;
use App\Events\OfferStatusChanged;
use App\Events\OfferCreate;
use App\Events\OfferDelete;
use App\Events\OfferSubscribeChanged;
use Illuminate\Support\Str;

class OfferController extends Controller
{

    /**
     * Выводим данные по офферам в зависимости от роли
     * @return \Illuminate\Contracts\View\View
     */
    public function index () 
    {
        switch (auth()->user()->role) {
            case 'admin':
                $offers = Offer::with('theme')->with('advertiser')->get();
                $percent = null;
                $subscriptions = null;
                break;
            case 'advertiser':
                $offers = Offer::with('theme')->where('advertiser_id', auth()->id())->get();
                $percent = null;
                $subscriptions = null;
                break;
            case 'webmaster':
                // $offers = Offer::with('theme')->with('advertiser')->withCount('subscribe')->where('status', true)->get();
                $offers = Offer::with('theme')->with('advertiser')->where('status', 1)
                    ->whereDoesntHave('subscribe', function ($query) {
                        $query->where('webmaster_id', auth()->id());
                        })->get();
                $commission = Commission::get('commission')->value('commission');
                $percent = round((100 - $commission) / 100, 2);
                $subscriptions = OfferSubscription::with('offer')->where('webmaster_id', auth()->id())->get();
                break;
        }

        // это можно и не отправлять, т.к. работает и без этого
        // $themes = OfferTheme::get(['id', 'name']);
        // $users = User::get(['id', 'name']);
        // return view(Auth::user()->role . '.offers', compact('offers', 'themes', 'users')); 

        return view(auth()->user()->role . '.offers', compact('offers', 'percent', 'subscriptions')); 
    }

    /**
     * Создаем новый оффер
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $themes = OfferTheme::all();

        return view('advertiser.create', compact('themes')); 
    }

    /**
     * Сохраняем новый оффер
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . Offer::class],
            'url' => ['required', 'string', 'max:255', 'url'],
            'price' => ['required', 'numeric'],
            'theme' => 'required'
        ]);

        // убираем get-параметры из url
        // $url = $request->url;
        // if (mb_stripos($url, '?')) {
        //     $url = mb_substr($url, 0, mb_stripos($url, '?'));
        // }

        $offer = Offer::create([
            'name' => $request->name,
            'url' =>$request->url,
            'price' => $request->price,
            'theme_id' => $request->theme,
            'advertiser_id' => auth()->id()
        ]);

        // отправляем сообщение о создании оффера
        broadcast(new OfferCreate($offer));

        return redirect()->route('advertiser.offers');
    }

    /**
     * Записываем в БД изменение статуса оффера и отсылаем сообщение
     * @param Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request, Offer $offer)
    {
        $offer->update([
            'status' => $request->status
        ]);

        // отправляем сообщение об изменении статуса оффера
        broadcast(new OfferStatusChanged($offer, auth()->user()->role));

        return response()->json(['success' => true]);
    }

    /**
     * Записываем в БД подписку на оффер и отсылаем сообщение
     * @param Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request, Offer $offer)
    {
        // проверяем, была ли ранее подписка в удалена
        $subscription = OfferSubscription::withTrashed()
            ->where('offer_id', $offer->id)
            ->where('webmaster_id', auth()->id())
            ->first();

        // если подписки раньше не было, создаем новую
        if (!$subscription) {
            $subscription = OfferSubscription::create([
                'offer_id' => $offer->id,
                'webmaster_id' => auth()->id(),
                'ref_code' => Str::random(16),
            ]);

            // если была, то востанавливаем
        } elseif ($subscription->trashed()) {
            $subscription->restore();
        }

        // $subscription = OfferSubscription::firstOrCreate([
        //     'offer_id' => $offer->id,
        //     'webmaster_id' => auth()->id(),
        //     'ref_code' => Str::random(16),
        // ]);

        // отправляем сообщение о подписке на оффер
        broadcast(new OfferSubscribeChanged($offer, auth()->id(), 'subscribed'));

        return response()->json([
            'success' => true,
            'ref_code' => $subscription->ref_code
        ]);
    }

    /**
     * Записываем в БД отподписку от оффера и отсылаем сообщение
     * @param Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request, Offer $offer)
    {
        // используем мягкое удаление
        OfferSubscription::where('offer_id', $offer->id)->where('webmaster_id', auth()->id())->delete();

        // отправляем сообщение об отписке от оффера
        broadcast(new OfferSubscribeChanged($offer, auth()->id(), 'unsubscribed'));

        return response()->json(['success' => true]);
    }

    /**
     * Удаляем оффер
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $offer = Offer::findOrFail($id);

        // отправляем сообщение об удалении оффера
        broadcast(new OfferDelete($offer->id));
        
        $offer->delete();
        

        return redirect()->route(auth()->user()->role . '.offers');
    }
}
