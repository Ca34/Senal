<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * Muestra la vista principal (Catálogo).
     * Reto DOR: Diseño Mobile-first y mapas.
     */
    public function index()
    {
        return view('rutas.index');
    }

    /**
     * Muestra la vista de detalle de una ruta.
     * Reto DEW: Aquí es donde Vue.js consume la API y gestiona el GPS/Clima.
     */
    public function show($id)
    {
        return view('rutas.show', compact('id'));
    }

    /**
     * Reto DSW: Gestión administrativa (CRUD) protegida por roles.
     */
    public function create()
    {
        return view('admin.rutas.create');
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'nombre' => 'required|string|max:255',
            'dificultad' => 'required|string',
            'distancia' => 'required|numeric',
        ]);

        Ruta::create($datosValidados);

        return redirect()->route('rutas.index')->with('success', 'Ruta creada con éxito.');
    }

    public function edit($id)
    {
        $rutaParaEditar = Ruta::findOrFail($id);
        return view('admin.rutas.edit', compact('rutaParaEditar'));
    }

    public function update(Request $request, $id)
    {
        $rutaExistente = Ruta::findOrFail($id);
        
        $datosParaActualizar = $request->validate([
            'nombre' => 'required|string|max:255',
            'dificultad' => 'required|string',
            'distancia' => 'required|numeric',
        ]);

        $rutaExistente->update($datosParaActualizar);

        return redirect()->route('rutas.show', $id)->with('success', 'Ruta actualizada.');
    }

    public function destroy($id)
    {
        $rutaAEliminar = Ruta::findOrFail($id);
        $rutaAEliminar->delete();

        return redirect()->route('rutas.index')->with('success', 'Ruta eliminada.');
    }
}
