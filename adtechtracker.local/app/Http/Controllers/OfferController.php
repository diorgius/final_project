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

/**
 * Summary of OfferController
 */
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
                $commission = Commission::value('commission');
                $percent = round((100 - $commission) / 100, 2);
                $subscriptions = OfferSubscription::with('offer')->where('webmaster_id', auth()->id())->get();
                break;
            default:
                abort(404, __('http-statuses.404'));
                break;
        }

        return view(auth()->user()->role . '.offers', compact('offers', 'percent', 'subscriptions')); 
    }

    /**
     * Открываем форму создания нового оффера
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        // получаем темы
        $themes = OfferTheme::all();

        return view('advertiser.create', compact('themes')); 
    }

    /**
     * Создаем новый оффер
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!$request->boolean('force_create')) {
            
            // проверяем текущие офферы
            $offer = Offer::where('advertiser_id', auth()->id())
                ->where('url', $request->url)
                ->first();

            // если оффер есть, выводим сообщение
            if ($offer) {
                throw ValidationException::withMessages([
                    'url' => __('offers.offer_exists'),
                ]);
            }

            // проверяем удалненные офферы, если есть, то открываем модальное окно с предложением восстановить оффер
            $deletedOffer = Offer::onlyTrashed()
                ->where('advertiser_id', auth()->id())
                ->where('url', $request->url)
                ->first();

            // передаем данные найденного оффера
            if ($deletedOffer) {
                return back()
                    ->withInput()
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

        // если ничего не нашлость, то создаем новый оффер
        // стандартные проверки
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'price' => ['required', 'numeric'],
            'theme' => ['required', 'exists:offer_themes,id'],
        ]);

        // запись в БД
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
     * Открываем форму редактирования оффера
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        // получаем оффер
        $offer = Offer::with('theme')->findOrFail($id);

        // проверяем, удалить активный оффер нельзя
        if ($offer->status) {
            abort(403, __('http-statuses.403'));
        }

        // проверяем владельца оффера, если не совпадает, то выводим ошибку
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403, __('http-statuses.403'));
        }

        // получаем темы
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
        $offer = Offer::findOrFail($id);

        // проверяем владельца оффера, если не совпадает, то выводим ошибку
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403, __('http-statuses.403'));
        }

        // проверяем наличие других офферов с таким же url
        $exists = Offer::where('advertiser_id', auth()->id())
            ->where('url', $request->url)
            ->where('id', '!=', $offer->id)
            ->exists();

        // если находим выводим сообщение
        if ($exists) {
            throw ValidationException::withMessages([
                'url' => __('offers.offer_exists'),
            ]);
        }

        // проверяем данные
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255', 'url'],
            'price' => ['required', 'numeric'],
            'theme' => ['required', 'exists:offer_themes,id'],
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
        // проверяем владельца оффера, если не совпадает и роль не админ, то выводим ошибку
        if ($offer->advertiser_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, __('http-statuses.403'));
        }

        // проверяем, что статус есть и это ожидаемый тип
        $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        // обновляем статус
        $offer->update([
            'status' => $request->status
        ]);

        // отправляем сообщение об изменении статуса оффера
        broadcast(new OfferStatusChanged($offer, auth()->user()->role));

        // возвращаем данные на фронт
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
        // проверяем, что только вебмастер может подписаться
        if (auth()->user()->role !== 'webmaster') {
            abort(403, __('http-statuses.403'));
        }

        // проверяем, что нельзя подписаться не неактивный оффер
        if ($offer->status === 0) {
            abort(404, __('http-statuses.404'));
        }

        // проверяем, что нельзя подписаться не удаленный оффер
        if ($offer->trashed()) {
            abort(404, __('http-statuses.404'));
        }
        
        // проверяем, была ли ранее подписка удалена
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

        // возвращаем данные на фронт
        return response()->json([
            'success' => true,
            'ref_code' => $subscription->ref_code
        ]);
    }

    /**
     * Записываем в БД отписку от оффера и отсылаем сообщение
     * @param Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request, Offer $offer)
    {
        // проверяем, что только вебмастер может отписаться
        if (auth()->user()->role !== 'webmaster') {
            abort(403, __('http-statuses.403'));
        }

        // удаляем подписку
        OfferSubscription::where('offer_id', $offer->id)->where('webmaster_id', auth()->id())->delete();

        // отправляем сообщение об отписке от оффера
        broadcast(new OfferSubscribeChanged($offer, auth()->id(), 'unsubscribed'));

        // возвращаем данные на фронт
        return response()->json(['success' => true]);
    }

    /**
     * Удаляем оффер
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        // находим оффер
        $offer = Offer::findOrFail($id);

        // проверяем, удалить активный оффер нельзя
        if ($offer->status) {
            abort(403, __('http-statuses.403'));
        }
        
        // проверяем владельца оффера, если не совпадает и роль не админ, то выводим ошибку
        if ($offer->advertiser_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, __('http-statuses.403'));
        }

        // удаляем подписки
        $offer->subscribe()->delete();

        // удаляем оффер
        $offer->delete();

        // отправляем сообщение об удалении оффера
        broadcast(new OfferDelete($offer->id));

        return redirect()->route(auth()->user()->role . '.offers');
    }

    /**
     * Восстанавливаем удаленный ранее оффер
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $id)
    {
        // получаем оффер
        $offer = Offer::onlyTrashed()->findOrFail($id);

        // проверяем владельца оффера, если не совпадает, то выводим ошибку
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403, __('http-statuses.403'));
        }

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
}
