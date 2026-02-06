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
        Schema::create('conteos_sobres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')
                ->constrained('services')
                ->onDelete('cascade');
            
            // Canastas
            $table->json('canastas'); // {entregadas: int}
            
            // Sobres
            $table->json('ofrendas'); // {inicial, recibidos, entregados}
            $table->json('protemplo'); // {inicial, recibidos, entregados}
            $table->json('iglekids'); // {inicial, recibidos, entregados}
            
            // Totales calculados
            $table->integer('total_ofrendas')->default(0);
            $table->integer('final_ofrendas')->default(0);
            $table->integer('total_protemplo')->default(0);
            $table->integer('final_protemplo')->default(0);
            $table->integer('total_iglekids')->default(0);
            $table->integer('final_iglekids')->default(0);
            
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
        Schema::dropIfExists('conteos_sobres');
    }
};
