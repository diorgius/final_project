<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // устанавливаем язык на все маршруты
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // задаем алиасы для middleware
        $middleware->alias([
            'checkadmin' => \App\Http\Middleware\CheckAdmin::class,
            'checkadvertiser' => \App\Http\Middleware\CheckAdvertiser::class,
            'checkwebmaster' => \App\Http\Middleware\CheckWebmaster::class,
            'checkactive' => \App\Http\Middleware\CheckActive::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
