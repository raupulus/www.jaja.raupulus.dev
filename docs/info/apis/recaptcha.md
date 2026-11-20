# Integración: Google reCAPTCHA v3

Cómo integra **JaJa Project** la verificación de Google reCAPTCHA v3.

## Punto de integración en el proyecto

- **Middleware**: `app/Http/Middleware/VerifyRecaptcha.php` (`recaptcha:score,expectedAction`)
- **Endpoint consumido**: `POST https://www.google.com/recaptcha/api/siteverify` (vía `Http::asForm()->connectTimeout(5)->timeout(10)->post(...)`).
- **Parámetro de entrada esperado**: campo `g_recaptcha` en el cuerpo de la petición HTTP.
- **Umbral de puntuación (`score`) y acción**: por defecto `0.5` (en envío de sugerencias web: `0.7` y acción esperada `suggestion_send`).

## Configuración

- `config('google.recaptcha.secret_key')` — Clave secreta de reCAPTCHA.
  - Si está vacía o no configurada, el middleware hace bypass y permite continuar la petición.
- `config('google.recaptcha.site_key')` — Clave pública usada en `home.blade.php` mediante `@json(...)`.

## Carga bajo demanda en cliente (Privacidad RGPD)

- El script oficial `https://www.google.com/recaptcha/api.js` **no** se carga en `<head>` durante las visitas de solo lectura ni en vistas sin formulario.
- `resources/views/partials/forms/_form_suggestions.blade.php` expone `data-recaptcha-site-key` en `<form id="form-suggestion-send">` y `resources/js/suggestions_forms.js` inyecta el script de Google bajo demanda al primer foco/interacción con el formulario (`focusin`, `pointerdown` o `input`) o justo antes de completar el `submit`.

## Flujo y manejo de errores

1. Si falta `g_recaptcha`, lanza `ValidationException` con mensaje en español en la clave `g_recaptcha`.
2. Envía `secret`, `response` (token) y `remoteip` (`$request->ip()`) a `siteverify` con timeouts explícitos (`5s` conexión / `10s` petición) dentro de un bloque `try/catch`.
3. Si falla la red, la respuesta no es HTTP 2xx, `success` es falso, `score < $score` o `action` no coincide con `$expectedAction`, registra aviso (en fallo de conexión) y lanza `ValidationException`.

## Estado de verificación

- Comportamiento del middleware (`action`, `score` y timeouts): verificado mediante tests Feature con `Http::fake()` en `tests/Feature/AccessControlAndSecurityTest.php`.
- Petición real contra `siteverify` de Google en producción: `⚠️ sin verificar`.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
