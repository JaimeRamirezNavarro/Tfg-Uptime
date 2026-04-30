<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MetricController;

// Health check endpoint - público para monitoring externo
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'service' => 'uptime-monitor',
        'version' => '2.0.0',
    ], 200);
});

// Esta es la ruta que usará el script de Python
// El agente debe usar un Token válido en el header Authorization: Bearer
Route::post('/metrics', [MetricController::class, 'store']);