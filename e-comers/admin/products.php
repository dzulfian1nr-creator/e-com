<?php
/**
 * ===================================================
 * HALAMAN ADMIN - DAFTAR PRODUK
 * ===================================================
 * CRUD Produk untuk admin
 */

$page_title = "Admin - Daftar Produk - Mini E-Commerce";

// Require admin login
require_admin();

$admin_message = '';

// Proses update stok inline
if (isset($_POST['update_stock'])) {
    $product_id = intval($_POST['product_id']);
    $new_stock = intval($_POST['new_stock']);
    
    if ($new_stock < 0) {
        $admin_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Stok tidak boleh negatif!</div>';
    } else {
        $query = "UPDATE produk SET stok = ? WHERE id_produk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $new_stock, $product_id);
        
        if ($stmt->execute()) {
            $admin_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Stok berhasil diperbarui!</div>';
        } else {
            $admin_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal memperbarui stok!</div>';
        }
    }
}

// Proses hapus produk
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    
    // Validasi input
    $product = get_product_by_id($conn, $product_id);
    if (!$product) {
        $admin_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Produk tidak ditemukan!</div>';
    } else {
        // Hapus gambar jika ada
        if (!empty($product['gambar']) && file_exists("uploads/" . $product['gambar'])) {
            unlink("uploads/" . $product['gambar']);
        }
        
        // Hapus produk
        $query = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            $admin_message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil dihapus!</div>';
        } else {
            $admin_message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal menghapus produk!</div>';
        }
    }
}

// Ambil semua produk
$products = get_all_products($conn);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-cogs"></i> Admin Panel - Daftar Produk</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?page=admin_add_product" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
    </div>
</div>

<hr>

<?php if (!empty($admin_message)) echo $admin_message; ?>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Belum ada produk. <a href="index.php?page=admin_add_product" class="alert-link">Tambah produk baru</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><small>#<?php echo $product['id_produk']; ?></small></td>
                        <td>
                            <?php if (!empty($product['gambar'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" 
                                     width="50" height="50" class="rounded" style="object-fit: cover;">
                            <?php else: ?>
                                <span class="badge bg-secondary">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($product['nama_produk']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars(substr($product['deskripsi'], 0, 50)); ?>...</small>
                        </td>
                        <td><?php echo format_price($product['harga']); ?></td>
                        <td>
                            <form method="POST" class="stock-form d-flex gap-2 align-items-center" 
                                  style="max-width: 150px;">
                                <input type="hidden" name="update_stock" value="1">
                                <input type="hidden" name="product_id" value="<?php echo $product['id_produk']; ?>">
                                <input type="number" name="new_stock" value="<?php echo $product['stok']; ?>" 
                                       min="0" class="form-control form-control-sm" style="width: 70px;">
                                <button type="submit" class="btn btn-sm btn-primary" title="Update Stok">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                            
                            <!-- Stock Badge -->
                            <?php if ($product['stok'] > 15): ?>
                                <span class="badge bg-success mt-1"><i class="fas fa-check"></i> Tersedia (<?php echo $product['stok']; ?>)</span>
                            <?php elseif ($product['stok'] > 0): ?>
                                <span class="badge bg-warning mt-1"><i class="fas fa-exclamation"></i> Sedikit (<?php echo $product['stok']; ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-danger mt-1"><i class="fas fa-times"></i> Habis</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?page=admin_edit_product&id=<?php echo $product['id_produk']; ?>" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="index.php?page=admin_products&delete=<?php echo $product['id_produk']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Stock Distribution Statistics -->
    <hr class="my-5">
    <h4><i class="fas fa-chart-bar"></i> Statistik Pembagian Stok</h4>
    <div class="row mt-4">
        <?php 
        // Calculate stock distribution
        $available = 0;     // > 15
        $low_stock = 0;     // 1-15
        $out_of_stock = 0;  // = 0
        $total_stock = 0;
        
        foreach ($products as $product) {
            if ($product['stok'] > 15) {
                $available++;
            } elseif ($product['stok'] > 0) {
                $low_stock++;
            } else {
                $out_of_stock++;
            }
            $total_stock += $product['stok'];
        }
        ?>
        
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Tersedia</h5>
                    <h2 class="text-success"><?php echo $available; ?></h2>
                    <p class="text-muted">Stok > 15 unit</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Sedikit</h5>
                    <h2 class="text-warning"><?php echo $low_stock; ?></h2>
                    <p class="text-muted">Stok 1-15 unit</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Habis</h5>
                    <h2 class="text-danger"><?php echo $out_of_stock; ?></h2>
                    <p class="text-muted">Stok = 0 unit</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-boxes fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Total Stok</h5>
                    <h2 class="text-info"><?php echo number_format($total_stock); ?></h2>
                    <p class="text-muted">Seluruh unit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mt-5">
        <h5>Ringkasan Stok</h5>
        <div class="progress" style="height: 30px;">
            <?php if ($available > 0): ?>
                <div class="progress-bar bg-success" style="width: <?php echo ($available / count($products)) * 100; ?>%">
                    Tersedia <?php echo $available; ?>
                </div>
            <?php endif; ?>
            <?php if ($low_stock > 0): ?>
                <div class="progress-bar bg-warning" style="width: <?php echo ($low_stock / count($products)) * 100; ?>%">
                    Sedikit <?php echo $low_stock; ?>
                </div>
            <?php endif; ?>
            <?php if ($out_of_stock > 0): ?>
                <div class="progress-bar bg-danger" style="width: <?php echo ($out_of_stock / count($products)) * 100; ?>%">
                    Habis <?php echo $out_of_stock; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
