<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;
use phpseclib3\Net\SSH2;
use Exception;

class ServerTerminal extends Component
{
    public Server $server;
    public string $command = '';
    public array $history = [];

    public function mount(Server $server)
    {
        $this->server = $server;
        $this->history[] = ['type' => 'system', 'text' => "Conectado a la terminal web de {$server->name}."];
    }

    public function executeCommand()
    {
        if (empty(trim($this->command))) return;

        $cmd = trim($this->command);
        $this->history[] = ['type' => 'input', 'text' => "jaime@{$this->server->name}:~$ " . $cmd];
        
        try {
            if (empty($this->server->ssh_user) || empty($this->server->ssh_password)) {
                throw new Exception("Credenciales SSH no configuradas en este servidor.");
            }

            $ssh = new SSH2($this->server->ip_address);
            if (!$ssh->login($this->server->ssh_user, $this->server->ssh_password)) {
                throw new Exception("Autenticación SSH fallida.");
            }

            $output = $ssh->exec($cmd . ' 2>&1');
            if (empty(trim($output))) {
                $output = "[Comando ejecutado sin salida]";
            }
            $this->history[] = ['type' => 'output', 'text' => $output];
        } catch (Exception $e) {
            $this->history[] = ['type' => 'error', 'text' => $e->getMessage()];
        }

        $this->command = '';
    }

    public function render()
    {
        return view('livewire.server-terminal');
    }
}
