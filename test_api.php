<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Server;
use App\Models\Metric;

// Create a dummy server
$server = Server::create([
    'name' => 'Test Server',
    'api_token' => 'test-token',
    'ip_address' => '127.0.0.1',
    'check_type' => 'agent',
    'is_enabled' => true,
]);

// Simulate the JSON payload from the bash agent (Dashboard.php)
$payloadBash = [
    'cpu_load' => 10,
    'ram_usage' => 20,
    'disk_free' => 30,
    'details' => [
        'services' => ['ssh.service', 'docker.service'],
        'containers' => ['nginx (Up)', 'mysql (Up)']
    ]
];

// Simulate the JSON payload from the python agent (monitor.py)
$payloadPython = [
    'cpu_load' => 15,
    'ram_usage' => 25,
    'disk_free' => 35,
    'details' => json_encode([
        'services' => ['cron', 'systemd'],
        'containers' => ['redis (Up)']
    ])
];

$requestBash = \Illuminate\Http\Request::create('/api/metrics', 'POST', [], [], [], [
    'HTTP_AUTHORIZATION' => 'Bearer test-token',
], json_encode($payloadBash));
$requestBash->headers->set('Content-Type', 'application/json');

echo "Sending Bash payload...\n";
$response1 = app()->handle($requestBash);
echo $response1->getContent() . "\n";

$requestPython = \Illuminate\Http\Request::create('/api/metrics', 'POST', [], [], [], [
    'HTTP_AUTHORIZATION' => 'Bearer test-token',
], json_encode($payloadPython));
$requestPython->headers->set('Content-Type', 'application/json');

echo "Sending Python payload...\n";
$response2 = app()->handle($requestPython);
echo $response2->getContent() . "\n";

// Check the database
$server->refresh();
echo "Server last_sync_details: " . json_encode($server->last_sync_details) . "\n";

$metrics = Metric::where('server_id', $server->id)->get();
foreach ($metrics as $m) {
    echo "Metric " . $m->id . " details: " . json_encode($m->details) . "\n";
}

$server->delete();
Metric::where('server_id', $server->id)->delete();
