# Sistema Electoral

Gestión de resultados electorales: un panel de administración con Filament y
varias páginas públicas de consulta en vivo.

## Stack

| Capa | Versión |
| --- | --- |
| PHP | ^8.3 (desarrollado y probado en 8.5) |
| Laravel | ^13.0 |
| Filament | ^5.0 |
| Livewire | ^4.0 (vía Filament) |
| Livewire Volt | ^1.11 |
| Tailwind CSS | ^4.3, compilado con Vite |
| Vite | ^8.3 |
| bun | gestor de paquetes del frontend |
| SQLite | base de datos por defecto |

## Puesta en marcha local

```bash
composer install
bun install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

php artisan storage:link
```

`php artisan serve` para el backend, `bun run dev` para el frontend con
recarga en caliente.

## Comandos habituales

```bash
bun run build          # compila CSS y JS a public/build
bun run dev            # servidor de Vite con HMR
bun audit              # vulnerabilidades del frontend
composer audit         # vulnerabilidades de PHP

php artisan test       # 38 tests
vendor/bin/pint        # formateador (configurado en pint.json)

php artisan election:simulate --interval=0   # datos de prueba de una eleccion
```

`php artisan election:simulate` reinicia la base de datos, ejecuta los
seeders y simula el conteo de las 49 mesas con un intervalo de segundos entre
mesa y mesa. Sin `--interval=0` tarda unos once minutos.

## Rutas

- `/` — portada
- `/alcaldes`, `/concejales`, `/resultados`, `/talonador` — consulta pública
- `/admin` — panel de Filament
- `/up` — endpoint de salud

## Despliegue

El despliegue se gestiona desde [Ploi](https://ploi.io). Dos advertencias
importantes:

**1. Hay que compilar el frontend.** Desde que se eliminó el CDN de Tailwind,
el CSS se sirve desde `public/build/`, que está en `.gitignore`. Si se despliega
sin compilar, `@vite` falla y el sitio queda sin estilos. El script de
despliegue debe incluir:

```bash
composer install --no-dev --optimize-autoloader
bun install --frozen-lockfile
bun run build
php artisan migrate --force
php artisan filament:upgrade   # solo tras actualizar Filament
```

**2. `APP_DEBUG` debe ser `false` en producción.** `.env.example` trae `true`
porque es la plantilla de desarrollo. Con el debug activo se expone
información interna en la página de error de Laravel.

## Notas de operación

### Contraseña del usuario semilla

`database/seeders/UserSeeder.php` crea `dev@nqu.me` con un hash bcrypt
fijado en el código. Cambiar ese hash cambia la contraseña de ese usuario en
cualquier entorno donde se haya ejecutado `db:seed`, así que conviene
sustituirlo por una cuenta propia antes de usar el seed en producción.

### Base de datos de los tests

`phpunit.xml` fuerza `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:`, así que
la suite nunca toca `database/database.sqlite`. `BCRYPT_ROUNDS` no se fija en
`phpunit.xml` a propósito: los tests usan los mismos 12 rounds que
`.env.example`, y un valor menor hace fallar el `UserSeeder`.

### Modelos excluidos de Pint

`app/Models/` está excluido en `pint.json`. Los docblocks de los modelos los
genera `ide-helper:models --write`, que no emite los separadores ` *` que pide
la regla `phpdoc_separation` de Pint. Sin la exclusión, cada
`composer update` reintroduce las violaciones.

### Archivos generados

`_ide_helper.php` y `.phpstorm.meta.php` están en `.gitignore` y se regeneran
solos con `composer update` (scripts `post-update-cmd`). No hay que editarlos
ni versionarlos.

### Tailwind

`resources/css/app.css` es el entrypoint. Tailwind 4 se configura desde el
propio CSS, así que las rutas que se escanean se declaran ahí con directivas
`@source`. Al añadir vistas nuevas hay que acordarse de que el directorio esté
cubierto, o sus clases no se compilarán.
