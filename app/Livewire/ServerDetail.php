<?php

namespace App\Livewire;

use App\Models\Server;
use App\Models\Metric;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

class ServerDetail extends Component
{
    public Server $server;
    public $timeframe = 'day'; // 'day', 'month', 'year'
    public $chartData = [];

    public function mount(Server $server)
    {
        $this->server = $server;
        $this->loadChartData();
    }

    public function setTimeframe($tf)
    {
        $this->timeframe = $tf;
        $this->loadChartData();
        $this->dispatch('update-chart', data: $this->chartData);
    }

    public function poll()
    {
        $this->loadChartData();
        // Disparamos un evento para que JS actualice la gráfica sin recargar toda la página
        $this->dispatch('update-chart', data: $this->chartData);
    }

    public function loadChartData()
    {
        $query = Metric::where('server_id', $this->server->id);

        if ($this->server->check_type === 'ping' || $this->server->check_type === 'http') {
            // Modo Ping o HTTP: La gráfica muestra Latencia (representada en cpu_load)
            if ($this->timeframe === 'day') {
                $data = $query->where('created_at', '>=', now()->subDay())
                              ->orderBy('created_at', 'asc')
                              ->get(['cpu_load', 'created_at']);
                
                $this->chartData = [
                    'labels' => $data->map(fn($m) => $m->created_at->format('H:i')),
                    'cpu' => $data->pluck('cpu_load'), // Realmente es Latencia
                    'ram' => $data->map(fn() => 0),
                ];
            } else {
                // Mes/Año para Ping
                $data = $query->where('created_at', '>=', $this->timeframe === 'month' ? now()->subMonth() : now()->subYear())
                              ->selectRaw('AVG(cpu_load) as cpu, DATE(created_at) as date')
                              ->groupBy('date')
                              ->orderBy('date', 'asc')
                              ->get();

                $this->chartData = [
                    'labels' => $data->pluck('date'),
                    'cpu' => $data->pluck('cpu')->map(fn($v) => round($v, 0)),
                    'ram' => $data->map(fn() => 0),
                ];
            }
        } else {
            // Modo Agente: Original CPU/RAM
            if ($this->timeframe === 'day') {
                $data = $query->where('created_at', '>=', now()->subDay())
                              ->orderBy('created_at', 'asc')
                              ->get(['cpu_load', 'ram_usage', 'created_at']);
                
                $this->chartData = [
                    'labels' => $data->map(fn($m) => $m->created_at->format('H:i')),
                    'cpu' => $data->pluck('cpu_load'),
                    'ram' => $data->pluck('ram_usage'),
                ];
            } elseif ($this->timeframe === 'month') {
                $data = $query->where('created_at', '>=', now()->subMonth())
                              ->selectRaw('AVG(cpu_load) as cpu, AVG(ram_usage) as ram, DATE(created_at) as date')
                              ->groupBy('date')
                              ->orderBy('date', 'asc')
                              ->get();

                $this->chartData = [
                    'labels' => $data->pluck('date'),
                    'cpu' => $data->pluck('cpu'),
                    'ram' => $data->pluck('ram'),
                ];
            } else {
                $data = $query->where('created_at', '>=', now()->subYear())
                              ->selectRaw('AVG(cpu_load) as cpu, AVG(ram_usage) as ram, strftime("%m", created_at) as month_num')
                              ->groupBy('month_num')
                              ->orderBy('month_num', 'asc')
                              ->get();

                $monthNames = ['01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'];

                $this->chartData = [
                    'labels' => $data->map(fn($m) => $monthNames[$m->month_num] ?? $m->month_num),
                    'cpu' => $data->pluck('cpu')->map(fn($v) => round($v, 1)),
                    'ram' => $data->pluck('ram')->map(fn($v) => round($v, 1)),
                ];
            }
        }
    }

    #[Title('Detalles de Servidor - UPTIME')]
    public function render()
    {
        $lastMetrics = $this->server->metrics()->latest()->take(10)->get();
        return view('livewire.server-detail', [
            'lastMetrics' => $lastMetrics
        ]);
    }
}
