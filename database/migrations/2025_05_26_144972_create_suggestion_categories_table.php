<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suggestion_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->foreignId('suggestion_id')
                ->constrained('suggestions')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suggestion_categories');
    }
};
