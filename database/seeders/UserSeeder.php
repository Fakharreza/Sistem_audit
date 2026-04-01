<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bikin akun Auditor
        User::create([
            'name' => 'Fakhar (Auditor)',
            'email' => 'auditor@pal.co.id',
            'password' => Hash::make('password123'),
            'role' => 'auditor',
        ]);

        // Bikin akun Manajer TI
        User::create([
            'name' => 'Manajer TI',
            'email' => 'manajer@pal.co.id',
            'password' => Hash::make('password123'),
            'role' => 'manajer',
        ]);
    }
}
