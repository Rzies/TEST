<?php

declare(strict_types=1);

$rows = db()->query("SELECT c.id, c.name, c.serial_onu, c.status, c.last_seen, c.latitude, c.longitude, c.raw_json,
       o.name AS odp_name,
       (6371000 * ACOS(
            COS(RADIANS(o.latitude)) * COS(RADIANS(c.latitude)) * COS(RADIANS(c.longitude) - RADIANS(o.longitude)) +
            SIN(RADIANS(o.latitude)) * SIN(RADIANS(c.latitude))
       )) AS distance_meter
FROM clients c
JOIN odps o ON o.id = c.odp_id
ORDER BY c.name")
->fetchAll();

ob_start();
?>
<div class="card border-0 shadow-soft mb-3">
    <div class="card-body d-flex flex-column flex-md-row gap-2">
        <input id="deviceSearch" type="search" class="form-control" placeholder="Cari nama / serial / ODP...">
        <select id="deviceStatusFilter" class="form-select" style="max-width:220px">
            <option value="all">Semua Status</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>
    </div>
</div>

<div class="card border-0 shadow-soft">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="devicesTable">
            <thead class="table-light">
            <tr>
                <th>Nama</th><th>Serial ONU</th><th>Status</th><th>ODP</th><th>Jarak ke ODP</th><th>Last Seen</th><th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr data-status="<?= e($row['status']) ?>">
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['serial_onu']) ?></td>
                    <td>
                        <span class="badge rounded-pill <?= $row['status'] === 'online' ? 'text-bg-success' : 'text-bg-danger' ?>">
                            <?= e($row['status']) ?>
                        </span>
                    </td>
                    <td><?= e($row['odp_name']) ?></td>
                    <td><?= number_format((float) $row['distance_meter'], 0) ?> m</td>
                    <td><?= e((string) $row['last_seen']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary view-device"
                                data-name="<?= e($row['name']) ?>"
                                data-json="<?= e($row['raw_json']) ?>">
                            Detail
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="deviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-soft">
            <div class="modal-header">
                <h5 class="modal-title" id="deviceModalTitle">Detail Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre class="bg-dark text-light rounded p-3 mb-0"><code id="deviceJson"></code></pre>
            </div>
        </div>
    </div>
</div>
<?php
$content = (string) ob_get_clean();
$title = 'Devices';
$activePage = 'devices';
require __DIR__ . '/../layout.php';
