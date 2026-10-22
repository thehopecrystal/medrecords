<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Support subdirectory installs (e.g. http://localhost/medrecords/ on XAMPP):
// the rewrite rules deliver /medrecords/... to this front controller. Point
// SCRIPT_NAME at a script inside the install folder so Symfony derives the
// base URL ('/medrecords') from it: routes still resolve (/login, /admin, …)
// while fullUrl(), root() and back()-redirects keep the folder. REQUEST_URI is
// left untouched. A no-op when the app is served at the domain root (Apache
// VirtualHost with DocumentRoot=public, or `php artisan serve`).
$appBasePath = '/medrecords';
$requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
if ($requestUri === $appBasePath) {
    // Treat http://.../medrecords (no trailing slash) as the folder root.
    $_SERVER['REQUEST_URI'] = $requestUri .= '/';
}
if (str_starts_with($requestUri, $appBasePath . '/')) {
    $_SERVER['SCRIPT_NAME'] = $appBasePath . '/index.php';
    $_SERVER['PHP_SELF']    = $appBasePath . '/index.php';
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
