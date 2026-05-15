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
    public bool $isConnected = false;
    public string $currentPath = '~';

    public function mount(Server $server)
    {
        $this->server = $server;
    }

    private function getSSHConnection()
    {
        if (empty($this->server->ssh_user) || empty($this->server->ssh_password)) {
            throw new Exception("Credenciales SSH no configuradas en este servidor.");
        }

        $ssh = new SSH2($this->server->ip_address);
        if (!$ssh->login($this->server->ssh_user, $this->server->ssh_password)) {
            throw new Exception("Autenticación SSH fallida.");
        }

        return $ssh;
    }

    public function connect()
    {
        try {
            $ssh = $this->getSSHConnection();
            
            // Obtenemos la ruta inicial absoluta para no usar '~' que puede dar problemas
            $pwd = trim($ssh->exec('pwd'));
            $this->currentPath = $pwd;
            
            $this->isConnected = true;
            $this->history = [
                ['type' => 'system', 'text' => "Sesión iniciada. Conectado a {$this->server->name} en {$this->currentPath}"]
            ];
        } catch (Exception $e) {
            $this->history = [
                ['type' => 'error', 'text' => "Error al conectar: " . $e->getMessage()]
            ];
        }
    }

    public function disconnect()
    {
        $this->isConnected = false;
        $this->history = [];
        $this->currentPath = '~';
        $this->command = '';
    }

    public function executeCommand()
    {
        if (empty(trim($this->command)) || !$this->isConnected) return;

        $cmd = trim($this->command);
        $this->history[] = ['type' => 'input', 'text' => $this->server->ssh_user . "@" . $this->server->name . ":" . $this->currentPath . "$ " . $cmd];
        
        try {
            $ssh = $this->getSSHConnection();

            // Si es un comando 'cd', tratamos de ejecutar el cd, luego un pwd para guardar la nueva ruta
            if (str_starts_with($cmd, 'cd ')) {
                $checkCmd = "cd " . escapeshellarg($this->currentPath) . " && {$cmd} && pwd";
                $output = trim($ssh->exec($checkCmd . ' 2>&1'));
                
                // Si el output es una ruta (empieza por /), actualizamos
                if (str_starts_with($output, '/')) {
                    $this->currentPath = $output;
                } else {
                    // Posible error, ej: no existe el directorio
                    $this->history[] = ['type' => 'error', 'text' => $output];
                }
            } else {
                // Comando normal: nos movemos a currentPath y lo ejecutamos
                $fullCmd = "cd " . escapeshellarg($this->currentPath) . " && {$cmd}";
                $output = $ssh->exec($fullCmd . ' 2>&1');
                
                if (empty(trim($output))) {
                    $output = "[Comando ejecutado sin salida]";
                }
                $this->history[] = ['type' => 'output', 'text' => $output];
            }
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
