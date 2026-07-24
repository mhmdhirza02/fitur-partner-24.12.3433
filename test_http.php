<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/admin/events/2', 'POST', ['_method' => 'DELETE', '_token' => csrf_token()]);
// We need to bypass auth or authenticate as admin
$user = \App\Models\User::first();
$app['auth']->login($user);

$response = $kernel->handle($request);
echo $response->getStatusCode() . "\n";
if ($response->getStatusCode() != 302 && $response->getStatusCode() != 200) {
    echo $response->getContent();
} else {
    echo "Success! Redirected to: " . $response->headers->get('Location');
}
