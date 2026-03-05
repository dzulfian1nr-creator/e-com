# Quick Reference - Mini E-Commerce

Referensi cepat fitur, login, dan troubleshooting.

## 🌐 URLs

| Page | URL | Login Required | Role |
|------|-----|----------------|------|
| Home/Products | `/e-comers/` | No | Public |
| Login | `/?page=login` | No | Public |
| Register | `/?page=register` | No | Public |
| Products | `/?page=products` | No | Public |
| Cart | `/?page=cart` | **Yes** | User |
| Checkout | `/?page=checkout` | **Yes** | User |
| Invoice | `/?page=invoice&id=1` | **Yes** | User |
| History | `/?page=history` | **Yes** | User |
| Admin Products | `/?page=admin_products` | **Yes** | Admin |
| Add Product | `/?page=admin_add_product` | **Yes** | Admin |
| Edit Product | `/?page=admin_edit_product&id=1` | **Yes** | Admin |

## 👤 Test Accounts

### Admin Account
```
Email: admin@ecommerce.com
Password: admin123
Role: Admin
Access: Product management
```

### User Account 1
```
Email: john@example.com
Password: admin123
Role: User
Access: Shopping only
```

### User Account 2
```
Email: jane@example.com
Password: admin123
Role: User
Access: Shopping only
```

## 📁 Key Files

### Core Application
- `index.php` - Main router & entry point
- `config/db.php` - Database configuration

### Helper Functions
- `includes/functions.php` - All utility functions (Auth, Cart, Checkout, etc.)

### Pages
- `pages/login.php` - Authentication
- `pages/products.php` - Product browsing
- `pages/cart.php` - Cart management
- `pages/checkout.php` - Order confirmation
- `pages/invoice.php` - Receipt

### Admin
- `admin/products.php` - Product list
- `admin/add_product.php` - Create product
- `admin/edit_product.php` - Update product

### Styling/Scripts
- `assets/css/style.css` - Custom CSS
- `assets/js/scripts.js` - Client-side logic

## 🔑 Key Functions

All functions di `includes/functions.php`:

### Authentication
```php
hash_password($password)              // Hash password dengan bcrypt
verify_password($password, $hash)     // Verify password
get_user_by_email($conn, $email)      // Get user dari database
is_logged_in()                        // Check jika user sudah login
is_admin()                            // Check jika user adalah admin
require_login()                       // Redirect jika belum login
require_admin()                       // Redirect jika bukan admin
```

### Validation
```php
sanitize_input($data)                 // Sanitize user input
validate_email($email)                // Validate email format
```

### Products
```php
get_all_products($conn)               // Get semua produk
get_product_by_id($conn, $id)         // Get single product
```

### Cart
```php
get_cart_items($conn, $user_id)       // Get cart items user
add_to_cart($conn, $user_id, $product_id, $quantity)  // Add to cart
update_cart_item($conn, $id_cart, $quantity)  // Update quantity
delete_cart_item($conn, $id_cart)     // Remove from cart
clear_cart($conn, $user_id)           // Clear all items
calculate_cart_total($cart_items)     // Count total
```

### Checkout
```php
process_checkout($conn, $user_id)     // Main checkout logic
// INCLUDES: Stock validation, transaction creation, stock reduction, cart clear
```

### Transactions
```php
get_transaction_by_id($conn, $id)     // Get transaction detail
get_transaction_details($conn, $id)   // Get transaction items
```

### Formatting
```php
format_price($price)                  // Format as Rupiah (Rp)
format_date($date)                    // Format date time
```

## 🚀 Common Tasks

### Add Product (as Admin)

1. Login: `admin@ecommerce.com` / `admin123`
2. Go to: `/?page=admin_products`
3. Click "Tambah Produk Baru"
4. Fill form:
   - Nama Produk: e.g., "Mouse Gaming"
   - Deskripsi: "High precision gaming mouse"
   - Harga: e.g., "300000"
   - Stok: e.g., "25"
   - Gambar: Upload JPG/PNG/GIF (max 5MB)
5. Click "Tambah"

### Buy Product (as User)

1. Login: `john@example.com` / `admin123`
2. Go to: `/?page=products`
3. For each product:
   - Set quantity in input
   - Click "Tambah ke Keranjang"
4. Notification: "✓ Produk ditambahkan ke keranjang"
5. Go to: `/?page=cart`
6. Review items
7. Click "Lanjut ke Checkout"
8. Review order
9. Check "Saya setuju dengan pembelian ini"
10. Click "Konfirmasi Pembelian"
11. Invoice generated!

### Check Stock Deduction

1. Before: Admin edit product → See stock = 15
2. During: User checkout → Buy 5 items
3. After: Admin check → Stock = 10 (15 - 5)

> **Note:** Uses database transaction to prevent double orders

## 🐛 Common Issues & Quick Fixes

| Issue | Solution |
|-------|----------|
| "Connection Refused" | Start MySQL from XAMPP Control Panel |
| Database not found | Import database.sql from phpMyAdmin |
| Can't upload image | Check uploads/ folder write permission |
| Session not work | Clear browser cookies, restart XAMPP |
| Blank page | Check F12 console, read error.log |
| Login always fails | Check database has users table |
| Cart doesn't work | Must login first, check session |
| Checkout fails | Insufficient stock, check database |

## 📝 Code Examples

### Add Item to Cart
```php
// In pages/products.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if (add_to_cart($conn, $_SESSION['user_id'], $product_id, $quantity)) {
        $_SESSION['success'] = "✓ Produk ditambahkan ke keranjang";
    }
}
```

### Process Checkout
```php
// In pages/checkout.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = process_checkout($conn, $_SESSION['user_id']);
    
    if ($transaction_id) {
        // Redirect to invoice
        header("Location: ?page=invoice&id=$transaction_id");
    }
}
```

### Update Product
```php
// In admin/edit_product.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = "UPDATE produk SET 
              nama_produk = ?,
              harga = ?,
              stok = ?
              WHERE id_produk = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdii", $name, $price, $stock, $id);
    $stmt->execute();
}
```

### Check Admin Role
```php
// Any admin-only page
require_admin();  // Will redirect if not admin

// Or manual check
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied!";
}
```

## 🔐 Security Checklist

✅ **Passwords**
- Hashed dengan bcrypt
- Min 6 chars validation
- Verified during login

✅ **SQL Queries**
- All use prepared statements
- Prevents SQL injection

✅ **User Input**
- Sanitized dengan htmlspecialchars()
- Validated before processing

✅ **Sessions**
- Check is_logged_in() on protected pages
- Check is_admin() on admin pages

✅ **File Upload**
- Validate extension (JPG/PNG/GIF)
- Validate size (max 5MB)
- Unique filename (uniqid)

✅ **Database**
- Transactions for atomic operations
- Row locking with FOR UPDATE
- Rollback on error

## 📊 Database Tables

### users
```
id | nama | email | password | role | created_at
```

### produk
```
id_produk | nama_produk | deskripsi | harga | stok | gambar
```

### cart
```
id_cart | id_user | id_produk | jumlah | created_at
```

### transaksi
```
id_transaksi | id_user | tanggal | total_harga | status | created_at
```

### detail_transaksi
```
id_detail | id_transaksi | id_produk | jumlah | harga_satuan | subtotal
```

## 🎯 Development Tips

### Enable Debug Mode
Edit `config/db.php`:
```php
// Uncomment these lines
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Database
```sql
-- Open phpMyAdmin
-- Execute queries to debug

-- See all users
SELECT * FROM users;

-- See products low stock
SELECT * FROM produk WHERE stok < 5;

-- See all transactions
SELECT * FROM transaksi ORDER BY tanggal DESC;
```

### Add Custom Function
1. Open `includes/functions.php`
2. Add new function at end
3. Use in any page with just: `function_name($args)`

### Style Customization
Edit `assets/css/style.css`:
```css
/* Change primary color */
:root {
    --primary-color: #007bff;    /* Change this */
}

/* Or modify directly */
.btn-primary {
    background-color: #yourcolor;
}
```

## 📞 Support

For more help:
- Check `README.md` for detailed documentation
- Check `SETUP_GUIDE.md` for step-by-step setup
- Check `DOKUMENTASI_LENGKAP.md` (coming soon)
- Read code comments in each file

---

**Last Updated:** 2024
**Version:** 1.0
**Status:** Production Ready ✅
