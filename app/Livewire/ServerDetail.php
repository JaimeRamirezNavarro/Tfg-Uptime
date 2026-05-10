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

    public function toggleServer()
    {
        $this->server->is_enabled = !$this->server->is_enabled;
        $this->server->save();
        session()->flash('message', 'Monitorización ' . ($this->server->is_enabled ? 'reactivada' : 'pausada') . '.');
    }

    public function deleteServer()
    {
        $this->server->delete();
        session()->flash('message', 'Servidor eliminado del inventario.');
        return redirect()->route('dashboard');
    }

    public function loadChartData()
    {
        $query = Metric::where('server_id', $this->server->id);

        if ($this->server->check_type === 'ping' || $this->server->check_type === 'http') {
            // Modo Ping o HTTP: La gráfica muestra Latencia (representada en cpu_load)
            if ($this->timeframe === 'day') {
                $data = $query->where('created_at', '>=', now()->subDay())
                               ->selectRaw('AVG(cpu_load) as cpu, strftime("%H:%M", created_at) as minute')
                               ->groupBy('minute')
                               ->orderBy('minute', 'asc')
                               ->get();
                
                // Agrupar por bloques de 5 minutos para reducir puntos (288 puntos máx)
                $downsampled = $data->groupBy(function($item) {
                    $time = explode(':', $item->minute);
                    $m = floor((int)$time[1] / 5) * 5;
                    return $time[0] . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                })->map(fn($group) => $group->avg('cpu'));

                $this->chartData = [
                    'labels' => $downsampled->keys(),
                    'cpu' => $downsampled->values()->map(fn($v) => round($v, 0)),
                    'ram' => $downsampled->keys()->map(fn() => 0),
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
                               ->selectRaw('AVG(cpu_load) as cpu, AVG(ram_usage) as ram, strftime("%H:%M", created_at) as minute')
                               ->groupBy('minute')
                               ->orderBy('minute', 'asc')
                               ->get();
                
                // Agrupar por bloques de 5 minutos para reducir puntos (288 puntos máx)
                $downsampled = $data->groupBy(function($item) {
                    $time = explode(':', $item->minute);
                    $m = floor((int)$time[1] / 5) * 5;
                    return $time[0] . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                });

                $this->chartData = [
                    'labels' => $downsampled->keys(),
                    'cpu' => $downsampled->map(fn($g) => round($g->avg('cpu'), 1))->values(),
                    'ram' => $downsampled->map(fn($g) => round($g->avg('ram'), 1))->values(),
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
