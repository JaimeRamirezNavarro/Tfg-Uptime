<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Servidor - {{ $server->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        h1 { color: #2ecc71; border-bottom: 2px solid #2ecc71; padding-bottom: 10px; }
        .stats-grid { width: 100%; margin-top: 30px; }
        .stats-grid td { width: 50%; padding: 20px; background: #f9f9f9; text-align: center; border: 1px solid #eee; }
        .stats-grid h3 { margin: 0; font-size: 14px; color: #777; text-transform: uppercase; }
        .stats-grid p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #333; }
        .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table th { background-color: #2ecc71; color: white; }
    </style>
</head>
<body>
    <h1>Informe de Rendimiento: {{ $server->name }}</h1>
    <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>
    <p>Estado actual: <strong>{{ strtoupper($server->status) }}</strong></p>

    <table class="stats-grid">
        <tr>
            <td>
                <h3>Media CPU (24h)</h3>
                <p>{{ $avgCpu }}%</p>
            </td>
            <td>
                <h3>Media RAM (24h)</h3>
                <p>{{ $avgRam }}%</p>
            </td>
        </tr>
    </table>

    <h2 style="margin-top: 40px;">Últimos Registros</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>Disco Libre</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics as $metric)
            <tr>
                <td>{{ $metric->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $metric->cpu_load }}%</td>
                <td>{{ $metric->ram_usage }}%</td>
                <td>{{ $metric->disk_free }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
