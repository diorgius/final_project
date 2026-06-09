<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebmasterController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('webmaster.dashboard');
    }
}
