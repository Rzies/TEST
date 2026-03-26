<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$page = $_GET['page'] ?? 'dashboard';

if ($page === 'login') {
    if (is_logged_in()) {
        header('Location: /index.php?page=dashboard');
        exit;
    }
    require dirname(__DIR__) . '/modules/auth/login.php';
    exit;
}

if ($page === 'logout') {
    logout_user();
    header('Location: /index.php?page=login');
    exit;
}

require_auth();

$routes = [
    'dashboard' => '/modules/dashboard/index.php',
    'devices' => '/modules/devices/index.php',
    'map' => '/modules/map/index.php',
    'settings' => '/modules/settings/index.php',
];

if (!isset($routes[$page])) {
    http_response_code(404);
    echo 'Halaman tidak ditemukan';
    exit;
}

require dirname(__DIR__) . $routes[$page];
