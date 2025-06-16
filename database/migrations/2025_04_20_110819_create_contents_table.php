<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
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
            $table->string('title', 255);
            $table->text('content');
            $table->string('image', 255)->nullable();
            $table->string('uploaded_by', 255)
                ->index()
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contents');
    }
};
