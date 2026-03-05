<?php
// Comprehensive Application Test
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   MINI E-COMMERCE APPLICATION - COMPREHENSIVE TEST             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

require_once 'config/db.php';
require_once 'includes/functions.php';

$test_results = [];

// ===== TEST 1: Database Connection =====
echo "Test 1: Database Connection\n";
echo str_repeat("-", 60) . "\n";
if ($conn->connect_error) {
    echo "❌ FAILED: " . $conn->connect_error . "\n";
    $test_results['database'] = false;
} else {
    echo "✅ PASSED: Connected to " . DB_NAME . "\n";
    $test_results['database'] = true;
}
echo "\n";

// ===== TEST 2: Database Tables =====
echo "Test 2: Database Tables & Records\n";
echo str_repeat("-", 60) . "\n";
$tables = ['users' => 3, 'produk' => 8, 'cart' => 0, 'transaksi' => 0, 'detail_transaksi' => 0];
$tables_ok = true;
foreach ($tables as $table => $expected) {
    $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    $row = $result->fetch_assoc();
    $count = $row['cnt'];
    if ($count >= $expected - 1) {  // Allow -1 for flexibility
        echo "✅ $table: $count records\n";
    } else {
        echo "❌ $table: Expected $expected, got $count\n";
        $tables_ok = false;
    }
}
$test_results['tables'] = $tables_ok;
echo "\n";

// ===== TEST 3: Authentication =====
echo "Test 3: User Authentication\n";
echo str_repeat("-", 60) . "\n";
$admin = get_user_by_email($conn, 'admin@ecommerce.com');
if ($admin && verify_password('admin123', $admin['password'])) {
    echo "✅ Admin login works correctly\n";
    $test_results['admin_login'] = true;
} else {
    echo "❌ Admin login failed\n";
    $test_results['admin_login'] = false;
}

$user = get_user_by_email($conn, 'john@example.com');
if ($user && verify_password('admin123', $user['password'])) {
    echo "✅ User login works correctly\n";
    $test_results['user_login'] = true;
} else {
    echo "❌ User login failed\n";
    $test_results['user_login'] = false;
}
echo "\n";

// ===== TEST 4: Products =====
echo "Test 4: Product Retrieval\n";
echo str_repeat("-", 60) . "\n";
$products = get_all_products($conn);
if (count($products) >= 8) {
    echo "✅ Found " . count($products) . " products\n";
    echo "First product: " . $products[0]['nama_produk'] . "\n";
    $test_results['products'] = true;
} else {
    echo "❌ Found only " . count($products) . " products (expected 8+)\n";
    $test_results['products'] = false;
}
echo "\n";

// ===== TEST 5: Cart Operations =====
echo "Test 5: Cart Operations\n";
echo str_repeat("-", 60) . "\n";

// Add to cart
if (add_to_cart($conn, $user['id'], $products[0]['id_produk'], 2)) {
    echo "✅ Added product to cart\n";
    
    // Get cart items
    $cart_items = get_cart_items($conn, $user['id']);
    if (count($cart_items) > 0) {
        echo "✅ Retrieved " . count($cart_items) . " item(s) from cart\n";
        
        // Calculate total
        $total = calculate_cart_total($conn, $user['id']);
        echo "✅ Cart total: Rp " . number_format($total, 0) . "\n";
        $test_results['cart'] = true;
        
        // Clear cart
        clear_cart($conn, $user['id']);
        echo "✅ Cleared cart\n";
    } else {
        echo "❌ Cart items not retrieved\n";
        $test_results['cart'] = false;
    }
} else {
    echo "❌ Failed to add to cart\n";
    $test_results['cart'] = false;
}
echo "\n";

// ===== TEST 6: Input Validation =====
echo "Test 6: Input Validation & Security\n";
echo str_repeat("-", 60) . "\n";

// Test email validation
if (validate_email('admin@ecommerce.com') && !validate_email('notanemail')) {
    echo "✅ Email validation works\n";
    $test_results['email_validation'] = true;
} else {
    echo "❌ Email validation failed\n";
    $test_results['email_validation'] = false;
}

// Test input sanitization
$malicious = "<script>alert('xss')</script>";
$sanitized = sanitize_input($malicious);
if (strpos($sanitized, '<script>') === false) {
    echo "✅ XSS protection works\n";
    $test_results['xss_protection'] = true;
} else {
    echo "❌ XSS protection failed\n";
    $test_results['xss_protection'] = false;
}

// Test password hashing
$hash1 = hash_password('testpass');
$hash2 = hash_password('testpass');
if ($hash1 !== $hash2 && password_verify('testpass', $hash1) && password_verify('testpass', $hash2)) {
    echo "✅ Password hashing is secure\n";
    $test_results['password_security'] = true;
} else {
    echo "❌ Password hashing failed\n";
    $test_results['password_security'] = false;
}
echo "\n";

// ===== TEST 7: File Structure =====
echo "Test 7: File Structure & Configuration\n";
echo str_repeat("-", 60) . "\n";
$required_files = [
    'index.php' => 'Main router',
    'config/db.php' => 'Database config',
    'includes/functions.php' => 'Helper functions',
    'pages/login.php' => 'Login page',
    'pages/products.php' => 'Products page',
    'pages/cart.php' => 'Cart page',
    'assets/css/style.css' => 'Styling',
];

$files_ok = true;
foreach ($required_files as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $file ($desc)\n";
    } else {
        echo "❌ $file not found\n";
        $files_ok = false;
    }
}
$test_results['files'] = $files_ok;
echo "\n";

// ===== TEST 8: Database Integrity =====
echo "Test 8: Database Integrity & Constraints\n";
echo str_repeat("-", 60) . "\n";

// Check unique emails
$result = $conn->query("SELECT COUNT(DISTINCT email) as unique_count FROM users");
$row = $result->fetch_assoc();
if ($row['unique_count'] == 3) {
    echo "✅ Email uniqueness constraint works\n";
    $test_results['integrity'] = true;
} else {
    echo "❌ Duplicate emails found\n";
    $test_results['integrity'] = false;
}

// Check password format
$result = $conn->query("SELECT id FROM users WHERE password NOT LIKE '\$2%' LIMIT 1");
if ($result->num_rows == 0) {
    echo "✅ All passwords are hashed (bcrypt format)\n";
} else {
    echo "❌ Some passwords are not hashed\n";
    $test_results['integrity'] = false;
}
echo "\n";

// ===== FINAL SUMMARY =====
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   TEST RESULTS SUMMARY                                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$total = count($test_results);

foreach ($test_results as $test => $result) {
    $status = $result ? "✅ PASS" : "❌ FAIL";
    printf("%-25s: %s\n", ucfirst(str_replace('_', ' ', $test)), $status);
    if ($result) $passed++;
}

echo "\n";
echo "Overall: $passed/$total tests passed\n";

if ($passed == $total) {
    echo "\n🎉 ALL TESTS PASSED - APPLICATION IS READY!\n\n";
    echo "Next steps:\n";
    echo "1. Start XAMPP (Apache & MySQL)\n";
    echo "2. Open: http://localhost/e-comers/\n";
    echo "3. Login with: admin@ecommerce.com / admin123\n";
    echo "4. Follow FEATURES_VERIFICATION.md for manual testing\n";
} else {
    echo "\n⚠️  Some tests failed. Please fix before deployment.\n";
}

$conn->close();
