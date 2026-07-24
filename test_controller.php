<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = app(\App\Http\Controllers\Admin\EventController::class);
    $event = \App\Models\Event::first();
    if ($event) {
        $response = $controller->destroy($event);
        echo "Success, redirects to: " . $response->getTargetUrl();
    } else {
        echo 'No event';
    }
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
