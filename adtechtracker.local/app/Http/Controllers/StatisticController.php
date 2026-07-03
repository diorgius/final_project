<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferClick;
use App\Models\OfferSubscription;
use App\Models\OfferAccessLog;

/**
 * Summary of StatisticController
 */
class StatisticController extends Controller
{
    /**
     * Выводим данные статистику в зависимости от роли
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
                    'activeOffers' => Offer::where('status', 1)->count(),
                    'deactiveOffers' => Offer::where('status', 0)->count(),
                    'deletedOffers' => Offer::withTrashed()->whereNotNull('deleted_at')->count(),                    
                    'subscriptions' => OfferSubscription::withTrashed()->count(),
                    'activeSubscriptions' => OfferSubscription::count(),
                    'deactiveSubscriptions' => OfferSubscription::withTrashed()->whereNotNull('deleted_at')->count(),
                    'clicks' => OfferClick::count(),
                    'rejectedClicks' => OfferAccessLog::where('status', 'rejected')->count(),
                    'advertiserExpenses' => OfferClick::sum('advertiser_cost'),
                    'webmasterIncome' => OfferClick::sum('webmaster_income'),
                    'systemProfit' => OfferClick::sum('system_commission'),
                    ]);
                break;
            case 'advertiser':
                $offers = Offer::query()->withTrashed()
                                ->where('advertiser_id', auth()->id())
                                ->withCount('click')
                                ->withSum('click as advertiser_expenses', 'advertiser_cost')
                                ->orderBy('name')->get();
                $totalClicks = $offers->sum('click_count');
                $totalExpenses = $offers->sum('advertiser_expenses');
                return view(auth()->user()->role . '.statistics', compact('offers', 'totalClicks', 'totalExpenses'));
                break;
            case 'webmaster':
                $offers = Offer::query()->withTrashed()
                                ->whereHas('subscribe', function ($query) {
                                    $query->where('webmaster_id', auth()->id())->withTrashed();
                                })
                                ->withCount('click')
                                ->withSum('click as webmaster_revenue', 'webmaster_income')
                                ->orderBy('name')->get();
                $totalClicks = $offers->sum('click_count');
                $totalRevenue = $offers->sum('webmaster_revenue');
                return view(auth()->user()->role . '.statistics', compact('offers', 'totalClicks', 'totalRevenue'));
                break;
            default:
                abort(404, __('http-statuses.404'));
                break;
        }
    }

    /**
     * Получаем диапазоны дат
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
            default => [Carbon::create(1970, 1, 1), now()],
        };
    }

    /**
     * Получаем статистику в зависимости от диапазона, для вывода на фронте
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        // получаем с фронта период
        $period = $request->input('period');

        // определяем диапазоны для запросов
        [$start, $end] = $this->getDate($period);

        // получаем статистику в зависимости от роли
        switch (auth()->user()->role) {
            case 'admin':
                return response()->json([
                    'advertisers' => User::where('role', 'advertiser')->whereBetween('created_at', [$start, $end])->count(),
                    'webmasters' => User::where('role', 'webmaster')->whereBetween('created_at', [$start, $end])->count(),
                    'offers' => Offer::query()->whereBetween('created_at', [$start, $end])->count(),
                    'activeOffers' => Offer::where('status', 1)->whereBetween('created_at', [$start, $end])->count(),
                    'deactiveOffers' => Offer::where('status', 0)->whereBetween('created_at', [$start, $end])->count(),
                    'deletedOffers' => Offer::query()->withTrashed()->whereBetween('deleted_at', [$start, $end])->count(),
                    'subscriptions' => OfferSubscription::query()->withTrashed()->whereBetween('created_at', [$start, $end])->count(),
                    'activeSubscriptions' => OfferSubscription::query()->whereBetween('created_at', [$start, $end])->count(),
                    'deactiveSubscriptions' => OfferSubscription::query()->withTrashed()->whereBetween('deleted_at', [$start, $end])->count(),
                    'clicks' => OfferClick::query()->whereBetween('created_at', [$start, $end])->count(),
                    'rejectedClicks' => OfferAccessLog::where('status', 'rejected')->whereBetween('created_at', [$start, $end])->count(),
                    'advertiserExpenses' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('advertiser_cost'),
                    'webmasterIncome' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('webmaster_income'),
                    'systemProfit' => OfferClick::query()->whereBetween('created_at', [$start, $end])->sum('system_commission'),
                ]);
                break;
            case 'advertiser':
                $offers = Offer::query()->withTrashed()
                    ->where('advertiser_id', auth()->id())
                    ->withCount([
                        'click as click_count' => function ($query) use ($start, $end) {
                            $query->whereBetween('created_at', [$start, $end]);
                        }
                    ])
                    ->withSum([
                        'click as advertiser_expenses' => function ($query) use ($start, $end) {
                            $query->whereBetween('created_at', [$start, $end]);
                        }
                    ], 'advertiser_cost')
                    ->orderBy('name')->get();
                $totalClicks = $offers->sum('click_count');
                $totalExpenses = $offers->sum('advertiser_expenses');
                return response()->json([
                    'offers' => $offers,
                    'totalClicks' => $totalClicks,
                    'totalExpenses' => $totalExpenses,
                ]);
                break;
            case 'webmaster':
                $offers = Offer::query()->withTrashed()
                    ->whereHas('subscribe', function ($query) {
                        $query->where('webmaster_id', auth()->id())->withTrashed();
                    })
                    ->withCount([
                        'click as click_count' => function ($query) use ($start, $end) {
                            $query->where('webmaster_id', auth()->id())
                                ->whereBetween('created_at', [$start, $end]);
                        }
                    ])
                    ->withSum([
                        'click as webmaster_revenue' => function ($query) use ($start, $end) {
                            $query->where('webmaster_id', auth()->id())
                                ->whereBetween('created_at', [$start, $end]);
                        }
                    ], 'webmaster_income')
                    ->orderBy('name')->get();
                $totalClicks = $offers->sum('click_count');
                $totalRevenue = $offers->sum('webmaster_revenue');
                return response()->json([
                    'offers' => $offers,
                    'totalClicks' => $totalClicks,
                    'totalRevenue' => $totalRevenue,
                ]);
                break;
            default:
                abort(404, __('http-statuses.404'));
                break;
        }        
    }
}
