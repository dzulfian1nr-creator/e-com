<?php
/**
 * ===================================================
 * HALAMAN RIWAYAT TRANSAKSI
 * ===================================================
 */

$page_title = "Riwayat Transaksi - Mini E-Commerce";

// Require login
require_login();

// Ambil riwayat transaksi user
$query = "SELECT * FROM transaksi WHERE id_user = ? ORDER BY tanggal DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2><i class="fas fa-history"></i> Riwayat Transaksi</h2>
<hr>

<?php if (empty($transactions)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Anda belum melakukan pembelian apapun.
        <a href="index.php?page=products" class="alert-link">Mulai belanja sekarang</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $trans): ?>
                    <tr>
                        <td>
                            <strong>#<?php echo str_pad($trans['id_transaksi'], 5, '0', STR_PAD_LEFT); ?></strong>
                        </td>
                        <td><?php echo format_date($trans['tanggal']); ?></td>
                        <td>
                            <strong><?php echo format_price($trans['total_harga']); ?></strong>
                        </td>
                        <td>
                            <?php if ($trans['status'] === 'berhasil'): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Berhasil</span>
                            <?php elseif ($trans['status'] === 'pending'): ?>
                                <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Batal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?page=invoice&id=<?php echo $trans['id_transaksi']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-receipt"></i> Invoice
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="mt-3">
    <a href="index.php?page=products" class="btn btn-secondary">
        <i class="fas fa-shopping-bag"></i> Belanja Lagi
    </a>
</div>
