<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function download(Server $server)
    {
        $metrics = $server->metrics()->latest()->take(24)->get()->reverse();
        
        $avgCpu = $metrics->avg('cpu_load') ?? 0;
        $avgRam = $metrics->avg('ram_usage') ?? 0;
        
        $pdf = Pdf::loadView('pdf.server-report', [
            'server' => $server,
            'metrics' => $metrics,
            'avgCpu' => round($avgCpu, 2),
            'avgRam' => round($avgRam, 2)
        ]);

        return $pdf->download("reporte-{$server->name}-" . date('Y-m-d') . ".pdf");
    }
}
