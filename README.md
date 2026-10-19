# Donatella TI

Sistema web de mesa de ayuda para gestionar los tickets de soporte de Grupo Donatella.

## Funciones actuales

- Registro e inicio de sesión con correo electrónico.
- Roles de Usuario, Técnico y Administrador.
- Panel administrativo para cambiar roles, activar cuentas y cambiar contraseñas.
- Creación y seguimiento de tickets sin prioridades ni asignación individual.
- Estados: Nuevo, En proceso, En espera, Resuelto y Cerrado.
- Solicitud opcional de soporte por AnyDesk.
- Comentarios y archivos adjuntos.
- Panel de control con resumen de tickets.
- Búsqueda y filtros de tickets por estado y categoría.
- Categorías administrables e historial de cambios importantes.

## Tecnologías

- PHP 8.5 y Laravel 13.
- Livewire 4, Flux UI y Tailwind CSS.
- SQLite para el desarrollo local.
- PostgreSQL para la siguiente etapa.
- Pest para pruebas automatizadas.

## Preparación local

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Para crear la cuenta administradora principal:

```bash
php artisan app:create-admin correo@grupodonatella.com --name="Nombre del administrador"
```

Si la cuenta ya fue registrada, el comando conservará su contraseña y únicamente la promoverá a Administrador. Si todavía no existe, solicitará una contraseña de manera privada.

Para iniciar el entorno local:

```bash
composer run dev
```

## Pruebas

```bash
php artisan test
```
