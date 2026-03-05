<?php
/**
 * ===================================================
 * HALAMAN INVOICE / BUKTI TRANSAKSI
 * ===================================================
 */

$page_title = "Invoice - Mini E-Commerce";

// Require login
require_login();

$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($transaction_id <= 0) {
    header("Location: index.php?page=products");
    exit;
}

// Ambil data transaksi
$transaction = get_transaction_by_id($conn, $transaction_id);

if (!$transaction || $transaction['id_user'] != $_SESSION['user_id']) {
    header("Location: index.php?page=products");
    exit;
}

// Ambil detail transaksi
$details = get_transaction_details($conn, $transaction_id);

// Ambil data user
$user = get_user_by_id($conn, $transaction['id_user']);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <!-- Header Invoice -->
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="fas fa-shopping-bag"></i> E-Commerce Store</h4>
                        <small class="text-muted">
                            Jakarta, Indonesia<br>
                            info@ecommerce.com | +62 812-3456-7890
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>INVOICE</h5>
                        <p class="mb-1">
                            <strong>No:</strong> #<?php echo str_pad($transaction_id, 5, '0', STR_PAD_LEFT); ?>
                        </p>
                        <small class="text-muted">
                            <strong>Tanggal:</strong> <?php echo format_date($transaction['tanggal']); ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Data Pembeli -->
            <div class="card-body border-bottom">
                <h6><strong>Kepada:</strong></h6>
                <p class="mb-0"><strong><?php echo htmlspecialchars($user['nama']); ?></strong></p>
                <p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                <p class="text-muted small">Jakarta, Indonesia</p>
            </div>
            
            <!-- Detail Item -->
            <div class="card-body border-bottom">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $detail): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                                    <td class="text-center"><?php echo $detail['jumlah']; ?></td>
                                    <td class="text-end"><?php echo format_price($detail['harga_satuan']); ?></td>
                                    <td class="text-end"><strong><?php echo format_price($detail['subtotal']); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Total -->
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <table class="w-100">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end"><?php echo format_price($transaction['total_harga']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>PPN (0%):</strong></td>
                                <td class="text-end">Rp 0</td>
                            </tr>
                            <tr style="font-size: 1.1rem;">
                                <td><strong>TOTAL:</strong></td>
                                <td class="text-end text-danger"><strong><?php echo format_price($transaction['total_harga']); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Status Pesanan:</strong></p>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-sm btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Aksi -->
        <div class="mt-3">
            <a href="index.php?page=history" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
            </a>
            <a href="index.php?page=products" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Belanja Lagi
            </a>
        </div>
    </div>
</div>

<!-- Print Styling -->
<style media="print">
    .btn, .navbar, .container > * > .btn, .mt-3 {
        display: none !important;
    }
    
    body {
        background: white;
        margin: 0;
        padding: 20px;
    }
    
    .card {
        box-shadow: none !important;
        border-color: #dee2e6 !important;
    }
</style>
