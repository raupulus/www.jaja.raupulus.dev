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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('types');
            $table->string('nick', 255)->nullable();
            $table->string('title', 255);
            $table->text('content');
            $table->string('image_path', 255)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
