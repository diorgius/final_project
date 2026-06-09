<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\OfferTheme;
use App\Models\Offer;
use App\Models\User;
use App\Events\OfferStatusChanged;


class OfferController extends Controller
{
    /**
     * Выводим данные по офферам в зависимости от роли
     * @return \Illuminate\Contracts\View\View
     */
    public function index () 
    {
        switch (Auth::user()->role) {
            case 'admin':
                $offers = Offer::with('theme')->with('advertiser')->get();
                break;
            case 'advertiser':
                $offers = Offer::with('theme')->where('advertiser_id', auth()->id())->get();
                break;
            case 'webmaster':
                $offers = Offer::with('theme')->get();
                break;
        }
        // это можно и не отправлять, т.к. работает и без этого
        // $themes = OfferTheme::get(['id', 'name']);
        // $users = User::get(['id', 'name']);

        // return view(Auth::user()->role . '.offers', compact('offers', 'themes', 'users')); 
        return view(Auth::user()->role . '.offers', compact('offers')); 
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
        $url = $request->url;
        if (mb_stripos($url, '?')) {
            $url = mb_substr($url, 0, mb_stripos($url, '?'));
        }

        Offer::create([
            'name' => $request->name,
            'url' => $url,
            'price' => $request->price,
            'theme_id' => $request->theme,
            'advertiser_id' => auth()->id()
        ]);

        return redirect()->route('advertiser.offers');
    }

    /**
     * Записываем в БД изменение статуса карточки и отсылаем сообщение
     * @param Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request, Offer $offer)
    {
        $offer->update([
            'status' => $request->status
        ]);

        logger('before broadcast');

        broadcast(new OfferStatusChanged($offer));

        logger('after broadcast');

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Удаляем оффер
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $offer = Offer::find($id);
        $offer->delete();

        return redirect()->route(Auth()->user()->role . '.offers');
    }
}
