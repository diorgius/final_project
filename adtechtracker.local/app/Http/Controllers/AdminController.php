<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commission;
use App\Models\OfferClick;

/**
 * Summary of AdminController
 */
class AdminController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $commissions = Commission::get(['id', 'commission']);

        $advertiserExpenses = OfferClick::sum('advertiser_cost');
        $webmasterIncome = OfferClick::sum('webmaster_income');
        $systemProfit = OfferClick::sum('system_commission');

        return view('admin.dashboard', compact('commissions', 'advertiserExpenses', 'webmasterIncome', 'systemProfit'));
    }
}
