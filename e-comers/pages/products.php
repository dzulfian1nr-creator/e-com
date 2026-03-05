<?php
/**
 * ===================================================
 * HALAMAN DAFTAR PRODUK
 * ===================================================
 * Menampilkan semua produk yang tersedia di toko
 */

$page_title = "Produk - Mini E-Commerce";

// Proses tambah ke cart jika ada request
$cart_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!is_logged_in()) {
        $cart_message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Silakan login terlebih dahulu!</div>';
    } else if (is_admin()) {
        $cart_message = '<div class="alert alert-danger"><i class="fas fa-ban"></i> Admin tidak dapat melakukan pembelian!</div>';
    } else {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']) ?? 1;
        
        // Validasi quantity
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        $result = add_to_cart($conn, $_SESSION['user_id'], $product_id, $quantity);
        
        if ($result['status']) {
            $cart_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $result['message'] . '</div>';
        } else {
            $cart_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' . $result['message'] . '</div>';
        }
    }
}

// Ambil semua produk dari database
$products = get_all_products($conn);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-store"></i> Daftar Produk</h2>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="search" placeholder="Cari produk...">
    </div>
</div>

<?php if (!empty($cart_message)) echo $cart_message; ?>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Belum ada produk yang tersedia.
    </div>
<?php else: ?>
    <div class="row" id="products-container">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4 product-card" data-product="<?php echo strtolower($product['nama_produk']); ?>">
                <div class="card h-100 shadow-sm hover-shadow">
                    <!-- Product Image -->
                    <div class="position-relative product-image-wrapper" style="height: 250px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <?php if (!empty($product['gambar'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" 
                                 class="card-img-top h-100 object-fit-cover">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                <div class="text-center">
                                    <i class="fas fa-image" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <p>No Image</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Stok Badge -->
                        <?php if ($product['stok'] > 0): ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Stok: <?php echo $product['stok']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Habis</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                        
                        <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">
                            <?php echo htmlspecialchars($product['deskripsi'] ?? 'Produk berkualitas'); ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-primary mb-0">
                                <strong><?php echo format_price($product['harga']); ?></strong>
                            </h6>
                        </div>
                        
                        <!-- Add to Cart Form -->
                        <?php if ($product['stok'] > 0): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $product['id_produk']; ?>">
                                
                                <div class="input-group mb-2">
                                    <input type="number" class="form-control" name="quantity" value="1" min="1" max="<?php echo $product['stok']; ?>">
                                    <button class="btn btn-primary" type="submit" name="add_to_cart">
                                        <i class="fas fa-shopping-cart"></i> Tambah
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-times"></i> Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- CSS untuk hover effect -->
<style>
.hover-shadow:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    transform: translateY(-5px);
    transition: all 0.3s ease;
}

.product-image-wrapper {
    position: relative;
}

.hidden-product {
    display: none !important;
}
</style>

<!-- JavaScript untuk search -->
<script>
document.getElementById('search').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productName = product.getAttribute('data-product');
        if (productName.includes(searchTerm)) {
            product.classList.remove('hidden-product');
        } else {
            product.classList.add('hidden-product');
        }
    });
});
</script>
