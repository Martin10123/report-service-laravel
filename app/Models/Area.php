<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'areas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activa',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Get the sedes that have this area.
     */
    public function sedes()
    {
        return $this->belongsToMany(Sede::class, 'sede_areas', 'area_id', 'sede_id');
    }

    /**
     * Scope a query to only include active areas.
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
