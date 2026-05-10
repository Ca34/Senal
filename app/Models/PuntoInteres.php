<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
