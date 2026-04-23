<?php

$dir = __DIR__ . '/resources/views';

function processDirectory($dir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') border;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDirectory($path);
        } elseif (is_file($path) && str_ends_with($path, '.blade.php')) {
            processFile($path);
        }
    }
}

function processFile($path) {
    $content = file_get_contents($path);
    $original = $content;

    // CSS Custom properties in app.blade.php
    $content = preg_replace('/border-radius:\s*1\.5rem;/', 'border-radius: 0; /* serious */', $content);
    $content = preg_replace('/border-radius:\s*0\.875rem\s*!important;/', 'border-radius: 0 !important; /* serious */', $content);

    // Tailwind corner classes
    $content = preg_replace('/\brounded-(2xl|3xl|xl|lg|md|sm)\b/', 'rounded-none', $content);
    $content = preg_replace('/\brounded-\[.*?\]/', 'rounded-none', $content);
    $content = preg_replace('/rounded-none-none/', 'rounded-none', $content);

    // Handle rounded-full but keep small dots
    // First, change all rounded-full
    $content = preg_replace_callback('/\brounded-full\b/', function($matches) {
        return 'rounded-none'; // Aggressively make everything square
    }, $content);

    // Attempt to restore small dots: h-1 to h-3 w-1 to w-3
    // E.g., class="h-2 w-2 rounded-none bg-emerald-500" -> class="h-2 w-2 rounded-full bg-emerald-500"
    $content = preg_replace_callback('/(h-(?:[1-3]|1\.5|2\.5)\s+w-(?:[1-3]|1\.5|2\.5)[^>]+?)rounded-none/i', function($m) {
        return $m[1] . 'rounded-full';
    }, $content);
    $content = preg_replace_callback('/rounded-none([^>]+?h-(?:[1-3]|1\.5|2\.5)\s+w-(?:[1-3]|1\.5|2\.5))/i', function($m) {
        return 'rounded-full' . $m[1];
    }, $content);

    // Also maybe there are some specific pulse indicators 
    $content = preg_replace('/bg-current rounded-none/', 'bg-current rounded-full', $content);

    // Let's also restore it where 'rounded-full animated-pulse' or similar might exist, but the above regex catches most.
    
    // Also change some 'shadow-2xl' or 'shadow-lg' to be sharper or different? The user specifically mentioned "bordes redondos". We'll just stick to un-rounding.
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}

// Run it
processDirectory($dir);
echo "Done.\n";
