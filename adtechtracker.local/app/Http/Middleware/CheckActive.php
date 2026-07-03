<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityLogger;

class CheckActive
{
    /**
     * Проверка активности учетной записи.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->status === 0) {

            // пишем в лог попытку входа заблокированного пользователя
            SecurityLogger::blockedUserLogin(Auth::user(), $request);
            
            // если учетка заблокирована, разлогиниваем
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // редиректим на страницу логина, с сообщением о блокировке учетки
            return redirect()->route('login')->withErrors([
                    'email' => __('http-statuses.423'),
                ]);
        }
        
        return $next($request);
    }
}
