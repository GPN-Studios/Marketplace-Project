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

        // Atrás do proxy reverso da plataforma de deploy (que termina TLS e
        // encaminha como HTTP internamente) — sem isso, url()/asset() geram
        // links http:// mesmo em produção servida via https://.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
