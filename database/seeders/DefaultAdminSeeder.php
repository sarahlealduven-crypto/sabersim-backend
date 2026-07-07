<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Default Admin',
                'password' => Hash::make('password'),
                'grade_level' => null,
                'current_level' => 1,
                'total_xp' => 0,
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
