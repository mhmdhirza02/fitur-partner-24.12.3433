<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = app(\App\Http\Controllers\Admin\EventController::class);
    $view = $controller->index();
    echo "Success: " . substr($view->render(), 0, 100);
} catch (\Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
}
