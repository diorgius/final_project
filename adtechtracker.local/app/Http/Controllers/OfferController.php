<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\OfferSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
                $offers = Offer::with('theme')->with('advertiser')->where('status', 1)
                                ->whereDoesntHave('subscribe', function ($query) {
                                    $query->where('webmaster_id', auth()->id());
                                })->get();
                $commission = Commission::get('commission')->value('commission');
                $percent = round((100 - $commission) / 100, 2);
                $subscriptions = OfferSubscription::with('offer')->where('webmaster_id', auth()->id())->get();
                break;
        }
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
     * Проверка текущих и удаленных офферов
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {

        $offer = Offer::where('advertiser_id', auth()->id())
            ->where('url', $request->url)
            ->first();

        if ($offer) {
            throw ValidationException::withMessages([
                'email' => __('offers.offer_exists'),
            ]);
        }

        // if ($offer) {
        //     return response()->json([
        //         // 'message' => __('offers.offer_exists'),
        //         'errors' => [
        //             'url' => [__('offers.offer_exists')],
        //         ],
        //     ], 422);
        // }    

        $deletedOffer = Offer::onlyTrashed()
            ->where('advertiser_id', auth()->id())
            ->where('url', $request->url)
            ->first();
        
        return response()->json([
            'offer' => $deletedOffer,
        ]);
    }


    /**
     * Сохраняем новый оффер
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        if (!$request->boolean('force_create')) {

            $offer = Offer::where('advertiser_id', auth()->id())
                ->where('url', $request->url)
                ->first();

            if ($offer) {
                throw ValidationException::withMessages([
                    'url' => __('offers.offer_exists'),
                ]);
            }

            $deletedOffer = Offer::onlyTrashed()
                ->where('advertiser_id', auth()->id())
                ->where('url', $request->url)
                ->first();

            if ($deletedOffer) {
                return back()
                    ->withInput()
                    ->with('theme')
                    ->with('restore_offer', [
                        'id' => $deletedOffer->id,
                        'name' => $deletedOffer->name,
                        'url' => $deletedOffer->url,
                        'price' => $deletedOffer->price,
                        'theme' => $deletedOffer->theme->name,
                        'deleted_at' => $deletedOffer->deleted_at->setTimezone('Europe/Moscow')->format('H:i:s d.m.Y'),
                    ]);
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'price' => ['required', 'numeric'],
            'theme' => 'required'
        ]);

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
     * Восстанавливаем удаленный ранее оффер
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $id) {
        
        // получаем оффер
        $offer = Offer::onlyTrashed()->find($id);
        // восстанавливаем
        $offer->restore();
        // отсылаем сообщение о создании
        broadcast(new OfferCreate($offer));
        // восстанавливаем все подписки
        $offer->subscribe()->onlyTrashed()->restore();
        // получаем восстановленные подписки
        $subscriptions = $offer->subscribe()->get();
        // отправляем сообщение о подписках
        foreach ($subscriptions as $subscription) {
            broadcast(new OfferSubscribeChanged($offer, $subscription->webmaster_id, 'subscribed'));
        }
        return redirect()->route('advertiser.offers');
    }

    /**
     * Редактируем оффер
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $offer = Offer::with('theme')->find($id);
        $themes = OfferTheme::all();
        return view('advertiser.edit', compact('offer', 'themes'));
    }

    /**
     * Обновляем данные оффера
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        // получаем оффер
        $offer = Offer::find($id);
        // проверяем данные
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'price' => ['required', 'numeric'],
            'theme' => 'required'
        ]);
        // обновляем
        $offer->name = $request->name;
        $offer->url = $request->url;
        $offer->price = $request->price;
        $offer->theme_id = $request->theme;
        $offer->save();
        // отправляем сообщение об изменении оффера
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

        // удаляем подписки
        $offer->subscribe()->delete();

        // удаляем оффер
        $offer->delete();
        
        return redirect()->route(auth()->user()->role . '.offers');
    }
}
