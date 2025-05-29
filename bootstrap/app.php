<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use \Laravel\Sanctum\Http\Middleware\CheckAbilities;
use \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        //$middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) { // Aquí 'Request' se resolverá correctamente
             //dd($e, $request->expectsJson(), $request->headers->all()); // Descomenta esta línea para depurar

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'No autenticado.'], 401);
                }

                return redirect()->guest(route('filament.admin.auth.login'));
            }

            // Si es otra excepción, permite que Laravel continúe con su manejo por defecto.
            return false; // Importante: retornar false permite que el manejador de Laravel siga con otras excepciones.
        });

        /*
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
        });
        */

    })->create();
