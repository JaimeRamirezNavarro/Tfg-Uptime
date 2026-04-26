<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Client\Factory as HttpFactory;

// Minimal setup to use Laravel's Http client standalone
$http = new HttpFactory(new Dispatcher(new Container()));

$urls = ['https://google.com', 'https://github.com'];

foreach ($urls as $url) {
    echo "Testing $url...\n";
    try {
        $response = $http->timeout(3)->get($url);
        echo "Status: " . $response->status() . "\n";
        echo "Successful: " . ($response->successful() ? 'YES' : 'NO') . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}
