<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->comment('Taxonomía: Grupos demográficos o temáticos (adultos, general).');
            $table->id()->comment('Identificador único');
            $table->foreignId('type_id')
                ->nullable()
                ->index()
                ->constrained('types')
                ->onDelete('SET NULL')
                ->onUpdate('CASCADE');
            $table->string('title', 255)->comment('Título');
            $table->string('slug', 255)->unique()->index()->comment('Slug URL-friendly');
            $table->string('description', 255)->index()->comment('Descripción o texto explicativo');
            $table->string('image', 255)->nullable()->comment('Ruta o nombre de la imagen');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('groups');
    }
};
