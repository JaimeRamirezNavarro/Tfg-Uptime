<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;
use Livewire\Attributes\On;

class ServerCard extends Component
{
    public Server $server;
    public $activeDetailsId = null; // En este componente solo manejamos "abierto/cerrado"

    public function mount(Server $server)
    {
        $this->server = $server;
    }

    public function toggleServer()
    {
        $this->server->update([
            'is_enabled' => !$this->server->is_enabled
        ]);
        
        $this->dispatch('server-updated');
    }

    public function delete()
    {
        $this->dispatch('delete-server', id: $this->server->id);
    }

    public function toggleDetails()
    {
        $this->activeDetailsId = $this->activeDetailsId ? null : $this->server->id;
    }

    #[On('echo:server.{server.id},MetricUpdated')]
    public function refreshMetric($event)
    {
        // El componente se refresca automáticamente al recibir el evento
        // Podemos registrar si queremos algún log extra
    }

    public function render()
    {
        // Recargar el modelo para tener las últimas métricas y el estado online actualizado
        $this->server->load(['metrics' => function($query) {
            $query->latest()->limit(20);
        }]);

        // Calcular estado online
        $lastMetric = $this->server->metrics->first();
        $this->server->is_online = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 40;

        // Extraer los datos de Docker y Servicios del JSON guardado
        $services = [];
        $containers = [];
        if ($lastMetric && !empty($lastMetric->details)) {
            $details = json_decode($lastMetric->details, true);
            $services = $details['services'] ?? [];
            $containers = $details['containers'] ?? [];
        }

        return view('livewire.server-card', [
            'services' => $services,
            'containers' => $containers
        ]);
    }
}
