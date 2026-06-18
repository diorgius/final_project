<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferClick;
use App\Models\OfferSubscription;
use App\Models\OfferAccessLog;

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
                $rejectedClicks = OfferAccessLog::where('status', 'rejected')->count();
                $advertiserExpenses = OfferClick::sum('advertiser_cost');
                $webmasterIncome = OfferClick::sum('webmaster_income');
                $systemProfit = OfferClick::sum('system_commission');
                return view(auth()->user()->role . '.statistics', compact(
                    'advertisers',
                    'webmasters',
                    'offers',
                    'subscriptions',
                    'clicks',
                    'rejectedClicks',
                    'advertiserExpenses',
                    'webmasterIncome',
                    'systemProfit'));
                break;
            case 'advertiser':
                $offers = Offer::query()->where('advertiser_id', auth()->id())
                            ->withCount('click')
                            ->withSum('click as advertiser_expenses', 'advertiser_cost')
                            ->orderBy('name')->get();
                return view(auth()->user()->role . '.statistics', compact('offers'));
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
