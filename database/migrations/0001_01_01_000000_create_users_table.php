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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Usuarios registrados y administradores de la plataforma.');
            $table->id()->comment('Identificador único');
            $table->integer('role_id')->default(2);
            $table->string('name', 255)->comment('Nombre');
            $table->string('nick', 50)->nullable()->comment('Alias del creador');
            $table->string('email')->unique()->comment('Correo electrónico');
            $table->string('avatar', 255)->default('images/default/avatar.webp')->nullable();
            $table->timestamp('email_verified_at')->nullable()->comment('Fecha de verificación del correo');
            $table->string('password', 255)->comment('Contraseña encriptada');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->comment('Tokens de reseteo de contraseñas.');
            $table->string('email')->primary()->comment('Correo electrónico');
            $table->string('token')->comment('Cadena del token');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->comment('Sesiones de usuario (para driver database).');
            $table->string('id')->primary()->comment('Identificador único');
            $table->foreignId('user_id')->nullable()->index()->comment('ID del usuario asociado');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload')->comment('Datos del trabajo');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
