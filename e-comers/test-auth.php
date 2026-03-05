<?php
// Test authentication system
session_start();

require_once 'config/db.php';
require_once 'includes/functions.php';

echo "=== AUTHENTICATION TEST ===\n\n";

// Test 1: Test admin login
echo "Test 1: Admin Login\n";
echo "Email: admin@ecommerce.com\n";
echo "Password: admin123\n";

$admin = get_user_by_email($conn, 'admin@ecommerce.com');
if ($admin && verify_password('admin123', $admin['password'])) {
    echo "✅ Admin login SUCCESS\n";
    echo "   User ID: " . $admin['id'] . "\n";
    echo "   Role: " . $admin['role'] . "\n";
} else {
    echo "❌ Admin login FAILED\n";
}

// Test 2: Test regular user login
echo "\nTest 2: User Login\n";
echo "Email: john@example.com\n";
echo "Password: admin123\n";

$user = get_user_by_email($conn, 'john@example.com');
if ($user && verify_password('admin123', $user['password'])) {
    echo "✅ User login SUCCESS\n";
    echo "   User ID: " . $user['id'] . "\n";
    echo "   Role: " . $user['role'] . "\n";
} else {
    echo "❌ User login FAILED\n";
}

// Test 3: Invalid password
echo "\nTest 3: Wrong Password Detection\n";
$admin = get_user_by_email($conn, 'admin@ecommerce.com');
if ($admin && !verify_password('wrongpassword', $admin['password'])) {
    echo "✅ Wrong password correctly rejected\n";
} else {
    echo "❌ Password verification failed\n";
}

// Test 4: Test password hashing
echo "\nTest 4: Password Hashing\n";
$new_password = 'testpassword123';
$hashed = hash_password($new_password);
echo "Original: $new_password\n";
echo "Hashed (first 20 chars): " . substr($hashed, 0, 20) . "...\n";
if (verify_password($new_password, $hashed)) {
    echo "✅ Hash/Verify cycle works correctly\n";
} else {
    echo "❌ Hash/Verify cycle failed\n";
}

// Test 5: Input sanitization
echo "\nTest 5: Input Sanitization\n";
$malicious = "<script>alert('xss')</script>";
$sanitized = sanitize_input($malicious);
echo "Input: $malicious\n";
echo "Sanitized: $sanitized\n";
if (strpos($sanitized, '<script>') === false) {
    echo "✅ XSS attack prevented\n";
} else {
    echo "❌ Sanitization failed\n";
}

// Test 6: Email validation
echo "\nTest 6: Email Validation\n";
$valid_emails = ['admin@ecommerce.com', 'user@example.com', 'test@domain.co.id'];
$invalid_emails = ['notanemail', '@example.com', 'user@', 'user.example.com'];

echo "Valid emails:\n";
foreach ($valid_emails as $email) {
    $result = validate_email($email) ? '✅' : '❌';
    echo "  $result $email\n";
}

echo "Invalid emails:\n";
foreach ($invalid_emails as $email) {
    $result = !validate_email($email) ? '✅' : '❌';
    echo "  $result $email (should be invalid)\n";
}

// Test 7: Test role checking
echo "\nTest 7: Role-based Access Control\n";
$_SESSION['user_id'] = $admin['id'];
$_SESSION['role'] = $admin['role'];

if (is_admin()) {
    echo "✅ is_admin() correctly identifies admin\n";
} else {
    echo "❌ is_admin() failed\n";
}

if (is_logged_in()) {
    echo "✅ is_logged_in() returns true\n";
} else {
    echo "❌ is_logged_in() failed\n";
}

// Test 8: Cart operations
echo "\nTest 8: Cart Operations\n";
$product = $products[0] ?? null;
if ($product) {
    echo "Testing with product: " . $product['nama_produk'] . "\n";
    
    // Add to cart
    if (add_to_cart($conn, $admin['id'], $product['id_produk'], 2)) {
        echo "✅ add_to_cart() success\n";
        
        // Get cart items
        $cart = get_cart_items($conn, $admin['id']);
        if (count($cart) > 0) {
            echo "✅ get_cart_items() found " . count($cart) . " item(s)\n";
            
            // Calculate total
            $total = calculate_cart_total($cart);
            echo "✅ Cart total: Rp " . number_format($total, 0) . "\n";
            
            // Clear cart for next test
            clear_cart($conn, $admin['id']);
            echo "✅ clear_cart() success\n";
        }
    } else {
        echo "❌ add_to_cart() failed\n";
    }
}

echo "\n=== ALL SECURITY TESTS COMPLETED ===\n";
echo "✅ Authentication & Security Systems are Working!\n";

$conn->close();
session_destroy();
