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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Juan Rafael',
            'email' => 'juan_curbelo@cifpzonzamas.es',
            'password' => Hash::make('2daw.pass'),
        ]);

        $this->call([
            RutaSeeder::class,
        ]);

        // Roles and permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);

        $createPost = Permission::firstOrCreate(['name' => 'create post']);
        $editPost = Permission::firstOrCreate(['name' => 'edit post']);
        $deletePost = Permission::firstOrCreate(['name' => 'delete post']);

        $admin->givePermissionTo($createPost, $editPost, $deletePost);
        $editor->givePermissionTo($editPost);

        $user = User::where('email', 'juan_curbelo@cifpzonzamas.es')->first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
