<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SedesSeeder extends Seeder
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
            ],
            [
                'nombre' => 'Turbaco',
                'slug' => 'turbaco',
                'tiene_areas_multiples' => false,
                'tiene_parqueadero' => false,
                'tiene_gradas' => false,
                'numero_areas' => 1,
                'activa' => true,
            ],
            [
                'nombre' => 'Bocagrande',
                'slug' => 'bocagrande',
                'tiene_areas_multiples' => false,
                'tiene_parqueadero' => false,
                'tiene_gradas' => false,
                'numero_areas' => 1,
                'activa' => true,
            ],
        ];

        foreach ($sedes as $sedeData) {
            Sede::updateOrCreate(
                ['slug' => $sedeData['slug']], // Buscar por slug
                $sedeData // Actualizar o crear con estos datos
            );
        }

        $this->command->info('Sedes creadas exitosamente.');
    }
}
