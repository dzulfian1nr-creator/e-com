# Setup Guide - Mini E-Commerce

Panduan lengkap untuk setup dan menjalankan aplikasi Mini E-Commerce.

## ✅ Prasyarat

1. **XAMPP** sudah terinstall (Apache, MySQL, PHP 8.0+)
2. MySQL sudah running
3. File aplikasi sudah di-extract ke `C:\xampp\htdocs\e-comers\`

## 🚀 Langkah-Langkah Setup

### Step 1: Import Database Schema

#### Cara 1: Menggunakan phpMyAdmin (GUI)

1. Buka `http://localhost/phpmyadmin`
2. Klik "Import" di menu atas
3. Choose file → Browse ke `c:\xampp\htdocs\e-comers\database\database.sql`
4. Scroll ke bawah dan klik "Import"
5. Tunggu sampai selesai (page akan refresh)
6. Cek left panel → Database "e_commerce" harus sudah ada

**Expected:** Database "e_commerce" dengan 5 tabel (users, produk, cart, transaksi, detail_transaksi)

#### Cara 2: Menggunakan Command Line

1. Buka Command Prompt / PowerShell
2. Navigate ke folder e-comers:
   ```powershell
   cd C:\xampp\htdocs\e-comers
   ```

3. Import database menggunakan MySQL client:
   ```bash
   mysql -u root -e "CREATE DATABASE e_commerce;"
   mysql -u root e_commerce < database/database.sql
   ```

**Expected Output:** Command selesai tanpa error

### Step 2: Verifikasi Database Connection

Buka file `config/db.php` dan pastikan configurasi:

```php
define('DB_HOST', 'localhost');    // ✓ CORRECT
define('DB_USER', 'root');         // ✓ CORRECT (default XAMPP)
define('DB_PASS', '');             // ✓ CORRECT (default XAMPP no password)
define('DB_NAME', 'e_commerce');   // ✓ CORRECT (database name)
define('DB_PORT', 3306);           // ✓ CORRECT (default MySQL port)
```

> **Note:** Jika Anda mengubah password MySQL atau nama database berbeda, update di sini!

### Step 3: Setup Folder Uploads

Pastikan folder `uploads/` memiliki write permission:

1. Buka Command Prompt as Administrator
2. Navigate ke e-comers folder:
   ```powershell
   cd C:\xampp\htdocs\e-comers
   ```

3. Create uploads folder jika belum ada:
   ```powershell
   mkdir uploads
   ```

4. Set permission (Windows):
   ```powershell
   icacls uploads /grant:r Users:(OI)(CI)F /T
   ```

**Atau manual:**
- Right-click folder `uploads/`
- Properties → Security → Edit
- Select "Users" → Allow "Full Control" → OK

### Step 4: Start Apache & MySQL

1. Buka **XAMPP Control Panel**
2. Klik tombol **"Start"** di baris **Apache**
3. Klik tombol **"Start"** di baris **MySQL**
4. Tunggu sampai status berubah menjadi **"Running"** (text berwarna hijau)

**Expected:**
```
Apache    [Running] ✓
MySQL     [Running] ✓
```

### Step 5: Akses Aplikasi

1. Buka browser (Chrome, Firefox, Edge, Safari)
2. Ketik URL: `http://localhost/e-comers/`
3. Page loading... dan Anda akan ke halaman **Login**

**Expected:**
```
┌──────────────────────────────────────┐
│   TOKO ONLINE - Mini E-Commerce      │
├──────────────────────────────────────┤
│                                      │
│   Email:     [________________]      │
│   Password:  [________________]      │
│                                      │
│              [ Login ]               │
│              [ Register ]            │
│                                      │
│   Demo: admin@ecommerce.com          │
│          Password: admin123          │
└──────────────────────────────────────┘
```

## 🧪 Testing Workflow

### Test 1: Admin Login & Product Management

**Steps:**
1. Login dengan email: `admin@ecommerce.com` password: `admin123`
2. Di halaman Products → Click "Admin Panel"
3. Lihat daftar 8 sample products (Laptop, Monitor, Keyboard, dll)
4. Click "Edit" pada salah satu produk
5. Ubah nama/harga/stok → Click "Update"
6. Lihat perubahan terupdate di list
7. Klik "Tambah Produk Baru"
8. Isi form → Upload gambar → Click "Tambah"
9. Lihat produk baru di list

**Expected:** Semua CRUD operation berfungsi (Create, Read, Update, Delete)

### Test 2: User Registration

**Steps:**
1. Click "Register" link di login page
2. Isi form:
   - Nama: `Test User`
   - Email: `test@example.com`
   - Password: `password123`
   - Confirm: `password123`
3. Click "Register"

**Expected:** 
- Redirect ke login page
- Alert: "Registrasi berhasil, silakan login"
- Bisa login dengan email & password yang baru dibuat

### Test 3: Shopping Cart Flow

**Steps:**
1. Login dengan user biasa
2. Di halaman "Products" → Lihat semua produk
3. Klik "Tambah ke Keranjang" pada produk manapun
4. Ubah quantity → Click "Update Keranjang"
5. Ada notification "Item ditambahkan"
6. Click "Lihat Keranjang"
7. Lihat items dalam keranjang + total harga

**Expected:**
- Cart items ditampilkan dengan detail
- Total price calculated correctly
- Bisa update quantity atau delete items

### Test 4: Checkout & Invoice

**Steps:**
1. Di halaman Cart → Click "Lanjut ke Checkout"
2. Review items, harga, total
3. Centang "Saya setuju dengan pembelian ini"
4. Click "Konfirmasi Pembelian"

**Expected:**
- Redirect ke Invoice page
- Tampilkan nomor transaksi, tanggal, detail items
- Ada tombol "Print Invoice"
- Di Cart → sekarang kosong (items cleared)

### Test 5: Transaction History

**Steps:**
1. Login dengan user
2. Click "Riwayat Transaksi"
3. Lihat list semua pembelian user
4. Click "Lihat Invoice" untuk melihat detail

**Expected:**
- List transaksi dengan nomor, tanggal, total, status
- Bisa click untuk lihat detail invoice

### Test 6: Stock Management

**Steps:**
1. Admin login → Lihat stok produk "Laptop" (default: 15)
2. User login → Add 5 "Laptop" ke cart → Checkout
3. Admin → Edit "Laptop" → Lihat stok berkurang jadi 10

**Expected:** Stok otomatis berkurang setelah checkout

## 🐛 Troubleshooting

### Error: "Connection Refused"
```
Error: Connection refused (connection_error)
```

**Solution:**
- [ ] MySQL tidak running → Start dari XAMPP Control Panel
- [ ] Database "e_commerce" tidak ada → Import database.sql
- [ ] Cek config/db.php → DB credentials benar?

### Error: "Access Denied for user 'root'@'localhost'"
```
Fatal error: mysqli_connect(): Access denied for user 'root'@'localhost'
```

**Solution:**
- [ ] MySQL password berbeda → Update di config/db.php
- [ ] Syntax password salah → Check quotes dan escape

### Error: "Table doesn't exist"
```
Error: Table 'e_commerce.produk' doesn't exist
```

**Solution:**
- [ ] Database tidak di-import → Import database.sql
- [ ] Database name salah di config/db.php
- [ ] Re-import fresh database.sql

### Error: "Can't upload file"
```
file_exists() failed for /e-comers/uploads/filename.jpg
```

**Solution:**
- [ ] Folder `uploads/` tidak ada → Create manual
- [ ] Permission denied → Give write access (icacls command)
- [ ] Check server error log

### Error: "Session not working"
```
Warning: Can't read session data
```

**Solution:**
- [ ] Check php.ini session.save_path setting
- [ ] Temp folder not writable → Give permissions
- [ ] Browser cookies disabled → Enable cookies

### Blank Page / No Content
```
Page loads but shows nothing
```

**Solution:**
- [ ] Check browser F12 → Console untuk JavaScript errors
- [ ] Check Apache error log: `C:\xampp\apache\logs\error.log`
- [ ] Check PHP error log: `C:\xampp\php\logs\php_error.log`
- [ ] Add debug di index.php:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```

## 📝 Database Verification

Untuk verify database sudah import dengan benar:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database "e_commerce" di left panel
3. Verify tables ada:
   - [ ] users (1 admin + 2 users)
   - [ ] produk (8 products)
   - [ ] cart (empty)
   - [ ] transaksi (empty atau ada sample)
   - [ ] detail_transaksi (empty atau ada sample)

**SQL Query untuk check data:**

```sql
-- Check users
SELECT COUNT(*) as total_users FROM users;
-- Result: 3

-- Check products
SELECT COUNT(*) as total_products FROM produk;
-- Result: 8

-- Check admin user
SELECT email, role FROM users WHERE role = 'admin';
-- Result: admin@ecommerce.com | admin

-- Check if password hash valid
SELECT email, password FROM users WHERE id = 1;
-- Result: password harus dimulai dengan $2y$ (bcrypt hash)
```

## 🔑 Default Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@ecommerce.com | admin123 | Admin |
| john@example.com | admin123 | User |
| jane@example.com | admin123 | User |

## 📊 Performance Tips

### 1. Optimize Database Queries
Sudah ada INDEXES pada:
- `produk.nama_produk`
- `produk.id_produk`
- `transaksi.id_user`
- `transaksi.tanggal`

### 2. Cache Static Assets
Browser cache CSS, JS, images otomatis

### 3. Limit Upload Size
Max 5MB per image (set di `admin/add_product.php`)

### 4. Session Cleanup
Session files di-cleanup automatic oleh PHP

## 🔄 Backup & Restore

### Backup Database

```bash
mysqldump -u root e_commerce > backup.sql
```

### Restore Database

```bash
mysql -u root e_commerce < backup.sql
```

## 📱 Browser Compatibility

Tested pada:
- ✓ Chrome 90+
- ✓ Firefox 88+
- ✓ Edge 90+
- ✓ Safari 14+
- ✓ Mobile browsers (responsive)

## 🎓 Learning Resources

Concepts used di aplikasi:
- **MVC Pattern** - Model, View, Controller separation
- **Session Management** - PHP $_SESSION
- **Password Hashing** - bcrypt algorithm
- **Prepared Statements** - SQL injection prevention
- **Database Transactions** - ACID properties
- **File Upload** - Validation & security
- **Responsive Design** - Bootstrap + CSS Grid
- **Form Validation** - Frontend & backend

## 📞 Next Steps

Setelah setup berhasil:

1. **Explore code** di folder `includes/functions.php` → Lihat helper functions
2. **Test all flows** → Login, register, products, cart, checkout
3. **Try admin panel** → Manage products, test CRUD
4. **Check database** → phpMyAdmin untuk verify data
5. **Customize** → Change colors, texts, branding di `assets/css/style.css`

---

**Good luck! Happy coding! 🚀**

For detailed documentation, see [README.md](README.md)
