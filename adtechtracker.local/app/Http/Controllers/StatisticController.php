<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
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
                return view(auth()->user()->role . '.statistics', [
                    'advertisers' => User::where('role', 'advertiser')->count(),
                    'webmasters' => User::where('role', 'webmaster')->count(),
                    'offers' => Offer::count(),
                    'subscriptions' => OfferSubscription::count(),
                    'unsubscriptions' => OfferSubscription::withTrashed()->whereNotNull('deleted_at')->count(),
                    'clicks' => OfferClick::count(),
                    'rejectedClicks' => OfferAccessLog::where('status', 'rejected')->count(),
                    'advertiserExpenses' => OfferClick::sum('advertiser_cost'),
                    'webmasterIncome' => OfferClick::sum('webmaster_income'),
                    'systemProfit' => OfferClick::sum('system_commission'),
                    ]);
                break;
            case 'advertiser':
                $offers = Offer::query()->where('advertiser_id', auth()->id())
                                ->withCount('click')
                                ->withSum('click as advertiser_expenses', 'advertiser_cost')
                                ->orderBy('name')->get();
                $totalClicks = $offers->sum('click_count');
                $totalExpenses = $offers->sum('advertiser_expenses');
                return view(auth()->user()->role . '.statistics', compact('offers', 'totalClicks', 'totalExpenses'));
                break;
            case 'webmaster':
                return view(auth()->user()->role . '.statistics');
                break;
            default:
                abort(404, 'Запрашиваемая страница не найдена');
                break;
        }
    }

    /**
     * Summary of getDate
     * @param string $period
     * @return void
     */
    public function getDate(string $period): array
    {
        return match ($period) {
            'day' => [now()->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'all' => [Carbon::create(1970, 1, 1), now()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function summary(Request $request)
    {
        $period = $request->input('period');

        [$start, $end] = $this->getDate($period);

        switch (auth()->user()->role) {
            case 'admin':
                return response()->json([
                    'advertisers' => User::where('role', 'advertiser')->whereBetween('created_at', [$start, $end])->count(),
                    'webmasters' => User::where('role', 'webmaster')->whereBetween('created_at', [$start, $end])->count(),
                    'offers' => Offer::query()->whereBetween('created_at', [$start, $end])->count(),
                    'subscriptions' => OfferSubscription::query()->whereBetween('created_at', [$start, $end])->count(),
                    'unsubscriptions' => OfferSubscription::query()->withTrashed()->whereBetween('deleted_at', [$start, $end])->count(),
                    'clicks' => OfferClick::query()->whereBetween('created_at', [$start, $end])->count(),
                    'rejectedClicks' => OfferAccessLog::where('status', 'rejected')->whereBetween('created_at', [$start, $end])->count(),
                    'advertiserExpenses' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('advertiser_cost'),
                    'webmasterIncome' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('webmaster_income'),
                    'systemProfit' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('system_commission'),
                ]);
                break;
            case 'advertiser':
                $offers = Offer::query()->where('advertiser_id', auth()->id())
                    ->withCount('click')
                    ->withSum('click as advertiser_expenses', 'advertiser_cost')
                    ->orderBy('name')->get();
                return response()->json([
                    'offers' => $offers,
                ]);
                break;
            case 'webmaster':
                return view(auth()->user()->role . '.statistics');
                break;
        }        
    }

}
