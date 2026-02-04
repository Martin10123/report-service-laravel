<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sede_id',
        'sede',
        'numero_servicio',
        'fecha',
        'dia_semana',
        'hora',
        'estado',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Scope para servicios activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para servicios finalizados.
     */
    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    /**
     * Scope para filtrar por sede.
     */
    public function scopePorSede($query, $sede)
    {
        return $query->where('sede', $sede);
    }

    /**
     * Scope para filtrar por rango de fechas.
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Get the sede that owns the service.
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Accesor para obtener el nombre formateado.
     */
    public function getNombreCompletoAttribute(): string
    {
        $sedeNombre = $this->sede ? $this->sede->nombre : $this->sede;
        return "{$sedeNombre} - Servicio #{$this->numero_servicio} - {$this->fecha->format('d/m/Y')}";
    }
}
