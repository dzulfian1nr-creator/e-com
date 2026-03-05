<?php
/**
 * ===================================================
 * HALAMAN CHECKOUT
 * ===================================================
 * Proses checkout dan pembayaran
 */

$page_title = "Checkout - Mini E-Commerce";

// Require login
require_login();

// Admin tidak boleh akses checkout
if (is_admin()) {
    header("Location: index.php");
    exit;
}

// Ambil cart items
$cart_items = get_cart_items($conn, $_SESSION['user_id']);

// Jika cart kosong, redirect ke halaman products
if (empty($cart_items)) {
    header("Location: index.php?page=products");
    exit;
}

$checkout_message = '';
$checkout_success = false;
$transaction_id = null;

// Proses checkout jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_checkout'])) {
    $result = process_checkout($conn, $_SESSION['user_id']);
    
    if ($result['status']) {
        $checkout_success = true;
        $transaction_id = $result['transaction_id'];
        $checkout_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $result['message'] . '</div>';
    } else {
        $checkout_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' . $result['message'] . '</div>';
    }
}

$total = calculate_cart_total($conn, $_SESSION['user_id']);
$user = get_user_by_id($conn, $_SESSION['user_id']);

?>

<?php if (!empty($checkout_message)) echo $checkout_message; ?>

<?php if ($checkout_success && $transaction_id): ?>
    <!-- CHECKOUT BERHASIL - INVOICE -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h2 class="text-success mt-3">Checkout Berhasil!</h2>
                    <p class="text-muted mb-4">Terima kasih telah berbelanja di toko kami</p>
                    
                    <div class="alert alert-info">
                        <strong>No. Transaksi:</strong> #<?php echo str_pad($transaction_id, 5, '0', STR_PAD_LEFT); ?>
                    </div>
                    
                    <a href="index.php?page=invoice&id=<?php echo $transaction_id; ?>" class="btn btn-primary me-2">
                        <i class="fas fa-receipt"></i> Lihat Invoice
                    </a>
                    
                    <a href="index.php?page=history" class="btn btn-info me-2">
                        <i class="fas fa-history"></i> Riwayat Transaksi
                    </a>
                    
                    <a href="index.php?page=products" class="btn btn-secondary">
                        <i class="fas fa-shopping-bag"></i> Belanja Lagi
                    </a>
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <!-- FORM CHECKOUT -->
    <h2><i class="fas fa-credit-card"></i> Checkout</h2>
    <hr>
    
    <div class="row">
        <div class="col-md-8">
            <!-- Data Pengiriman -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Nama:</strong></label>
                            <p><?php echo htmlspecialchars($user['nama']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Pesanan -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Ringkasan Pesanan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                    <td><?php echo format_price($item['harga']); ?></td>
                                    <td class="text-center"><?php echo $item['jumlah']; ?></td>
                                    <td><?php echo format_price($item['harga'] * $item['jumlah']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Ringkasan Harga -->
            <div class="card border-primary sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Ringkasan Harga</h5>
                </div>
                <div class="card-body">
                    <table class="w-100 mb-3">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-end"><?php echo format_price($total); ?></td>
                        </tr>
                        <tr>
                            <td>Ongkos Kirim:</td>
                            <td class="text-end">Gratis</td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr style="font-size: 1.3rem;">
                            <td><strong>Total:</strong></td>
                            <td class="text-end text-danger"><strong><?php echo format_price($total); ?></strong></td>
                        </tr>
                    </table>
                    
                    <!-- Konfirmasi Checkout -->
                    <form method="POST">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Saya setuju dengan syarat dan ketentuan
                            </label>
                        </div>
                        
                        <button type="submit" name="process_checkout" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-check"></i> Konfirmasi Pembelian
                        </button>
                    </form>
                    
                    <a href="index.php?page=cart" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
