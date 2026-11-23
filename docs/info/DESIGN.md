# Diseño y Estilos (`DESIGN.md`)

Documentación del sistema de estilos del frontend público de **JaJa Project**.
Los paneles de administración (`/admin`) y usuario (`/panel`) utilizan los estilos propios de **Filament 3** con color primario accesible (`#0e7490`, contraste WCAG AA en botones).

## Stack de estilos verificado

- **CSS Modular con tokens nativos** compilado mediante **Vite** (`vite.config.js` con `pnpm run dev` / `pnpm run build`, compatible también con `npm`), manteniendo `pnpm-lock.yaml` y `package-lock.json` sin fijar `packageManager` en `package.json` para evitar bloqueos de Corepack.
- **`color-scheme: dark`** declarado en `<meta name="color-scheme" content="dark">` (`head.blade.php`) y `:root` (`frontend.css`).
- **Tipografía unificada**: familia `Inter` cargada localmente desde `public/fonts/inter/inter.css` con `preload` en `<head>` para `inter-400.woff2` e `inter-700.woff2`, expuesta como `--font-sans` y `--font-mono`.

## Sistema de Tokens (`resources/css/frontend.css`)

- **Colores de marca y superficie**: `--color-primary` (`#00bcd4`), `--color-primary-hover`, `--color-primary-soft`, `--color-on-primary`, `--color-bg` (`#121212`), `--color-surface` (`#1e1e1e`), `--color-surface-elevated`, `--color-border`, `--color-border-control`.
- **Texto y estados WCAG AA**: `--color-text`, `--color-text-muted`, `--color-text-accent`, `--color-success`/`-text`, `--color-warning`/`-text`, `--color-danger`/`-text`/`-accent`, `--color-info`/`-text`. Se mantienen alias en español (`--color-primario`, etc.) apuntando a los tokens canónicos.
- **Maquetación y lectura**: `--container-max: 1280px`, `--prose-max: 68ch`, `scroll-padding-top`/`scroll-padding-bottom` para no ocultar el foco tras la cabecera o banner de cookies, `text-wrap: balance` en `h1–h3`, `text-wrap: pretty` en párrafos de `.page-content-wrapper`, `font-variant-numeric: tabular-nums` en contadores y `overflow-wrap: break-word` (sin `word-break: break-all`).
- **Accesibilidad de foco, enlaces y movimiento**: utilidad `.visually-hidden`, anillo global `:focus-visible` de alto contraste (`3px solid var(--color-text-accent)`), subrayado visible en enlaces dentro de texto fluido (`p`, `li`, `label`, `dd`, `td`, `.page-content-wrapper`), `.skip-link:focus-visible` y respeto estricto de `@media (prefers-reduced-motion: no-preference)` / `reduce` sin animaciones continuas indefinidas superiores a 5 segundos.
- **Botones**: `.btn` sólido principal, `.btn--secondary` y `.btn--outline`.
- **Animaciones**: keyframes prefijados sin colisión (`timer-pulse`, `dc-bot-ripple`, `contributor-value-pulse`, `randomPulse`, `loading-shimmer`) basados exclusivamente en `transform` y `opacity`, ejecutados bajo interacción o límite acotado.

## Hojas CSS modulares activas en `vite.config.js`

- Base y layout: `frontend.css`, `buttons.css`, `footer.css`, `page.css`
- Portada y navegación: `groups_links.css`, `grid_categories.css`, `general_stats.css`
- Componentes visuales: `horizontal_card.css`, `social_icons.css`, `cookies.css`
- Colaboradores y proyectos: `collaborators.css`, `contributors.css`, `projects.css`
- Formularios: `suggestions_form.css`

> Nota: `home.css` y `pages.css` (vacíos) y las entradas huérfanas `app.css`/`app.js` fueron retirados de `vite.config.js`.

## Convenciones

- Los textos visibles para el usuario deben estar en español.
- Los nombres de ficheros CSS, variables de diseño (`--color-*`), clases e identificadores en inglés.
- Antes de añadir nuevas hojas CSS, comprobar si el estilo corresponde a un módulo existente en `resources/css/` usando sus tokens `:root`.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-23
