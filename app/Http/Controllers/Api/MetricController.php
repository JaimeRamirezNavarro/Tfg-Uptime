<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metric;

use App\Events\MetricUpdated;

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

        // 2. Obtenemos el servidor (Sanctum o columna api_token como fallback)
        $server = $request->user() ?: \App\Models\Server::where('api_token', $request->bearerToken())->first();

        if (!$server) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // 3. Guardamos la métrica
        $metric = $server->metrics()->create([
            'cpu_load'  => $validated['cpu_load'],
            'ram_usage' => $validated['ram_usage'],
            'disk_free' => $validated['disk_free'],
        ]);

        // 4. Actualizamos los detalles del servidor
        if (!empty($validated['details'])) {
            $server->update(['last_sync_details' => $validated['details']]);
        }

        // 5. DISPARAMOS EL EVENTO EN TIEMPO REAL (REVERB)
        broadcast(new MetricUpdated($server, $metric))->toOthers();

        return response()->json([
            'status' => 'success',
            'data' => $metric
        ], 201);
    }
}
