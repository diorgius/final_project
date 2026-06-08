<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\OfferTheme;
use App\Models\Offer;


class OfferController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index () 
    {
        switch (Auth::user()->role) {
            case 'admin':
                $offers = Offer::with('theme')->get();
                break;
            case 'advertiser':
                $offers = Offer::with('theme')->where('advertiser_id', auth()->id())->get();
                break;
            case 'webmaster':
                $offers = Offer::with('theme')->where('advertiser_id', auth()->id())->get();
                break;
        }
        // $offers = Offer::with('theme')->where('advertiser_id', auth()->id())->get();
        $themes = OfferTheme::all();

        return view(Auth::user()->role . '.offers', compact('offers', 'themes')); 
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $themes = OfferTheme::all();

        return view('advertiser.create', compact('themes')); 
    }

    /**
     * Summary of store
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

        // убираем get-параметры
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
     * Summary of destroy
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $offer = Offer::find($id);
        $offer->delete();

        return redirect()->route('advertiser.offers');
    }
}
