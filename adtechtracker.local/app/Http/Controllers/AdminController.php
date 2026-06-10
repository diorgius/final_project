<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commission;

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
        // dd($commissions);

        return view('admin.dashboard', compact('commissions'));
    }
}
