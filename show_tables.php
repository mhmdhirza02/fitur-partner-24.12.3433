<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $vars = get_object_vars($table);
        echo array_values($vars)[0] . "\n";
    }
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
