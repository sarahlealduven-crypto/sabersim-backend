<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        $users = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@ejemplo.com',
                'password' => bcrypt('password123'),
                'grade_level' => 11,
                'current_level' => 'grado_11',
                'total_xp' => 1250,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@ejemplo.com',
                'password' => bcrypt('password123'),
                'grade_level' => 11,
                'current_level' => 'grado_11',
                'total_xp' => 980,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ana María Martínez',
                'email' => 'ana.martinez@ejemplo.com',
                'password' => bcrypt('password123'),
                'grade_level' => 10,
                'current_level' => 'grado_10',
                'total_xp' => 750,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Juan Pablo Silva',
                'email' => 'juan.silva@ejemplo.com',
                'password' => bcrypt('password123'),
                'grade_level' => 11,
                'current_level' => 'grado_11',
                'total_xp' => 1500,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Laura Sofía Ramírez',
                'email' => 'lara.ramirez@ejemplo.com',
                'password' => bcrypt('password123'),
                'grade_level' => 10,
                'current_level' => 'grado_10',
                'total_xp' => 620,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'grade_level' => 11,
                'current_level' => 'grado_11',
                'total_xp' => 0,
                'email_verified_at' => now(),
            ],
        ];

        User::insert($users);
    }
}
