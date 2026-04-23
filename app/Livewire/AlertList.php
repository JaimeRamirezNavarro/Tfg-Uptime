<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metric;
use App\Models\Server;
use Livewire\Attributes\Title;

#[Title('Alertas - UPTIME')]
class AlertList extends Component
{
    public function render()
    {
        // Simulamos alertas buscando métricas que superen el 90%
        $history = Metric::where('cpu_load', '>', 90)
            ->orWhere('ram_usage', '>', 95)
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.alert-list', [
            'history' => $history,
            'servers' => Server::all()
        ]);
    }
}
