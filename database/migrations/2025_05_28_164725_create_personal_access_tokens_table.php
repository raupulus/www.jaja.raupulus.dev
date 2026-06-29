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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->comment('Tokens de acceso de Laravel Sanctum para la API.');
            $table->id()->comment('Identificador único');
            $table->morphs('tokenable');
            $table->string('name')->comment('Nombre');
            $table->string('token', 64)->unique()->comment('Cadena del token');
            $table->text('abilities')->nullable()->comment('Permisos o habilidades del token');
            $table->timestamp('last_used_at')->nullable()->comment('Fecha del último uso');
            $table->timestamp('expires_at')->nullable()->comment('Fecha de expiración');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
