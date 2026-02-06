<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'codigo' => 'A1',
                'nombre' => 'Área 1',
                'descripcion' => 'Primera área de conteo',
                'activa' => true,
            ],
            [
                'codigo' => 'A2',
                'nombre' => 'Área 2',
                'descripcion' => 'Segunda área de conteo',
                'activa' => true,
            ],
            [
                'codigo' => 'A3',
                'nombre' => 'Área 3',
                'descripcion' => 'Tercera área de conteo',
                'activa' => true,
            ],
            [
                'codigo' => 'A4',
                'nombre' => 'Área 4',
                'descripcion' => 'Cuarta área de conteo',
                'activa' => true,
            ],
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(
                ['codigo' => $area['codigo']],
                $area
            );
        }
    }
}
