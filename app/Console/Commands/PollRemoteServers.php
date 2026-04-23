<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Metric;
use phpseclib3\Net\SSH2;
use Throwable;

class PollRemoteServers extends Command
{
    protected $signature = 'servers:poll';
    protected $description = 'Obtiene CPU, RAM y Disco de los servidores registrados';

    public function handle()
    {
        $servers = Server::all();

        if ($servers->isEmpty()) {
            $this->warn("No hay servidores en la base de datos.");
            return;
        }

        foreach ($servers as $server) {
            $serverId = $server->id;
            $this->info("--------------------------------------------------");
            $this->info("Conectando a: {$server->name} (ID: {$serverId})...");

            try {
                $ssh = new SSH2($server->ip_address);
                $ssh->setTimeout(10);

                if (!$ssh->login($server->ssh_user, $server->ssh_password)) {
                    $this->error("Error de Login en {$server->name}");
                    continue;
                }

                // Ejecución de comandos PowerShell para Windows
                $cpuRaw  = $ssh->exec('powershell -command "(Get-CimInstance Win32_Processor).LoadPercentage"');
                $ramRaw  = $ssh->exec('powershell -command "$os = Get-CimInstance Win32_OperatingSystem; [math]::Round(($os.TotalVisibleMemorySize - $os.FreePhysicalMemory) / $os.TotalVisibleMemorySize * 100)"');
                $diskRaw = $ssh->exec('powershell -command "$disk = Get-CimInstance Win32_LogicalDisk -Filter \"DeviceID=\'C:\'\"; [math]::Round(($disk.FreeSpace / $disk.Size) * 100)"');

                // Limpieza y conversión a números
                $cpu  = is_numeric(trim($cpuRaw))  ? (float)trim($cpuRaw)  : 0;
                $ram  = is_numeric(trim($ramRaw))  ? (float)trim($ramRaw)  : 0;
                $disk = is_numeric(trim($diskRaw)) ? (float)trim($diskRaw) : 0;

                // Guardado manual ultra-seguro
                $nuevaMetrica = new Metric();
                $nuevaMetrica->server_id = (int)$serverId;
                $nuevaMetrica->cpu_load  = $cpu;
                $nuevaMetrica->ram_usage = $ram;
                $nuevaMetrica->disk_free = $disk; // Aquí enviamos el dato que faltaba
                $nuevaMetrica->save();

                $this->info("¡ÉXITO! >> CPU: {$cpu}% | RAM: {$ram}% | DISCO LIBRE: {$disk}%");

            } catch (Throwable $e) {
                $this->error("Fallo en {$server->name}: " . $e->getMessage());
            }
        }

        $this->info("--------------------------------------------------");
        $this->info("Sincronización finalizada.");
    }
}