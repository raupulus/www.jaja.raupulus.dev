# Módulo de Reportes (Report)

## Qué hace y qué NO hace
- **Qué hace**: Permite a clientes autenticados en la API REST notificar contenido inapropiado, ofensivo o erróneo (`Content`), asigna prioridades/estados y centraliza la revisión y resolución en `/admin/reports`.
- **Qué NO hace**: No expone un formulario web público sin autenticación ni oculta o elimina automáticamente contenidos sin intervención de un administrador.

## Modelo de datos
- **`App\Models\Report`** (tabla `reports`, con `HasFactory`):
  - Campos clave: `user_id`, `reporter_name`, `reporter_email`, `reporter_ip`, `reportable_type`, `reportable_id`, `type` (`spam`, `inappropriate_content`, `adult_content`, `hate_speech`, `copyright`, `violence`, `harassment`, `misinformation`, `other`), `title`, `description`, `additional_info`, `status` (`pending`, `reviewing`, `resolved`, `rejected`, `closed`), `priority` (`low`, `medium`, `high`, `critical`), `assigned_to`, `admin_notes`, `resolved_at`.
  - Relaciones reales: `morphTo('reportable')` (apuntando actualmente a `Content`), `belongsTo(User)` (`user`) y `belongsTo(User, 'assigned_to')` (`assignedTo`).
  - Scopes: `scopePending()`, `scopeHighPriority()`, `scopeCritical()`.
  - Métodos de estado: `markAsResolved(?string $adminNotes)`, `markAsRejected(?string $adminNotes)`.
  - Accessors: `reporter_name` y `reporter_email` hacen fallback al usuario relacionado (`$this->user?->name` / `$this->user?->email`) o `'Anónimo'` / `'No disponible'`.
  - Índices adicionales: `reports_user_id_index`, `reports_assigned_to_index` y compuesto `['status', 'priority']`.

## Flujos principales
1. **Envío vía API (`V1Controller::sendReport`)**:
   - Valida `content_id` y campos del reporte mediante `Api\SendReportRequest`.
   - Si `description` viene omitido o nulo, lo normaliza a `''` para satisfacer la restricción `NOT NULL` en base de datos sin alterar contratos existentes.
   - Crea el registro en estado `pending` y prioridad `medium` registrando IP real (`IpHelper::getClientIp()`).
2. **Moderación en Filament (`/admin/reports`)**:
   - `Admin\ReportResource` (grupo `'Moderación'`, `$recordTitleAttribute = 'title'`) precarga `user` y `assignedTo` en `getEloquentQuery()` para evitar violaciones de `preventLazyLoading()`.
   - Permite filtrar por `status`, `type`, `priority`, `reportable_type` y resolución, y muestra contador en el badge de navegación (`(string)` de reportes pendientes) con color según criticidad (`danger` si hay críticos, `warning` si hay pendientes).

## Puntos de entrada
- **API v1**: `POST /api/v1/report/send` (`auth:sanctum`, `throttle.real.ip:10,1`).
- **Filament (`/admin`)**: `Admin\ReportResource` protegido por `ReportPolicy` / `AdminOnlyPolicy`.

## Dependencias
- **Depende de**: `Content` (modelo reportable), `User`, `Api\SendReportRequest`, `IpHelper`, `ThrottleWithRealIp`, `ReportPolicy`.
- **Usado por**: `Admin\ReportResource`.

## Configuración
- Límite de tasa API: 10 peticiones por minuto por IP real (`throttle.real.ip:10,1`).

## Trampas conocidas
- El accessor `getReporterNameAttribute()` accede a `$this->user` si `reporter_name` es `null`; cualquier listado en Filament debe precargar `with(['user', 'assignedTo'])` en `getEloquentQuery()` para no disparar `LazyLoadingViolationException`.
- La columna `reports.description` es `NOT NULL` en BD; `V1Controller::sendReport()` convierte `null` a `''`.

## Tests que lo cubren
- `tests/Feature/ApiAndValidationStabilityTest.php`
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/DatabaseIndexesAndSoftDeletesTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`

## Pendiente real
- Evaluar un futuro formulario público web de reporte únicamente si se requiere fuera del flujo autenticado por API.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
