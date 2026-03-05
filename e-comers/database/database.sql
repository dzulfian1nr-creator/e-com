-- ===================================================
-- MINI E-COMMERCE DATABASE
-- Struktur database untuk aplikasi e-commerce sederhana
-- ===================================================

-- Drop database jika ada
DROP DATABASE IF EXISTS e_commerce;

-- Buat database baru
CREATE DATABASE e_commerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE e_commerce;

-- ===================================================
-- 1. TABEL USERS (Penyimpanan data user dan admin)
-- ===================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================================================
-- 2. TABEL PRODUK (Penyimpanan data produk)
-- ===================================================
CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================================================
-- 3. TABEL CART (Keranjang belanja user)
-- ===================================================
CREATE TABLE cart (
    id_cart INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (id_user, id_produk)
);

-- ===================================================
-- 4. TABEL TRANSAKSI (Riwayat transaksi/order)
-- ===================================================
CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'berhasil', 'batal') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_tanggal (tanggal)
);

-- ===================================================
-- 5. TABEL DETAIL TRANSAKSI (Detail produk dalam transaksi)
-- ===================================================
CREATE TABLE detail_transaksi (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE,
    INDEX idx_transaksi (id_transaksi)
);

-- ===================================================
-- DATA DUMMY
-- ===================================================

-- Insert admin user
INSERT INTO users (nama, email, password, role) VALUES 
('Admin Store', 'admin@ecommerce.com', '$2y$10$sW3M/0h5nFoW5nzFoFoFoOnFoFoFoFoFoFoFoFoFoFoFoFoFoFoFoFo', 'admin');

-- Password untuk admin: admin123 (sudah di-hash dengan bcrypt)

-- Insert user biasa
INSERT INTO users (nama, email, password, role) VALUES 
('John Doe', 'john@example.com', '$2y$10$sW3M/0h5nFoW5nzFoFoFoOnFoFoFoFoFoFoFoFoFoFoFoFoFoFoFoFo', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$sW3M/0h5nFoW5nzFoFoFoOnFoFoFoFoFoFoFoFoFoFoFoFoFoFoFoFo', 'user');

-- Insert data produk
INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) VALUES 
('Laptop Premium ASUS', 'Laptop gaming dengan spesifikasi tinggi, prosesor Intel i7, RAM 16GB, SSD 512GB', 15000000.00, 10, 'laptop.jpg'),
('Smartphone Android', 'Smartphone terbaru dengan layar OLED 6.7 inch, kamera 108MP, baterai 5000mAh', 8000000.00, 25, 'smartphone.jpg'),
('Headphone Wireless', 'Headphone Bluetooth dengan noise cancellation, baterai tahan 30 jam', 2500000.00, 50, 'headphone.jpg'),
('Smartwatch Pro', 'Jam tangan pintar dengan GPS, monitor detak jantung, tahan air IP68', 3500000.00, 30, 'smartwatch.jpg'),
('Tablet 10 Inch', 'Tablet layar besar dengan prosesor octa-core, baterai 8000mAh, 128GB storage', 5500000.00, 15, 'tablet.jpg'),
('Camera DSLR', 'Kamera profesional 24MP, ISO 100-25600, video 4K, lensa kit included', 12000000.00, 8, 'camera.jpg'),
('Monitor 4K', 'Monitor ultrawide 34 inch, resolusi 4K, refresh rate 144Hz, RGB lighting', 7500000.00, 12, 'monitor.jpg'),
('Keyboard Mechanical', 'Keyboard gaming mechanical RGB, switch blue, hot-swap compatible', 1500000.00, 40, 'keyboard.jpg');

-- ===================================================
-- CREATE INDEXES
-- ===================================================
CREATE INDEX idx_produk_nama ON produk(nama_produk);
CREATE INDEX idx_produk_harga ON produk(harga);
CREATE INDEX idx_transaksi_user ON transaksi(id_user);
CREATE INDEX idx_cart_user ON cart(id_user);

-- ===================================================
-- END OF DATABASE SETUP
-- ===================================================
