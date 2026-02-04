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
}
