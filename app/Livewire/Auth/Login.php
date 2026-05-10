<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[Title('Login - UPTIME')]
class Login extends Component
{
    public $username = '';
    public $password = '';
    public $token = '';
    public $show2fa = false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        // Ensure root user exists for this demo
        if (User::where('name', 'root')->count() === 0) {
            User::create([
                'name' => 'root',
                'email' => 'root@uptime.local',
                'password' => Hash::make('MihermanoesAle1'),
            ]);
        }
    }

    public function toggleTheme()
    {
        $current = session('theme_mode', 'dark');
        session(['theme_mode' => $current === 'dark' ? 'light' : 'dark']);
    }

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('name', $this->username)->first();

        if ($user && Hash::check($this->password, $user->password)) {
            if ($user->two_factor_secret) {
                $this->show2fa = true;
                return;
            }

            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        session()->flash('error', 'Invalid credentials.');
    }

    public function verify2fa()
    {
        $this->validate(['token' => 'required|digits:6']);

        $user = User::where('name', $this->username)->first();
        
        $google2fa = app('pragmarx.google2fa');
        
        if ($google2fa->verifyKey($user->two_factor_secret, $this->token)) {
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        session()->flash('error', 'Invalid 2FA token.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app_auth');
    }
}
