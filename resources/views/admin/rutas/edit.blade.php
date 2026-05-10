@extends('layouts.app_senal')

@section('content')
<div class="container mx-auto p-8 max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
        <h2 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fa-solid fa-pen-to-square text-primary"></i> Editar Ruta
        </h2>

        <form action="{{ route('admin.rutas.update', $ruta->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre de la Ruta</label>
                <input type="text" name="nombre" value="{{ $ruta->nombre }}" 
                    class="w-full p-3 border rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary outline-none" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dificultad</label>
                    <select name="dificultad" class="w-full p-3 border rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                        <option value="Baja" {{ $ruta->dificultad == 'Baja' ? 'selected' : '' }}>Baja</option>
                        <option value="Media" {{ $ruta->dificultad == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Alta" {{ $ruta->dificultad == 'Alta' ? 'selected' : '' }}>Alta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Distancia (km)</label>
                    <input type="number" step="0.01" name="distancia" value="{{ $ruta->distancia }}" 
                        class="w-full p-3 border rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary outline-none" required>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-grow bg-primary hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300">
                    Guardar Cambios
                </button>
                <a href="{{ route('rutas.show', $ruta->id) }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-3 px-6 rounded-lg transition duration-300 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
