<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suggestion_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')
                ->constrained('suggestions')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->string('value', 255)->comment('Contenido de esta opción');
            $table->boolean('is_correct')->default(false)->comment('Es la respuesta correcta');
            $table->string('image_path', 255)->nullable()->comment('Ruta de la imagen');
            $table->smallInteger('order')->default(1)->comment('Posición en la lista');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suggestion_options');
    }
};
