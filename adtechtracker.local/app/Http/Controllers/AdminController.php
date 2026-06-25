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
        return view('admin.dashboard');
    }
}
