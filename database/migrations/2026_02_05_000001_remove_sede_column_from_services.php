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
        Schema::table('services', function (Blueprint $table) {
            // Eliminar todos los índices que dependen de la columna 'sede'
            $table->dropIndex(['sede']); // services_sede_index
            $table->dropIndex(['sede', 'fecha', 'estado']); // services_sede_fecha_estado_index
            $table->dropIndex(['sede', 'numero_servicio', 'deleted_at']); // services_sede_numero_servicio_deleted_at_index
        });
        
        Schema::table('services', function (Blueprint $table) {
            // Eliminar la columna sede
            $table->dropColumn('sede');
        });
        
        Schema::table('services', function (Blueprint $table) {
            // Crear nuevos índices usando sede_id
            $table->index('sede_id'); // Para búsquedas por sede
            $table->index(['sede_id', 'fecha', 'estado']); // Para búsquedas combinadas
            $table->index(['sede_id', 'numero_servicio', 'deleted_at']); // Para unicidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Eliminar índices de sede_id
            $table->dropIndex(['sede_id', 'numero_servicio', 'deleted_at']);
            $table->dropIndex(['sede_id', 'fecha', 'estado']);
            $table->dropIndex(['sede_id']);
        });
        
        Schema::table('services', function (Blueprint $table) {
            // Restaurar columna sede
            $table->string('sede')->nullable()->after('sede_id');
            // Restaurar índices de sede
            $table->index('sede');
            $table->index(['sede', 'fecha', 'estado']);
            $table->index(['sede', 'numero_servicio', 'deleted_at']);
        });
    }
};
