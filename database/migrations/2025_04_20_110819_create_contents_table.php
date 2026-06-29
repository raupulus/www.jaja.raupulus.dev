<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->comment('Tabla principal de contenidos aprobados de la plataforma.');
            $table->id()->comment('Identificador único');
            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->onDelete('SET NULL')
                ->onUpdate('CASCADE');
            $table->foreignId('group_id')
                ->nullable()
                ->index()
                ->constrained('groups')
                ->onDelete('SET NULL')
                ->onUpdate('CASCADE');
            $table->string('title', 255)->comment('Título');
            $table->text('content')->comment('Contenido principal');
            $table->string('image', 255)->nullable()->comment('Ruta o nombre de la imagen');
            $table->string('uploaded_by', 255)
                ->index()
                ->nullable();
            $table->boolean('is_adult')->default(false);
            $table->boolean('is_ai')->default(false);
            $table->timestamp('last_social_published')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contents');
    }
};
