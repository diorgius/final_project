<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferClick;
use App\Models\OfferSubscription;

class StatisticController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index ()
    {
        switch (auth()->user()->role) {
            case 'admin':

                $advertisers = User::where('role', 'advertiser')->count();
                $webmasters = User::where('role', 'webmaster')->count();
                $offers = Offer::count();
                $subscriptions = OfferSubscription::count();
                $clicks = OfferClick::count();
                $advertiserExpenses = OfferClick::sum('advertiser_cost');
                $webmasterIncome = OfferClick::sum('webmaster_income');
                $systemProfit = OfferClick::sum('system_commission');
                return view(auth()->user()->role . '.statistics', compact(
                    'advertisers',
                    'webmasters',
                    'offers',
                    'subscriptions',
                    'clicks',
                    'advertiserExpenses',
                    'webmasterIncome',
                    'systemProfit'));

                break;
            case 'advertiser':
                return view(auth()->user()->role . '.statistics');
                break;
            case 'webmaster':
                return view(auth()->user()->role . '.statistics');
                break;
            default:
                abort(404, 'Запрашиваемая страница не найдена');
                break;
        }
    }
}
