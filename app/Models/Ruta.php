<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    // Desactivamos la protección de campos para facilitar el seeding
    protected $guarded = [];
    
    // Reto DSW: Cast de JSON a Array automático para el trazado del KML
    protected $casts = [
        'trazado' => 'array',
    ];

    /**
     * Relación Uno a Muchos con Puntos de Interés.
     */
    public function puntosInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }

    /**
     * Relación Uno a Muchos con Valoraciones de usuarios.
     */
    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class);
    }
}
