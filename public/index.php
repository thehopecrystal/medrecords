<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Support subdirectory installs (e.g. http://localhost/medrecords/ on XAMPP):
// strip the install folder from the request path so Laravel's root-relative
// routes match. A no-op when the app is served at the domain root (Apache
// VirtualHost with DocumentRoot=public, or `php artisan serve`).
$appBasePath = '/medrecords';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if ($requestUri === $appBasePath || str_starts_with($requestUri, $appBasePath . '/')) {
    $_SERVER['REQUEST_URI'] = substr($requestUri, strlen($appBasePath)) ?: '/';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
