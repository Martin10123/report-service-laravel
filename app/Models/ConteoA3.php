<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConteoA3 extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conteos_a3';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'servicio_id',
        'areas',
        'total_adultos',
        'total_ninos',
        'total_asistencia',
        'completado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'areas' => 'array',
        'total_adultos' => 'integer',
        'total_ninos' => 'integer',
        'total_asistencia' => 'integer',
        'completado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the servicio that owns the conteo A3.
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }

    /**
     * Calcular totales desde las áreas.
     *
     * @param array $areas
     * @return array
     */
    public static function calcularTotales(array $areas): array
    {
        $totalAdultos = 0;
        $totalNinos = 0;

        foreach ($areas as $area) {
            $totalAdultos += $area['adultos'] ?? 0;
            $totalNinos += $area['ninos'] ?? 0;
        }

        return [
            'total_adultos' => $totalAdultos,
            'total_ninos' => $totalNinos,
            'total_asistencia' => $totalAdultos + $totalNinos,
        ];
    }
}
