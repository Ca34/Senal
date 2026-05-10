<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'trazado' => 'array',
    ];

    public function puntosInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }

    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class);
    }
}
