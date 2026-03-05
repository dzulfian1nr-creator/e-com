<?php
// Direct password hash test
require_once 'includes/functions.php';

echo "=== PASSWORD HASH DEBUGGING ===\n\n";

// Get actual hash from database
require_once 'config/db.php';
$result = $conn->query("SELECT password FROM users WHERE email='admin@ecommerce.com'");
$row = $result->fetch_assoc();
$stored_hash = $row['password'];

echo "Stored hash in DB: " . substr($stored_hash, 0, 30) . "...\n\n";

// Test verification with the password
$test_password = 'admin123';
echo "Testing password: '$test_password'\n";

// Direct password_verify
$verify_result = password_verify($test_password, $stored_hash);
echo "password_verify() result: " . ($verify_result ? "TRUE ✅" : "FALSE ❌") . "\n\n";

// Test our verify_password function
$our_verify = verify_password($test_password, $stored_hash);
echo "verify_password() result: " . ($our_verify ? "TRUE ✅" : "FALSE ❌") . "\n\n";

// Show password requirements
echo "=== PASSWORD REQUIREMENTS CHECK ===\n";

// Check if hash is valid bcrypt
if (strpos($stored_hash, '$2y$') === 0 || strpos($stored_hash, '$2a$') === 0 || strpos($stored_hash, '$2b$') === 0) {
    echo "✅ Valid bcrypt hash format\n";
} else {
    echo "❌ Invalid hash format\n";
}

// Hash length
if (strlen($stored_hash) == 60) {
    echo "✅ Correct bcrypt hash length (60 chars)\n";
} else {
    echo "❌ Wrong hash length: " . strlen($stored_hash) . " (should be 60)\n";
}

// Test hashing consistency
echo "\n=== HASH CONSISTENCY TEST ===\n";
$hash1 = hash_password($test_password);
$hash2 = hash_password($test_password);

echo "Hash 1: " . substr($hash1, 0, 30) . "...\n";
echo "Hash 2: " . substr($hash2, 0, 30) . "...\n";
echo "Hashes different (as expected): " . ($hash1 !== $hash2 ? "✅ YES" : "❌ NO") . "\n";

// But both should verify
$verify1 = password_verify($test_password, $hash1);
$verify2 = password_verify($test_password, $hash2);
echo "Hash 1 verifies: " . ($verify1 ? "✅ YES" : "❌ NO") . "\n";
echo "Hash 2 verifies: " . ($verify2 ? "✅ YES" : "❌ NO") . "\n";

$conn->close();
