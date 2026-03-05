<?php
/**
 * ===================================================
 * KONFIGURASI DATABASE
 * ===================================================
 * File ini berisi konfigurasi koneksi ke database MySQL
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'e_commerce');
define('DB_PORT', 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Set charset untuk support UTF-8
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

?>
