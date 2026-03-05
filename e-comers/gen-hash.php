<?php
require_once 'includes/functions.php';

echo "=== GENERATING CORRECT PASSWORD HASHES ===\n\n";

$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "Length: " . strlen($hash) . " (should be 60)\n";
echo "Verify: " . (password_verify($password, $hash) ? "✅ YES" : "❌ NO") . "\n\n";

echo "SQL UPDATE COMMANDS:\n";
echo str_repeat("=", 70) . "\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'admin@ecommerce.com';\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'john@example.com';\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'jane@example.com';\n";
echo str_repeat("=", 70) . "\n";
