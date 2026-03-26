CREATE DATABASE IF NOT EXISTS genie_monitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE genie_monitor;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS server_locations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS odps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    serial_onu VARCHAR(80) NOT NULL UNIQUE,
    status ENUM('online','offline') NOT NULL DEFAULT 'offline',
    odp_id INT UNSIGNED NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    last_seen DATETIME NOT NULL,
    raw_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_clients_odp FOREIGN KEY (odp_id) REFERENCES odps(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$12$E7cYayIO1AzCWYqI1UV5TuTUkqu1r8xY47U0AveSbqQruvUM3UZCW')
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);

INSERT INTO server_locations (id, name, description, latitude, longitude) VALUES
(1, 'Main POP Server', 'Core server GenieACS & NMS', -6.1753924, 106.8271528)
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), latitude=VALUES(latitude), longitude=VALUES(longitude);

INSERT INTO odps (id, name, latitude, longitude) VALUES
(1, 'ODP-MERDEKA-01', -6.1738000, 106.8255000),
(2, 'ODP-MERDEKA-02', -6.1773000, 106.8304000),
(3, 'ODP-MERDEKA-03', -6.1710000, 106.8332000)
ON DUPLICATE KEY UPDATE name=VALUES(name), latitude=VALUES(latitude), longitude=VALUES(longitude);

INSERT INTO clients (name, serial_onu, status, odp_id, latitude, longitude, last_seen, raw_json) VALUES
('Budi Santoso', 'ONU-ZTE-0001', 'online', 1, -6.1731000, 106.8249000, NOW() - INTERVAL 5 MINUTE, JSON_OBJECT('rxPower', '-21.5 dBm', 'txPower', '2.1 dBm', 'uptime', '5d 2h')),
('Siti Aminah', 'ONU-HW-0002', 'offline', 1, -6.1742000, 106.8261000, NOW() - INTERVAL 2 HOUR, JSON_OBJECT('rxPower', '-27.1 dBm', 'txPower', '2.4 dBm', 'uptime', '0d 0h')),
('Andi Pratama', 'ONU-NK-0003', 'online', 2, -6.1762000, 106.8312000, NOW() - INTERVAL 10 MINUTE, JSON_OBJECT('rxPower', '-20.2 dBm', 'txPower', '1.8 dBm', 'uptime', '12d 8h')),
('Rina Wijaya', 'ONU-HW-0004', 'offline', 2, -6.1780000, 106.8296000, NOW() - INTERVAL 1 DAY, JSON_OBJECT('rxPower', '-30.1 dBm', 'txPower', '3.1 dBm', 'uptime', '0d 0h')),
('Doni Saputra', 'ONU-ZTE-0005', 'online', 3, -6.1707000, 106.8343000, NOW() - INTERVAL 2 MINUTE, JSON_OBJECT('rxPower', '-19.8 dBm', 'txPower', '1.7 dBm', 'uptime', '30d 4h'));

INSERT INTO settings (setting_key, setting_value) VALUES
('genieacs_url', 'http://127.0.0.1:7557'),
('genieacs_username', 'admin')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
