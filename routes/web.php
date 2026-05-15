<?php

use App\Livewire\Dashboard;
use App\Livewire\ServerList;
use App\Livewire\AlertList;
use App\Livewire\LogList;
use App\Livewire\Settings;
use App\Livewire\ServerDetail;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/servidores', ServerList::class)->name('servers');
    Route::get('/alertas', AlertList::class)->name('alerts');
    Route::get('/logs', LogList::class)->name('logs');
    Route::get('/ajustes', Settings::class)->name('settings');
    Route::get('/servidores/{server}', ServerDetail::class)->name('server.detail');
    Route::get('/servidores/{server}/reporte', [ReportController::class, 'download'])->name('server.report');

    Route::get('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});