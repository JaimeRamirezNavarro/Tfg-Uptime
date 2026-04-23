<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Ajustes - UPTIME')]
class Settings extends Component
{
    public $appName = 'UPTIME';
    public $dbHost = '127.0.0.1';
    public $dbUser = 'root';
    public $dbPass = '';
    public $themeColor;

    public function mount()
    {
        $this->themeColor = session('theme_color', 'emerald');
    }

    public function setTheme($color)
    {
        $this->themeColor = $color;
        session(['theme_color' => $color]);
        $this->dispatch('theme-updated');
    }

    public function saveSettings()
    {
        session()->flash('message', 'Configuración guardada correctamente.');
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
