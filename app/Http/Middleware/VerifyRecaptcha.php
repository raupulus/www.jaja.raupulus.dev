<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware VerifyRecaptcha
 *
 * Verifica el token de Google reCAPTCHA v3 cuando está configurada la clave secreta.
 */
class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, float|string $score = 0.5, ?string $expectedAction = null): Response
    {
        if (! config('google.recaptcha.secret_key')) {
            return $next($request);
        }

        $recaptchaToken = $request->input('g_recaptcha');

        if (! $recaptchaToken) {
            throw ValidationException::withMessages([
                'g_recaptcha' => 'El campo reCAPTCHA es obligatorio.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(5)
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('google.recaptcha.secret_key'),
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification request failed', [
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'g_recaptcha' => 'No se pudo verificar el reCAPTCHA por un fallo temporal de conexión. Inténtalo de nuevo.',
            ]);
        }

        $result = $response->json();

        $actionValid = $expectedAction === null || (($result['action'] ?? null) === $expectedAction);

        if (
            ! $response->successful()
            || ! is_array($result)
            || ($result['success'] ?? false) !== true
            || ! isset($result['score'])
            || (float) $result['score'] < (float) $score
            || ! $actionValid
        ) {
            throw ValidationException::withMessages([
                'g_recaptcha' => 'Verificación del reCAPTCHA fallida. A veces es por tiempo de espera agotado y funciona si vuelves a intentarlo.',
            ]);
        }

        return $next($request);
    }
}
