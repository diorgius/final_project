<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityLogger;

class CheckWebmaster
{
    /**
     * Проверка уровня доступа вебмастера
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->role !== 'webmaster') {

            // пишем в лог попытку входа на запрещенную страницу
            SecurityLogger::attemptToLoginForbiddenPage(Auth::user(), $request);
                    
            abort(403, __('http-statuses.403'));
        }
        return $next($request);
    }
}
