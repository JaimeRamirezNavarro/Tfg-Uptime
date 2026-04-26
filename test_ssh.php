<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use phpseclib3\Net\SSH2;

$ssh = new SSH2('casaos-1.tailbd65b1.ts.net');
if (!$ssh->login('jaime', 'MihermanoesAle1')) {
    echo "SSH Login Failed\n";
    exit(1);
}

echo "=== TAILSCALE STATUS ===\n";
echo $ssh->exec('tailscale status');
echo "\n=== IP RULE ===\n";
echo $ssh->exec('ip rule');



