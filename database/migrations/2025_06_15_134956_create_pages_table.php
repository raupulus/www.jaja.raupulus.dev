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
        Schema::create('pages', function (Blueprint $table) {
            $table->comment('Páginas estáticas y legales del CMS integrado.');
            $table->id()->comment('Identificador único');
            $table->string('title', 255)->comment('Título');
            $table->string('slug', 255)->unique()->index()->comment('Slug URL-friendly');
            $table->string('excerpt', 255)->comment('Resumen');
            $table->text('content')->comment('Contenido principal');
            $table->string('image', 255)->nullable()->comment('Ruta o nombre de la imagen');
            $table->string('keywords', 255)->nullable()->comment('Palabras clave para SEO');
            $table->enum('status', ['draft', 'published'])->default('draft')->comment('Estado del registro');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
