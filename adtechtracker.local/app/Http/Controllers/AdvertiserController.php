<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdvertiserController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('advertiser.dashboard');
    }
}
