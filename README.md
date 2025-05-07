# jaja.raupulus.dev

Proyecto Laravel con backend Filament y api para gestionar chistes y adivinanzas que consumir externamente mediante bots.

## Iniciar

### Ejecutar migraciones para generar la base de datos

```bash
php artisan migrate:refresh
```

### Crear tantos usuarios como necesitemos

```bash
php artisan make:filament-user
```

### Ejecutar seeders para añadir datos de ejemplo a la base de datos

```bash
php artisan db:seed
```

## Rutas web

Panel: /admin
Login: /admin/login
