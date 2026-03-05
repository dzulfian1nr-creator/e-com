<?php
/**
 * ===================================================
 * HELPER FUNCTIONS
 * ===================================================
 * File ini berisi fungsi-fungsi utilities yang digunakan di seluruh aplikasi
 */

// ===================================================
// VALIDASI INPUT FUNCTIONS
// ===================================================

/**
 * Validasi dan sanitasi input dari $_POST atau $_GET
 * @param string $data
 * @return string
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validasi email
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validasi password (minimal 6 karakter)
 * @param string $password
 * @return bool
 */
function validate_password($password) {
    return strlen($password) >= 6;
}

// ===================================================
// AUTHENTICATION FUNCTIONS
// ===================================================

/**
 * Hash password menggunakan bcrypt
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password dengan hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check apakah user sudah login
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check apakah user adalah admin
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirect ke halaman login jika belum login
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: index.php?page=login");
        exit;
    }
}

/**
 * Redirect ke halaman utama jika bukan admin
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: index.php");
        exit;
    }
}

// ===================================================
// DATABASE QUERY FUNCTIONS
// ===================================================

/**
 * Ambil data user berdasarkan email
 * @param object $conn
 * @param string $email
 * @return array|null
 */
function get_user_by_email($conn, $email) {
    $email = sanitize_input($email);
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Ambil data user berdasarkan ID
 * @param object $conn
 * @param int $id
 * @return array|null
 */
function get_user_by_id($conn, $id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Ambil semua data produk
 * @param object $conn
 * @return array
 */
function get_all_products($conn) {
    $query = "SELECT * FROM produk ORDER BY created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Ambil data produk berdasarkan ID
 * @param object $conn
 * @param int $id
 * @return array|null
 */
function get_product_by_id($conn, $id) {
    $query = "SELECT * FROM produk WHERE id_produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Ambil detail cart user
 * @param object $conn
 * @param int $user_id
 * @return array
 */
function get_cart_items($conn, $user_id) {
    $query = "SELECT c.*, p.nama_produk, p.harga, p.stok, p.gambar 
              FROM cart c 
              JOIN produk p ON c.id_produk = p.id_produk 
              WHERE c.id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Hitung total harga cart
 * @param object $conn
 * @param int $user_id
 * @return float
 */
function calculate_cart_total($conn, $user_id) {
    $items = get_cart_items($conn, $user_id);
    $total = 0;
    foreach ($items as $item) {
        $total += ($item['harga'] * $item['jumlah']);
    }
    return $total;
}

// ===================================================
// CART FUNCTIONS
// ===================================================

/**
 * Tambah produk ke cart
 * @param object $conn
 * @param int $user_id
 * @param int $product_id
 * @param int $quantity
 * @return array
 */
function add_to_cart($conn, $user_id, $product_id, $quantity = 1) {
    // Cek produk ada atau tidak
    $product = get_product_by_id($conn, $product_id);
    if (!$product) {
        return ['status' => false, 'message' => 'Produk tidak ditemukan'];
    }
    
    // Cek stok cukup
    if ($product['stok'] < $quantity) {
        return ['status' => false, 'message' => 'Stok tidak cukup'];
    }
    
    // Cek apakah produk sudah ada di cart
    $query = "SELECT * FROM cart WHERE id_user = ? AND id_produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update jumlah jika sudah ada
        $query = "UPDATE cart SET jumlah = jumlah + ? WHERE id_user = ? AND id_produk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    } else {
        // Insert jika belum ada
        $query = "INSERT INTO cart (id_user, id_produk, jumlah) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }
    
    if ($stmt->execute()) {
        return ['status' => true, 'message' => 'Produk ditambahkan ke keranjang'];
    } else {
        return ['status' => false, 'message' => 'Gagal menambahkan ke keranjang'];
    }
}

/**
 * Update jumlah produk di cart
 * @param object $conn
 * @param int $cart_id
 * @param int $quantity
 * @return bool
 */
function update_cart_item($conn, $cart_id, $quantity) {
    if ($quantity <= 0) {
        return delete_cart_item($conn, $cart_id);
    }
    
    $query = "UPDATE cart SET jumlah = ? WHERE id_cart = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $quantity, $cart_id);
    return $stmt->execute();
}

/**
 * Hapus produk dari cart
 * @param object $conn
 * @param int $cart_id
 * @return bool
 */
function delete_cart_item($conn, $cart_id) {
    $query = "DELETE FROM cart WHERE id_cart = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_id);
    return $stmt->execute();
}

/**
 * Kosongkan seluruh cart user
 * @param object $conn
 * @param int $user_id
 * @return bool
 */
function clear_cart($conn, $user_id) {
    $query = "DELETE FROM cart WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

// ===================================================
// TRANSACTION FUNCTIONS
// ===================================================

/**
 * Proses checkout - ciptakan transaksi dan detail transaksi
 * HOTS: Mencegah double order dengan menggunakan database transaction
 * 
 * @param object $conn
 * @param int $user_id
 * @param float $total_harga
 * @return array
 */
function process_checkout($conn, $user_id) {
    // Ambil item di cart
    $cart_items = get_cart_items($conn, $user_id);
    
    if (empty($cart_items)) {
        return ['status' => false, 'message' => 'Keranjang belanja kosong', 'transaction_id' => null];
    }
    
    // Hitung total
    $total_harga = calculate_cart_total($conn, $user_id);
    
    // START TRANSACTION untuk mencegah double order
    $conn->begin_transaction();
    
    try {
        // 1. Validasi stok untuk semua produk
        foreach ($cart_items as $item) {
            // Lock row untuk mencegah race condition
            $query = "SELECT stok FROM produk WHERE id_produk = ? FOR UPDATE";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $item['id_produk']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if ($product['stok'] < $item['jumlah']) {
                throw new Exception("Stok {$item['nama_produk']} tidak cukup!");
            }
        }
        
        // 2. Buat transaksi baru
        $query = "INSERT INTO transaksi (id_user, total_harga, status) VALUES (?, ?, 'berhasil')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("id", $user_id, $total_harga);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal membuat transaksi");
        }
        
        $transaction_id = $conn->insert_id;
        
        // 3. Buat detail transaksi dan kurangi stok produk
        foreach ($cart_items as $item) {
            // Insert detail transaksi
            $subtotal = $item['harga'] * $item['jumlah'];
            $query = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiiii", $transaction_id, $item['id_produk'], $item['jumlah'], $item['harga'], $subtotal);
            
            if (!$stmt->execute()) {
                throw new Exception("Gagal membuat detail transaksi");
            }
            
            // UPDATE: Kurangi stok produk
            $query = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $item['jumlah'], $item['id_produk']);
            
            if (!$stmt->execute()) {
                throw new Exception("Gagal mengupdate stok");
            }
        }
        
        // 4. Kosongkan cart user
        if (!clear_cart($conn, $user_id)) {
            throw new Exception("Gagal mengosongkan keranjang");
        }
        
        // COMMIT TRANSACTION
        $conn->commit();
        
        return [
            'status' => true,
            'message' => 'Checkout berhasil!',
            'transaction_id' => $transaction_id,
            'total' => $total_harga
        ];
        
    } catch (Exception $e) {
        // ROLLBACK jika ada error
        $conn->rollback();
        return [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'transaction_id' => null
        ];
    }
}

/**
 * Ambil data transaksi berdasarkan ID
 * @param object $conn
 * @param int $transaction_id
 * @return array|null
 */
function get_transaction_by_id($conn, $transaction_id) {
    $query = "SELECT * FROM transaksi WHERE id_transaksi = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Ambil detail transaksi
 * @param object $conn
 * @param int $transaction_id
 * @return array
 */
function get_transaction_details($conn, $transaction_id) {
    $query = "SELECT dt.*, p.nama_produk FROM detail_transaksi dt 
              JOIN produk p ON dt.id_produk = p.id_produk 
              WHERE dt.id_transaksi = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// ===================================================
// FORMAT FUNCTIONS
// ===================================================

/**
 * Format harga ke rupiah
 * @param float $price
 * @return string
 */
function format_price($price) {
    return "Rp " . number_format($price, 0, ',', '.');
}

/**
 * Format tanggal
 * @param string $date
 * @return string
 */
function format_date($date) {
    $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $timestamp = strtotime($date);
    return date('d', $timestamp) . ' ' . $months[date('n', $timestamp)] . ' ' . date('Y H:i', $timestamp);
}

?>
