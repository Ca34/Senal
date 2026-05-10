@extends('layouts.app_senal')

@section('content')
<div id="vue-app" class="relative w-full h-full flex flex-col md:flex-row">
    <!-- Panel Lateral -->
    <div class="w-full md:w-1/4 bg-white dark:bg-gray-800 shadow-xl z-20 flex flex-col h-auto md:h-full overflow-y-auto absolute md:relative bottom-0 max-h-[50%] md:max-h-full transition-all">
        <div v-if="loading" class="p-8 text-center text-gray-500">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-primary mb-4"></i>
            <p>Cargando ruta...</p>
        </div>
        
        <div v-if="ruta && !loading" class="flex flex-col h-full">
            <!-- Header Image -->
            <div class="h-40 bg-cover bg-center shrink-0 relative" :style="{ backgroundImage: 'url(' + getRutaImagen() + ')' }">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <button onclick="window.history.back()" class="absolute top-4 left-4 text-white hover:text-emerald-300 transition z-10 font-semibold drop-shadow-md">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </button>
                <div class="absolute bottom-4 left-4 right-4 z-10 text-white flex justify-between items-end">
                    <h1 class="text-2xl font-bold drop-shadow-lg">@{{ ruta.nombre }}</h1>
                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                        <div class="flex gap-2">
                            <a :href="'/admin/rutas/' + ruta.id + '/edit'" class="bg-white/20 hover:bg-white/40 backdrop-blur-md text-white px-3 py-1 rounded text-sm transition border border-white/30">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                            <button @click="eliminarRuta" class="bg-red-500/80 hover:bg-red-600 backdrop-blur-md text-white px-3 py-1 rounded text-sm transition border border-red-400/30">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto flex-grow">
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6">
                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                    <i class="fa-solid fa-layer-group text-primary"></i> @{{ ruta.dificultad || 'Media' }}
                </span>
                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                    <i class="fa-solid fa-route text-primary"></i> @{{ ruta.distancia }} km
                </span>
            </div>

            <!-- Clima / Alertas Mock (Reto DEW - Fetch y PWA) -->
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <h3 class="font-bold text-blue-800 dark:text-blue-300 mb-2">
                    <i class="fa-solid fa-cloud-sun"></i> Condiciones Actuales
                </h3>
                <div class="flex justify-between items-center text-sm">
                    <div>
                        <p class="text-gray-700 dark:text-gray-300">Viento: <span class="font-bold">@{{ clima.viento }} km/h</span></p>
                        <p class="text-gray-700 dark:text-gray-300">Calima: <span class="font-bold">@{{ clima.calima }}</span></p>
                    </div>
                    <div v-if="clima.alerta" class="text-red-500 font-bold animate-pulse">
                        <i class="fa-solid fa-triangle-exclamation"></i> ¡Precaución!
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -            <!-- Botones de Acción -->
            <div class="flex flex-col gap-3">
                <button @click="centrarEnUsuario" 
                        :class="isTracking ? 'bg-red-500 hover:bg-red-600' : 'bg-primary hover:bg-emerald-600'"
                        class="w-full text-white font-bold py-2 px-4 rounded shadow-md transition flex justify-center items-center gap-2">
                    <i class="fa-solid" :class="isTracking ? 'fa-location-dot' : 'fa-location-crosshairs'"></i>
                    @{{ isTracking ? 'Detener GPS' : 'Mi Ubicación GPS' }}
                </button>
            </div>
            
                </div> <!-- Cierre de Content Area -->
            </div> 
        </div> 

        <!-- Mapa -->
        <div class="w-full md:w-3/4 h-full relative z-10 bg-gray-200 dark:bg-gray-900">
            <div id="map" class="w-full h-full"></div>
        </div>
    </div>

<style>
    /* Estilos para el marcador de ubicación estilo Google Maps */
    .user-location-container {
        position: relative;
        width: 30px;
        height: 30px;
    }
    .user-location-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        background: rgba(59, 130, 246, 0.4);
        border-radius: 50%;
        animation: pulse-animation 2.5s infinite;
    }
    .user-location-arrow-box {
        position: absolute;
        top: 0;
        left: 0;
        width: 30px;
        height: 30px;
        background: #3b82f6;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 8px rgba(0,0,0,0.4);
        transition: transform 0.3s ease;
        z-index: 2;
    }
    .user-location-arrow-box svg {
        width: 18px;
        height: 18px;
        fill: white;
        /* La flecha apunta hacia arriba por defecto */
    }
    @keyframes pulse-animation {
        0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(3.5); opacity: 0; }
    }
</style>

<script>
    const { createApp, ref, onMounted, onUnmounted } = Vue;

    createApp({
        setup() {
            const ruta = ref(null);
            const loading = ref(true);
            const rutaId = {{ $id }};
            const clima = ref({ viento: 15, calima: 'Leve', alerta: false });
            const isTracking = ref(false);
            let map = null;
            let polyline = null;
            let userMarker = null;
            let watchId = null;

            const getRutaImagen = () => {
                if (!ruta.value) return '/images/hero.png';
                return ruta.value.imagen || '/images/hero.png';
            };

            const fetchRuta = async () => {
                try {
                    const response = await fetch(`/api/rutas/${rutaId}`);
                    ruta.value = await response.json();
                    simularClima();
                    dibujarRuta();
                } catch (error) {
                    console.error("Error cargando detalles de la ruta:", error);
                } finally {
                    loading.value = false;
                }
            };

            const simularClima = async () => {
                try {
                    let lat = 29.0469;
                    let lng = -13.5899;
                    if (ruta.value && ruta.value.trazado && ruta.value.trazado.length > 0) {
                        lat = ruta.value.trazado[0][0];
                        lng = ruta.value.trazado[0][1];
                    }
                    const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=wind_speed_10m`);
                    const data = await response.json();
                    const windSpeed = data.current.wind_speed_10m || 0;
                    let calima = 'Ninguna';
                    if (windSpeed > 20) calima = 'Moderada';
                    if (windSpeed > 35) calima = 'Intensa';

                    clima.value = {
                        viento: windSpeed,
                        calima: calima,
                        alerta: windSpeed > 30 || calima === 'Intensa'
                    };
                } catch (error) {
                    console.error("Error fetching weather:", error);
                    clima.value = { viento: 15, calima: 'Leve', alerta: false };
                }
            };

            const initMap = () => {
                map = L.map('map').setView([29.0469, -13.5899], 10);
                L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    maxZoom: 17,
                    attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
                }).addTo(map);
            };

            const dibujarRuta = () => {
                if (!ruta.value || !ruta.value.trazado) return;
                polyline = L.polyline(ruta.value.trazado, {
                    color: '#e11d48',
                    weight: 5,
                    opacity: 0.8
                }).addTo(map);
                map.fitBounds(polyline.getBounds());
            };

            const createUserIcon = (heading) => {
                // Si no hay heading, no rotamos el contenedor interno
                const rotation = heading !== null ? `transform: rotate(${heading}deg)` : '';
                
                return L.divIcon({
                    className: 'custom-user-icon',
                    html: `
                        <div class="user-location-container">
                            <div class="user-location-pulse"></div>
                            <div class="user-location-arrow-box" style="${rotation}">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z"/>
                                </svg>
                            </div>
                        </div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
            };

            const centrarEnUsuario = () => {
                if (isTracking.value) {
                    // Detener seguimiento
                    if (watchId) navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                    isTracking.value = false;
                    if (userMarker) {
                        map.removeLayer(userMarker);
                        userMarker = null;
                    }
                    return;
                }

                if (!navigator.geolocation) {
                    alert("Tu navegador no soporta geolocalización.");
                    return;
                }

                isTracking.value = true;

                watchId = navigator.geolocation.watchPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const heading = position.coords.heading; // Grados respecto al Norte (0-360)

                        if (userMarker) {
                            userMarker.setLatLng([lat, lng]);
                            userMarker.setIcon(createUserIcon(heading));
                        } else {
                            userMarker = L.marker([lat, lng], {
                                icon: createUserIcon(heading),
                                zIndexOffset: 1000
                            }).addTo(map);
                            map.setView([lat, lng], 16);
                        }
                    },
                    (error) => {
                        console.error("Error GPS: ", error);
                        isTracking.value = false;
                    },
                    { enableHighAccuracy: true }
                );
            };

            const eliminarRuta = async () => {
                if (!confirm("¿Seguro que quieres eliminar esta ruta?")) return;
                try {
                    const response = await fetch(`/admin/rutas/${rutaId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    if (response.ok) window.location.href = '/rutas';
                } catch (error) { console.error("Error:", error); }
            };

            onMounted(() => {
                initMap();
                fetchRuta();
            });

            onUnmounted(() => {
                if (watchId) navigator.geolocation.clearWatch(watchId);
            });

            return {
                ruta,
                loading,
                clima,
                isTracking,
                centrarEnUsuario,
                getRutaImagen,
                eliminarRuta
            };
        }
    }).mount('#vue-app');
</script>
@endsection
