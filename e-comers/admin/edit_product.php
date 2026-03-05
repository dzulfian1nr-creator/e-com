<?php
/**
 * ===================================================
 * HALAMAN ADMIN - EDIT PRODUK
 * ===================================================
 */

$page_title = "Admin - Edit Produk - Mini E-Commerce";

// Require admin
require_admin();

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = get_product_by_id($conn, $product_id);

if (!$product) {
    header("Location: index.php?page=admin_products");
    exit;
}

$form_error = '';
$form_success = false;

// Proses update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $nama_produk = sanitize_input($_POST['nama_produk']);
    $deskripsi = sanitize_input($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    
    // Validasi input
    $errors = [];
    
    if (empty($nama_produk)) {
        $errors[] = "Nama produk harus diisi!";
    }
    
    if ($harga <= 0) {
        $errors[] = "Harga harus lebih dari 0!";
    }
    
    if ($stok < 0) {
        $errors[] = "Stok tidak boleh negatif!";
    }
    
    // Handle file upload
    $gambar_name = $product['gambar']; // Default keep old image
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_size = $_FILES['gambar']['size'];
        
        // Validasi file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Tipe file harus JPG, PNG, atau GIF!";
        }
        
        if ($file_size > 5 * 1024 * 1024) { // 5MB
            $errors[] = "Ukuran file maksimal 5MB!";
        }
        
        // Upload file jika valid
        if (empty($errors)) {
            // Delete old image
            if (!empty($product['gambar']) && file_exists("uploads/" . $product['gambar'])) {
                unlink("uploads/" . $product['gambar']);
            }
            
            $gambar_name = uniqid() . '.' . $file_type;
            $upload_path = "uploads/" . $gambar_name;
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors[] = "Gagal mengupload gambar!";
            }
        }
    }
    
    // Jika tidak ada error, update produk
    if (empty($errors)) {
        $query = "UPDATE produk SET nama_produk = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id_produk = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            $errors[] = "Terjadi kesalahan sistem!";
        } else {
            $stmt->bind_param("ssdisi", $nama_produk, $deskripsi, $harga, $stok, $gambar_name, $product_id);
            
            if ($stmt->execute()) {
                $form_success = true;
                $_SESSION['product_updated'] = "Produk berhasil diupdate!";
                header("Location: index.php?page=admin_products");
                exit;
            } else {
                $errors[] = "Gagal mengupdate produk!";
            }
        }
    }
    
    // Set error message
    if (!empty($errors)) {
        $form_error = implode("<br>", $errors);
    }
}
?>

<h2><i class="fas fa-edit"></i> Edit Produk</h2>
<hr>

<?php if (!empty($form_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $form_error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" 
                               placeholder="Masukkan nama produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Deskripsi produk"><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="harga" name="harga" 
                                           placeholder="0" value="<?php echo htmlspecialchars($product['harga']); ?>" step="1000" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stok" name="stok" 
                                       placeholder="0" value="<?php echo htmlspecialchars($product['stok']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    </div>
                    
                    <?php if (!empty($product['gambar']) && file_exists("uploads/" . $product['gambar'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini:</label>
                            <div>
                                <img src="uploads/<?php echo htmlspecialchars($product['gambar']); ?>" 
                                     width="150" height="150" class="rounded" style="object-fit: cover;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=admin_products" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" name="edit_product" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
