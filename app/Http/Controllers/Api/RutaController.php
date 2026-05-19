<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    /**
     * Reto DSW: API REST para obtener todas las rutas.
     * Solo seleccionamos campos básicos para optimizar la carga inicial (Reto Green IT).
     */
    public function index()
    {
        $listaRutas = Ruta::select('id', 'nombre', 'dificultad', 'distancia', 'imagen')->get();
        return response()->json($listaRutas);
    }

    /**
     * Reto DSW: API REST para obtener el detalle completo de una ruta.
     * Incluimos el 'trazado' (KML procesado) y relaciones Eloquent (Puntos de Interés y Valoraciones).
     */
    public function show($id)
    {
        $detalleRuta = Ruta::with(['puntosInteres.categoria', 'valoraciones.user'])->findOrFail($id);
        return response()->json($detalleRuta);
    }
}
