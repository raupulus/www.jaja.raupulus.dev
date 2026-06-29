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
        Schema::create('cache', function (Blueprint $table) {
            $table->comment('Tabla de caché manejada por Laravel.');
            $table->string('key')->primary()->comment('Clave de caché');
            $table->mediumText('value')->comment('Valor almacenado');
            $table->integer('expiration')->comment('Expiración de caché');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->comment('Manejo de bloqueos de caché de Laravel.');
            $table->string('key')->primary()->comment('Clave de caché');
            $table->string('owner')->comment('Propietario del bloqueo');
            $table->integer('expiration')->comment('Expiración de caché');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
