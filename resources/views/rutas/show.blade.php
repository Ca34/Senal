@extends('layouts.app_senal')

@section('content')
<!-- html2pdf.js y html2canvas para exportar fichas PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div id="vue-app" class="relative w-full h-full flex flex-col md:flex-row">
    <!-- Panel Lateral -->
    <div class="w-full md:w-1/4 bg-white dark:bg-gray-800 shadow-xl z-20 flex flex-col h-auto md:h-full overflow-y-auto absolute md:relative bottom-0 max-h-[50%] md:max-h-full transition-all">
        <div v-if="estaCargando" class="p-8 text-center text-gray-500">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-primary mb-4"></i>
            <p>Cargando ruta...</p>
        </div>
        
        <div v-if="rutaDetalle && !estaCargando" class="flex flex-col h-full">
            <!-- Header Image -->
            <div class="h-40 bg-cover bg-center shrink-0 relative" :style="{ backgroundImage: 'url(' + obtenerImagenFondo() + ')' }">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <button onclick="window.history.back()" class="absolute top-4 left-4 text-white hover:text-emerald-300 transition z-10 font-semibold drop-shadow-md">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </button>
                <div class="absolute bottom-4 left-4 right-4 z-10 text-white flex justify-between items-end">
                    <h1 class="text-2xl font-bold drop-shadow-lg">@{{ rutaDetalle.nombre }}</h1>
                        <div class="flex gap-2">
                            <button @click="exportarAPdf" class="bg-white/20 hover:bg-white/40 backdrop-blur-md text-white px-3 py-1 rounded text-sm transition border border-white/30">
                                <i class="fa-solid fa-file-pdf text-red-400"></i> PDF
                            </button>
                            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestor')))
                                <a :href="'/admin/rutas/' + rutaDetalle.id + '/edit'" class="bg-white/20 hover:bg-white/40 backdrop-blur-md text-white px-3 py-1 rounded text-sm transition border border-white/30">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                    <button @click="borrarRutaAdministrador" class="bg-red-500/80 hover:bg-red-600 backdrop-blur-md text-white px-3 py-1 rounded text-sm transition border border-red-400/30">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 overflow-y-auto flex-grow">
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6">
                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                    <i class="fa-solid fa-layer-group text-primary"></i> @{{ rutaDetalle.dificultad || 'Media' }}
                </span>
                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                    <i class="fa-solid fa-route text-primary"></i> @{{ rutaDetalle.distancia }} km
                </span>
            </div>

            <!-- Clima / Alertas Real-time (Reto DEW) -->
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <h3 class="font-bold text-blue-800 dark:text-blue-300 mb-2">
                    <i class="fa-solid fa-cloud-sun"></i> Condiciones Actuales
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <p class="text-gray-700 dark:text-gray-300">Temp: <span class="font-bold">@{{ datosClima.temperatura }}°C</span></p>
                        <p class="text-gray-700 dark:text-gray-300">Lluvia: <span class="font-bold">@{{ datosClima.lluvia }}%</span></p>
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-300">Viento: <span class="font-bold">@{{ datosClima.viento }} km/h</span></p>
                        <p class="text-gray-700 dark:text-gray-300">Calima: <span class="font-bold">@{{ datosClima.calima }}</span></p>
                    </div>
                </div>
                <div v-if="datosClima.alerta" class="mt-3 text-red-500 font-bold animate-pulse text-xs text-center border-t border-red-200 pt-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> ¡Precaución! Condiciones adversas
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col gap-3">
                <button @click="alternarSeguimientoGps" 
                        :class="seguimientoGpsActivo ? 'bg-red-500 hover:bg-red-600' : 'bg-primary hover:bg-emerald-600'"
                        class="w-full text-white font-bold py-2 px-4 rounded shadow-md transition flex justify-center items-center gap-2">
                    <i class="fa-solid" :class="seguimientoGpsActivo ? 'fa-location-dot' : 'fa-location-crosshairs'"></i>
                    @{{ seguimientoGpsActivo ? 'Detener GPS' : 'Mi Ubicación GPS' }}
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
    // Reto DEW: Usamos Vue.js 3 para gestionar la interactividad (GPS, Clima, API).
    const { createApp, ref, onMounted, onUnmounted } = Vue;

    createApp({
        setup() {
            const rutaDetalle = ref(null);
            const estaCargando = ref(true);
            const idDeRuta = {{ $id }};
            const datosClima = ref({ viento: 15, calima: 'Leve', temperatura: 22, lluvia: 0, alerta: false });
            const seguimientoGpsActivo = ref(false);
            
            let mapaLeaflet = null;
            let lineaRuta = null;
            let marcadorUsuario = null;
            let idSeguimientoGps = null;

            const obtenerImagenFondo = () => {
                if (!rutaDetalle.value) return '/images/hero.png';
                return rutaDetalle.value.imagen || '/images/hero.png';
            };

            // Reto DEW: Consumo de API REST para obtener detalles
            const cargarDetalleRuta = async () => {
                try {
                    const respuesta = await fetch(`/api/rutas/${idDeRuta}`);
                    rutaDetalle.value = await respuesta.json();
                    
                    // Una vez tenemos la ruta, cargamos el clima y dibujamos el mapa
                    consultarClimaReal();
                    dibujarTrazadoRuta();
                } catch (error) {
                    console.error("Error cargando detalles de la ruta:", error);
                } finally {
                    estaCargando.value = false;
                }
            };

            // Reto DEW: Integración con API de terceros (Open-Meteo)
            const consultarClimaReal = async () => {
                try {
                    let lat = 29.0469;
                    let lng = -13.5899;
                    if (rutaDetalle.value && rutaDetalle.value.trazado && rutaDetalle.value.trazado.length > 0) {
                        lat = rutaDetalle.value.trazado[0][0];
                        lng = rutaDetalle.value.trazado[0][1];
                    }
                    
                    // Pedimos viento, temperatura y probabilidad de lluvia
                    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,wind_speed_10m&hourly=precipitation_probability&forecast_days=1`;
                    const respuestaClima = await fetch(url);
                    const data = await respuestaClima.json();
                    
                    const velocidadViento = data.current.wind_speed_10m || 0;
                    const temperaturaActual = data.current.temperature_2m || 0;
                    const probLluvia = data.hourly.precipitation_probability[0] || 0;
                    
                    let estadoCalima = 'Ninguna';
                    if (velocidadViento > 20) estadoCalima = 'Moderada';
                    if (velocidadViento > 35) estadoCalima = 'Intensa';

                    datosClima.value = {
                        viento: velocidadViento,
                        temperatura: temperaturaActual,
                        lluvia: probLluvia,
                        calima: estadoCalima,
                        alerta: velocidadViento > 30 || estadoCalima === 'Intensa' || probLluvia > 70
                    };
                } catch (error) {
                    console.error("Error consultando el clima:", error);
                }
            };

            // Reto DOR: Inicialización de Leaflet
            const inicializarMapa = () => {
                mapaLeaflet = L.map('map', {
                    renderer: L.canvas()
                }).setView([29.0469, -13.5899], 10);
                L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    maxZoom: 17,
                    crossOrigin: 'anonymous',
                    attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
                }).addTo(mapaLeaflet);
            };

            // Reto DOR: Dibujar el KML procesado (GeoJSON/Polyline)
            const dibujarTrazadoRuta = () => {
                if (!rutaDetalle.value || !rutaDetalle.value.trazado) return;
                
                lineaRuta = L.polyline(rutaDetalle.value.trazado, {
                    color: '#e11d48', // Color rojizo corporativo
                    weight: 5,
                    opacity: 0.8
                }).addTo(mapaLeaflet);
                
                // Ajustar el zoom automáticamente para ver toda la ruta
                mapaLeaflet.fitBounds(lineaRuta.getBounds());
            };

            // Reto Especial: Marcador dinámico con flecha de dirección (Heading)
            const crearIconoUsuario = (gradosDeRotacion) => {
                const rotacionCss = gradosDeRotacion !== null ? `transform: rotate(${gradosDeRotacion}deg)` : '';
                
                return L.divIcon({
                    className: 'custom-user-icon',
                    html: `
                        <div class="user-location-container">
                            <div class="user-location-pulse"></div>
                            <div class="user-location-arrow-box" style="${rotacionCss}">
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

            // Reto Especial: Seguimiento GPS en tiempo real
            const alternarSeguimientoGps = () => {
                if (seguimientoGpsActivo.value) {
                    // Detener GPS
                    if (idSeguimientoGps) navigator.geolocation.clearWatch(idSeguimientoGps);
                    idSeguimientoGps = null;
                    seguimientoGpsActivo.value = false;
                    if (marcadorUsuario) {
                        mapaLeaflet.removeLayer(marcadorUsuario);
                        marcadorUsuario = null;
                    }
                    return;
                }

                if (!navigator.geolocation) {
                    alert("Tu navegador no soporta geolocalización.");
                    return;
                }

                seguimientoGpsActivo.value = true;

                idSeguimientoGps = navigator.geolocation.watchPosition(
                    (posicion) => {
                        const lat = posicion.coords.latitude;
                        const lng = posicion.coords.longitude;
                        const rumbo = posicion.coords.heading; // Rumbo en grados (0-360)

                        if (marcadorUsuario) {
                            marcadorUsuario.setLatLng([lat, lng]);
                            marcadorUsuario.setIcon(crearIconoUsuario(rumbo));
                        } else {
                            marcadorUsuario = L.marker([lat, lng], {
                                icon: crearIconoUsuario(rumbo),
                                zIndexOffset: 1000
                            }).addTo(mapaLeaflet);
                            mapaLeaflet.setView([lat, lng], 16);
                        }
                    },
                    (error) => {
                        console.warn("Error de GPS:", error);
                        
                        if (error.code === error.PERMISSION_DENIED) {
                            alert("Permiso de localización denegado. Por favor, activa el acceso a la ubicación en el candado de la barra de direcciones de tu navegador.");
                        } else {
                            alert("No se pudo obtener la ubicación GPS.");
                        }
                        
                        seguimientoGpsActivo.value = false;
                        if (idSeguimientoGps) navigator.geolocation.clearWatch(idSeguimientoGps);
                        idSeguimientoGps = null;
                    },
                    { 
                        enableHighAccuracy: true
                    }
                );
            };

            const borrarRutaAdministrador = async () => {
                if (!confirm("¿Seguro que quieres eliminar esta ruta?")) return;
                try {
                    const respuesta = await fetch(`/admin/rutas/${idDeRuta}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    if (respuesta.ok) window.location.href = '/rutas';
                } catch (error) { console.error("Error al borrar:", error); }
            };

            const exportarAPdf = async () => {
                if (!rutaDetalle.value) return;

                let mapImage = '';
                try {
                    const mapElement = document.getElementById('map');
                    if (mapElement) {
                        // Ocultar controles de zoom temporalmente para la foto
                        const zoomControl = mapElement.querySelector('.leaflet-control-zoom');
                        if (zoomControl) zoomControl.style.display = 'none';
                        
                        const mapPane = mapElement.querySelector('.leaflet-map-pane');
                        let originalTransform = '';
                        let originalLeft = '';
                        let originalTop = '';

                        if (mapPane) {
                            originalTransform = mapPane.style.transform;
                            originalLeft = mapPane.style.left;
                            originalTop = mapPane.style.top;

                            // Solución al problema de los desplazamientos en CSS Transform de Leaflet
                            const matrix = window.getComputedStyle(mapPane).transform;
                            if (matrix && matrix !== 'none') {
                                const values = matrix.split('(')[1].split(')')[0].split(',');
                                let x = 0, y = 0;
                                if (matrix.indexOf('3d') !== -1) {
                                    x = parseFloat(values[12]) || 0;
                                    y = parseFloat(values[13]) || 0;
                                } else {
                                    x = parseFloat(values[4]) || 0;
                                    y = parseFloat(values[5]) || 0;
                                }
                                mapPane.style.transform = 'none';
                                mapPane.style.left = x + 'px';
                                mapPane.style.top = y + 'px';
                            }
                        }

                        const canvas = await html2canvas(mapElement, {
                            useCORS: true,
                            allowTaint: false, // Evita que se rompa el export si una imagen no tiene CORS
                            scale: 1.5,
                            scrollX: 0,
                            scrollY: 0
                        });
                        mapImage = canvas.toDataURL('image/jpeg', 0.95);
                        
                        // Restaurar estado original del mapa
                        if (mapPane) {
                            mapPane.style.transform = originalTransform;
                            mapPane.style.left = originalLeft;
                            mapPane.style.top = originalTop;
                        }

                        if (zoomControl) zoomControl.style.display = '';
                    }
                } catch (e) {
                    console.error("Error al capturar el mapa:", e);
                }

                const element = document.createElement('div');
                element.className = 'p-8 bg-white text-gray-800 font-sans';
                element.style.width = '700px';

                let puntosHtml = '';
                if (rutaDetalle.value.puntos_interes && rutaDetalle.value.puntos_interes.length > 0) {
                    puntosHtml = '<h3 style="font-size: 15px; font-weight: bold; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-top: 20px; margin-bottom: 8px;">Puntos de Interés</h3><ul style="padding-left: 20px; margin-top: 0; font-size: 13px; color: #4b5563;">';
                    rutaDetalle.value.puntos_interes.forEach(p => {
                        puntosHtml += `<li style="margin-bottom: 4px;"><strong>${p.nombre}</strong>${p.descripcion ? ' - ' + p.descripcion : ''}</li>`;
                    });
                    puntosHtml += '</ul>';
                }

                let mapaHtml = '';
                if (mapImage) {
                    mapaHtml = `
                        <div style="margin-top: 20px;">
                            <h3 style="font-size: 15px; font-weight: bold; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px;">Visualización del Trazado</h3>
                            <div style="text-align: center; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px; background: #f9fafb;">
                                <img src="${mapImage}" style="max-width: 100%; height: auto; border-radius: 4px;" />
                            </div>
                        </div>
                    `;
                }

                element.innerHTML = `
                    <div style="border-bottom: 3px solid #10b981; padding-bottom: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin: 0;">Senal - Ficha de Sendero</h1>
                            <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">Aplicación de Senderismo y Georreferenciación de Lanzarote</p>
                        </div>
                        <span style="font-size: 14px; font-weight: bold; color: #10b981;">LANZAROTE</span>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h2 style="font-size: 20px; font-weight: bold; color: #10b981; margin-top: 0; margin-bottom: 6px;">${rutaDetalle.value.nombre}</h2>
                        <div style="display: flex; gap: 20px; font-size: 14px; color: #374151; background: #f9fafb; padding: 10px; border-radius: 6px; border: 1px solid #e5e7eb;">
                            <span><strong>Dificultad:</strong> ${rutaDetalle.value.dificultad || 'Media'}</span>
                            <span><strong>Distancia:</strong> ${rutaDetalle.value.distancia} km</span>
                        </div>
                    </div>
                    
                    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px;">
                        <h3 style="font-size: 14px; font-weight: bold; color: #065f46; margin-top: 0; margin-bottom: 8px;">Condiciones del Clima (Real-time)</h3>
                        <div style="display: flex; gap: 20px; font-size: 13px; color: #047857;">
                            <div>Temperatura: <strong>${datosClima.value.temperatura}°C</strong></div>
                            <div>Viento: <strong>${datosClima.value.viento} km/h</strong></div>
                            <div>Lluvia: <strong>${datosClima.value.lluvia}%</strong></div>
                            <div>Calima: <strong>${datosClima.value.calima}</strong></div>
                        </div>
                    </div>

                    ${puntosHtml}

                    ${mapaHtml}

                    <div style="margin-top: 40px; font-size: 11px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                        Documento oficial generado dinámicamente desde Senal - PWA de Senderismo.
                    </div>
                `;

                const opt = {
                    margin:       15,
                    filename:     `ficha_${rutaDetalle.value.nombre.toLowerCase().replace(/\s+/g, '_')}.pdf`,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().from(element).set(opt).save();
            };

            onMounted(() => {
                inicializarMapa();
                cargarDetalleRuta();
            });

            onUnmounted(() => {
                if (idSeguimientoGps) navigator.geolocation.clearWatch(idSeguimientoGps);
            });

            return {
                rutaDetalle,
                estaCargando,
                datosClima,
                seguimientoGpsActivo,
                alternarSeguimientoGps,
                obtenerImagenFondo,
                borrarRutaAdministrador,
                exportarAPdf
            };
        }
    }).mount('#vue-app');
</script>
@endsection
