<?php
/**
 * ===================================================
 * HALAMAN LOGIN
 * ===================================================
 */

$page_title = "Login - Mini E-Commerce";

// Proses login jika form di-submit
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Jangan sanitasi password sebelum hash check
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $login_error = "Email dan password harus diisi!";
    } else if (!validate_email($email)) {
        $login_error = "Format email tidak valid!";
    } else {
        // Cari user di database
        $user = get_user_by_email($conn, $email);
        
        if ($user && verify_password($password, $user['password'])) {
            // Login berhasil - set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect ke halaman yang sesuai
            if ($user['role'] === 'admin') {
                header("Location: index.php?page=admin_products");
            } else {
                header("Location: index.php?page=products");
            }
            exit;
        } else {
            $login_error = "Email atau password salah!";
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="text-center mb-4"><i class="fas fa-sign-in-alt text-primary"></i> Login</h2>
                
                <?php if (!empty($login_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email Anda" required>
                        <small class="text-muted">
                            Demo: admin@ecommerce.com (admin) atau john@example.com (user)
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                        <small class="text-muted">Password demo: admin123 untuk semua akun</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <hr>
                
                <p class="text-center">
                    Belum punya akun? <a href="index.php?page=register" class="text-decoration-none">Daftar di sini</a>
                </p>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="alert alert-info mt-3">
            <strong><i class="fas fa-info-circle"></i> Akun Demo:</strong><br>
            <small>
                Adobe: admin@ecommerce.com (Password: admin123)<br>
                Regular User: john@example.com (Password: admin123)
            </small>
        </div>
    </div>
</div>
