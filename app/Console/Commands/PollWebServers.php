<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Metric;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Traits\SendsWhatsAppAlerts;

class PollWebServers extends Command
{
    use SendsWhatsAppAlerts;
    protected $signature = 'uptime:poll-web';
    protected $description = 'Revisar estado de los servidores web sin agente.';

    public function handle()
    {
        $servers = Server::where('is_enabled', true)
            ->whereIn('check_type', ['ping', 'http'])
            ->get();

        if ($servers->isEmpty()) {
            $this->info("No web/ping servers found to poll.");
            return;
        }

        foreach ($servers as $server) {
            $this->info("Revisando servidor: {$server->name} ({$server->check_type})");

            $startTime = microtime(true);
            $success = false;
            $latency = 0;

            try {
                if ($server->check_type === 'http') {
                    // HTTP Check
                    $response = Http::timeout(5)->get($server->ip_address);
                    $success = $response->successful();
                    $latency = round((microtime(true) - $startTime) * 1000, 2);
                } else {
                    // Ping Check (ICMP simulation or simple TCP connect if needed, but let's try ping)
                    $host = $server->ip_address;
                    $output = [];
                    $result = -1;
                    exec("ping -c 1 -W 2 " . escapeshellarg($host), $output, $result);
                    
                    if ($result === 0) {
                        $success = true;
                        // Extract latency from output if possible
                        foreach ($output as $line) {
                            if (str_contains($line, 'time=')) {
                                preg_match('/time=([\d.]+)/', $line, $matches);
                                if (isset($matches[1])) {
                                    $latency = (float)$matches[1];
                                }
                            }
                        }
                    }
                }

                if ($success) {
                    Metric::create([
                        'server_id' => $server->id,
                        'cpu_load' => $latency, // We use this field to store latency for ping/http
                        'ram_usage' => 0,
                        'disk_free' => 0
                    ]);
                    $this->info("Servidor Conectado: {$latency}ms");
                    // Reset alert flag
                    if ($server->status === 'offline') {
                        $this->sendWhatsAppMessage("✅ UPTIME RECUPERADO\nEl servidor *{$server->name}* vuelve a estar en línea.");
                        $server->update(['status' => 'online']);
                    }
                } else {
                    $this->error("Servidor Desconectado");
                    if ($server->status !== 'offline') {
                        $this->sendWhatsAppMessage("❌ ALERTA UPTIME\nEl servidor *{$server->name}* se ha desconectado.");
                        $server->update(['status' => 'offline']);
                    }
                }

            } catch (Exception $e) {
                $this->error("Error polling {$server->name}: " . $e->getMessage());
            }
        }
    }
}
