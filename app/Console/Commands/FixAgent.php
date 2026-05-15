<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

class FixAgent extends Command
{
    protected $signature = 'fix:agent';
    protected $description = 'Arregla automáticamente el agente en la Zimablade';

    public function handle()
    {
        // 1. Obtener el servidor correcto (el que ha enviado datos más recientemente o el único)
        $server = Server::where('check_type', 'agent')->orderBy('id', 'desc')->first();
        
        if (!$server) {
            $this->error("No se encontró ningún servidor agente en la base de datos.");
            return;
        }

        $this->info("Arreglando agente para el servidor: {$server->name} (Token: {$server->api_token})");

        // 2. Definir el script con RUTAS ABSOLUTAS y PATH forzado para evitar problemas de systemd
        $apiUrl = "http://localhost:8080/api/metrics"; 
        $token = $server->api_token;

        $this->info("---------------------------------------------------------");
        $this->info("Copia y pega TODO este bloque de texto en la terminal de tu Zimablade (¡Fuera de docker!):");
        $this->info("---------------------------------------------------------");
        
        $output = <<<EOT
sudo mkdir -p /opt/uptime-agent
sudo cat << 'EOF' > /tmp/agent.sh
#!/bin/bash
export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
export LC_ALL=C

API_URL="{$apiUrl}"
API_TOKEN="{$token}"

while true; do
  CPU=\$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\\\\1/" | awk '{print 100 - \$1}')
  CPU=\${CPU:-0}
  RAM=\$(free | grep Mem | awk '{print \$3/\$2 * 100.0}')
  RAM=\${RAM:-0}
  DISK=\$(df / | grep / | head -n 1 | awk '{ print \$5}' | sed 's/%//g')
  DISK=\${DISK:-0}

  SERVICES=\$(systemctl list-units --type=service --state=running --no-pager | head -n 12 | tail -n +2 | awk '{print "\"" \$1 "\""}' | paste -sd "," -)
  SERVICES=\${SERVICES:-""}

  if command -v docker &> /dev/null; then
    CONTAINERS=\$(docker ps --format '"{{.Names}} ({{.Status}})"' | paste -sd "," -)
  else
    CONTAINERS=""
  fi
  CONTAINERS=\${CONTAINERS:-""}

  PAYLOAD="{\"cpu_load\": \$CPU, \"ram_usage\": \$RAM, \"disk_free\": \$DISK, \"details\": {\"services\": [\$SERVICES], \"containers\": [\$CONTAINERS]}}"
  
  curl -s -X POST "\$API_URL" \
    -H "Authorization: Bearer \$API_TOKEN" \
    -H "Content-Type: application/json" \
    -d "\$PAYLOAD" > /dev/null

  sleep 15
done
EOF
sudo cp /tmp/agent.sh /opt/uptime-agent/agent.sh
sudo chmod +x /opt/uptime-agent/agent.sh

sudo cat << 'EOF' > /etc/systemd/system/uptime-agent.service
[Unit]
Description=Uptime Monitoring Agent for ZimaBlade
After=network.target docker.service
Wants=docker.service

[Service]
Type=simple
User=root
ExecStart=/bin/bash /opt/uptime-agent/agent.sh
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable uptime-agent.service
sudo systemctl restart uptime-agent.service
echo "Agente instalado correctamente"
EOT;

        $this->line($output);
        $this->info("---------------------------------------------------------");
    }
}
