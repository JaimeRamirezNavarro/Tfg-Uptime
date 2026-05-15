<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;

class DebugAgent extends Command
{
    protected $signature = 'debug:agent';
    protected $description = 'Verifica los datos exactos que está recibiendo el servidor del agente';

    public function handle()
    {
        $servers = Server::where('check_type', 'agent')->get();
        if ($servers->isEmpty()) {
            $this->error("No hay servidores tipo agente.");
            return;
        }

        foreach ($servers as $server) {
            $this->info("Servidor: " . $server->name . " (ID: " . $server->id . ")");
            
            $details = $server->last_sync_details;
            
            if (is_null($details)) {
                $this->error("  -> last_sync_details es NULL en la base de datos.");
            } elseif (is_string($details)) {
                $this->error("  -> last_sync_details está guardado como STRING. Hay un problema con el formato JSON: " . $details);
            } elseif (is_array($details)) {
                $this->info("  -> Formato correcto (Array).");
                $this->line("  -> Servicios recibidos: " . count($details['services'] ?? []));
                $this->line("  -> Contenedores recibidos: " . count($details['containers'] ?? []));
                
                if (empty($details['services']) && empty($details['containers'])) {
                    $this->warn("  -> ¡El agente está enviando las listas vacías!");
                    $this->warn("     (Probablemente el script del agente no tenga permisos o docker/systemctl fallaron).");
                }
            } else {
                $this->error("  -> Formato desconocido.");
            }
            $this->line("-----------------------------------");
        }
    }
}
