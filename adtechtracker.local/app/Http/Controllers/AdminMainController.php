<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commission;
use App\Models\OfferClick;

/**
 * Summary of AdminController
 */
class AdminMainController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // получаем данные для вывода на главной странице админа
        $commissions = Commission::get(['id', 'commission']);
        $advertiserExpenses = (float) OfferClick::sum('advertiser_cost');
        $webmasterIncome = (float) OfferClick::sum('webmaster_income');
        $systemProfit = (float) OfferClick::sum('system_commission');

        return view('admin.main', compact('commissions', 'advertiserExpenses', 'webmasterIncome', 'systemProfit'));
    }
}
