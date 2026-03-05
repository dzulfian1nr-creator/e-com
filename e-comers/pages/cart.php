<?php
/**
 * ===================================================
 * HALAMAN KERANJANG BELANJA
 * ===================================================
 * Menampilkan item di keranjang dan opsi checkout
 */

$page_title = "Keranjang Belanja - Mini E-Commerce";

// Require login
require_login();

// Admin tidak boleh akses cart
if (is_admin()) {
    header("Location: index.php");
    exit;
}

$cart_message = '';

// Proses update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if (update_cart_item($conn, $cart_id, $quantity)) {
        $cart_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Keranjang diperbarui!</div>';
    }
}

// Proses hapus item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $cart_id = intval($_GET['delete']);
    if (delete_cart_item($conn, $cart_id)) {
        $cart_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk dihapus dari keranjang!</div>';
    }
}

// Ambil item di cart
$cart_items = get_cart_items($conn, $_SESSION['user_id']);
$total = calculate_cart_total($conn, $_SESSION['user_id']);
?>

<h2><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
<hr>

<?php if (!empty($cart_message)) echo $cart_message; ?>

<?php if (empty($cart_items)): ?>
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle"></i> Keranjang belanja Anda kosong.
        <a href="index.php?page=products" class="alert-link">Belanja sekarang</a>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['gambar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($item['gambar']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['nama_produk']); ?>" 
                                                     width="50" class="me-3 rounded">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['nama_produk']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo format_price($item['harga']); ?></td>
                                    <td>
                                        <form method="POST" class="d-flex" style="width: 120px;">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id_cart']; ?>">
                                            <input type="number" class="form-control form-control-sm" name="quantity" 
                                                   value="<?php echo $item['jumlah']; ?>" min="1" max="<?php echo $item['stok']; ?>">
                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-info ms-2">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <strong><?php echo format_price($item['harga'] * $item['jumlah']); ?></strong>
                                    </td>
                                    <td>
                                        <a href="index.php?page=cart&delete=<?php echo $item['id_cart']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="w-100 mb-3">
                        <tr>
                            <td>Total Produk:</td>
                            <td class="text-end"><strong><?php echo count($cart_items); ?> item</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr style="font-size: 1.2rem;">
                            <td><strong>Total Harga:</strong></td>
                            <td class="text-end text-primary"><strong><?php echo format_price($total); ?></strong></td>
                        </tr>
                    </table>
                    
                    <a href="index.php?page=checkout" class="btn btn-success w-100 btn-lg">
                        <i class="fas fa-credit-card"></i> Lanjut ke Checkout
                    </a>
                    
                    <a href="index.php?page=products" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-shopping-bag"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
