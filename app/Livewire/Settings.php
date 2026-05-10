<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Ajustes - UPTIME')]
class Settings extends Component
{
    public $appName = 'UPTIME';
    public $themeColor;
    public $isDark = true;
    public $tempThemeColor;
    public $tempIsDark;

    public $twoFactorSecret;
    public $twoFactorQrCode;
    public $twoFactorEnabled;
    public $verificationCode;

    public function mount()
    {
        $this->appName = session('app_name', 'UPTIME');
        $this->themeColor = session('theme_color', 'emerald');
        $this->isDark = session('theme_mode', 'dark') === 'dark';
        $this->tempThemeColor = $this->themeColor;
        $this->tempIsDark = $this->isDark;
        
        $user = auth()->user();
        $this->twoFactorEnabled = !empty($user->two_factor_secret);
    }

    public function generateTwoFactorSecret()
    {
        $google2fa = app('pragmarx.google2fa');
        $this->twoFactorSecret = $google2fa->generateSecretKey();
        
        $this->twoFactorQrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            auth()->user()->name,
            $this->twoFactorSecret
        );
    }

    public function confirmTwoFactor()
    {
        $this->validate(['verificationCode' => 'required|digits:6']);

        $google2fa = app('pragmarx.google2fa');
        
        if ($google2fa->verifyKey($this->twoFactorSecret, $this->verificationCode)) {
            auth()->user()->update([
                'two_factor_secret' => $this->twoFactorSecret,
                'two_factor_confirmed_at' => now(),
            ]);

            $this->twoFactorEnabled = true;
            $this->twoFactorSecret = null;
            $this->twoFactorQrCode = null;
            
            session()->flash('message', 'Two-Factor Authentication established.');
        } else {
            session()->flash('error', 'Invalid verification code.');
        }
    }

    public function disableTwoFactor()
    {
        auth()->user()->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $this->twoFactorEnabled = false;
        session()->flash('message', 'Two-Factor Authentication disabled.');
    }

    public function saveSettings()
    {
        session([
            'theme_color' => $this->tempThemeColor,
            'theme_mode' => $this->tempIsDark ? 'dark' : 'light',
            'app_name' => $this->appName
        ]);

        session()->flash('message', 'Changes committed to Neural Core.');
        return redirect()->to(route('settings'));
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
