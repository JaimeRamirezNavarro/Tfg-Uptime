<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'root@uptime.local'],
            [
                'name' => 'root',
                'password' => \Illuminate\Support\Facades\Hash::make('MihermanoesAle1'),
                'email_verified_at' => now(),
            ]
        );
    }
}
