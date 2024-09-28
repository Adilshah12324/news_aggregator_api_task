<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin'),
            'age' => 34,
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'user',
            'email' => 'user@email.com',
            'password' => Hash::make('user'),
            'age' => 25,
            'role' => 'visitor',
        ]);
    }
}
