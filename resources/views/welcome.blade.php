<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Senal') }} - Senderismo en Lanzarote</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            body {
                font-family: 'Figtree', sans-serif;
            }
            .bg-lanzarote {
                /* Imagen local de volcán de Lanzarote */
                background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('{{ asset('images/volcano.png') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }
        </style>
    </head>
    <body class="antialiased bg-lanzarote min-h-screen flex flex-col text-white">

        <!-- Top Navigation -->
        <div class="w-full flex justify-between items-center p-6">
            <div class="text-2xl font-bold tracking-wider uppercase text-white drop-shadow-md">
                Senal
            </div>
            
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/rutas') }}" class="font-semibold text-white hover:text-gray-300 transition duration-300 ease-in-out bg-white/20 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/30">
                            Ir al Mapa
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-white hover:text-gray-300 transition duration-300 ease-in-out bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 hover:bg-white/20">
                            Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-semibold text-gray-900 bg-white hover:bg-gray-200 transition duration-300 ease-in-out px-4 py-2 rounded-lg shadow-lg">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="flex-grow flex items-center justify-center sm:justify-start px-6 sm:px-16 lg:px-24">
            <div class="max-w-2xl text-center sm:text-left drop-shadow-xl">
                <h1 class="text-5xl sm:text-6xl font-extrabold mb-6 text-white tracking-tight leading-tight">
                    Descubre la magia de <span class="text-orange-400">Lanzarote</span> a pie
                </h1>
                <p class="text-lg sm:text-xl mb-8 text-gray-200 font-medium">
                    Explora senderos mágicos entre paisajes volcánicos, costas salvajes y montañas únicas. 
                    Senal es tu guía interactiva para descubrir cada rincón de la isla de Lanzarote de forma segura.
                    <br><br>
                    <strong>Regístrate gratis</strong> para acceder al mapa topográfico interactivo, consultar los detalles técnicos de cada ruta y revisar el pronóstico del tiempo en tiempo real antes de tu caminata.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center sm:justify-start">
                    @auth
                        <a href="{{ url('/rutas') }}" class="inline-block px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition duration-300 shadow-lg text-lg">
                            Comenzar Aventura
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition duration-300 shadow-lg text-lg">
                            Únete a la Aventura
                        </a>
                    @endauth
                </div>
            </div>
        </div>

    </body>
</html>
