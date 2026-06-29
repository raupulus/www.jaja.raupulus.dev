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
        Schema::create('reports', function (Blueprint $table) {
            $table->comment('Reportes de contenido inapropiado o erróneo creados por usuarios.');
            $table->id()->comment('Identificador único');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('ID del usuario asociado');
            $table->string('reporter_name')->nullable(); // Para reportes anónimos
            $table->string('reporter_email')->nullable(); // Para reportes anónimos
            $table->string('reporter_ip')->nullable()->comment('IP del reportador');

            // Contenido reportado - polimórfico
            $table->morphs('reportable'); // Esto ya crea el índice automáticamente

            $table->enum('type', [
                'spam',
                'inappropriate_content',
                'adult_content',
                'hate_speech',
                'copyright',
                'violence',
                'harassment',
                'misinformation',
                'other'
            ])->default('other');

            $table->string('title')->nullable()->comment('Título');
            $table->text('description')->comment('Descripción o texto explicativo');
            $table->text('additional_info')->nullable();

            $table->enum('status', [
                'pending',
                'reviewing',
                'resolved',
                'rejected',
                'closed'
            ])->default('pending');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'critical'
            ])->default('medium');

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->comment('ID del admin asignado');
            $table->text('admin_notes')->nullable()->comment('Notas internas del administrador');
            $table->timestamp('resolved_at')->nullable()->comment('Fecha de resolución del reporte');
            $table->timestamps();

            // Solo añadir los índices que NO se crean automáticamente
            $table->index(['status', 'priority']);
            $table->index('created_at')->comment('Fecha de creación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
