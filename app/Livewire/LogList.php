<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metric;
use Livewire\Attributes\Title;

#[Title('Logs - UPTIME')]
class LogList extends Component
{
    public $filter = 'ALL';

    public function setFilter($level)
    {
        $this->filter = $level;
    }

    public function render()
    {
        $query = Metric::with('server')->latest();

        if ($this->filter === 'ALERT') {
            $query->where('cpu_load', '>', 90)->orWhere('ram_usage', '>', 95);
        } elseif ($this->filter === 'WARN') {
            $query->whereBetween('cpu_load', [70, 90])->orWhereBetween('ram_usage', [80, 95]);
        } elseif ($this->filter === 'INFO') {
            $query->where('cpu_load', '<', 70)->where('ram_usage', '<', 80);
        }

        return view('livewire.log-list', [
            'logs' => $query->take(50)->get()
        ]);
    }
}
