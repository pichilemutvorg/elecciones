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

### Con Lerd (recomendado)

[Lerd](https://github.com/taylorotwell/lerd) levanta el sitio con nginx y
PHP-FPM reales, así que lo que se ve en el navegador es lo que se despliega.
El entorno está versionado en `.lerd.yaml` y `.php-version`, de modo que
cualquiera que clone el proyecto obtiene el mismo stack.

```bash
lerd install          # una sola vez por maquina
lerd link elecciones  # registra el sitio en elecciones.test
lerd setup            # dependencias, .env, migraciones, assets y HTTPS
```

`lerd setup` es interactivo: pregunta versión de PHP, HTTPS y servicios. En
este proyecto la respuesta es PHP 8.5, sin servicios externos (SQLite) y
HTTPS activado.

Para levantar un sitio ya configurado:

```bash
lerd start            # DNS, nginx y PHP-FPM
lerd open             # abre https://elecciones.test
```

Comandos útiles:

```bash
lerd status           # salud del entorno
lerd sites            # sitios registrados
lerd site:doctor      # diagnostico de la aplicacion
lerd logs             # logs de PHP-FPM y nginx
lerd test             # php artisan test en el contenedor
lerd console          # tinker
lerd db:shell         # consola de base de datos
```

`lerd site:doctor` comprueba `.env`, claves, enlace de storage, migraciones,
dependencias, auditorias y tiempo de respuesta. Es la forma rápida de detectar
que algo se rompio tras un `composer update`.

El runtime de JavaScript está fijado a **bun** en `.lerd.yaml`, coherente con
`bun.lock`. Si Lerd cayera en Node crearía un `package-lock.json` que
introduciría exactamente la deriva de lock que ya se corrigió.

### Sin Lerd

```bash
composer install
bun install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

php artisan storage:link
```

`php artisan serve` para el backend y `bun run dev` para el frontend con
recarga en caliente. En este modo las rutas se sirven por `http://localhost:8000`
y los assets los resuelve Vite en desarrollo, no `public/build`.

## Comandos habituales

```bash
bun run build          # compila CSS y JS a public/build
bun run dev            # servidor de Vite con HMR
bun audit              # vulnerabilidades del frontend
composer audit         # vulnerabilidades de PHP

php artisan test       # 56 tests
vendor/bin/pint        # formateador (configurado en pint.json)

php artisan election:simulate --interval=0   # datos de prueba de una eleccion
```

Con Lerd, `lerd test` y `lerd console` ejecutan lo mismo dentro del contenedor,
que es el entorno real de desarrollo.

`php artisan election:simulate` reinicia la base de datos, ejecuta los
seeders y simula el conteo de las 49 mesas con un intervalo de segundos entre
mesa y mesa. Sin `--interval=0` tarda unos once minutos.

## Rutas

- `/` — portada
- `/alcaldes`, `/concejales`, `/resultados`, `/talonador` — consulta pública
- `/admin` — panel de Filament
- `/up` — endpoint de salud

## Requisitos

- PHP >= 8.3 con `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`,
  `json`, `fileinfo` y el driver de base de datos que se use
- [bun](https://bun.sh) para el frontend. El proyecto usa `bun.lock` y no
  `package-lock.json` a propósito: mezclar gestores de paquetes es lo que
  dejó el lock desactualizado en el pasado
- Una base de datos SQLite, MySQL o PostgreSQL

## Despliegue

El proyecto no está atado a ningún panel de control. `bin/deploy.sh` encadena
los pasos para cualquier host que cumpla los requisitos:

```bash
./bin/deploy.sh            # despliegue normal
./bin/deploy.sh --seed     # además ejecuta los seeders
./bin/deploy.sh --help
```

El script pone la aplicación en mantenimiento, instala dependencias de PHP y
del frontend, compila, limpia cachés, migra, vuelve a publicar los assets de
Filament y la vuelve a levantar. Si algo falla, la aplicación se restaura
igual mediante un `trap`.

Dos puntos que no son opcionales:

**Hay que compilar el frontend.** Desde que se eliminó el CDN de Tailwind, el
CSS se sirve desde `public/build/`, que está en `.gitignore`. Un despliegue
que no ejecute `bun install && bun run build` deja el sitio sin estilos y con
`@vite` fallando. El script aborta si tras el build falta el manifiesto.

**`APP_DEBUG` debe ser `false` en producción.** `.env.example` trae `true`
porque es la plantilla de desarrollo. Con el debug activo se expone
información interna en la página de error de Laravel.

Si prefieres orquestar los pasos a mano, el equivalente al script sin la
protección del modo de mantenimiento es:

```bash
composer install --no-dev --optimize-autoloader
bun install --frozen-lockfile
bun run build
php artisan optimize:clear
php artisan migrate --force
php artisan filament:upgrade
```

## Acceso al panel

El panel está en `/admin` y solo lo alcanzan las cuentas con `is_admin` a
`true`. Ese campo no es asignable en masa, de modo que ningún formulario
puede concederse a sí mismo el acceso; se cambia con
`forceFill(['is_admin' => true])->save()`.

La autorización sobre las cuentas vive en `App\Policies\UserPolicy`.

Filament limita el login a cinco intentos y, cuando el usuario no tiene
acceso, responde con el mismo mensaje que ante una contraseña incorrecta, de
modo que no se revela que la cuenta existe.

### La cuenta de administración

No hay ninguna credencial en el repositorio. `Database\Seeders\UserSeeder` la
construye desde el entorno:

| Variable | Por defecto | Descripción |
| --- | --- | --- |
| `ADMIN_EMAIL` | `admin@example.org` | Correo de la cuenta |
| `ADMIN_NAME` | `Administrador` | Nombre visible |
| `ADMIN_PASSWORD` | — | Si falta, se genera una aleatoria de 32 caracteres y se imprime **una sola vez** |

Conviene fijar `ADMIN_EMAIL` y `ADMIN_PASSWORD` en el `.env` del servidor
antes de sembrar, para no depender de la salida por consola.

Para promover una cuenta existente sin tocar la base de datos a mano:

```bash
php artisan tinker
>>> $u = App\Models\User::where('email', 'alguien@example.org')->first();
>>> $u->forceFill(['is_admin' => true])->save();
```

## Notas de operación

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
