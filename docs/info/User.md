# Módulo de Usuarios (User)

## Descripción General
El módulo `User` gestiona la autenticación, roles básicos y perfiles públicos (uploaders) dentro de la aplicación. Es la base para acceder a los paneles privados (tanto admin como colaboradores/usuarios) y provee autenticación para los endpoints protegidos de la API.

## Componentes y Modelos
- **User**: Representa a un individuo registrado en el sistema.

## Funcionalidad Técnica
1. **Autenticación**:
   - Gestionada a través de Laravel Sanctum para la emisión y verificación de tokens en la API.
   - Existen dos puntos de entrada web: `/admin/login` para administradores y `/panel/login` (o `/login`) para usuarios generales.
2. **Sistema de Roles**:
   - El modelo carece de un paquete complejo de roles y permisos (como Spatie) por simplicidad de negocio.
   - Se gestiona mediante el campo `role_id`. Por convención, el `role_id = 1` otorga privilegios totales y acceso al panel de administración `/admin`. Los nuevos usuarios creados (`php artisan make:filament-user`) adquieren el `role_id = 2` por defecto, restringiendo su acceso.
3. **Perfiles y Estadísticas (Uploaders)**:
   - Los usuarios que aportan contenido se vinculan mediante el campo "nick" de las sugerencias.
   - Tienen estadísticas asociadas, las cuales son calculadas periódicamente por comandos en el cron. Estos cálculos excluyen el contenido eliminado y el asociado al modelo `Report`.
   - Aparecen listados dinámicamente en la página de agradecimientos si han aportado gran cantidad de contenido.
4. **Relaciones**:
   - Tienen relación con las tablas de tokens de acceso personal de Sanctum.
   - Poseen relación implícita con `Content` para calcular las estadísticas y aportes.
