<?php

declare(strict_types=1);

$totalClient = (int) db()->query('SELECT COUNT(*) FROM clients')->fetchColumn();
$onuOnline = (int) db()->query("SELECT COUNT(*) FROM clients WHERE status = 'online'")->fetchColumn();
$onuOffline = (int) db()->query("SELECT COUNT(*) FROM clients WHERE status = 'offline'")->fetchColumn();

ob_start();
?>
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-soft border-0">
            <div class="card-body">
                <p class="text-secondary mb-1">Total Client</p>
                <h2 class="mb-0"><?= $totalClient ?></h2>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-soft border-0">
            <div class="card-body">
                <p class="text-secondary mb-1">ONU Online</p>
                <h2 class="text-success mb-0"><?= $onuOnline ?></h2>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-soft border-0">
            <div class="card-body">
                <p class="text-secondary mb-1">ONU Offline</p>
                <h2 class="text-danger mb-0"><?= $onuOffline ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-soft border-0">
    <div class="card-header bg-white border-0 py-3">
        <h3 class="h6 mb-0">Distribusi Status ONU</h3>
    </div>
    <div class="card-body">
        <div id="onuStatusChart" style="height:320px;"></div>
    </div>
</div>

<script>
window.dashboardData = {
    online: <?= $onuOnline ?>,
    offline: <?= $onuOffline ?>
};
</script>
<?php
$content = (string) ob_get_clean();
$title = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/../layout.php';
