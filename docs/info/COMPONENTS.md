# Componentes Frontend (`COMPONENTS.md`)

Inventario real de layouts, parciales Blade y módulos JavaScript del frontend público.

## Layouts (`resources/views/layouts/`)

- `master.blade.php` — Layout principal de las vistas públicas.
- `head.blade.php` — Cabecera HTML, metadatos SEO y carga de assets.
- `navbar.blade.php` — Barra de navegación principal.
- `footer.blade.php` — Pie de página público.
- `footer_meta.blade.php` — Metadatos y cierre de scripts en el pie.

## Parciales reutilizables (`resources/views/partials/`)

- `_beta.blade.php` — Aviso/banner de versión beta.
- `_cookies.blade.php` — Aviso y gestión de cookies.
- `_general_stats.blade.php` — Bloque de estadísticas generales del proyecto.
- `_groups_links.blade.php` — Enlaces rápidos a grupos de contenido.
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
