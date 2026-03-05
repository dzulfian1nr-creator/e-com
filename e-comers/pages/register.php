<?php
/**
 * ===================================================
 * HALAMAN REGISTER
 * ===================================================
 */

$page_title = "Daftar - Mini E-Commerce";
$register_error = '';
$register_success = false;

// Proses register jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize_input($_POST['nama']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validasi input
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = "Nama harus diisi!";
    }
    
    if (empty($email) || !validate_email($email)) {
        $errors[] = "Email tidak valid!";
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi!";
    } else if (!validate_password($password)) {
        $errors[] = "Password minimal 6 karakter!";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Password tidak cocok!";
    }
    
    // Cek email sudah terdaftar
    if (empty($errors)) {
        $existing_user = get_user_by_email($conn, $email);
        if ($existing_user) {
            $errors[] = "Email sudah terdaftar!";
        }
    }
    
    // Jika tidak ada error, simpan user baru
    if (empty($errors)) {
        $hashed_password = hash_password($password);
        
        $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            $errors[] = "Terjadi kesalahan sistem!";
        } else {
            $stmt->bind_param("sss", $nama, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $register_success = true;
                $_SESSION['register_success'] = "Daftar berhasil! Silakan login dengan akun Anda.";
                header("Location: index.php?page=login");
                exit;
            } else {
                $errors[] = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
    
    // Set error message
    if (!empty($errors)) {
        $register_error = implode("<br>", $errors);
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="text-center mb-4"><i class="fas fa-user-plus text-success"></i> Daftar Akun Baru</h2>
                
                <?php if (!empty($register_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $register_error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['register_success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" 
                               placeholder="Masukkan nama lengkap" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email Anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Minimal 6 karakter" required>
                        <small class="text-muted">Password harus terdiri dari minimal 6 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                               placeholder="Ulangi password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                </form>
                
                <hr>
                
                <p class="text-center">
                    Sudah punya akun? <a href="index.php?page=login" class="text-decoration-none">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
