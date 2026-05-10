<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Http;

class Dashboard extends Component
{
    public $newName;
    public $ip;
    public $sshUser = 'root';
    public $sshPassword;
    public $autoDeploy = true;
    public $checkType = 'agent';
    public $open = false; // Drawer state control

    public function addServer()
    {
        $rules = [
            'newName' => 'required|min:3',
            'ip' => 'required|min:3', // Relaxed IP validation to allow domains
            'checkType' => 'required|in:agent,ping,http',
        ];

        if ($this->checkType === 'agent' && $this->autoDeploy) {
            $rules['sshUser'] = 'required';
            $rules['sshPassword'] = 'required';
        }

        $this->validate($rules);
        $apiToken = Str::random(32);

        // Limpiar la IP o dominio introducido por el usuario (elimina http://, https:// y errores comunes como hhtps://)
        $cleanIp = preg_replace('#^h?t?tps?://#i', '', trim($this->ip));
        $cleanIp = rtrim($cleanIp, '/');

        if ($this->checkType === 'agent' && $this->autoDeploy) {
            try {
                $ssh = new \phpseclib3\Net\SSH2($cleanIp, 22, 10); // 10 segundos de timeout
                if (!$ssh->login($this->sshUser, $this->sshPassword)) {
                    $this->addError('sshPassword', 'Fallo de autenticación SSH. Verifica las credenciales.');
                    return;
                }

                // Asegurar que el ZimaBlade se comunique con la IP de Tailscale del Mac y no caiga en su propio localhost
                $apiUrl = url('/api/metrics');

                // Bash daemon script that sleeps every 2 seconds
                // It runs natively sending CPU, RAM and Disk via curl
                $script = <<<EOT
mkdir -p /opt/uptime-agent
cat << 'EOF' > /opt/uptime-agent/agent.sh
#!/bin/bash
while true; do
  # 1. Metricas basicas (con valores por defecto)
  CPU=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\\([0-9.]*\\)%* id.*/\\1/" | awk '{print 100 - \$1}')
  CPU=\${CPU:-0}
  RAM=$(free | grep Mem | awk '{print \$3/\$2 * 100.0}')
  RAM=\${RAM:-0}
  DISK=$(df / | grep / | head -n 1 | awk '{ print \$5}' | sed 's/%//g')
  DISK=\${DISK:-0}

  # 2. Servicios (Top 10 running)
  SERVICES=$(systemctl list-units --type=service --state=running --no-pager | head -n 12 | tail -n +2 | awk '{print "\"" \$1 "\""}' | paste -sd "," -)
  SERVICES=\${SERVICES:-""}

  # 3. Contenedores Docker
  if command -v docker &> /dev/null; then
    CONTAINERS=$(docker ps --format '"{{.Names}} ({{.Status}})"' | paste -sd "," -)
  else
    CONTAINERS=""
  fi
  CONTAINERS=\${CONTAINERS:-""}

  # 4. Enviar via cURL con JSON directo
  PAYLOAD="{\"cpu_load\": \$CPU, \"ram_usage\": \$RAM, \"disk_free\": \$DISK, \"details\": {\"services\": [\$SERVICES], \"containers\": [\$CONTAINERS]}}"
  
  curl -s -X POST $apiUrl \
    -H "Authorization: Bearer $apiToken" \
    -H "Content-Type: application/json" \
    -d "$PAYLOAD" > /dev/null
  sleep 15
done
EOF
chmod +x /opt/uptime-agent/agent.sh
cat << 'EOF' > /etc/systemd/system/uptime-agent.service
[Unit]
Description=Uptime Monitoring Agent
After=network.target

[Service]
ExecStart=/opt/uptime-agent/agent.sh
Restart=always
User=root

[Install]
WantedBy=multi-user.target
EOF
systemctl daemon-reload
systemctl enable uptime-agent.service
systemctl restart uptime-agent.service
EOT;

                $ssh->exec("echo " . escapeshellarg($this->sshPassword) . " | sudo -S bash -c " . escapeshellarg($script) . " || bash -c " . escapeshellarg($script));

            } catch (\Exception $e) {
                $this->addError('ip', 'No se pudo conectar vía SSH: ' . $e->getMessage());
                return;
            }
        }

        Server::create([
            'name' => $this->newName,
            'ip_address' => $cleanIp,
            'api_token' => $apiToken,
            'is_enabled' => true,
            'check_type' => $this->checkType,
            'ssh_user' => ($this->checkType === 'agent' && $this->autoDeploy) ? $this->sshUser : null,
            'ssh_password' => ($this->checkType === 'agent' && $this->autoDeploy) ? $this->sshPassword : null,
        ]);

        $this->reset(['newName', 'ip', 'sshUser', 'sshPassword', 'checkType']);
        session()->flash('message', $this->checkType === 'agent' ? ($this->autoDeploy ? 'Servidor Linux conectado y automatizado remotamente.' : 'Servidor registrado. Listo para sincronizar agente manual.') : 'Monitor de Ping activado para ' . $this->ip);
    }

    #[On('delete-server')]
    public function deleteServer($id)
    {
        Server::find($id)?->delete();
        session()->flash('message', 'Servidor eliminado.');
    }

    #[On('server-updated')]
    public function refresh()
    {
        // Solo refresca la lista
    }

    public function toggleServer($id)
    {
        $server = Server::find($id);
        if ($server) {
            $server->is_enabled = !$server->is_enabled;
            $server->save();

            session()->flash('message', 'Estado de monitorización actualizado. El servidor procesará métricas cuando el agente las envíe.');
        }
    }

    public function reconnectServer($id)
    {
        session()->flash('message', 'Usando Arquitectura Push: Asegúrate de que el agente Python (monitor.py) esté corriendo en el servidor remoto.');
    }

    public function render()
    {
        $servers = Server::all();
        
        // Calcular KPIs para el Mockup
        $activeServers = 0;
        $totalUptime = 0;
        $activeAlerts = 0;
        $metricsLastHour = \App\Models\Metric::where('created_at', '>=', now()->subHour())->count();

        // Solución al problema N+1: Obtenemos los IDs de los servidores y buscamos la última métrica de cada uno de golpe.
        $serverIds = $servers->pluck('id');
        $lastMetrics = \App\Models\Metric::whereIn('server_id', $serverIds)
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')->from('metrics')->groupBy('server_id');
            })->get()->keyBy('server_id');

        foreach ($servers as $server) {
            $lastMetric = $lastMetrics->get($server->id);
            $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
            
            if ($server->check_type !== 'agent' && $lastMetric) {
                $details = $lastMetric->details ?? [];
                $isOnline = $isOnline && ($details['online'] ?? false);
            }
            
            if ($isOnline && $server->is_enabled) {
                $activeServers++;
            }

            if ($server->check_type === 'agent' && $lastMetric && ($lastMetric->cpu_load > 90 || $lastMetric->ram_usage > 95)) {
                $activeAlerts++;
            }
        }

        $averageUptime = $servers->count() > 0 ? round(($activeServers / $servers->count()) * 100, 1) : 0;

        // Chart Data (Últimos 30 minutos agrupados por minuto en SQL)
        $chartDataRaw = \App\Models\Metric::where('created_at', '>=', now()->subMinutes(30))
            ->selectRaw('strftime("%H:%M", created_at) as minute, AVG(cpu_load) as cpu, AVG(ram_usage) as ram')
            ->groupBy('minute')
            ->orderBy('minute', 'asc')
            ->get();

        $chartLabels = $chartDataRaw->pluck('minute')->toArray();
        $chartCpuInfo = $chartDataRaw->pluck('cpu')->map(fn($v) => round($v, 1))->toArray();
        $chartRamInfo = $chartDataRaw->pluck('ram')->map(fn($v) => round($v, 1))->toArray();

        if (empty($chartLabels)) {
            $chartLabels = [now()->format('H:i')];
            $chartCpuInfo = [0];
            $chartRamInfo = [0];
        }

        $chartData = [
            'labels' => $chartLabels,
            'cpu' => $chartCpuInfo,
            'ram' => $chartRamInfo
        ];

        // Logs recientes: Obtenemos el último registro de CADA servidor para evitar spam
        $recentLogs = \App\Models\Metric::with('server')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')->from('metrics')->groupBy('server_id');
            })
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.dashboard', [
            'servers' => $servers,
            'lastMetrics' => $lastMetrics,
            'recentLogs' => $recentLogs,
            'chartData' => $chartData,
            'stats' => [
                'activeServers' => $activeServers,
                'activeAlerts' => $activeAlerts,
                'averageUptime' => $averageUptime,
                'metricsPerHour' => $metricsLastHour
            ]
        ]);
    }
}