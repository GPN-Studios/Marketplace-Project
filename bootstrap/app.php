<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);

        // Rede de segurança geral contra flood/scraping básico, por cima dos
        // limites mais específicos já aplicados rota a rota em routes/web.php.
        $middleware->web(append: 'throttle:global');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
