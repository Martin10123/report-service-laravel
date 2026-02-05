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
        Schema::create('conteos_a1', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('services')->onDelete('cascade');
            $table->json('areas'); // [{area: 'A1', adultos: 0, ninos: 0}, ...]
            $table->integer('total_adultos')->default(0);
            $table->integer('total_ninos')->default(0);
            $table->integer('total_asistencia')->default(0);
            $table->boolean('completado')->default(false);
            $table->timestamps();

            // Índices
            $table->index('servicio_id');
            $table->index('completado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteos_a1');
    }
};
