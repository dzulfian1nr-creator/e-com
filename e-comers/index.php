<?php
/**
 * ===================================================
 * MINI E-COMMERCE - INDEX (MAIN FILE)
 * ===================================================
 * Entry point aplikasi - handle routing dan session
 */

// Start session
session_start();

// Include config
require_once 'config/db.php';
require_once 'includes/functions.php';

// Determine page
$page = isset($_GET['page']) ? $_GET['page'] : 'products';

// Sanitize page input untuk security
$allowed_pages = [
    'login',
    'register', 
    'products',
    'cart',
    'checkout',
    'invoice',
    'history',
    'admin_products',
    'admin_add_product',
    'admin_edit_product'
];

// Redirect ke products jika page tidak valid
if (!in_array($page, $allowed_pages)) {
    $page = 'products';
}

// Include header
include 'includes/header.php';

// Route pages
if ($page === 'login') {
    if (is_logged_in()) {
        header("Location: index.php?page=products");
        exit;
    }
    include 'pages/login.php';
} elseif ($page === 'register') {
    if (is_logged_in()) {
        header("Location: index.php?page=products");
        exit;
    }
    include 'pages/register.php';
} elseif ($page === 'products') {
    include 'pages/products.php';
} elseif ($page === 'cart') {
    require_login();
    include 'pages/cart.php';
} elseif ($page === 'checkout') {
    require_login();
    include 'pages/checkout.php';
} elseif ($page === 'invoice') {
    require_login();
    include 'pages/invoice.php';
} elseif ($page === 'history') {
    require_login();
    include 'pages/history.php';
} elseif ($page === 'admin_products') {
    require_admin();
    include 'admin/products.php';
} elseif ($page === 'admin_add_product') {
    require_admin();
    include 'admin/add_product.php';
} elseif ($page === 'admin_edit_product') {
    require_admin();
    include 'admin/edit_product.php';
}

// Include footer
include 'includes/footer.php';

// Close database connection
$conn->close();

?>
