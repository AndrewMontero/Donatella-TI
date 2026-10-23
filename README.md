# Donatella TI

Sistema web interno de Grupo Donatella para administrar solicitudes de soporte técnico e inventario de equipos. La aplicación está preparada para ejecutarse localmente y utiliza una interfaz adaptable con modos claro y oscuro.

## Contenido

- [Funciones principales](#funciones-principales)
- [Roles y permisos](#roles-y-permisos)
- [Flujo de tickets](#flujo-de-tickets)
- [Inventario de TI](#inventario-de-ti)
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación local](#instalación-local)
- [Cuenta administradora](#cuenta-administradora)
- [Ejecución](#ejecución)
- [Pruebas y calidad](#pruebas-y-calidad)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Seguridad y datos](#seguridad-y-datos)
- [Solución de problemas](#solución-de-problemas)
- [Próximas etapas](#próximas-etapas)

## Funciones principales

### Autenticación y usuarios

- Registro de cuentas con nombre, correo electrónico y contraseña.
- Inicio de sesión usando el correo electrónico como usuario.
- Cuentas activas o desactivadas.
- Roles de Usuario, Técnico y Administrador.
- El Administrador puede cambiar el rol, estado y contraseña de otros usuarios.
- No existen credenciales administrativas predeterminadas; la cuenta principal se crea mediante un comando seguro.

### Mesa de ayuda

- Creación y consulta de tickets de soporte.
- Categorías administrables por el Administrador.
- Estados simples: Pendiente, En proceso y Hecho.
- Comentarios para dar seguimiento a cada solicitud.
- Archivos adjuntos de hasta 10 MB.
- Solicitud opcional de soporte mediante AnyDesk.
- Cuando se requiere AnyDesk, el usuario indica únicamente la dirección o código; nunca debe compartir la contraseña.
- Búsqueda por número, asunto y descripción.
- Técnicos y Administradores también pueden buscar por solicitante o correo.
- Filtros por estado y categoría.
- Los filtros permanecen activos al cambiar de página o regresar desde una edición.

### Administración

- Panel de control con totales y tickets recientes.
- Administración de usuarios, roles, contraseñas y estado de las cuentas.
- Administración de categorías de tickets.
- Historial de acciones administrativas y cambios de estado.
- Tema claro y oscuro desde el botón de apariencia.

> El sistema no utiliza prioridades, asignación individual de técnicos ni encuestas de satisfacción.

## Roles y permisos

| Función | Usuario | Técnico | Administrador |
| --- | :---: | :---: | :---: |
| Crear tickets | Sí | Sí | Sí |
| Ver tickets propios | Sí | Sí | Sí |
| Ver todos los tickets | No | Sí | Sí |
| Comentar tickets autorizados | Sí | Sí | Sí |
| Cambiar estado de tickets | No | Sí | Sí |
| Ver panel de control | No | Sí | Sí |
| Consultar y editar inventario | No | Sí | Sí |
| Importar y exportar inventario | No | Sí | Sí |
| Administrar usuarios y roles | No | No | Sí |
| Administrar categorías | No | No | Sí |
| Consultar historial administrativo | No | No | Sí |

Los permisos se validan en el servidor. Ocultar una opción en la interfaz no sustituye la autorización de la ruta.

## Flujo de tickets

1. El usuario registra una cuenta e inicia sesión.
2. Crea un ticket con asunto, categoría y descripción.
3. Opcionalmente solicita AnyDesk y adjunta un archivo.
4. El ticket inicia en estado **Pendiente**.
5. Un Técnico o Administrador puede cambiarlo a **En proceso** o **Hecho**.
6. El usuario y el personal de soporte pueden comunicarse mediante comentarios.

Un usuario normal solo puede abrir, comentar y descargar archivos de sus propios tickets. Técnicos y Administradores tienen acceso a todas las solicitudes.

## Inventario de TI

El módulo de inventario está disponible únicamente para Técnicos y Administradores. Permite:

- Registrar y editar celulares, equipos y monitores.
- Guardar colaborador, movimiento, fecha, área o puesto, sociedad, marca opcional, línea empresarial, número celular, estado y archivo de origen.
- Buscar por colaborador o marca.
- Filtrar por tipo, estado y departamento.
- Mantener filtros y página al avanzar, retroceder o editar un registro.
- Descargar una plantilla de Excel compatible.
- Importar archivos `.xlsx`, `.xls` o `.csv` de hasta 10 MB.
- Exportar los resultados filtrados a Excel o PDF.
- Evitar duplicados al importar nuevamente el mismo inventario.

### Formato oficial de importación

La hoja preferida se llama `Inventario unificado`. Puede contener títulos y filas vacías antes de los datos, así como varios bloques dentro de la misma hoja. El importador detecta cada fila de encabezados automáticamente.

Las columnas requeridas, en cualquier bloque, son:

| Columna | Descripción | Ejemplo |
| --- | --- | --- |
| Tipo de activo | Celular, Equipo o Monitor | `Celular` |
| Movimiento | Tipo de registro o movimiento | `Entrega` |
| Fecha | Fecha presentada en el archivo | `Oct-26` |
| Colaborador | Persona responsable | `Ana Rodríguez` |
| Área o puesto | Departamento, área o cargo | `Contabilidad` |
| Sociedad | Empresa relacionada | `Dolcesia Fruit` |
| Línea empresarial | Indica si posee línea | `Sí` |
| Número celular | Número asignado, si corresponde | `8888-8888` |
| Estado | Estado del registro | `Activo` |
| Archivo de origen | Fuente del dato | `Equipos.xlsx` |

El sistema reconoce `Equipo`, `Laptop` o `Computadora` como laptop/equipo; `Celular` o `Teléfono` como celular; y `Monitor` como monitor.

## Tecnologías

### Servidor

- PHP 8.3 o superior.
- Laravel 13.
- Laravel Fortify para autenticación.
- SQLite durante el desarrollo local.
- PhpSpreadsheet para importaciones y exportaciones de Excel.
- Dompdf para reportes PDF.

### Interfaz

- Livewire 4.
- Flux UI.
- Tailwind CSS 4.
- Vite Plus.

### Calidad

- Pest para pruebas automatizadas.
- Laravel Pint para formato de código.
- Larastan/PHPStan para análisis estático.

## Requisitos

- PHP `>= 8.3` con las extensiones habituales de Laravel y las extensiones `pdo_sqlite`, `sqlite3`, `mbstring`, `fileinfo`, `xml`, `zip` y `gd`.
- Composer 2.
- Node.js y npm.
- Git.

Para verificar las herramientas:

```bash
php --version
composer --version
node --version
npm --version
```

## Instalación local

### Instalación automática

Después de clonar el repositorio:

```bash
composer run setup
```

Este comando instala las dependencias, crea `.env`, genera la llave, ejecuta las migraciones y compila los recursos de la interfaz.

### Instalación manual

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

En Linux o macOS reemplace `copy` por:

```bash
cp .env.example .env
```

La configuración local predeterminada usa:

```dotenv
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=America/Costa_Rica
APP_LOCALE=es
DB_CONNECTION=sqlite
```

El archivo SQLite se encuentra en `database/database.sqlite`.

## Cuenta administradora

Para crear la cuenta principal:

```bash
php artisan app:create-admin correo@grupodonatella.com --name="Nombre del administrador"
```

- Si el correo todavía no existe, el comando solicita y confirma una contraseña de al menos 8 caracteres.
- Si la cuenta ya existe, conserva su contraseña, la activa y cambia su rol a Administrador.
- La contraseña se solicita de forma privada y no aparece en la terminal.

Los demás usuarios pueden registrarse desde `/register`. Todas las cuentas nuevas reciben inicialmente el rol Usuario.

## Ejecución

Para ejecutar todos los procesos de desarrollo:

```bash
composer run dev
```

Para levantar únicamente Laravel:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Abra [http://127.0.0.1:8000](http://127.0.0.1:8000).

Si modificó estilos o JavaScript, ejecute en otra terminal:

```bash
npm run dev
```

Para generar los recursos optimizados:

```bash
npm run build
```

## Pruebas y calidad

Ejecutar las pruebas:

```bash
php artisan test --compact
```

Ejecutar solamente las pruebas del inventario:

```bash
php artisan test --compact --filter=EquipmentInventoryTest
```

Revisar formato sin modificar archivos:

```bash
composer run lint:check
```

Aplicar formato:

```bash
composer run lint
```

Ejecutar análisis estático:

```bash
composer run types:check
```

Ejecutar todas las validaciones configuradas por el proyecto:

```bash
composer test
```

## Estructura del proyecto

```text
app/
├── Console/Commands/       Comandos administrativos
├── Enums/                  Roles y estados del sistema
├── Http/Controllers/       Lógica de tickets, inventario y administración
├── Http/Middleware/        Control de cuentas, soporte y administración
└── Models/                 Modelos de datos
database/
├── factories/              Datos para pruebas
├── migrations/             Estructura de la base de datos
└── seeders/                Datos iniciales opcionales
resources/
├── css/app.css             Tema y paleta de Grupo Donatella
└── views/                  Pantallas Blade y componentes
routes/
├── web.php                 Rutas principales
└── settings.php            Perfil y apariencia
tests/Feature/              Pruebas funcionales
```

## Seguridad y datos

- Las contraseñas se almacenan mediante el sistema de hashing de Laravel.
- El Administrador no puede cambiar accidentalmente su propio rol o desactivar su propia cuenta desde el panel.
- Las rutas administrativas requieren rol Administrador.
- Las rutas de inventario requieren rol Técnico o Administrador.
- Las descargas de adjuntos vuelven a comprobar el acceso al ticket.
- Los adjuntos se guardan en el disco local privado, no en una carpeta pública.
- El historial registra cambios importantes junto con el usuario y la dirección IP.
- `.env` y la base de datos local no deben publicarse en Git.

Antes de utilizar el sistema en un servidor real:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

También deben configurarse HTTPS, copias de seguridad, correo, almacenamiento persistente y una base de datos de producción.

## Solución de problemas

### Error al subir un archivo temporal en Windows

Si PHP muestra `unable to create a temporary file`, cree una carpeta escribible:

```powershell
New-Item -ItemType Directory -Force storage\framework\uploads
```

Luego configure en el `php.ini` utilizado por la terminal:

```ini
upload_tmp_dir="C:\ruta\al\proyecto\storage\framework\uploads"
```

Localice el archivo de configuración activo con:

```bash
php --ini
```

Reinicie el servidor después del cambio.

### Cambios de interfaz que no aparecen

```bash
npm run build
php artisan optimize:clear
```

Después actualice el navegador con `Ctrl + F5`.

### Error de base de datos o tablas inexistentes

```bash
php artisan migrate
```

Para consultar el estado:

```bash
php artisan migrate:status
```

### El puerto 8000 está ocupado

Utilice otro puerto:

```bash
php artisan serve --port=8001
```

### La importación no acepta el Excel

- Verifique que el archivo no supere 10 MB.
- Confirme que contiene las 10 columnas del formato oficial.
- Descargue la plantilla desde el módulo Inventario y compare los encabezados.
- No es necesario eliminar títulos, filas vacías o repetir encabezados entre bloques.

## Próximas etapas

- Migrar de SQLite a PostgreSQL para producción.
- Configurar servidor web, HTTPS y dominio interno.
- Automatizar copias de seguridad de la base de datos y adjuntos.
- Configurar correo para recuperación de acceso y notificaciones.
- Definir monitoreo, registro de errores y política de retención.

## Licencia y uso

Proyecto interno de Grupo Donatella. Antes de publicarlo o distribuirlo, defina formalmente la licencia y las políticas de uso de la organización.
