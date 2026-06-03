<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\UserRole;

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
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // dd($request);
        $request->authenticate();

        $request->session()->regenerate();

        // $roleId = User::where('email', $request->email)->first('role_id');

        $role = User::with('role')->where('email', $request->email)->first('role_id');

        // dd($role);

        switch ($role->role->role) {
            case 'Администратор':
                return redirect()->intended(route('admin.dashboard', absolute: false));
                break;
            case 'Рекламодатель':
                return redirect()->intended(route('advertiser.dashboard', absolute: false));
                break;
            case 'Вебмастер':
                return redirect()->intended(route('webmaster.dashboard', absolute: false));
                break;
            default:
                return redirect()->intended(route('/404', absolute: false));
                break;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
