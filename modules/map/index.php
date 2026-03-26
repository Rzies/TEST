<?php

declare(strict_types=1);

$server = db()->query('SELECT name, latitude, longitude, description FROM server_locations LIMIT 1')->fetch();
$odps = db()->query('SELECT o.id, o.name, o.latitude, o.longitude, COUNT(c.id) as total_clients FROM odps o LEFT JOIN clients c ON c.odp_id = o.id GROUP BY o.id ORDER BY o.name')->fetchAll();
$clients = db()->query('SELECT c.name, c.status, c.latitude, c.longitude, o.name AS odp_name, o.latitude AS odp_lat, o.longitude AS odp_lon FROM clients c JOIN odps o ON c.odp_id = o.id ORDER BY c.name')->fetchAll();

ob_start();
?>
<div class="card border-0 shadow-soft">
    <div class="card-body">
        <div id="networkMap"></div>
    </div>
</div>
<script>
window.mapData = {
    server: <?= json_encode($server, JSON_THROW_ON_ERROR) ?>,
    odps: <?= json_encode($odps, JSON_THROW_ON_ERROR) ?>,
    clients: <?= json_encode($clients, JSON_THROW_ON_ERROR) ?>
};
</script>
<?php
$content = (string) ob_get_clean();
$title = 'Map';
$activePage = 'map';
require __DIR__ . '/../layout.php';
