<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckWebmaster
{
    /**
     * Проверка уровня доступа.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->role !== 'webmaster') {
            abort(403, 'Доступ запрещен');
        }
        return $next($request);
    }
}
