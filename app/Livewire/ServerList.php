<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;
use Livewire\Attributes\Title;

use phpseclib3\Net\SSH2;

#[Title('Servidores - UPTIME')]
class ServerList extends Component
{
    public $name, $ip_address, $sshUser = 'root', $sshPassword;
    public $autoDeploy = true;
    public $checkType = 'agent';

    protected $rules = [
        'name' => 'required|min:3',
        'ip_address' => 'required|min:3',
        'checkType' => 'required|in:agent,ping',
    ];

    public function saveServer()
    {
        $rules = [
            'name' => 'required|min:3',
            'ip_address' => 'required|min:3',
        ];

        if ($this->checkType === 'agent' && $this->autoDeploy) {
            $rules['sshUser'] = 'required';
            $rules['sshPassword'] = 'required';
        }

        $this->validate($rules);
        $apiToken = \Illuminate\Support\Str::random(32);

        if ($this->checkType === 'agent' && $this->autoDeploy) {
            try {
                $ssh = new SSH2($this->ip_address);
                if (!$ssh->login($this->sshUser, $this->sshPassword)) {
                    $this->addError('sshPassword', 'Fallo de autenticación SSH. Verifica las credenciales.');
                    return;
                }

                $apiUrl = url('/api/metrics');

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
    -d "{\\"cpu_load\\": \$CPU, \\"ram_usage\\": \$RAM, \\"disk_free\\": \$DISK, \\"details\\": \\"{\\\\"services\\\\": [], \\\\"containers\\\\": []}\\"}" > /dev/null
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

                $ssh->exec("sudo bash -c " . escapeshellarg($script) . " || bash -c " . escapeshellarg($script));

            } catch (\Exception $e) {
                $this->addError('ip_address', 'No se pudo conectar vía SSH: ' . $e->getMessage());
                return;
            }
        }

        Server::create([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'api_token' => $apiToken,
            'is_enabled' => true,
            'check_type' => $this->checkType,
            'ssh_user' => ($this->checkType === 'agent' && $this->autoDeploy) ? $this->sshUser : null,
            'ssh_password' => ($this->checkType === 'agent' && $this->autoDeploy) ? $this->sshPassword : null,
        ]);

        $this->reset(['name', 'ip_address', 'sshUser', 'sshPassword', 'checkType']);
        session()->flash('message', $this->checkType === 'agent' ? ($this->autoDeploy ? 'Servidor "' . $this->name . '" vinculado y automatizado.' : 'Servidor vinculado. Listo para el agente Manual.') : 'Monitor Ping activado para ' . $this->name);
        $this->dispatch('server-saved');
    }
    public function toggleServer($id)
    {
        $server = Server::find($id);
        if ($server) {
            $server->is_enabled = !$server->is_enabled;
            $server->save();

            session()->flash('message', 'Monitorización ' . ($server->is_enabled ? 'reactivada' : 'pausada') . ' para ' . $server->name);
        }
    }

    public function deleteServer($id)
    {
        Server::find($id)?->delete();
        session()->flash('message', 'Servidor eliminado del inventario.');
    }

    public function reconnectServer($id)
    {
        session()->flash('message', 'Usando Arquitectura Push: Asegúrate de iniciar el agente python remotamente.');
    }

    public function render()
    {
        return view('livewire.server-list', [
            'servers' => Server::all()
        ]);
    }
}
