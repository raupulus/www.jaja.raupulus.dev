<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')
                ->nullable()
                ->index()
                ->constrained('types')
                ->onDelete('SET NULL')
                ->onUpdate('CASCADE');
            $table->string('title', 255);
            $table->string('slug', 255)->unique()->index();
            $table->string('description', 255)->index();
            $table->string('image', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('groups');
    }
};
