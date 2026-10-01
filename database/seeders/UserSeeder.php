<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Crea la cuenta de administracion inicial.
     *
     * El repositorio es publico, asi que ninguna credencial puede estar escrita
     * aqui: un hash bcrypt versionado es una credencial published. La cuenta se
     * construye a partir del entorno.
     *
     * Variables de entorno opcionales:
     *   ADMIN_EMAIL     correo de la cuenta (por defecto admin@example.com)
     *   ADMIN_PASSWORD  contrasena; si falta, se genera una aleatoria de 32
     *                   caracteres y se imprime una unica vez
     *   ADMIN_NAME      nombre visible (por defecto Administrador)
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        if (User::where('email', $email)->exists()) {
            $this->command?->warn("El usuario {$email} ya existe, no se crea de nuevo.");

            return;
        }

        $password = env('ADMIN_PASSWORD');

        if (blank($password)) {
            $password = Str::password(32);
            $generated = true;
        } else {
            $generated = false;
        }

        $user = User::create([
            'name' => env('ADMIN_NAME', 'Administrador'),
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // is_admin no es asignable en masa a proposito, asi que se fija
        // explicitamente en lugar de colarlo en el create().
        $user->forceFill(['is_admin' => true])->save();

        $this->command?->info("Usuario administrador creado: {$email}");

        if ($generated) {
            $this->command?->newLine();
            $this->command?->warn("  Contrasena generada: {$password}");
            $this->command?->warn('  Guardala ahora: no se vuelve a mostrar. Para fijarla,');
            $this->command?->warn('  define ADMIN_PASSWORD en el entorno antes de sembrar.');
            $this->command?->newLine();
        }
    }
}
