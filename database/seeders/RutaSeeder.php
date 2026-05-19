<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruta;
use Illuminate\Support\Facades\File;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        // Ruta donde se guardan los archivos KML (Reto DSW/BDD)
        $directorioKml = storage_path('app/kml');
        
        if (!File::exists($directorioKml)) {
            echo "Aviso: No se encontró el directorio de KML en {$directorioKml}.\n";
            return;
        }

        $archivos = File::files($directorioKml);
        $contador = 1;

        foreach ($archivos as $archivo) {
            if (strtolower($archivo->getExtension()) === 'kml') {
                $contenido = File::get($archivo->getPathname());
                
                // Extraer el nombre de la ruta desde el XML del KML
                preg_match('/<name>(.*?)<\/name>/s', $contenido, $coincidenciasNombre);
                $nombreRuta = isset($coincidenciasNombre[1]) ? trim(str_replace('.kml', '', $coincidenciasNombre[1])) : 'Ruta ' . $contador;
                
                // Extraer las coordenadas del trazado
                preg_match('/<coordinates>(.*?)<\/coordinates>/s', $contenido, $coincidenciasCoord);
                
                if (isset($coincidenciasCoord[1])) {
                    $stringCoordenadas = trim($coincidenciasCoord[1]);
                    $puntosBrutos = preg_split('/\s+/', $stringCoordenadas);
                    
                    $trazadoFinal = [];
                    foreach ($puntosBrutos as $punto) {
                        $partes = explode(',', $punto);
                        if (count($partes) >= 2) {
                            // Guardamos como [Latitud, Longitud] para Leaflet (Reto DOR)
                            $trazadoFinal[] = [(float)$partes[1], (float)$partes[0]]; 
                        }
                    }
                    
                    if (count($trazadoFinal) > 0) {
                        // Lógica de asignación de imágenes según el nombre (Reto Green IT / Contexto)
                        $etiquetasImagen = 'lanzarote,hiking';
                        $nombreMinusculas = strtolower($nombreRuta);
                        
                        if (str_contains($nombreMinusculas, 'volcan') || str_contains($nombreMinusculas, 'corona')) $etiquetasImagen = 'lanzarote,volcano';
                        if (str_contains($nombreMinusculas, 'costa') || str_contains($nombreMinusculas, 'playa')) $etiquetasImagen = 'lanzarote,coast,ocean';
                        if (str_contains($nombreMinusculas, 'graciosa')) $etiquetasImagen = 'la-graciosa,island';

                        // Inserción en la base de datos (Reto DSW)
                        Ruta::create([
                            'nombre' => $nombreRuta,
                            'dificultad' => 'Media',
                            'distancia' => round(count($trazadoFinal) * 0.05, 2), // Cálculo aproximado
                            'trazado' => $trazadoFinal,
                            'imagen' => "https://loremflickr.com/800/600/{$etiquetasImagen}?lock={$contador}"
                        ]);
                        $contador++;
                    }
                }
            }
        }
    }
}
