<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * El panel de Filament exige que canAccessPanel() devuelva true, y hasta
     * ahora esa comprobacion no miraba nada, con lo que cualquier cuenta
     * autenticada tenia acceso completo al panel. Esta columna introduce el
     * control de acceso minimo.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Las cuentas existentes se marcan como administradoras para que nadie
        // pierda el acceso al panel al desplegar esta migracion: hasta ahora
        // todas lo tenian.
        DB::table('users')->update(['is_admin' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
