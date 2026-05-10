@extends('layouts.app_senal')

@section('content')
<div id="vue-app" class="flex flex-col md:flex-row h-full">
    <!-- Lista de Rutas -->
    <div class="w-full md:w-1/3 bg-white dark:bg-gray-800 shadow-lg z-10 flex flex-col h-1/2 md:h-full overflow-hidden">
        <div class="relative p-6 bg-cover bg-center border-b dark:border-gray-700 text-white" style="background-image: url('/images/hero.png');">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="relative z-10 flex justify-between items-center mb-3">
                <h2 class="text-2xl font-bold shadow-sm">Catálogo de Rutas</h2>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.rutas.create') }}" class="bg-primary hover:bg-emerald-600 text-white px-3 py-1 rounded-md text-sm font-bold shadow-md transition">
                        <i class="fa-solid fa-plus"></i> Nueva
                    </a>
                @endif
            </div>
            <div class="relative z-10">
                <input type="text" v-model="searchQuery" placeholder="Buscar ruta..." 
                    class="w-full p-2 border rounded-md bg-white/90 text-black dark:bg-gray-800/90 dark:border-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-500">
            </div>
        </div>
        <div class="flex-grow overflow-y-auto p-2">
            <div v-if="loading" class="p-4 text-center text-gray-500">Cargando rutas...</div>
            <div v-for="ruta in filteredRutas" :key="ruta.id" 
                class="mb-3 border rounded-lg cursor-pointer overflow-hidden bg-white dark:bg-gray-800 hover:shadow-md hover:-translate-y-1 transition-all duration-300"
                @click="verDetalle(ruta.id)">
                <div class="h-24 bg-cover bg-center" :style="{ backgroundImage: 'url(' + getRutaImagen(ruta) + ')' }"></div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">@{{ ruta.nombre }}</h3>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <span class="mr-4"><i class="fa-solid fa-layer-group text-primary"></i> @{{ ruta.dificultad || 'Media' }}</span>
                        <span><i class="fa-solid fa-route text-primary"></i> @{{ ruta.distancia }} km</span>
                    </div>
                </div>
            </div>
            <div v-if="!loading && filteredRutas.length === 0" class="p-4 text-center text-gray-500">
                No se encontraron rutas.
            </div>
        </div>
    </div>

    <!-- Mapa -->
    <div class="w-full md:w-2/3 h-1/2 md:h-full relative">
        <div id="map" class="w-full h-full z-0"></div>
    </div>
</div>

<script>
    const { createApp, ref, computed, onMounted } = Vue;

    createApp({
        setup() {
            const rutas = ref([]);
            const searchQuery = ref('');
            const loading = ref(true);

            const getRutaImagen = (ruta) => {
                return ruta.imagen || '/images/placeholder.png';
            };

            const fetchRutas = async () => {
                try {
                    const response = await fetch('/api/rutas');
                    rutas.value = await response.json();
                } catch (error) {
                    console.error("Error cargando rutas:", error);
                } finally {
                    loading.value = false;
                }
            };

            const filteredRutas = computed(() => {
                if (!searchQuery.value) return rutas.value;
                const query = searchQuery.value.toLowerCase();
                return rutas.value.filter(r => r.nombre.toLowerCase().includes(query));
            });

            const initMap = () => {
                // Centrado en Lanzarote
                map = L.map('map').setView([29.0469, -13.5899], 10);

                // Mapa Topográfico de OpenTopoMap
                L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    maxZoom: 17,
                    attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
                }).addTo(map);
            };

            const verDetalle = (id) => {
                window.location.href = `/rutas/${id}`;
            };

            onMounted(() => {
                fetchRutas();
                initMap();
            });

            return {
                rutas,
                searchQuery,
                filteredRutas,
                loading,
                verDetalle,
                getRutaImagen
            };
        }
    }).mount('#vue-app');
</script>
@endsection
