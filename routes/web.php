<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-db', function () {
    return [
        'routes_count' => \App\Models\Ruta::count(),
        'users_count' => \App\Models\User::count(),
        'users' => \App\Models\User::select('name', 'email')->get(),
        'kml_dir_exists' => file_exists(database_path('data/kml')),
        'kml_files' => file_exists(database_path('data/kml')) ? array_map('basename', glob(database_path('data/kml/*.kml'))) : [],
    ];
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/rutas', [ViewController::class, 'index'])->name('rutas.index');
    Route::get('/rutas/{id}', [ViewController::class, 'show'])->name('rutas.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de administración (Solo para rol 'admin')
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/rutas/create', [ViewController::class, 'create'])->name('admin.rutas.create');
        Route::post('/rutas', [ViewController::class, 'store'])->name('admin.rutas.store');
        Route::get('/rutas/{id}/edit', [ViewController::class, 'edit'])->name('admin.rutas.edit');
        Route::put('/rutas/{id}', [ViewController::class, 'update'])->name('admin.rutas.update');
        Route::delete('/rutas/{id}', [ViewController::class, 'destroy'])->name('admin.rutas.destroy');
    });
});

require __DIR__.'/auth.php';
