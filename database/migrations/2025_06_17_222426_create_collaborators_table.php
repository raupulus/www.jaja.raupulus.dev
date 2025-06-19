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
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('SET NULL')
                ->onUpdate('CASCADE');
            $table->string('name', 255)->comment('Nombre del colaborador');
            $table->string('nick', 50)->comment('Nick del colaborador');
            $table->string('website', 255)->comment('Sitio web')->nullable();
            $table->string('url_repositories', 255)->comment('Enlace al perfil de repositorios Personal (Github, Gitlab...')->nullable();
            $table->string('image', 255)
                ->nullable()
                ->comment('Imagen del colaborador por ejemplo su logo');
            $table->string('description', 255)
                ->comment('Un pequeño resumen de su biografía')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
