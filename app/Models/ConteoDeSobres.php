<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConteoDeSobres extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conteos_sobres';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'servicio_id',
        'canastas',
        'ofrendas',
        'protemplo',
        'iglekids',
        'total_ofrendas',
        'final_ofrendas',
        'total_protemplo',
        'final_protemplo',
        'total_iglekids',
        'final_iglekids',
        'completado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'canastas' => 'array',
        'ofrendas' => 'array',
        'protemplo' => 'array',
        'iglekids' => 'array',
        'total_ofrendas' => 'integer',
        'final_ofrendas' => 'integer',
        'total_protemplo' => 'integer',
        'final_protemplo' => 'integer',
        'total_iglekids' => 'integer',
        'final_iglekids' => 'integer',
        'completado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the servicio that owns the conteo de sobres.
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }

    /**
     * Calcular totales desde los sobres.
     *
     * @param array $ofrendas
     * @param array $protemplo
     * @param array $iglekids
     * @return array
     */
    public static function calcularTotales(array $ofrendas, array $protemplo, array $iglekids): array
    {
        $totalOfrendas = ($ofrendas['inicial'] ?? 0) + ($ofrendas['recibidos'] ?? 0);
        $finalOfrendas = $totalOfrendas - ($ofrendas['entregados'] ?? 0);

        $totalProtemplo = ($protemplo['inicial'] ?? 0) + ($protemplo['recibidos'] ?? 0);
        $finalProtemplo = $totalProtemplo - ($protemplo['entregados'] ?? 0);

        $totalIglekids = ($iglekids['inicial'] ?? 0) + ($iglekids['recibidos'] ?? 0);
        $finalIglekids = $totalIglekids - ($iglekids['entregados'] ?? 0);

        return [
            'total_ofrendas' => $totalOfrendas,
            'final_ofrendas' => $finalOfrendas,
            'total_protemplo' => $totalProtemplo,
            'final_protemplo' => $finalProtemplo,
            'total_iglekids' => $totalIglekids,
            'final_iglekids' => $finalIglekids,
        ];
    }
}
