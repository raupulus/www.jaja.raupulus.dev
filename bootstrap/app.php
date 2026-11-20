<?php

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\ThrottleWithRealIp;
use App\Http\Middleware\VerifyRecaptcha;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api/v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SecurityHeadersMiddleware::class);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'recaptcha' => VerifyRecaptcha::class,
            'isAdmin' => IsAdminMiddleware::class,
            'throttle.real.ip' => ThrottleWithRealIp::class,
        ]);

        // $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            $wantsJson = $request->is('api/*') || $request->expectsJson();

            if ($e instanceof AuthenticationException) {
                if ($wantsJson) {
                    return ApiResponse::error('No autenticado.', 401);
                }

                return redirect()->guest($e->redirectTo($request) ?? route('login'));
            }

            if ($e instanceof NotFoundHttpException && $wantsJson) {
                if (config('app.debug')) {
                    return ApiResponse::error($e->getMessage(), 404, null, null, ['trace' => $e->getTrace()]);
                }

                return ApiResponse::error($e->getMessage(), 404);
            }

            return false; // Importante: retornar false permite que el manejador de Laravel siga con otras excepciones.
        });
    })->create();
