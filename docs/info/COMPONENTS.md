# Componentes Frontend (`COMPONENTS.md`)

Inventario real de layouts, parciales Blade y módulos JavaScript del frontend público.

## Layouts (`resources/views/layouts/`)

- `master.blade.php` — Layout principal de las vistas públicas.
- `head.blade.php` — Cabecera HTML, metadatos SEO únicos (`<title>`, `<meta name="description">`, Open Graph `.jpg` con dimensiones y `og:type`, Twitter Cards con `twitter:image:alt`), `theme-color` por defecto (`#00bcd4`), carga de assets y `@stack('jsonld')` para datos estructurados Schema.org.
- `navbar.blade.php` — Barra de navegación principal (enlace `Dashboard` dinámico según rol: `/admin` para administradores y `/panel` para el resto de usuarios; enlaces externos con `rel="noopener noreferrer"`).
- `footer.blade.php` — Pie de página público.
- `footer_meta.blade.php` — Metadatos y cierre de scripts en el pie.

## Convenciones de seguridad y SEO en vistas y enlaces

- Todos los enlaces con `target="_blank"` en vistas y parciales incluyen `rel="noopener noreferrer"`.
- Las descripciones de grupos y categorías en `contents/groups/show.blade.php` y `contents/categories/show.blade.php` se imprimen escapadas con `{{ ... }}`.
- Las vistas públicas respetan la jerarquía secuencial de encabezados (`h1 → h2`, o `h1 → h2 → h3` dentro de parciales titulados), usan textos descriptivos en enlaces y mantienen atributos `alt` fieles a cada imagen/icono.

## Parciales reutilizables (`resources/views/partials/`)

- `_beta.blade.php` — Aviso/banner de versión beta.
- `_cookies.blade.php` — Aviso y gestión de cookies.
- `_general_stats.blade.php` — Bloque de estadísticas generales del proyecto.
- `_groups_links.blade.php` — Enlaces rápidos a grupos de contenido.
- `_jsonld.blade.php` — Renderiza un bloque `<script type="application/ld+json">` escapado de forma segura (`JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`).
- `_social_icons.blade.php` — Iconos y enlaces a redes sociales.

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
- `cookies.js` — Lógica del banner de cookies.
- `navbar.js` — Comportamiento interactivo de la barra de navegación.
- `suggestions_forms.js` — Interactividad del formulario de sugerencias (ej. opciones de quiz).

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
