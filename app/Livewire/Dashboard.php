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
    public $checkType = 'agent'; // New property

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
  CPU=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\\([0-9.]*\\)%* id.*/\\1/" | awk '{print 100 - $1}')
  RAM=$(free | grep Mem | awk '{print $3/$2 * 100.0}')
  DISK=$(df / | grep / | awk '{ print $5}' | sed 's/%//g')
  curl -s -X POST $apiUrl \
    -H "Authorization: Bearer $apiToken" \
    -H "Content-Type: application/json" \
    -d "{\"cpu_load\": \$CPU, \"ram_usage\": \$RAM, \"disk_free\": \$DISK, \"details\": \"{}\"}" > /dev/null
  sleep 2
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
                $details = json_decode($lastMetric->details, true) ?? [];
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

        // Chart Data (Últimos 30 minutos agrupados por minuto)
        $recentMetrics = \App\Models\Metric::where('created_at', '>=', now()->subMinutes(30))->orderBy('created_at')->get();
        $chartLabels = [];
        $chartCpuInfo = [];
        $chartRamInfo = [];

        $grouped = $recentMetrics->groupBy(function($item) {
            return $item->created_at->format('H:i');
        });

        foreach($grouped as $time => $metricsGroup) {
            $chartLabels[] = $time;
            $chartCpuInfo[] = round($metricsGroup->avg('cpu_load'), 1);
            $chartRamInfo[] = round($metricsGroup->avg('ram_usage'), 1);
        }

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

    /**
     * Este método se ejecuta vía wire:poll para actualizar las métricas de red
     * de forma asíncrona sin bloquear el renderizado del dashboard.
     */
    public function checkServers()
    {
        $servers = Server::where('check_type', '!=', 'agent')->get();
        
        foreach ($servers as $server) {
            if (!$server->is_enabled) continue;

            if ($server->check_type === 'ping') {
                $ip = escapeshellarg($server->ip_address);
                $startTime = microtime(true);
                exec("ping -c 1 -W 1 $ip", $output, $result);
                $latency = round((microtime(true) - $startTime) * 1000, 0);
                
                $isOnline = ($result === 0);
                
                \App\Models\Metric::create([
                    'server_id' => $server->id,
                    'cpu_load' => $latency, // Guardamos la latencia real en MS
                    'ram_usage' => 0,
                    'disk_free' => 0,
                    'details' => json_encode(['latency' => $latency, 'online' => $isOnline])
                ]);
            } elseif ($server->check_type === 'http') {
                $url = Str::startsWith($server->ip_address, ['http://', 'https://']) ? $server->ip_address : "https://{$server->ip_address}";
                $startTime = microtime(true);
                $statusCode = 0;

                try {
                    $response = Http::timeout(5)
                        ->connectTimeout(3)
                        ->withoutVerifying()
                        ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                        ->get($url);
                    
                    $latency = round((microtime(true) - $startTime) * 1000, 0);
                    $statusCode = $response->status();
                    $isOnline = $statusCode < 400;
                } catch (\Exception $e) {
                    $isOnline = false;
                    $latency = 0;
                    \Illuminate\Support\Facades\Log::error("HTTP Check Error for $url: " . $e->getMessage());
                }

                \App\Models\Metric::create([
                    'server_id' => $server->id,
                    'cpu_load' => $latency, // Guardamos la latencia real en MS
                    'ram_usage' => 0,
                    'disk_free' => 0,
                    'details' => json_encode(['latency' => $latency, 'status_code' => $statusCode, 'online' => $isOnline])
                ]);
            }
        }
    }
}