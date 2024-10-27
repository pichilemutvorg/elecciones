<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Marco González Luengo',
            'email' => 'dev@nqu.me',
            'password' => '$2y$12$lQhClMOF3dWDzVC1Ogf1p.MOI.6VbM4cwaa5kGyQGFz3BrCuAy2oO',
            'remember_token' => 'Oq0aNuiVr6',
        ]);
    }
}
