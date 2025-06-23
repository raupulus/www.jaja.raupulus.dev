<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $score = 0.5): Response
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

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('google.recaptcha.secret_key'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        if (!$result || !isset($result['success']) || !$result['success'] || $result['score'] < $score) {
            throw ValidationException::withMessages([
                'g_recaptcha' => 'Verificación del reCAPTCHA fallida.',
            ]);
        }

        return $next($request);
    }
}
