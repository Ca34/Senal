<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Senal - Rutas de Lanzarote</title>
    
    <!-- Tailwind CSS & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body { font-family: 'Inter', sans-serif; }
        .dark { background-color: #111827; color: white; }
    </style>
</head>
<body class="h-full flex flex-col bg-gray-50 dark:bg-darkbg dark:text-gray-200 transition-colors duration-300">

    <nav class="bg-primary text-white p-4 shadow-md flex justify-between items-center z-50">
        <div class="text-xl font-bold flex items-center">
            <i class="fa-solid fa-mountain mr-2"></i> Senal
        </div>
        <div class="flex items-center gap-4">
            <button id="theme-toggle" class="p-2 rounded-full hover:bg-emerald-600 transition">
                <i class="fa-solid fa-moon" id="theme-icon"></i>
            </button>
            <a href="{{ route('rutas.index') }}" class="hover:text-emerald-200">Catálogo</a>
            
            @auth
                <div class="flex items-center gap-4 border-l pl-4 ml-2">
                    <span class="text-sm hidden sm:inline">Hola, {{ auth()->user()->name }}</span>
                    @if(auth()->user()->hasRole('admin'))
                        <span class="bg-yellow-400 text-black text-[10px] px-1 rounded font-bold">ADMIN</span>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-emerald-200 text-sm">Salir</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="hover:text-emerald-200 text-sm">Entrar</a>
                <a href="{{ route('register') }}" class="bg-white text-primary px-3 py-1 rounded text-sm font-semibold">Registro</a>
            @endauth
        </div>
    </nav>

    <main class="flex-grow relative h-full flex flex-col overflow-hidden">
        @yield('content')
    </main>

    <script>
        // Dark mode logic (Green IT)
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlClass = document.documentElement.classList;

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlClass.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            htmlClass.remove('dark');
        }

        themeToggle.addEventListener('click', () => {
            htmlClass.toggle('dark');
            if (htmlClass.contains('dark')) {
                localStorage.theme = 'dark';
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.theme = 'light';
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        });

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>
</body>
</html>
