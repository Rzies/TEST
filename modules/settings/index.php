<?php

declare(strict_types=1);

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? null)) {
        $error = 'Token CSRF tidak valid.';
    } else {
        $url = filter_var((string) ($_POST['genieacs_url'] ?? ''), FILTER_SANITIZE_URL);
        $username = trim((string) ($_POST['genieacs_username'] ?? ''));
        $password = trim((string) ($_POST['genieacs_password'] ?? ''));
        $serverLat = (string) ($_POST['server_latitude'] ?? '');
        $serverLon = (string) ($_POST['server_longitude'] ?? '');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'URL GenieACS harus valid.';
        } elseif (!is_numeric($serverLat) || !is_numeric($serverLon)) {
            $error = 'Koordinat server harus numerik.';
        } else {
            upsert_setting('genieacs_url', $url);
            upsert_setting('genieacs_username', $username);
            if ($password !== '') {
                upsert_setting('genieacs_password', password_hash($password, PASSWORD_BCRYPT));
            }

            $stmt = db()->prepare('UPDATE server_locations SET latitude = :lat, longitude = :lon LIMIT 1');
            $stmt->execute(['lat' => $serverLat, 'lon' => $serverLon]);
            $message = 'Konfigurasi berhasil disimpan.';
        }
    }
}

$genieUrl = get_setting('genieacs_url', 'http://127.0.0.1:7557');
$genieUser = get_setting('genieacs_username', 'admin');
$serverLocation = db()->query('SELECT latitude, longitude FROM server_locations LIMIT 1')->fetch();

ob_start();
?>
<div class="card border-0 shadow-soft">
    <div class="card-body">
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

        <form method="post" class="row g-3">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="col-12">
                <label class="form-label">URL GenieACS</label>
                <input type="url" class="form-control" name="genieacs_url" required value="<?= e($genieUrl) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username GenieACS</label>
                <input type="text" class="form-control" name="genieacs_username" required value="<?= e($genieUser) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Password GenieACS (kosongkan jika tidak ganti)</label>
                <input type="password" class="form-control" name="genieacs_password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Latitude Server</label>
                <input type="text" class="form-control" name="server_latitude" value="<?= e((string) ($serverLocation['latitude'] ?? '')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitude Server</label>
                <input type="text" class="form-control" name="server_longitude" value="<?= e((string) ($serverLocation['longitude'] ?? '')) ?>" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = (string) ob_get_clean();
$title = 'Settings';
$activePage = 'settings';
require __DIR__ . '/../layout.php';
