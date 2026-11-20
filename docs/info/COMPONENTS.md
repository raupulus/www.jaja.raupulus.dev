# Componentes Frontend (`COMPONENTS.md`)

Inventario real de layouts, parciales Blade y módulos JavaScript del frontend público.

## Layouts (`resources/views/layouts/`)

- `master.blade.php` — Layout principal de las vistas públicas con enlace de salto (`.skip-link` hacia `#main-content`) y landmark `<main id="main-content" tabindex="-1">`.
- `head.blade.php` — Cabecera HTML, `<meta name="color-scheme" content="dark">`, preload de fuentes `Inter` (`400`/`700`), carga de CSS en `<head>` incluyendo `@yield('css')` (sin FOUC), metadatos SEO únicos (`<title>`, `<meta name="description">`, Open Graph `.jpg` con dimensiones y `og:type`, Twitter Cards con `twitter:image:alt`), `theme-color` por defecto (`#00bcd4`) y `@stack('jsonld')` para datos estructurados Schema.org.
- `navbar.blade.php` — Barra de navegación principal envuelta en `<header class="site-header"><nav aria-label="Navegación principal">` con enlaces directos (`Inicio`, `Grupos`, `Categorías`, `Colaboradores`, `API`, `Acerca de`, `Autor`), `aria-current="page"`, enlace `Admin`/`Panel` dinámico según rol, botón hamburguesa `<button type="button">` y enlaces externos con `rel="noopener noreferrer"`.
- `footer.blade.php` — Pie de página público con listas semánticas `<ul><li>`, iconos SVG decorativos (`aria-hidden="true" focusable="false"`, sin animaciones SMIL indefinidas) y áreas táctiles mínimas de `44px`.
- `footer_meta.blade.php` — Metadatos y scripts globales base en el pie.

## Parciales y Vistas de Error (`resources/views/partials/`, `resources/views/errors/`)

- `_breadcrumbs.blade.php` — Ruta de navegación reutilizable (`<nav aria-label="Ruta de navegación">`) usada en índices y vistas de detalle.
- `errors/404.blade.php` y `errors/500.blade.php` — Páginas de error HTTP integradas con `layouts.master` y `noindex, nofollow`.

## Convenciones de seguridad, SEO, W3C y rendimiento en vistas y enlaces

- Todos los enlaces con `target="_blank"` en vistas y parciales incluyen `rel="noopener noreferrer"`.
- Las descripciones de grupos y categorías en `contents/groups/show.blade.php` y `contents/categories/show.blade.php` se imprimen escapadas con `{{ ... }}`.
- Las vistas públicas respetan la jerarquía secuencial de encabezados (`h1 → h2`, o `h1 → h2 → h3` dentro de parciales titulados), usan `<article>` en tarjetas de contenido/páginas/colaboradores/proyectos, evitan errores W3C (`<p>` en `<span>`, `src=""` vacío, `alt` en `<a>`, `xml:space="preserve"` y `X-UA-Compatible`), usan textos descriptivos en español y mantienen atributos `alt` fieles a cada imagen/icono.
- La imagen LCP principal de portada (`portada-jaja-project.webp`) se carga con `fetchpriority="high"` y dimensiones explícitas (`800x300`); las imágenes *below-the-fold* declaran `width`, `height`, `loading="lazy"` y `decoding="async"`, y todos los iconos sociales se sirven localmente desde `public/images/icons/`.
- Las vistas no incluyen bloques `<style>` inline (`_beta.blade.php` estilado desde `frontend.css`) y cuentan con hoja de impresión `@media print`.

## Parciales reutilizables (`resources/views/partials/`)

- `_beta.blade.php` — Aviso/banner de versión beta.
- `_cookies.blade.php` — Banner de cookies esenciales (`role="dialog"`, `aria-labelledby`, `aria-describedby`) con botón de aceptación (`#acceptCookies`) y cierre sin consentir (`#dismissCookies`).
- `_general_stats.blade.php` — Bloque de estadísticas generales del proyecto.
- `_groups_links.blade.php` — Enlaces rápidos a grupos de contenido.
- `_jsonld.blade.php` — Renderiza un bloque `<script type="application/ld+json">` escapado de forma segura (`JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`).
- `_social_icons.blade.php` — Iconos y enlaces a redes sociales locales.

## Privacidad, cookies y formularios públicos

- `resources/js/cookies.js` gestiona `hidden` e `inert` sobre `#cookieBanner`, aplica `; Secure` cuando se sirve por HTTPS y al pulsar `Escape` o `#dismissCookies` llama a `hideBanner()` sin grabar consentimiento (`FSEC-08`).
- `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true` y `SESSION_SAME_SITE=lax` quedan configurados por defecto en `.env.example.production`.
- `partials/forms/_form_suggestions.blade.php` incluye nota RGPD de primera capa (`public@raupulus.dev` y enlace a Política de Privacidad) y carga diferida de reCAPTCHA v3 únicamente al interactuar con el formulario.

## Vistas por sección (`resources/views/`)

- `home.blade.php` — Portada principal.
- `template.blade.php` — Plantilla base auxiliar.
- `contents/` — Vistas de listado y detalle de contenidos.
- `collaborators/` — `index.blade.php` y `show.blade.php`.
- `pages/` — `index.blade.php` y `show.blade.php` para páginas CMS.
- `scribe/index.blade.php` — Vista generada para la documentación de la API (Scribe).

## Scripts JavaScript (`resources/js/`)

- `app.js` — Punto de entrada JS principal.
- `bootstrap.js` — Configuración base (Axios / bootstrap de Laravel).
- `alerts.js` — Gestión de alertas en la interfaz.
- `cookies.js` — Lógica accesible del banner de cookies (cierre con `Escape` sin consentir, `#dismissCookies`, atributos `inert`/`hidden`).
- `navbar.js` — Comportamiento interactivo de la barra de navegación móvil con `aria-expanded`, cierre con `Escape` o clic exterior y atributo `inert` automático cuando el menú está colapsado en pantallas `<= 1024px`.
- `suggestions_forms.js` — Interactividad accesible del formulario de sugerencias (filtrado de grupos, selector de respuesta correcta de quiz mediante `<button type="button" aria-pressed>`, vista previa de imagen mediante `<label>`, sincronización `aria-invalid` y contador de throttle).

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
