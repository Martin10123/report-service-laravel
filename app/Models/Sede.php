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
     * Get the areas associated with this sede.
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'sede_areas', 'sede_id', 'area_id');
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
        // Retornar áreas desde la relación con la base de datos
        return $this->areas()->pluck('codigo')->toArray();
    }

    /**
     * Get the areas available for this sede (legacy support).
     * If no areas are assigned in DB, fallback to numero_areas.
     * 
     * @return array
     */
    public function getAreasLegacy(): array
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
        // Si tiene opciones configuradas manualmente, usarlas
        if (!empty($this->opciones_disponibles)) {
            return $this->opciones_disponibles;
        }

        // Generar opciones dinámicamente basándose en las áreas
        $opciones = ['primer-conteo'];
        
        // Agregar opciones de conteo para cada área
        $areas = $this->getAreas();
        foreach ($areas as $area) {
            $opciones[] = 'conteo-' . strtolower($area);
        }
        
        // Agregar opciones finales
        $opciones[] = 'conteo-sobres';
        $opciones[] = 'informe-final';

        return $opciones;
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
