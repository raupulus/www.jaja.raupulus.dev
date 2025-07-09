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

        ## Verifico si la IP está en la lista de exclusión
        if ($this->isIpExcluded($ip)) {
            return $next($request);
        }

        $key = 'throttle:' . $ip;

        if (!RateLimiter::attempt($key, $maxAttempts, fn() => true, $decayMinutes * 60)) {
            $seconds = RateLimiter::availableIn($key);

            ## Si es una petición JSON/API, devuelvo respuesta con ApiResponse
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

            ## Para peticiones web, comportamiento similar al middleware throttle original
            return $this->buildWebResponse($request, $seconds);
        }

        return $next($request);
    }

    /**
     * Verifica si una IP está en la lista de exclusión del rate limit
     */
    private function isIpExcluded(string $ip): bool
    {
        $excludedIps = $this->getExcludedIps();

        ## Verifico coincidencia exacta
        if (in_array($ip, $excludedIps)) {
            return true;
        }

        ## Verifico rangos CIDR
        /*
        foreach ($excludedIps as $excludedIp) {
            if ($this->ipInRange($ip, $excludedIp)) {
                return true;
            }
        }
        */

        return false;
    }

    /**
     * Obtiene la lista de IPs excluidas desde la configuración
     */
    private function getExcludedIps(): array
    {
        return config('app.rate_limit.excluded_ips', []);
    }

    /**
     * Verifica si una IP está dentro de un rango CIDR
     */
    private function ipInRange(string $ip, string $range): bool
    {
        ## Si no es un rango CIDR, solo comparar exactamente
        if (!str_contains($range, '/')) {
            return $ip === $range;
        }

        list($subnet, $mask) = explode('/', $range);

        ## Convierto a formato binario para IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip_long = ip2long($ip);
            $subnet_long = ip2long($subnet);
            $mask_long = -1 << (32 - (int)$mask);

            return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
        }

        ## Para IPv6 (implementación básica)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return false;
        }

        return false;
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
