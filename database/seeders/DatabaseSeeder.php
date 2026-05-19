<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Libro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuarios base para pruebas
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Juan Rafael',
            'email' => 'juan_curbelo@cifpzonzamas.es',
            'password' => Hash::make('2daw.pass'),
        ]);

        // Llamada a los Seeders específicos (Reto DSW)
        $this->call([
            RutaSeeder::class,
            RoleSeeder::class, // Centralizamos la gestión de roles aquí
        ]);
    }
}
