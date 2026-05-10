<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
        // Devuelve lista sin el trazado completo para no sobrecargar
        $rutas = Ruta::select('id', 'nombre', 'dificultad', 'distancia')->get();
        return response()->json($rutas);
    }

    public function show($id)
    {
        $ruta = Ruta::with(['puntosInteres.categoria', 'valoraciones.user'])->findOrFail($id);
        return response()->json($ruta);
    }
}
