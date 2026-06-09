<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Summary of AdminController
 */
class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
