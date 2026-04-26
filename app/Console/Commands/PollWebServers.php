<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Metric;
use Illuminate\Support\Facades\Http;
use Exception;

class PollWebServers extends Command
{
    protected $signature = 'uptime:poll-web';
    protected $description = 'Perform Ping and HTTP checks for non-agent nodes.';

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
            $this->info("Checking node: {$server->name} ({$server->check_type})");

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
                    $this->info("Node ONLINE: {$latency}ms");
                } else {
                    $this->error("Node OFFLINE");
                }

            } catch (Exception $e) {
                $this->error("Error polling {$server->name}: " . $e->getMessage());
            }
        }
    }
}
