<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Server;

$server = Server::create([
    'name' => 'Test Server Empty',
    'api_token' => 'test-token-empty',
    'ip_address' => '127.0.0.1',
    'check_type' => 'agent',
    'is_enabled' => true,
]);

// Bash script when variables are empty:
$services = "";
$containers = "";
$payload = '{"cpu_load": 10, "ram_usage": 20, "disk_free": 30, "details": {}}';

$request = \Illuminate\Http\Request::create('/api/metrics', 'POST', [], [], [], [
    'HTTP_AUTHORIZATION' => 'Bearer test-token-empty',
], $payload);
$request->headers->set('Content-Type', 'application/json');

$response = app()->handle($request);
echo "Response: " . $response->getContent() . "\n";

$server->refresh();
dump($server->last_sync_details);

$server->delete();
