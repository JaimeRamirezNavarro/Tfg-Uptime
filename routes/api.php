<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MetricController;

// Esta es la ruta que usará el script de Python
// El middleware 'auth:sanctum' obliga a que el agente use un Token válido [cite: 40, 77]
Route::middleware('auth:sanctum')->post('/metrics', [MetricController::class, 'store']);