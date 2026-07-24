<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $createTable = \Illuminate\Support\Facades\DB::select('SHOW CREATE TABLE transactions');
    $vars = get_object_vars($createTable[0]);
    echo array_values($vars)[1];
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
