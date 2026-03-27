<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Server;

class Dashboard extends Component
{
    public function render()
    {
        $servers = \App\Models\Server::with(['metrics' => function($query) {
            $query->latest()->limit(1);
        }])->get();

        return view('livewire.dashboard', [
            'servers' => $servers
        ]);
    }
}