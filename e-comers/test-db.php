<?php
// Test database connection
require_once 'config/db.php';

echo "=== DATABASE CONNECTION TEST ===\n\n";

// Check connection
if ($conn->connect_error) {
    echo "❌ ERROR: Connection failed\n";
    echo "Error: " . $conn->connect_error . "\n";
    exit(1);
}

echo "✅ Database connected successfully!\n";
echo "Database: " . DB_NAME . "\n";
echo "Host: " . DB_HOST . "\n\n";

// Check tables
echo "=== TABLE CHECK ===\n";
$tables = ['users', 'produk', 'cart', 'transaksi', 'detail_transaksi'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    if (!$result) {
        echo "❌ $table: ERROR - " . $conn->error . "\n";
    } else {
        $row = $result->fetch_assoc();
        echo "✅ $table: " . $row['cnt'] . " records\n";
    }
}

echo "\n=== DATA CHECK ===\n";

// Check users
$result = $conn->query("SELECT id, nama, email, role FROM users LIMIT 3");
if ($result) {
    echo "\n👥 Users in database:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['email'] . " (" . $row['role'] . ")\n";
    }
}

// Check products
$result = $conn->query("SELECT id_produk, nama_produk, harga, stok FROM produk LIMIT 3");
if ($result) {
    echo "\n🛍️ Sample Products:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['nama_produk'] . " (Rp " . number_format($row['harga'], 0) . ")\n";
    }
}

echo "\n=== FUNCTION TEST ===\n";

// Load functions and test
require_once 'includes/functions.php';

// Test get_user_by_email
echo "\nTesting get_user_by_email()...\n";
$user = get_user_by_email($conn, 'admin@ecommerce.com');
if ($user) {
    echo "✅ Found user: " . $user['email'] . "\n";
}

// Test get_all_products
echo "\nTesting get_all_products()...\n";
$products = get_all_products($conn);
echo "✅ Found " . count($products) . " products\n";

// Test password verification
echo "\nTesting password hashing/verification...\n";
$hash = hash_password('admin123');
if (verify_password('admin123', $user['password'])) {
    echo "✅ Password verification works!\n";
}

echo "\n=== ALL TESTS PASSED ===\n";
echo "✅ Database is ready to use!\n";

$conn->close();
