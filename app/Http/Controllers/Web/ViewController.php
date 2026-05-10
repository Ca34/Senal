<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        return view('rutas.index');
    }

    public function show($id)
    {
        return view('rutas.show', compact('id'));
    }

    public function create()
    {
        return view('admin.rutas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dificultad' => 'required|string',
            'distancia' => 'required|numeric',
        ]);

        Ruta::create($validated);

        return redirect()->route('rutas.index')->with('success', 'Ruta creada con éxito.');
    }

    public function edit($id)
    {
        $ruta = Ruta::findOrFail($id);
        return view('admin.rutas.edit', compact('ruta'));
    }

    public function update(Request $request, $id)
    {
        $ruta = Ruta::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dificultad' => 'required|string',
            'distancia' => 'required|numeric',
        ]);

        $ruta->update($validated);

        return redirect()->route('rutas.show', $id)->with('success', 'Ruta actualizada.');
    }

    public function destroy($id)
    {
        $ruta = Ruta::findOrFail($id);
        $ruta->delete();

        return redirect()->route('rutas.index')->with('success', 'Ruta eliminada.');
    }
}
