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
            // Agregar la columna sede_id como foreign key
            $table->foreignId('sede_id')->nullable()->after('id')->constrained('sedes')->onDelete('cascade');
            
            // Mantener la columna sede por compatibilidad temporal
            // Después de migrar los datos, se puede eliminar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id');
        });
    }
};
