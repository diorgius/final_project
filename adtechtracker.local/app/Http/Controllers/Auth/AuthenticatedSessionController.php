<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Services\SecurityLogger;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Перенаправляем пользователя на страницу в зависимости от роли
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        switch (auth()->user()->role) {
            case 'admin':
                return redirect()->intended(route('admin.main', absolute: false));
                break;
            case 'advertiser':
                return redirect()->intended(route('advertiser.offers', absolute: false));
                break;
            case 'webmaster':
                return redirect()->intended(route('webmaster.offers', absolute: false));
                break;
            default:
                abort(404, __('http-statuses.404'));
                break;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // пишем событие в лог
        SecurityLogger::successfulLogout(Auth::user(), $request);
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
