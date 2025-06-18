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
        Schema::create('collaborator_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborator_id')
                ->comment('Colaborador al que pertenece el proyecto')
                ->constrained('collaborators')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');

            $table->string('title', 255)->comment('Título del proyecto');
            $table->string('slug', 255)->comment('Slug para url')->unique()->index();
            $table->string('url', 255)->comment('URL del proyecto')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('excerpt', 255)->comment('Resumen del proyecto');
            $table->text('content')->comment('Contenido del proyecto');
            $table->enum('type', ['web', 'mobile', 'desktop', 'bot', 'marketing', 'other'])
                ->comment('Tipo de proyecto')
                ->default('other');
            $table->enum('repository_type', ['github', 'gitlab', 'bitbucket', 'other'])
                ->comment('Tipo de repositorio')
                ->default('other');
            $table->string('keywords', 255)
                ->comment('Palabras clave para el SEO')
                ->nullable();
            $table->enum('status', ['draft', 'published'])
                ->comment('Estado del proyecto, puede ser publicado o borrador (draft|published)')
                ->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborator_projects');
    }
};
