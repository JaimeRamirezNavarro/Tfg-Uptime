<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'jaime@uptime.com'],
            [
                'name' => 'Jaime',
                'password' => bcrypt('password'),
            ]
        );

        // Reference Infrastructure
        \App\Models\Server::updateOrCreate(
            ['name' => 'Google Global (Ping)'],
            [
                'ip_address' => '8.8.8.8',
                'check_type' => 'ping',
                'api_token' => \Illuminate\Support\Str::random(64),
                'is_enabled' => true,
            ]
        );

        \App\Models\Server::updateOrCreate(
            ['name' => 'Cloudflare Edge (Ping)'],
            [
                'ip_address' => '1.1.1.1',
                'check_type' => 'ping',
                'api_token' => \Illuminate\Support\Str::random(64),
                'is_enabled' => true,
            ]
        );

        \App\Models\Server::updateOrCreate(
            ['name' => 'Google Search (HTTP)'],
            [
                'ip_address' => 'https://www.google.com',
                'check_type' => 'http',
                'api_token' => \Illuminate\Support\Str::random(64),
                'is_enabled' => true,
            ]
        );

        \App\Models\Server::updateOrCreate(
            ['name' => 'GitHub Platform (HTTP)'],
            [
                'ip_address' => 'https://github.com',
                'check_type' => 'http',
                'api_token' => \Illuminate\Support\Str::random(64),
                'is_enabled' => true,
            ]
        );

        \App\Models\Server::updateOrCreate(
            ['name' => 'ZimaBlade (Local Node)'],
            [
                'ip_address' => 'casaos-1.tailbd65b1.ts.net',
                'check_type' => 'agent',
                'api_token' => 'MASTER_TOKEN_ZIMA', // Puedes poner el que prefieras
                'is_enabled' => true,
            ]
        );
    }
}
