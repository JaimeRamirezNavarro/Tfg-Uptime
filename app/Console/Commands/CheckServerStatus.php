<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use Illuminate\Support\Facades\Http;

class CheckServerStatus extends Command
{
    protected $signature = 'uptime:check-status';
    protected $description = 'Verifica el estado de los servidores y envía alertas por WhatsApp si están caídos.';

    public function handle()
    {
        $servers = Server::where('is_enabled', true)->get();

        foreach ($servers as $server) {
            $lastMetric = $server->metrics()->latest()->first();
            $isOffline = !$lastMetric || $lastMetric->created_at->diffInSeconds(now()) > 60;
            if ($server->check_type !== 'agent' && $lastMetric) {
                $details = json_decode($lastMetric->details, true) ?? [];
                $isOffline = $isOffline || !($details['online'] ?? false);
            }

            if ($isOffline) {
                // Solo alertar si no se ha alertado en la última hora para evitar spam
                if (!$server->last_alerted_at || $server->last_alerted_at->diffInHours(now()) >= 1) {
                    $this->sendWhatsAppAlert($server);
                    $server->update(['last_alerted_at' => now()]);
                    $this->info("Alerta enviada para {$server->name}");
                }
            } else {
                // Si vuelve a estar online y tenía una alerta activa, resetearla
                if ($server->last_alerted_at) {
                    $server->update(['last_alerted_at' => null]);
                    $this->info("Servidor {$server->name} vuelve a estar ONLINE. Reset de alerta.");
                }
            }
        }
    }

    protected function sendWhatsAppAlert($server)
    {
        // NOTA: Para el TFG, estos datos se pueden configurar en .env
        $phone = env('WHATSAPP_PHONE', 'TU_NUMERO'); 
        $apikey = env('WHATSAPP_APIKEY', 'TU_APIKEY');
        
        if ($phone === 'TU_NUMERO' || $apikey === 'TU_APIKEY') {
            $this->warn("WhatsApp no configurado para {$server->name}. Configura WHATSAPP_PHONE y WHATSAPP_APIKEY en el .env");
            return;
        }

        $message = "⚠️ *ALERTA UPTIME* ⚠️\n\nEl servidor *{$server->name}* ({$server->ip_address}) esta *OFFLINE*.\n\nPor favor, revisa el estado del nodo.";

        try {
            Http::get("https://api.callmebot.com/whatsapp.php", [
                'phone' => $phone,
                'text' => $message,
                'apikey' => $apikey
            ]);
        } catch (\Exception $e) {
            $this->error("Error enviando WhatsApp: " . $e->getMessage());
        }
    }
}
