<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruta;
use Illuminate\Support\Facades\File;

class RutaSeeder extends Seeder
{
    public function run(): void
    {
        $kmlDir = storage_path('app/kml');
        if (!File::exists($kmlDir)) {
            echo "Directorio de KML no encontrado.\n";
            return;
        }

        $files = File::files($kmlDir);

        $index = 1;
        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'kml') {
                $content = File::get($file->getPathname());
                
                preg_match('/<name>(.*?)<\/name>/s', $content, $nameMatches);
                $name = isset($nameMatches[1]) ? trim(str_replace('.kml', '', $nameMatches[1])) : 'Ruta ' . $index;
                
                preg_match('/<coordinates>(.*?)<\/coordinates>/s', $content, $coordMatches);
                
                if (isset($coordMatches[1])) {
                    $coordsString = trim($coordMatches[1]);
                    $points = preg_split('/\s+/', $coordsString);
                    
                    $trazado = [];
                    foreach ($points as $point) {
                        $parts = explode(',', $point);
                        if (count($parts) >= 2) {
                            $trazado[] = [(float)$parts[1], (float)$parts[0]]; 
                        }
                    }
                    
                    if (count($trazado) > 0) {
                        $keywords = 'lanzarote,hiking';
                        $nombreLow = strtolower($name);
                        if (str_contains($nombreLow, 'volcan') || str_contains($nombreLow, 'corona') || str_contains($nombreLow, 'caldera')) $keywords = 'lanzarote,volcano';
                        if (str_contains($nombreLow, 'costa') || str_contains($nombreLow, 'playa') || str_contains($nombreLow, 'litoral') || str_contains($nombreLow, 'papagayo')) $keywords = 'lanzarote,coast,ocean';
                        if (str_contains($nombreLow, 'teguise') || str_contains($nombreLow, 'yaiza') || str_contains($nombreLow, 'tias')) $keywords = 'lanzarote,village,white';
                        if (str_contains($nombreLow, 'geria')) $keywords = 'lanzarote,vineyard,volcano';
                        if (str_contains($nombreLow, 'graciosa')) $keywords = 'la-graciosa,island,beach';

                        Ruta::create([
                            'nombre' => $name,
                            'dificultad' => 'Media',
                            'distancia' => round(count($trazado) * 0.05, 2),
                            'trazado' => $trazado,
                            'imagen' => "https://loremflickr.com/800/600/{$keywords}?lock={$index}"
                        ]);
                        $index++;
                    }
                }
            }
        }
    }
}
