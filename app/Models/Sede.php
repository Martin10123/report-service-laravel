<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sedes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'tiene_areas_multiples',
        'tiene_parqueadero',
        'tiene_gradas',
        'numero_areas',
        'activa',
        'opciones_disponibles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tiene_areas_multiples' => 'boolean',
        'tiene_parqueadero' => 'boolean',
        'tiene_gradas' => 'boolean',
        'activa' => 'boolean',
        'opciones_disponibles' => 'array',
    ];

    /**
     * Get the services for the sede.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'sede_id');
    }

    /**
     * Scope a query to only include active sedes.
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Get the areas available for this sede.
     * 
     * @return array
     */
    public function getAreas(): array
    {
        $areas = [];
        for ($i = 1; $i <= $this->numero_areas; $i++) {
            $areas[] = 'A' . $i;
        }
        return $areas;
    }

    /**
     * Check if this sede has a specific area.
     * 
     * @param string $area
     * @return bool
     */
    public function hasArea(string $area): bool
    {
        return in_array(strtoupper($area), $this->getAreas());
    }

    /**
     * Get the available options (menu items) for this sede.
     * 
     * @return array
     */
    public function getOpcionesDisponibles(): array
    {
        // Si no tiene opciones configuradas, devolver todas por defecto
        if (empty($this->opciones_disponibles)) {
            return [
                'primer-conteo',
                'conteo-a1',
                'conteo-a2',
                'conteo-a3',
                'conteo-a4',
                'conteo-sobres',
                'informe-final',
            ];
        }

        return $this->opciones_disponibles;
    }

    /**
     * Check if a specific option is available for this sede.
     * 
     * @param string $opcion
     * @return bool
     */
    public function tieneOpcion(string $opcion): bool
    {
        return in_array($opcion, $this->getOpcionesDisponibles());
    }

    /**
     * Set the default options based on sede name.
     * 
     * @return void
     */
    public function setOpcionesPorDefecto(): void
    {
        $opciones = match (strtolower($this->nombre)) {
            'turbaco', 'bocagrande' => [
                'primer-conteo',
                'conteo-a1',
                'conteo-sobres',
                'informe-final',
            ],
            'villagrande' => [
                'primer-conteo',
                'conteo-a1',
                'conteo-a2',
                'conteo-a3',
                'conteo-a4',
                'conteo-sobres',
                'informe-final',
            ],
            default => [
                'primer-conteo',
                'conteo-a1',
                'conteo-a2',
                'conteo-a3',
                'conteo-a4',
                'conteo-sobres',
                'informe-final',
            ],
        };

        $this->opciones_disponibles = $opciones;
        $this->save();
    }
}
