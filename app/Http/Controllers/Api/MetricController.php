<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metric;
use App\Models\Server;
use App\Events\MetricUpdated;
use Illuminate\Support\Facades\Log;

class MetricController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos que los datos sean correctos
        $validated = $request->validate([
            'cpu_load'  => 'required|numeric',
            'ram_usage' => 'required|numeric',
            'disk_free' => 'required|numeric',
            'details'   => 'nullable|string',
        ]);

        // 2. Obtenemos el servidor usando el token de la API
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token requerido'], 401);
        }

        $server = Server::where('api_token', $token)->first();

        if (!$server) {
            Log::warning("Intento de conexión con token inválido: " . substr($token, 0, 8) . '...');
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Verificar si el servidor está habilitado
        if (!$server->is_enabled) {
            return response()->json(['error' => 'Servidor deshabilitado'], 403);
        }

        // 3. Guardamos la métrica
        $metric = $server->metrics()->create([
            'cpu_load'  => $validated['cpu_load'],
            'ram_usage' => $validated['ram_usage'],
            'disk_free' => $validated['disk_free'],
            'details'   => $validated['details'] ?? null,
        ]);

        // 4. Actualizamos los detalles del servidor
        if (!empty($validated['details'])) {
            $server->update(['last_sync_details' => $validated['details']]);
        }

        // 5. Disparamos el evento en tiempo real (Reverb)
        broadcast(new MetricUpdated($server, $metric))->toOthers();

        return response()->json([
            'status' => 'success',
            'data' => $metric
        ], 201);
    }
}
