<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\Metric;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;

#[Signature('servers:check')]
#[Description('Verificar la salud de servidores tipo Ping y HTTP')]
class CheckServerHealth extends Command
{
    public function handle()
    {
        $servers = Server::where('check_type', '!=', 'agent')->get();
        
        $this->info("Iniciando comprobación de " . $servers->count() . " servidores.");

        foreach ($servers as $server) {
            if (!$server->is_enabled) continue;

            $this->comment("Verificando: {$server->name} ({$server->ip_address})");

            if ($server->check_type === 'ping') {
                $ip = escapeshellarg($server->ip_address);
                $startTime = microtime(true);
                exec("ping -c 1 -W 2 $ip", $output, $result);
                $latency = round((microtime(true) - $startTime) * 1000, 0);
                
                $isOnline = ($result === 0);
                
                Metric::create([
                    'server_id' => $server->id,
                    'cpu_load' => $latency,
                    'ram_usage' => 0,
                    'disk_free' => 0,
                    'details' => json_encode(['latency' => $latency, 'online' => $isOnline])
                ]);
            } elseif ($server->check_type === 'http') {
                $url = Str::startsWith($server->ip_address, ['http://', 'https://']) ? $server->ip_address : "https://{$server->ip_address}";
                $startTime = microtime(true);
                $statusCode = 0;

                try {
                    $response = Http::timeout(10)
                        ->connectTimeout(5)
                        ->withoutVerifying()
                        ->withUserAgent('UPTIME-Monitor/2.0')
                        ->get($url);
                    
                    $latency = round((microtime(true) - $startTime) * 1000, 0);
                    $statusCode = $response->status();
                    $isOnline = $statusCode < 400;
                } catch (\Exception $e) {
                    $isOnline = false;
                    $latency = 0;
                }

                Metric::create([
                    'server_id' => $server->id,
                    'cpu_load' => $latency,
                    'ram_usage' => 0,
                    'disk_free' => 0,
                    'details' => json_encode(['latency' => $latency, 'status_code' => $statusCode, 'online' => $isOnline])
                ]);
            }
        }

        $this->info("Comprobación finalizada.");
    }
}
