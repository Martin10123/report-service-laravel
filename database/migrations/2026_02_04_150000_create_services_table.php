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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('sede');
            $table->integer('numero_servicio');
            $table->date('fecha');
            $table->string('dia_semana');
            $table->time('hora');
            $table->enum('estado', ['activo', 'finalizado', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización de consultas
            $table->index('sede'); // Filtro por sede
            $table->index('fecha'); // Ordenamiento y filtro por fecha
            $table->index('estado'); // Filtro por estado
            $table->index('numero_servicio'); // Búsqueda por número
            
            // Índices compuestos para consultas frecuentes
            $table->index(['sede', 'fecha', 'estado']); // Filtro combinado más común
            $table->index(['fecha', 'hora']); // Ordenamiento principal
            $table->index(['estado', 'fecha']); // Listar servicios por estado y fecha
            $table->index(['sede', 'numero_servicio', 'deleted_at']); // Generación de número único
            
            // Índice para soft deletes
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
