# Reglas y Guías para Agentes en JaJa Project

Este archivo define las reglas específicas para agentes de inteligencia artificial cuando trabajen en este proyecto.

## Contexto del Proyecto

- **Framework**: Laravel 13 (PHP 8.4)
- **Backend Admin/Panel**: Filament 3 (`/admin` y `/panel`)
- **Autenticación API**: Laravel Sanctum
- **Frontend público**: Blade + Tailwind CSS v4 (Vite) + CSS/JS modular
- **Propósito**: Plataforma comunitaria para gestionar y consumir chistes, preguntas (quiz) y adivinanzas mediante API y web.

## Estructura del Repositorio

```text
app/
├── Actions/              # Acciones reutilizables (ConvertImageToWebp)
├── Console/Commands/     # Comandos Artisan (PublishContentToSocial, SitemapGenerator, Stats)
├── Exceptions/           # Manejo de excepciones
├── Filament/
│   ├── Admin/            # Panel /admin (Resources y Widgets)
│   └── Panel/            # Panel /panel (Resources y Widgets)
├── Helpers/              # IpHelper, PublishToSocialHelper, StatsHelper
├── Http/
│   ├── Controllers/      # Controladores web y Api/ (AuthController, V1Controller)
│   ├── Middleware/       # AdminMiddleware, IsAdminMiddleware, ThrottleWithRealIp, VerifyRecaptcha
│   ├── Requests/         # Form Requests (web y Api/)
│   ├── Resources/        # API Resources
│   ├── Responses/        # ApiResponse
│   └── Traits/           # ApiResponseTrait
├── Models/               # Modelos Eloquent
└── Providers/            # AppServiceProvider y proveedores Filament
config/                   # Configuración Laravel y paquetes
database/                 # Migraciones, seeders y factories
docs/
├── info/                 # Documentación técnica VIVA del proyecto (versionada)
│   └── apis/             # Cómo integramos APIs externas
├── planning/             # [NO GIT · EFÍMERO] Planificaciones locales por desarrollador
└── auditorias/           # [NO GIT · EFÍMERO] Informes de auditoría locales
lang/                     # Traducciones
public/                   # Punto de entrada público y assets compilados
resources/
├── css/                  # Tailwind v4 (app.css) y hojas CSS modulares
├── js/                   # Scripts JS del frontend público
└── views/                # Layouts, parciales y vistas Blade
routes/                   # web.php, console.php y api/v1.php
scripts/                  # Scripts auxiliares de entorno y base de datos
tests/                    # Tests PHPUnit (Feature/ y Unit/)
```

## Reglas de Arquitectura y Código

1. **Patrones de Diseño**: Sigue siempre las convenciones de Laravel. Usa Eloquent ORM para base de datos y relaciones.
2. **Filament**: Para `/admin` y `/panel`, utiliza los Recursos (Resources) de Filament. Configura formularios y tablas en Filament en lugar de crear vistas Blade manuales cuando sea posible.
3. **API REST**:
   - Devuelve siempre respuestas JSON con los headers `Accept: application/json` y `Content-type: application/json`.
   - Utiliza `Scribe` para documentar los endpoints. Tras realizar cambios, ejecuta `php artisan scribe:generate`.
   - Usa API Resources para transformar modelos y colecciones Eloquent en JSON de manera limpia.
4. **Validaciones**: Utiliza *Form Requests* para validaciones de entrada, especialmente en la API y envíos (como crear sugerencias).
5. **Taxonomías y Clasificación**:
   - El contenido se clasifica en **Grupos** (ej. Chistes, Adivinanzas), **Tipos** (ej. Normal, Quiz) y **Categorías**.
   - Los contenidos tipo **Quiz** requieren opciones asociadas (`ContentOption`, `SuggestionOption`).
6. **Seguridad**:
   - Endpoints privados deben estar protegidos con middleware `auth:sanctum`.
   - Los endpoints públicos están sujetos a rate limiting (`ThrottleWithRealIp`).
   - Las validaciones de rol deben comprobarse en base de datos (actualmente: admin si `role_id=1`).
7. **Idioma y atribución**:
   - Documentación, comentarios y textos de usuario en **español**.
   - Identificadores, nombres de fichero/directorio y mensajes de log en **inglés** (excepción: campos literales de APIs externas).
   - Atribución cuando aplique: `@raupulus` · `public@raupulus.dev`. Sin firmas de agente en commits, PRs ni documentación.
8. **Flujo de commits y compatibilidad en producción**:
   - Se realiza un **commit por cada funcionalidad completada** antes de pasar a la siguiente (incluyendo su documentación actualizada en el mismo commit).
   - La web está en producción y en uso activo por la comunidad: **mantener siempre la compatibilidad actual** (no romper ni cambiar contratos ni endpoints ya publicados).

## Documentación

### Reglas permanentes

1. **Documentar es parte de la tarea**: ninguna tarea está terminada si su documentación no se actualiza **en el mismo commit** que el código.
2. **Jerarquía de verdad sobre el estado actual**: código > `docs/info/` > `AGENTS.md` > el resto. `docs/planning/`, `docs/future/` y `docs/auditorias/` **nunca** son fuente de verdad del estado.
3. **Discrepancias**: si se detecta una discrepancia entre documentación y código, se corrige en el mismo commit en que se detecta.
4. **Ciclo de módulos**:
   - Modificas un módulo → actualizas su `.md` en `docs/info/`.
   - Creas un módulo → lo creas desde `docs/info/_MODULE_TEMPLATE.md` y lo indexas en `docs/info/README.md` y en este `AGENTS.md`.
   - Eliminas un módulo → borras su `.md` y lo quitas de **todos** los índices.
5. **Pie de fecha obligatorio**: todo archivo bajo `docs/` termina con esta línea exacta detrás de un separador `---`:
   `> Creado: YYYY-MM-DD · Última revisión: YYYY-MM-DD`
   La fecha de creación nunca se modifica; la de revisión se actualiza en el mismo commit que el documento.
6. **Trabajo efímero (`docs/planning/` y `docs/auditorias/`)**: están en `.gitignore` y son locales de un desarrollador. Ciclo: crear → trabajar → verificar (`[x]` solo tras verificar funcionando) → promocionar lo duradero (`docs/info/`, `decisiones-tecnicas.md`, tabla de trampas, `docs/future/` o tests) → **borrar**. Ningún archivo versionado puede enlazar a `docs/planning/` ni a `docs/auditorias/`.
7. **Lectura dirigida**: al trabajar en un módulo lee **solo** su `.md`; si tocas frontend añade `DESIGN.md` y `COMPONENTS.md`; si tocas una API de terceros añade `docs/info/apis/<api>.md` (y `docs/apis/<api>/` si existe: `README.md` → `00-fundamentos.md` + `ERRATAS.md` + `LIMITACIONES.md` → solo el dominio necesario).
8. **APIs externas**: nunca configurar nada solo a partir de documentación oficial sin verificarlo con una petición real; lo no comprobado se marca como `⚠️ sin verificar`.

### Índice de `docs/info/`

- [README.md](docs/info/README.md) — Índice maestro de documentación viva.
- [API.md](docs/info/API.md) — Arquitectura general de la API (Sanctum, rate limits, Scribe).
- [Content.md](docs/info/Content.md) — Módulo base de contenidos (chistes, adivinanzas, quiz).
- [Suggestion.md](docs/info/Suggestion.md) — Sistema de sugerencias y aportaciones comunitarias.
- [Taxonomy.md](docs/info/Taxonomy.md) — Clasificación de los contenidos (`Type`, `Group`, `Category`).
- [User.md](docs/info/User.md) — Usuarios, autenticación, roles y estadísticas de aportantes.
- [Collaborator.md](docs/info/Collaborator.md) — Directorio de colaboradores open source y sus proyectos.
- [Report.md](docs/info/Report.md) — Sistema de reportes de contenido.
- [Page.md](docs/info/Page.md) — Gestión CMS de páginas estáticas y legales.
- [commands.md](docs/info/commands.md) — Comandos Artisan, cron y scripts de soporte.
- [Actions.md](docs/info/Actions.md) — Acciones reutilizables y clases de negocio.
- [DESIGN.md](docs/info/DESIGN.md) — Sistema de estilos (Tailwind v4 y CSS modular).
- [COMPONENTS.md](docs/info/COMPONENTS.md) — Layouts, parciales Blade y scripts JS del frontend.
- [decisiones-tecnicas.md](docs/info/decisiones-tecnicas.md) — Decisiones deliberadas de arquitectura.
- [apis/recaptcha.md](docs/info/apis/recaptcha.md) — Integración de Google reCAPTCHA v3.
- [_MODULE_TEMPLATE.md](docs/info/_MODULE_TEMPLATE.md) — Plantilla para nuevos módulos.

## Trampas conocidas

| Área | Trampa | Referencia |
|---|---|---|
| SEO / Rutas aleatorias | `/categoria/{slug}/random` y `/grupo/{slug}/random` devuelven `404` intencionadamente si no hay contenido apto (evita *Soft 404*). | [decisiones-tecnicas.md](docs/info/decisiones-tecnicas.md), [Taxonomy.md](docs/info/Taxonomy.md) |
| Publicación social | `PublishToSocialHelper` rechaza cualquier contenido con `is_adult = true` y apunta a `http://localhost:8080/publish`. | [decisiones-tecnicas.md](docs/info/decisiones-tecnicas.md) |
| reCAPTCHA | Si `config('google.recaptcha.secret_key')` está vacío, `VerifyRecaptcha` deja pasar la petición sin validar. | [apis/recaptcha.md](docs/info/apis/recaptcha.md) |
