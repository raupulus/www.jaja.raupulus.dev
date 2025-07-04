<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleWithRealIp
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $ip = $this->getRealIpAddress($request);
        $key = 'throttle:' . $ip;

        if (!RateLimiter::attempt($key, $maxAttempts, fn() => true, $decayMinutes * 60)) {
            $seconds = RateLimiter::availableIn($key);

            // Si es una petición JSON/API, devolver respuesta con ApiResponse
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    message: "Demasiadas solicitudes. Espera {$seconds} segundos.",
                    status: 429,
                    errors: null,
                    meta: [
                        'retry_after' => $seconds,
                        'limit' => $maxAttempts,
                        'remaining' => 0,
                        'reset' => now()->addSeconds($seconds)->timestamp
                    ]
                );
            }

            // Para peticiones web, comportamiento similar al middleware throttle original
            return $this->buildWebResponse($request, $seconds);
        }

        return $next($request);
    }

    /**
     * Construye la respuesta para peticiones web (similar al middleware throttle original)
     */
    private function buildWebResponse(Request $request, int $retryAfter): Response
    {
        $response = response('Too Many Attempts.', 429);

        return $this->addHeaders(
            $response,
            $maxAttempts = 0,
            $remainingAttempts = 0,
            $retryAfter
        );
    }

    /**
     * Añade headers estándar de rate limiting (como el middleware throttle original)
     */
    private function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts, ?int $retryAfter = null): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);

        if (!is_null($retryAfter)) {
            $response->headers->add([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
            ]);
        }

        return $response;
    }

    private function getRealIpAddress(Request $request): string
    {
        $headers = [
            'CF-Connecting-IP',
            'HTTP_CF_CONNECTING_IP',
            'X-Forwarded-For',
            'X-Real-IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
        ];

        foreach ($headers as $header) {
            $ip = $request->header($header) ?? $_SERVER[$header] ?? null;

            if ($ip) {
                $ip = trim(explode(',', $ip)[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }
}
