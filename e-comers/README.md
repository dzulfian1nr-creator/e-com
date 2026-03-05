# Mini E-Commerce - Aplikasi Toko Online Sederhana

Aplikasi Mini E-Commerce yang dibuat menggunakan **PHP Native** dan **MySQL** tanpa framework. Dengan tampilan modern menggunakan Bootstrap dan responsive design.

## 🎯 Fitur Utama

✅ **Authentication System**
- Login / Register User
- System session untuk autentikasi
- Password hashing dengan bcrypt

✅ **Product Management**
- Daftar produk dengan gambar, harga, stok
- Search produk real-time

✅ **Shopping Cart**
- Tambah/hapus produk dari keranjang
- Update jumlah produk
- Hitung total otomatis

✅ **Checkout System**
- Checkout dengan validasi stok
- Automatic stock reduction
- Transaction history

✅ **Admin Panel**
- CRUD Produk (Create, Read, Update, Delete)
- Manage stok produk
- Upload gambar produk

✅ **Invoice & Transaction**
- Invoice digital setiap transaksi
- Riwayat transaksi user
- Print invoice

## 📁 Struktur Folder

```
e-comers/
├── index.php              # Main entry point
├── config/
│   └── db.php             # Database configuration
├── includes/
│   ├── functions.php      # Helper & utility functions
│   ├── header.php         # Navbar template
│   ├── footer.php         # Footer template
│   └── logout.php         # Logout handler
├── pages/
│   ├── login.php          # Halaman login
│   ├── register.php       # Halaman register
│   ├── products.php       # Daftar produk
│   ├── cart.php           # Keranjang belanja
│   ├── checkout.php       # Checkout
│   ├── invoice.php        # Invoice/bukti transaksi
│   └── history.php        # Riwayat transaksi
├── admin/
│   ├── products.php       # Admin - Daftar produk
│   ├── add_product.php    # Admin - Tambah produk
│   └── edit_product.php   # Admin - Edit produk
├── assets/
│   ├── css/
│   │   └── style.css      # Custom styling
│   ├── js/
│   │   └── scripts.js     # Custom JavaScript
│   └── images/            # Gambar statis
├── uploads/               # Folder upload produk
├── database/
│   └── database.sql       # SQL dump
└── README.md              # Dokumentasi ini
```

## 🗄️ Struktur Database

### 1. users
```sql
- id (INT, Primary Key)
- nama (VARCHAR)
- email (VARCHAR, Unique)
- password (VARCHAR, Hashed)
- role (ENUM: user/admin)
- created_at, updated_at (Timestamp)
```

### 2. produk
```sql
- id_produk (INT, Primary Key)
- nama_produk (VARCHAR)
- deskripsi (TEXT)
- harga (DECIMAL)
- stok (INT)
- gambar (VARCHAR)
- created_at, updated_at (Timestamp)
```

### 3. cart
```sql
- id_cart (INT, Primary Key)
- id_user (INT, Foreign Key)
- id_produk (INT, Foreign Key)
- jumlah (INT)
- created_at (Timestamp)
- UNIQUE: id_user + id_produk
```

### 4. transaksi
```sql
- id_transaksi (INT, Primary Key)
- id_user (INT, Foreign Key)
- tanggal (DATETIME)
- total_harga (DECIMAL)
- status (ENUM: pending/berhasil/batal)
- created_at (Timestamp)
```

### 5. detail_transaksi
```sql
- id_detail (INT, Primary Key)
- id_transaksi (INT, Foreign Key)
- id_produk (INT, Foreign Key)
- jumlah (INT)
- harga_satuan (DECIMAL)
- subtotal (DECIMAL)
```

## 🚀 Instalasi

### 1. Setup Database

1. Buka localhost/phpmyadmin
2. Buat database baru atau import file `database/database.sql`

### 2. Konfigurasi

Edit file `config/db.php` sesuai kredensial database Anda:
```php
define('DB_HOST', 'localhost');     // Host database
define('DB_USER', 'root');           // Username
define('DB_PASS', '');               // Password
define('DB_NAME', 'e_commerce');     // Nama database
```

### 3. Setup Folder

Pastikan folder `uploads/` memiliki **write permission**:
```bash
chmod 755 uploads/
```

### 4. Akses Aplikasi

Buka browser dan akses:
- **User**: http://localhost/e-comers/
- **Admin**: http://localhost/e-comers/ (login dengan admin account)

## 👤 Akun Demo

### Admin
- Email: `admin@ecommerce.com`
- Password: `admin123`

### User
- Email: `john@example.com`
- Password: `admin123`
- Email: `jane@example.com`
- Password: `admin123`

## 🔐 Analisis HOTS (Higher Order Thinking Skills)

### 1. Mencegah Double Order

Diimplementasikan menggunakan **Database Transaction**:

```php
function process_checkout($conn, $user_id) {
    // START TRANSACTION
    $conn->begin_transaction();
    
    try {
        // 1. Validasi stok dengan row locking
        $query = "SELECT stok FROM produk WHERE id_produk = ? FOR UPDATE";
        // Mencegah race condition
        
        // 2. Insert transaksi
        // 3. Insert detail transaksi
        // 4. UPDATE stok produk
        // 5. DELETE dari cart
        
        // COMMIT jika semua sukses
        $conn->commit();
    } catch (Exception $e) {
        // ROLLBACK jika ada error
        $conn->rollback();
    }
}
```

**Cara Kerja:**
- `BEGIN_TRANSACTION` - Mulai transaksi database
- `FOR UPDATE` pada SELECT - Lock baris untuk prevent concurrent updates
- `COMMIT` - Simpan semua perubahan jika sukses
- `ROLLBACK` - Batalkan semua perubahan jika ada error

**Hasil:**
- Stok tidak bisa negative atau double-sold
- Konsistensi data terjaga
- Jika ada error, semua perubahan di-rollback otomatis

### 2. Validasi Input

Implementasi validasi di beberapa level:

**Level 1: Input Sanitization**
```php
function sanitize_input($data) {
    $data = trim($data);              // Remove whitespace
    $data = stripslashes($data);      // Remove backslashes
    $data = htmlspecialchars($data);  // Escape HTML
    return $data;
}
```

**Level 2: Type Validation**
```php
// Intval untuk validasi angka
$product_id = intval($_POST['product_id']);

// Filter_var untuk email
filter_var($email, FILTER_VALIDATE_EMAIL);

// Custom validation
function validate_password($password) {
    return strlen($password) >= 6;
}
```

**Level 3: Database Level (Prepared Statements)**
```php
// Prevent SQL Injection dengan prepared statements
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
```

**Level 4: Frontend Validation**
```html
<form method="POST">
    <input type="email" required>
    <input type="number" min="1" max="<?php echo $stock; ?>">
    <input type="password" minlength="6" required>
</form>
```

## 🔄 Alur Sistem

```
┌─────────────┐
│   Login     │ → Validasi email & password → Create session
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│  Lihat Produk       │ → SELECT * FROM produk
│  (Browse Catalog)   │
└──────┬──────────────┘
       │
       ▼
┌──────────────────┐
│ Tambah ke Cart   │ → INSERT INTO cart atau UPDATE quantity
│                  │ → Validasi: stok cukup?
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Lihat Keranjang  │ → SELECT dari cart + produk
│                  │ → Hitung total
└──────┬───────────┘
       │
       ▼
┌──────────────────────────┐
│ Checkout                 │ → Validasi stok akhir
│                          │ → BEGIN TRANSACTION
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────────┐
│ Process Transaksi            │ → INSERT transaksi
│                              │ → INSERT detail transaksi
│ ├─ Lock stok produk          │ → FOR UPDATE
│ ├─ Kurangi stok              │ → UPDATE stok
│ ├─ Clear cart                │ → DELETE cart
│ └─ COMMIT/ROLLBACK           │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────┐
│ Tampilkan Invoice    │ → SELECT transaksi + detail
│ & Bukti Transaksi    │ → Print atau download
└──────────────────────┘
```

## 🔐 Security Features

✅ **Password Security**
- Hash dengan bcrypt (tidak reversible)
- Verify dengan `password_verify()`

✅ **SQL Injection Prevention**
- Prepared statements untuk semua query
- Parameter binding dengan `bind_param()`

✅ **XSS Prevention**
- `htmlspecialchars()` untuk output
- `strip_tags()` jika perlu

✅ **CSRF Protection**
- $_POST request validation
- Session-based authentication

✅ **Session Security**
- Session timeout
- Admin check untuk akses admin panel

## 📝 Validasi Form yang Diterapkan

### Login Form
```
✓ Email harus valid format
✓ Password minimal 6 karakter
✓ Check database untuk user exist
✓ Verify password dengan hash
```

### Register Form
```
✓ Nama tidak boleh kosong
✓ Email harus valid
✓ Email tidak boleh duplikat
✓ Password minimal 6 karakter
✓ Password harus cocok dengan konfirmasi
```

### Add/Edit Produk
```
✓ Nama produk tidak boleh kosong
✓ Harga harus > 0
✓ Stok tidak boleh negatif
✓ File gambar: JPG, PNG, GIF max 5MB
✓ Create unique filename untuk gambar
```

### Add to Cart
```
✓ User harus login
✓ Produk harus exist
✓ Stok harus cukup
✓ Quantity > 0
```

### Checkout
```
✓ Cart tidak boleh kosong
✓ Validasi stok SEbelum checkout
✓ Transaction atomic (all or nothing)
✓ Stok kurang → ROLLBACK transaksi
```

## 🛠️ Maintenance

### Clear Session Manual
```php
session_destroy();
```

### Reset Database
1. Backup data penting
2. Drop database: `DROP DATABASE e_commerce;`
3. Import ulang: `database/database.sql`

### Debug Mode
Set di `config/db.php`:
```php
// Uncomment untuk debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
```

## 📊 Fitur yang Bisa Dikembangkan

- [ ] Payment Gateway Integration
- [ ] Email notification
- [ ] Admin dashboard dengan statistik
- [ ] Product rating & review
- [ ] Wishlist
- [ ] Coupon/Promo code
- [ ] Multiple address untuk pengiriman
- [ ] Real-time inventory tracking
- [ ] PDF invoice download
- [ ] Two-factor authentication

## 📞 Support

Jika ada pertanyaan/error, silakan check:
1. Database connection di `config/db.php`
2. Folder `uploads/` permissions
3. Browser console untuk JS errors
4. PHP error log

---

**Created:** 2024
**License:** MIT
**Version:** 1.0
