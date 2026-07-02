<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Summary of WebmasterController
 */
class WebmasterController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard');
    }
}
