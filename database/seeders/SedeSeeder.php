<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sedes = [
            [
                'nombre' => 'Villa Grande',
                'slug' => 'villa-grande',
                'tiene_areas_multiples' => true,
                'tiene_parqueadero' => true,
                'tiene_gradas' => true,
                'numero_areas' => 4,
                'activa' => true,
                'opciones_disponibles' => [
                    'primer-conteo',
                    'conteo-a1',
                    'conteo-a2',
                    'conteo-a3',
                    'conteo-a4',
                    'conteo-sobres',
                    'consolidado',
                    'informe-final',
                ],
            ],
            [
                'nombre' => 'Turbaco',
                'slug' => 'turbaco',
                'tiene_areas_multiples' => false,
                'tiene_parqueadero' => false,
                'tiene_gradas' => false,
                'numero_areas' => 1,
                'activa' => true,
                'opciones_disponibles' => [
                    'primer-conteo',
                    'conteo-a1',
                    'conteo-sobres',
                    'consolidado',
                    'informe-final',
                ],
            ],
            [
                'nombre' => 'Bocagrande',
                'slug' => 'bocagrande',
                'tiene_areas_multiples' => false,
                'tiene_parqueadero' => false,
                'tiene_gradas' => false,
                'numero_areas' => 1,
                'activa' => true,
                'opciones_disponibles' => [
                    'primer-conteo',
                    'conteo-a1',
                    'conteo-sobres',
                    'consolidado',
                    'informe-final',
                ],
            ],
        ];

        foreach ($sedes as $sede) {
            Sede::updateOrCreate(
                ['slug' => $sede['slug']],
                $sede
            );
        }
    }
}
