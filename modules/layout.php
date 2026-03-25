<?php

declare(strict_types=1);
/** @var string $content */
/** @var string $title */
/** @var string $activePage */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - <?= e(app_config('name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"/>
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand px-3 py-3 d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><?= e(app_config('name')) ?></span>
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="closeSidebar"><i class="bi bi-x"></i></button>
        </div>
        <nav class="nav flex-column px-2 gap-1">
            <?php $menus = [
                'dashboard' => ['Dashboard', 'speedometer2'],
                'devices' => ['Devices', 'hdd-network'],
                'map' => ['Map', 'geo-alt'],
                'settings' => ['Settings', 'gear'],
                'logout' => ['Logout', 'box-arrow-right'],
            ]; ?>
            <?php foreach ($menus as $key => [$label, $icon]): ?>
                <a class="nav-link <?= $activePage === $key ? 'active' : '' ?>" href="/index.php?page=<?= e($key) ?>">
                    <i class="bi bi-<?= e($icon) ?>"></i><span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="main-panel">
        <header class="topbar d-flex justify-content-between align-items-center px-3 px-md-4 py-3">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary" id="toggleSidebar"><i class="bi bi-list"></i></button>
                <h1 class="h5 mb-0"><?= e($title) ?></h1>
            </div>
            <small class="text-secondary">Login: <?= e((string) ($_SESSION['username'] ?? '')) ?></small>
        </header>
        <main class="content-area px-3 px-md-4 pb-4">
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
