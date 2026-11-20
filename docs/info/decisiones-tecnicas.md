# Decisiones Técnicas

Registro de decisiones deliberadas del proyecto verificadas en el código, para evitar que futuros cambios las reviertan por error.

## 1. Devolver `404 Not Found` en rutas aleatorias sin contenido
- **Dónde**: Controladores web de contenido aleatorio por categoría (`/categoria/{slug}/random`) o grupo (`/grupo/{slug}/random`).
- **Decisión**: Si no hay contenidos aptos para mostrar, se devuelve HTTP `404` en lugar de una página `200` vacía.
- **Motivo**: Evitar penalizaciones SEO por *Soft 404* y *Thin Content* en Google Search Console.
- **Test asociado**: `tests/Feature/CategoryEmptyContentTest.php`.

## 2. Separación de paneles Filament (`/admin` y `/panel`)
- **Dónde**: `app/Providers/Filament/AdminPanelProvider.php` y `app/Providers/Filament/PanelPanelProvider.php`.
- **Decisión**: Mantener dos paneles Filament separados: administración completa en `/admin` y gestión de aportaciones/colaborador para usuarios en `/panel`.
- **Motivo**: Aislar permisos y recursos expuestos sin duplicar vistas Blade manuales.

## 3. Publicación en redes sociales mediante servicio externo configurable y política de tokens Sanctum
- **Dónde**: `config/services.php`, `config/sanctum.php`, `app/Helpers/PublishToSocialHelper.php` y `app/Console/Commands/PublishContentToSocialCommand.php`.
- **Decisión**: Delegar la publicación en redes sociales a la URL configurable `services.social_publisher.url` (`SOCIAL_PUBLISHER_URL`, con valor retrocompatible `http://localhost:8080/publish`), aplicando timeouts explícitos (`connect_timeout = 5s`, `timeout = 15s`) y excluyendo siempre contenido marcado con `is_adult = true`. En paralelo, mantener `config('sanctum.expiration')` gobernado por `SANCTUM_TOKEN_EXPIRATION` con valor por defecto `null` y habilidades completas retrocompatibles para no invalidar tokens activos de bots comunitarios.
- **Motivo**: Desacoplar las credenciales y despliegue del servicio de redes sociales garantizando tolerancia a fallos de red y conservar compatibilidad retroactiva total con integraciones en producción.

## 4. Verificación reCAPTCHA opcional según configuración
- **Dónde**: `app/Http/Middleware/VerifyRecaptcha.php`.
- **Decisión**: Si `config('google.recaptcha.secret_key')` no está configurado, el middleware deja pasar la petición sin bloquear.
- **Motivo**: Permitir desarrollo local y tests sin depender de credenciales de Google reCAPTCHA.

## 5. Cabeceras HTTP de seguridad en Laravel + Apache y rutas cacheables
- **Dónde**: `app/Http/Middleware/SecurityHeadersMiddleware.php`, `bootstrap/app.php`, `apache.conf` y `routes/web.php`.
- **Decisión**: Inyectar las cabeceras de seguridad (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` y `Strict-Transport-Security` en HTTPS) desde middleware global y replicarlas en `apache.conf` para cubrir también archivos estáticos. Evitar closures en archivos de rutas (`routes/web.php`) para mantener `php artisan route:cache` operativo.
- **Motivo**: Defensa en profundidad tanto para respuestas dinámicas PHP como para activos servidos directamente por Apache y despliegue optimizado con caché de rutas.

## 6. Dominio canónico único y archivos de rastreo SEO
- **Dónde**: `apache.conf`, `public/robots.txt`, `public/llms.txt` y `.env.example.production`.
- **Decisión**: Estandarizar el dominio canónico público en `https://jaja.raupulus.dev` (sin `www.`), redirigiendo `301` desde `www.jaja.raupulus.dev` en `apache.conf` y alineando `robots.txt` (`Sitemap`) y `llms.txt`. Mantener `public/llms.txt` limitado exclusivamente a rutas públicas reales y endpoints públicos de `/api/v1/random`.
- **Motivo**: Evitar contenido duplicado entre subdominios, preservar la autoridad SEO y no exponer rutas internas ni obsoletas a rastreadores e integraciones LLM.

## 7. Índices de claves foráneas e índices únicos parciales `WHERE deleted_at IS NULL`
- **Dónde**: `database/migrations/2026_11_20_120000_add_missing_foreign_key_indexes.php`, `database/migrations/2026_11_20_120100_replace_unique_constraints_with_partial_soft_delete_indexes.php` y formularios Filament (`UserResource`, `CategoryResource`, `TypeResource`, `GroupResource`, `PageResource`, `ContentResource`).
- **Decisión**: Añadir de forma puramente aditiva los índices sobre claves foráneas y columnas frecuentes (`content_categories`, `content_options`, `suggestions`, `suggestion_options`, `suggestion_categories`, `collaborators`, `collaborator_projects`, `reports`, `contents`) y sustituir las restricciones `unique` estándar en tablas con `SoftDeletes` (`users.email`, `categories.slug`, `types.slug`, `groups.slug`, `pages.slug`) por índices únicos parciales `WHERE deleted_at IS NULL` (compatibles con PostgreSQL y SQLite), alineando la regla `unique` en Filament con `whereNull('deleted_at')`.
- **Motivo**: Evitar *Sequential Scans* en joins frecuentes en PostgreSQL y permitir reutilizar un `email` o `slug` tras un borrado lógico sin provocar errores `500 UniqueConstraintViolationException`, manteniendo reversibilidad sin pérdida de datos en producción.

## 8. Filtrado público centralizado (`scopePublicSafe`) y resolución segura de IP (`TrustProxies`)
- **Dónde**: `config/content.php`, `app/Models/Content.php`, `app/Models/Group.php`, `config/trustedproxy.php` y `app/Helpers/IpHelper.php`.
- **Decisión**: Centralizar los IDs de grupos excluidos de listados públicos y redes sociales en `config('content.excluded_public_group_ids')` (respaldado por `Content::EXCLUDED_PUBLIC_GROUP_IDS = [4, 14]`) y encapsular el criterio en `Content::scopePublicSafe()` / `isPublicSafe()` junto con `Group::scopePublicSafe()`. Así mismo, gobernar la confianza en proxies inversos vía `config/trustedproxy.php` (`TRUSTED_PROXIES`) y resolver la IP real del cliente en un único punto (`IpHelper::getClientIp()`), aceptando `CF-Connecting-IP` únicamente cuando `$request->isFromTrustedProxy()` es verdadero.
- **Motivo**: Eliminar números mágicos dispersos en 7 archivos, permitir parametrizar grupos excluidos por entorno sin redeploy de código y evitar falsificación de IP (`X-Forwarded-For` / `CF-Connecting-IP`) para evadir rate limits.

## 9. Quality Gate automatizado, Larastan nivel 5 y paridad de tests en PostgreSQL
- **Dónde**: `phpstan.neon`, `composer.json`, `phpunit.xml`, `phpunit.pgsql.xml`, `database/factories/`, `tests/TestCase.php` y `tests/Feature/DatabaseIndexesAndSoftDeletesTest.php`.
- **Decisión**: Incorporar análisis estático con Larastan (`larastan/larastan`) al nivel 5 sobre `app/`, `config/` y `routes/` sin recurrir a supresiones `@phpstan-ignore`, mantener la suite rápida por defecto sobre SQLite `:memory:` (`phpunit.xml`) y añadir configuración dedicada `phpunit.pgsql.xml` (`composer test:pgsql` usando `-c phpunit.pgsql.xml`) con los 12 Model Factories completos para verificar paridad real con PostgreSQL. En `tests/TestCase.php` y `CategoryFactory::ensureGeneralCategoryExists()` se garantiza la categoría General (`id = 1`), se adelanta `groups_id_seq` a `>= 100` para que las secuencias persistentes bajo `RefreshDatabase` no colisionen accidentalmente con los grupos excluidos `[4, 14]`, y se envuelven las comprobaciones de `QueryException` en `DB::transaction()` (SAVEPOINT) para evitar el estado abortado `SQLSTATE[25P02]` en PostgreSQL.
- **Motivo**: Garantizar el mismo comportamiento de migraciones, índices únicos parciales y consultas tanto en desarrollo/CI ligero como frente al motor PostgreSQL de producción, con cero errores de tipado y formato unificado.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
