<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $respuesta = $next($request);

        /**
         * Reto SGY (Seguridad): Configuración de Cabeceras de Seguridad.
         * Estas cabeceras protegen la aplicación contra ataques comunes.
         */
        
        // Evita que el navegador intente adivinar el tipo de contenido (MIME Sniffing)
        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Protege contra ataques de Clickjacking (no permite cargar la web en iframes externos)
        $respuesta->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Filtro básico contra ataques XSS
        $respuesta->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Política de Referrer para privacidad
        $respuesta->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        
        // Content Security Policy (CSP): Define qué fuentes de scripts/estilos son seguras.
        // Hemos incluido las CDNs necesarias (Vue, Tailwind, Leaflet, FontAwesome).
        $respuesta->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://unpkg.com https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https://*.tile.opentopomap.org https://*.openstreetmap.org https://loremflickr.com https://images.unsplash.com; connect-src 'self' https://api.open-meteo.com;");

        return $respuesta;
    }
}
