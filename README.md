# GenieACS Network Monitor (PHP Native)

Dashboard monitoring jaringan ISP yang modern, minimalis, ringan, dan siap production untuk server kecil/STB.

## Fitur
- Login + session + CSRF protection.
- Dashboard utama berisi **stat card** dan **chart donut ONU online/offline** (tidak ada halaman chart terpisah).
- Devices page: tabel client/ONU, search, filter status, modal detail JSON.
- Map page (Leaflet): marker server, ODP, client; popup detail; hitung jarak ODP-client dalam meter; garis ODP->client.
- Settings page: simpan URL/kredensial GenieACS + koordinat server ke database.
- Sidebar hamburger responsif dengan state tersimpan di `localStorage`.

## Struktur Folder

```bash
.
├── assets/
│   ├── css/app.css
│   └── js/app.js
├── config/
│   ├── app.php
│   └── database.php
├── modules/
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── devices/index.php
│   ├── map/index.php
│   ├── settings/index.php
│   └── layout.php
├── public/index.php
├── sql/schema.sql
├── bootstrap.php
└── .env.example
```

## Instalasi (Linux / Umum)

1. Copy environment:
   ```bash
   cp .env.example .env
   ```
2. Buat database dan import schema + dummy data:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
3. Jalankan di PHP built-in (uji lokal):
   ```bash
   php -S 0.0.0.0:8080 -t public
   ```
4. Login default:
   - Username: `admin`
   - Password: `admin123`

## Cara Run dengan XAMPP di Windows

1. **Install XAMPP** (Apache + MySQL) dan pastikan service **Apache** serta **MySQL** jalan dari XAMPP Control Panel.
2. **Copy project** ini ke folder web XAMPP, misalnya:
   ```text
   C:\xampp\htdocs\genie-monitor
   ```
3. **Buat file `.env`** dari `.env.example`:
   - Buka folder project.
   - Copy `.env.example` menjadi `.env`.
   - Sesuaikan DB (default XAMPP biasanya tetap cocok):
     ```env
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=genie_monitor
     DB_USERNAME=root
     DB_PASSWORD=
     ```
4. **Buat database & import SQL**:
   - Buka `http://localhost/phpmyadmin`
   - Create database: `genie_monitor` (collation `utf8mb4_unicode_ci`)
   - Masuk ke database tersebut → tab **Import** → pilih file `sql/schema.sql` → klik **Go**
5. **Atur DocumentRoot ke folder `public`** (direkomendasikan, lebih aman):
   - Edit file Apache vhost (contoh: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`), tambahkan:
     ```apache
     <VirtualHost *:80>
         ServerName genie-monitor.local
         DocumentRoot "C:/xampp/htdocs/genie-monitor/public"
         <Directory "C:/xampp/htdocs/genie-monitor/public">
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```
   - Tambahkan hosts entry di `C:\Windows\System32\drivers\etc\hosts`:
     ```text
     127.0.0.1 genie-monitor.local
     ```
   - Restart Apache.
   - Akses: `http://genie-monitor.local/index.php?page=login`

   **Alternatif cepat (tanpa vhost)**
   - Akses langsung: `http://localhost/genie-monitor/public/index.php?page=login`
6. **Login awal**:
   - Username: `admin`
   - Password: `admin123`
7. **Ganti password default** setelah login untuk keamanan production.

## Konfigurasi Nginx (Production)

```nginx
server {
    listen 80;
    server_name monitor.example.com;
    root /var/www/genie-monitor/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~* \.(css|js|png|jpg|jpeg|gif|svg|ico)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}
```

## Penjelasan Tiap Bagian

- `bootstrap.php`: init session aman, helper auth, CSRF, koneksi PDO, helper settings.
- `public/index.php`: front controller + routing login/dashboard/devices/map/settings/logout.
- `modules/layout.php`: shell UI (sidebar, topbar, menu) + include assets global.
- `modules/auth/login.php`: halaman login modern + validasi form + verifikasi password hash.
- `modules/dashboard/index.php`: ringkasan total client, online/offline, data chart donut.
- `modules/devices/index.php`: tabel monitoring ONU lengkap + modal JSON detail.
- `modules/map/index.php`: lempar data map dari DB ke Leaflet.
- `modules/settings/index.php`: simpan konfigurasi GenieACS & koordinat server.
- `assets/js/app.js`: interaksi sidebar, chart ECharts, map Leaflet, search/filter tabel.
- `assets/css/app.css`: tema minimalis, modern, responsive.
- `sql/schema.sql`: schema MySQL/MariaDB + akun admin + data dummy client/ODP/server.

## Catatan Production
- Gunakan HTTPS agar cookie session `secure` aktif.
- Ganti password admin default.
- Batasi akses database (user minimal privilege).
- Aktifkan backup reguler database.
