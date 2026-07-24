<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $event = \App\Models\Event::first();
    if ($event) {
        $event->delete();
        echo "Success";
    } else {
        echo "No event";
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
