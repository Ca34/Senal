<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Reto SGY/DSW: Inicialización de Roles y Permisos.
     */
    public function run(): void
    {
        // 1. Crear Roles principales
        $rolAdmin = Role::firstOrCreate(['name' => 'admin']);
        $rolUsuario = Role::firstOrCreate(['name' => 'usuario']);

        // 2. Crear Permisos básicos (Ejemplos para el proyecto)
        $permisoGestionarRutas = Permission::firstOrCreate(['name' => 'gestionar rutas']);
        $permisoVerContenido = Permission::firstOrCreate(['name' => 'ver contenido']);

        // 3. Asignar Permisos a Roles
        $rolAdmin->givePermissionTo($permisoGestionarRutas, $permisoVerContenido);
        $rolUsuario->givePermissionTo($permisoVerContenido);

        // 4. Crear Usuarios de prueba con sus roles correspondientes
        
        // Administrador del sistema
        $admin = User::firstOrCreate(
            ['email' => 'admin@senal.es'],
            [
                'name' => 'Administrador Senal',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole($rolAdmin);

        // Usuario normal (Senderista)
        $senderista = User::firstOrCreate(
            ['email' => 'user@senal.es'],
            [
                'name' => 'Senderista de Lanzarote',
                'password' => Hash::make('user123'),
            ]
        );
        $senderista->assignRole($rolUsuario);

        // Usuario específico para el profesor (si existe en los requisitos)
        $profe = User::where('email', 'juan_curbelo@cifpzonzamas.es')->first();
        if ($profe) {
            $profe->assignRole($rolAdmin);
        }
    }
}
