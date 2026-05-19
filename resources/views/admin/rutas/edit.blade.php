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
                    <input type="number" step="0.01" name="distancia" id="distancia-input" value="{{ $ruta->distancia }}" 
                        class="w-full p-3 border rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-primary outline-none" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recorrido de la Ruta</label>
                    <button type="button" id="clear-map" class="text-xs text-red-500 hover:text-red-700 font-semibold transition hidden">
                        <i class="fa-solid fa-trash-can"></i> Limpiar recorrido
                    </button>
                </div>
                <div id="map" class="h-80 w-full rounded-lg border border-gray-300 dark:border-gray-600 mb-2 z-10"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400"><i class="fa-solid fa-info-circle"></i> Haz clic en el mapa sucesivamente para trazar los puntos del camino. La distancia se calculará automáticamente.</p>
                <input type="hidden" name="trazado" id="trazado-input" value="{{ json_encode($ruta->trazado ?? []) }}">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar mapa centrado en Lanzarote por defecto
        const mapa = L.map('map').setView([29.0469, -13.5899], 10);
        L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
        }).addTo(mapa);

        const trazadoInput = document.getElementById('trazado-input');
        const distanciaInput = document.getElementById('distancia-input');
        const clearBtn = document.getElementById('clear-map');

        let puntosTrazado = [];
        let marcadores = [];

        // Cargar trazado existente si hay alguno
        try {
            const rawTrazado = JSON.parse(trazadoInput.value);
            if (Array.isArray(rawTrazado) && rawTrazado.length > 0) {
                puntosTrazado = rawTrazado;
            }
        } catch (e) {
            console.error("Error al parsear el trazado existente:", e);
        }

        let polyline = L.polyline(puntosTrazado, { color: '#e11d48', weight: 4 }).addTo(mapa);

        // Si hay puntos existentes, los marcamos y enfocamos el mapa en el recorrido
        if (puntosTrazado.length > 0) {
            puntosTrazado.forEach(punto => {
                const marker = L.circleMarker(punto, {
                    radius: 5,
                    color: '#e11d48',
                    fillColor: '#e11d48',
                    fillOpacity: 1
                }).addTo(mapa);
                marcadores.push(marker);
            });
            mapa.fitBounds(polyline.getBounds());
            clearBtn.classList.remove('hidden');
        }

        function actualizarTrazado() {
            // Actualizar el input oculto
            trazadoInput.value = JSON.stringify(puntosTrazado);
            
            // Dibujar la línea
            polyline.setLatLngs(puntosTrazado);

            // Calcular distancia total
            let distMeters = 0;
            for (let i = 0; i < puntosTrazado.length - 1; i++) {
                const p1 = L.latLng(puntosTrazado[i]);
                const p2 = L.latLng(puntosTrazado[i+1]);
                distMeters += p1.distanceTo(p2);
            }
            const distKm = (distMeters / 1000).toFixed(2);
            distanciaInput.value = distKm;

            // Mostrar/ocultar botón de limpiar
            if (puntosTrazado.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        mapa.on('click', function (e) {
            const lat = parseFloat(e.latlng.lat.toFixed(6));
            const lng = parseFloat(e.latlng.lng.toFixed(6));
            
            puntosTrazado.push([lat, lng]);
            
            // Añadir un marcador simple
            const marker = L.circleMarker([lat, lng], {
                radius: 5,
                color: '#e11d48',
                fillColor: '#e11d48',
                fillOpacity: 1
            }).addTo(mapa);
            
            marcadores.push(marker);
            actualizarTrazado();
        });

        clearBtn.addEventListener('click', function () {
            puntosTrazado = [];
            marcadores.forEach(m => mapa.removeLayer(m));
            marcadores = [];
            actualizarTrazado();
        });
    });
</script>
@endsection
