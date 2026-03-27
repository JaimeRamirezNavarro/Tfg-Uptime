<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metric;

class MetricController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos que los datos sean correctos y numéricos
        $validated = $request->validate([
            'cpu_load'  => 'required|numeric',
            'ram_usage' => 'required|numeric',
            'disk_free' => 'required|numeric',
        ]);

        // 2. Obtenemos el servidor que envía los datos (gracias al Token)
        $server = $request->user();

        // 3. Guardamos la métrica asociada a ese servidor
        $metric = $server->metrics()->create($validated);

        // 4. Respondemos con éxito
        return response()->json([
            'status' => 'success',
            'message' => 'Métrica registrada correctamente',
            'data' => $metric
        ], 201);
    }
}
