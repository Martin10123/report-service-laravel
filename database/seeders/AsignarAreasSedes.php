<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Sede;
use Illuminate\Database\Seeder;

class AsignarAreasSedes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las sedes
        $sedes = Sede::all();
        
        foreach ($sedes as $sede) {
            // Limpiar áreas existentes
            $sede->areas()->detach();
            
            // Asignar áreas basándose en numero_areas
            $numeroAreas = (int) $sede->numero_areas;
            
            for ($i = 1; $i <= $numeroAreas; $i++) {
                $area = Area::where('codigo', 'A' . $i)->first();
                if ($area) {
                    $sede->areas()->attach($area->id);
                    $this->command->info("✓ Asignada área A{$i} a sede {$sede->nombre}");
                }
            }
        }
        
        $this->command->info('');
        $this->command->info('✓ Áreas asignadas correctamente a todas las sedes');
    }
}
