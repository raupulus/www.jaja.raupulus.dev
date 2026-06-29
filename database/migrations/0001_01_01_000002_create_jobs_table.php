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
        Schema::create('jobs', function (Blueprint $table) {
            $table->comment('Trabajos en segundo plano (colas).');
            $table->id()->comment('Identificador único');
            $table->string('queue')->index()->comment('Cola de ejecución');
            $table->longText('payload')->comment('Datos del trabajo');
            $table->unsignedTinyInteger('attempts')->comment('Intentos realizados');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Reservado en');
            $table->unsignedInteger('available_at')->comment('Disponible en');
            $table->unsignedInteger('created_at')->comment('Fecha de creación');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->comment('Lotes de trabajos en segundo plano.');
            $table->string('id')->primary()->comment('Identificador único');
            $table->string('name')->comment('Nombre');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at')->comment('Fecha de creación');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->comment('Trabajos fallidos.');
            $table->id()->comment('Identificador único');
            $table->string('uuid')->unique()->comment('Identificador UUID');
            $table->text('connection')->comment('Conexión usada');
            $table->text('queue')->comment('Cola de ejecución');
            $table->longText('payload')->comment('Datos del trabajo');
            $table->longText('exception')->comment('Excepción lanzada');
            $table->timestamp('failed_at')->useCurrent()->comment('Fecha de fallo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
