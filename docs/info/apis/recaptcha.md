# Integración: Google reCAPTCHA v3

Cómo integra **JaJa Project** la verificación de Google reCAPTCHA v3.

## Punto de integración en el proyecto

- **Middleware**: `app/Http/Middleware/VerifyRecaptcha.php`
- **Endpoint consumido**: `POST https://www.google.com/recaptcha/api/siteverify` (vía `Http::asForm()->post(...)`).
- **Parámetro de entrada esperado**: campo `g_recaptcha` en el cuerpo de la petición HTTP.
- **Umbral de puntuación (`score`)**: por defecto `0.5` (configurable como parámetro del middleware).

## Configuración

- `config('google.recaptcha.secret_key')` — Clave secreta de reCAPTCHA.
  - Si está vacía o no configurada, el middleware hace bypass y permite continuar la petición.

## Flujo y manejo de errores

1. Si falta `g_recaptcha`, lanza `ValidationException` con mensaje en español en la clave `g_recaptcha`.
2. Envía `secret`, `response` (token) y `remoteip` (`$request->ip()`) a `siteverify`.
3. Si la respuesta no es válida, `success` es falso o `score < $score`, lanza `ValidationException`.

## Estado de verificación

- Comportamiento del código local: verificado en `app/Http/Middleware/VerifyRecaptcha.php`.
- Petición real contra `siteverify` en esta sesión: `⚠️ sin verificar`.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
