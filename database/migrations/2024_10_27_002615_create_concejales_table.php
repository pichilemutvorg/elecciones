<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('concejales', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->boolean('is_independent')->nullable();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('partido_id')->nullable();
            $table->foreignId('pacto_id')->nullable();
            $table->foreignId('subpacto_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concejales');
    }
};
