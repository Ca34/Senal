<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
