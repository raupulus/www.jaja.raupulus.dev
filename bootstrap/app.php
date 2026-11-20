<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use \Laravel\Sanctum\Http\Middleware\CheckAbilities;
use \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Illuminate\Http\Request;
use \App\Http\Responses\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
            'isAdmin' => \App\Http\Middleware\IsAdminMiddleware::class,
            'throttle.real.ip' => \App\Http\Middleware\ThrottleWithRealIp::class,
        ]);

        //$middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            $wantsJson = $request->is('api/*') || $request->expectsJson();

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($wantsJson) {
                    return ApiResponse::error('No autenticado.', 401);
                }

                return redirect()->guest($e->redirectTo($request) ?? route('login'));
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException && $wantsJson) {
                if (config('app.debug')) {
                    return ApiResponse::error($e->getMessage(), 404, null, null, ['trace' => $e->getTrace()]);
                }

                return ApiResponse::error($e->getMessage(), 404);
            }

            return false; // Importante: retornar false permite que el manejador de Laravel siga con otras excepciones.
        });
    })->create();
