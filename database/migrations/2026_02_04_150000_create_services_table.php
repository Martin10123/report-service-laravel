<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Relación con sedes
            $table->foreignId('sede_id')
                ->constrained('sedes')
                ->cascadeOnDelete();

            // Datos del servicio
            $table->integer('numero_servicio')->nullable(); // nullable desde el inicio
            $table->date('fecha');
            $table->string('dia_semana');
            $table->time('hora');

            $table->enum('estado', ['activo', 'finalizado', 'cancelado'])
                ->default('activo');

            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('sede_id');
            $table->index('fecha');
            $table->index('estado');
            $table->index('numero_servicio');

            // Índices compuestos (consultas frecuentes)
            $table->index(['sede_id', 'fecha', 'estado']);
            $table->index(['fecha', 'hora']);
            $table->index(['estado', 'fecha']);
            $table->index(['sede_id', 'numero_servicio', 'deleted_at']);

            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
