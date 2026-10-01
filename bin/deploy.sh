#!/usr/bin/env bash
#
# Despliegue de la aplicacion en cualquier host que soporte el stack.
#
# No depende de ningun panel de control concreto: solo de PHP y Node con el
# gestor de paquetes que use el proyecto (bun). Ademas del codigo, el
# despliegue tiene que compilar el frontend, porque public/build esta en
# .gitignore y las vistas cargan sus estilos a traves de @vite.
#
# Uso:
#   ./bin/deploy.sh              # despliegue normal
#   ./bin/deploy.sh --seed       # ademas ejecuta los seeders
#
# Requisitos: PHP >= 8.3 con las extensiones de composer.json, bun y un .env
# configurado en el servidor.

set -euo pipefail

cd "$(dirname "$0")/.."

log()  { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
warn() { printf '\033[1;33maviso: %s\033[0m\n' "$*"; }
die()  { printf '\033[1;31merror: %s\033[0m\n' "$*" >&2; exit 1; }

RUN_SEEDERS=false
for arg in "$@"; do
    case "$arg" in
        --seed) RUN_SEEDERS=true ;;
        -h|--help)
            awk 'NR > 1 { if (/^#/) { sub(/^# ?/, ""); print } else { exit } }' "$0"
            exit 0
            ;;
        *) die "Opcion desconocida: $arg" ;;
    esac
done

[ -f .env ] || die 'No existe .env. Copia .env.example y ajustalo antes de desplegar.'

command -v php >/dev/null || die 'PHP no esta instalado o no esta en el PATH.'
command -v bun >/dev/null || die 'bun no esta instalado. Ver README.md, seccion Requisitos.'

log 'Comprobando la version minima de PHP'
php -r 'exit(version_compare(PHP_VERSION, "8.3", ">=") ? 0 : 1);' \
    || die "Se requiere PHP 8.3 o superior; hay $(php -r 'echo PHP_VERSION;')."

# Entorno de mantenimiento durante el despliegue: evita que una peticion a
# mitad de la migracion vea un estado inconsistente. Laravel ya sabe escribir el
# archivo y lo borra solo cuando la aplicacion vuelve a estar disponible.
log 'Activando el modo de mantenimiento'
php artisan down || warn 'No se pudo activar el modo de mantenimiento.'

cleanup() {
    log 'Restaurando la aplicacion'
    php artisan up || warn 'La aplicacion sigue en mantenimiento; ejecuta: php artisan up'
}
trap cleanup EXIT

log 'Instalando dependencias de PHP'
composer install --no-dev --optimize-autoloader --no-interaction

log 'Instalando dependencias del frontend'
bun install --frozen-lockfile

log 'Compilando el frontend'
bun run build

if [ ! -f public/build/manifest.json ]; then
    die 'public/build/manifest.json no existe tras el build. Las vistas usarán @vite y fallarán.'
fi

log 'Limpiando caches'
php artisan optimize:clear

log 'Ejecutando migraciones'
php artisan migrate --force

if [ "$RUN_SEEDERS" = true ]; then
    log 'Ejecutando seeders'
    warn 'ADMIN_EMAIL y ADMIN_PASSWORD recommended: sin ADMIN_PASSWORD se genera una contrasena'
    warn 'aleatoria que se imprime una sola vez por consola.'
    php artisan db:seed --force
fi

log 'Volviendo a compilar los assets de Filament'
php artisan filament:upgrade

log 'Despliegue completado'
