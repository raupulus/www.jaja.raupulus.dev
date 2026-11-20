# Diseño y Estilos (`DESIGN.md`)

Documentación del sistema de estilos del frontend público de **JaJa Project**.
Los paneles de administración (`/admin`) y usuario (`/panel`) utilizan los estilos propios de **Filament 3**.

## Stack de estilos verificado

- **Tailwind CSS v4** integrado mediante `@tailwindcss/vite` (`package.json`, `vite.config.js`).
- **Compilación**: Vite (`npm run dev` / `npm run build`).
- **Punto de entrada CSS**: `resources/css/app.css`.
  - Importa `@import 'tailwindcss';`.
  - Configura fuentes `@source` sobre vistas Blade, JS y paginación de Laravel.
  - Define `--font-sans` con `'Instrument Sans'` y fallbacks del sistema en `@theme`.

## Hojas CSS modulares (`resources/css/`)

Además de `app.css`, el frontend público organiza estilos específicos por sección o componente:

- Base y layout: `frontend.css`, `footer.css`, `page.css`, `pages.css`
- Portada y navegación: `home.css`, `groups_links.css`, `grid_categories.css`, `general_stats.css`
- Componentes visuales: `buttons.css`, `horizontal_card.css`, `social_icons.css`, `cookies.css`
- Colaboradores y proyectos: `collaborators.css`, `contributors.css`, `projects.css`
- Formularios: `suggestions_form.css`

## Convenciones

- Los textos visibles para el usuario deben estar en español.
- Los nombres de ficheros CSS, clases e identificadores en inglés.
- Antes de añadir nuevas hojas CSS, comprobar si el estilo corresponde a un módulo existente en `resources/css/` o puede resolverse con utilidades de Tailwind v4.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
