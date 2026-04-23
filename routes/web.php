<?php

use App\Livewire\Dashboard;
use App\Livewire\ServerList;
use App\Livewire\AlertList;
use App\Livewire\LogList;
use App\Livewire\Settings;
use App\Livewire\ServerDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/servidores', ServerList::class)->name('servers');
Route::get('/alertas', AlertList::class)->name('alerts');
Route::get('/logs', LogList::class)->name('logs');
Route::get('/ajustes', Settings::class)->name('settings');
Route::get('/servidores/{server}', ServerDetail::class)->name('server.detail');