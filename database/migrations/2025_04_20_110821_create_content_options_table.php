<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('content_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')
                ->constrained('contents')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->string('title', 255);
            $table->string('value', 255);
            $table->boolean('is_correct')->default(false);
            $table->string('image', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('content_options');
    }
};
