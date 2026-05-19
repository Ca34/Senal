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
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'juan_curbelo@cifpzonzamas.es'],
            [
                'name' => 'Juan Rafael',
                'password' => Hash::make('2daw.pass'),
            ]
        );

        // Llamada a los Seeders específicos (Reto DSW)
        $this->call([
            RutaSeeder::class,
            RoleSeeder::class, // Centralizamos la gestión de roles aquí
        ]);
    }
}
