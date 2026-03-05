# File & Folder Manifest

**Project:** Mini E-Commerce Application  
**Status:** ✅ Complete & Ready  
**Last Updated:** 2024

---

## 📁 Directory Structure

```
c:\xampp\htdocs\e-comers\
│
├── 📄 index.php                          ← MAIN APPLICATION ROUTER
├── 📄 README.md                          ← START HERE - PROJECT OVERVIEW
├── 📄 SETUP_GUIDE.md                     ← SETUP INSTRUCTIONS
├── 📄 QUICK_REFERENCE.md                 ← QUICK ACCESS GUIDE
├── 📄 DEPLOYMENT_CHECKLIST.md            ← VERIFICATION CHECKLIST
├── 📄 FEATURES_VERIFICATION.md           ← TEST SCENARIOS
├── 📄 FILE_MANIFEST.md                   ← THIS FILE
│
├── 📁 config/
│   └── 📄 db.php                         ← DATABASE CONFIGURATION
│
├── 📁 includes/
│   ├── 📄 functions.php                  ← HELPER FUNCTIONS (Core Logic)
│   ├── 📄 header.php                     ← NAVBAR TEMPLATE
│   ├── 📄 footer.php                     ← FOOTER TEMPLATE
│   └── 📄 logout.php                     ← LOGOUT HANDLER
│
├── 📁 pages/
│   ├── 📄 login.php                      ← LOGIN PAGE
│   ├── 📄 register.php                   ← REGISTRATION PAGE
│   ├── 📄 products.php                   ← PRODUCT LISTING
│   ├── 📄 cart.php                       ← SHOPPING CART
│   ├── 📄 checkout.php                   ← ORDER CHECKOUT
│   ├── 📄 invoice.php                    ← INVOICE/RECEIPT
│   └── 📄 history.php                    ← TRANSACTION HISTORY
│
├── 📁 admin/
│   ├── 📄 products.php                   ← ADMIN PRODUCT LIST
│   ├── 📄 add_product.php                ← ADD NEW PRODUCT
│   └── 📄 edit_product.php               ← EDIT EXISTING PRODUCT
│
├── 📁 assets/
│   ├── 📁 css/
│   │   └── 📄 style.css                  ← CUSTOM STYLING
│   ├── 📁 js/
│   │   └── 📄 scripts.js                 ← JAVASCRIPT LOGIC
│   └── 📁 images/
│       └── (static images - if any)
│
├── 📁 database/
│   └── 📄 database.sql                   ← COMPLETE SQL SCHEMA
│
├── 📁 uploads/
│   └── (product images stored here)
│
└── 📁 browser cache/
   └── (temp files - ignore)
```

---

## 📄 Core Application Files

### `index.php` (Main Router)
**Purpose:** Entry point for entire application  
**Size:** ~90 lines  
**Key Functions:**
- Session management (`session_start()`)
- Route dispatcher (page routing)
- Header/footer inclusion
- Database connection management

**Used By:** All requests go through this file  
**Dependencies:** config/db.php, all page files

**Key Code:**
```php
session_start();
$page = sanitize_input($_GET['page'] ?? 'products');
$allowed_pages = ['login', 'register', 'products', 'cart', 'checkout', 'invoice', 'history', 'admin_products', 'admin_add_product', 'admin_edit_product'];
// Routes to appropriate page file
```

---

## 🔧 Configuration Files

### `config/db.php` (Database Connection)
**Purpose:** Single point for database configuration  
**Size:** ~15 lines  
**Contains:**
- DB_HOST (localhost)
- DB_USER (root)
- DB_PASS (empty)
- DB_NAME (e_commerce)
- DB_PORT (3306)
- MySQLi connection object ($conn)

**Used By:** All files that need database access  
**Key Code:**
```php
define('DB_HOST', 'localhost');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
```

**Modification:** Only file you may need to edit if:
- Your MySQL has a password
- Database name is different
- Using different host

---

## 🎯 Helper Functions Library

### `includes/functions.php` (Core Logic)
**Purpose:** All utility functions for application  
**Size:** 350+ lines, 50+ functions  
**Categories:**

#### Authentication Functions
- `hash_password($password)` - Hash password with bcrypt
- `verify_password($password, $hash)` - Verify password
- `get_user_by_email($conn, $email)` - Fetch user from DB
- `get_user_by_id($conn, $id)` - Fetch user by ID
- `is_logged_in()` - Check if user logged in
- `is_admin()` - Check if user is admin
- `require_login()` - Redirect if not logged in
- `require_admin()` - Redirect if not admin

#### Validation Functions
- `sanitize_input($data)` - Clean user input
- `validate_email($email)` - Validate email format
- `validate_password($password)` - Check password requirements

#### Product Functions
- `get_all_products($conn)` - Get all products
- `get_product_by_id($conn, $id)` - Get single product
- `count_products($conn)` - Count products

#### Cart Functions
- `get_cart_items($conn, $user_id)` - Get user's cart items with product details
- `add_to_cart($conn, $user_id, $product_id, $quantity)` - Add/update cart item
- `update_cart_item($conn, $id_cart, $quantity)` - Change quantity
- `delete_cart_item($conn, $id_cart)` - Remove from cart
- `clear_cart($conn, $user_id)` - Remove all cart items
- `calculate_cart_total($cart_items)` - Sum cart prices

#### Checkout & Transaction Functions
- `process_checkout($conn, $user_id)` - **MAIN CHECKOUT LOGIC**
  - Validates stock
  - Uses database transaction
  - Creates order records
  - Deducts stock
  - Clears cart
  - Handles errors with rollback

#### Transaction History Functions
- `get_transaction_by_id($conn, $id)` - Get transaction header
- `get_transaction_details($conn, $id)` - Get transaction line items
- `get_user_transactions($conn, $user_id)` - Get all user transactions

#### Formatting Functions
- `format_price($price)` - Convert to Rupiah format (Rp 50.000)
- `format_date($date)` - Format datetime string

**Used By:** Every other file in application  
**Critical:** Must not be modified without careful testing

---

## 📋 Page Files

### `pages/login.php` (Authentication)
**Purpose:** User login page  
**Size:** ~60 lines  
**Features:**
- Email + password form
- Validate credentials
- Create user session
- Redirect by role (admin/user)
- Error messages for invalid login

**URL:** `/?page=login`  
**Requires Login:** No  
**Database Tables:** users

**Form Validation:**
- Email: Required, must be valid format
- Password: Required, min 6 chars
- Credentials must match database

---

### `pages/register.php` (Registration)
**Purpose:** New user account creation  
**Size:** ~80 lines  
**Features:**
- Registration form (nama, email, password)
- Input validation (all fields)
- Duplicate email check
- Password hash and store
- Success/error messages

**URL:** `/?page=register`  
**Requires Login:** No  
**Database Tables:** users (INSERT)

**Form Validation:**
- Nama: Required, not empty
- Email: Required, valid format, UNIQUE
- Password: Min 6 characters
- Confirmation: Must match password

---

### `pages/products.php` (Product Listing)
**Purpose:** Display all products with search  
**Size:** ~100 lines  
**Features:**
- Product grid display (4-column responsive)
- Product image, name, price, stock
- Add to cart functionality
- Search/filter by name
- Require login to add to cart
- Stock validation

**URL:** `/?page=products` (default)  
**Requires Login:** No (to view), Yes (to add cart)  
**Database Tables:** produk, cart

**Features:**
- Show all products
- Search filters by product name
- Add to cart with quantity input
- Stock availability check

---

### `pages/cart.php` (Shopping Cart)
**Purpose:** View and manage shopping cart  
**Size:** ~90 lines  
**Features:**
- Display cart items with details
- Update quantity
- Delete items
- Calculate total
- Proceed to checkout
- Empty cart message when empty

**URL:** `/?page=cart`  
**Requires Login:** Yes  
**Database Tables:** cart, produk

**Validations:**
- User must be logged in
- Quantity must be positive
- Stock must be available

---

### `pages/checkout.php` (Order Confirmation)
**Purpose:** Final order confirmation before purchase  
**Size:** ~80 lines  
**Features:**
- Display order summary
- Show items, prices, total
- Confirmation checkbox
- Process order
- Redirect to invoice on success

**URL:** `/?page=checkout`  
**Requires Login:** Yes  
**Database Tables:** cart, produk, transaksi, detail_transaksi

**Key Logic:**
- Calls `process_checkout()` function
- Returns transaction ID
- Redirects to invoice page

---

### `pages/invoice.php` (Receipt/Invoice)
**Purpose:** Display transaction receipt  
**Size:** ~60 lines  
**Features:**
- Show transaction header (ID, date, customer)
- Itemized product list
- Print-friendly format
- Print button
- Calculate totals

**URL:** `/?page=invoice&id=1`  
**Requires Login:** Yes  
**Database Tables:** transaksi, detail_transaksi, produk, users

**Security:** Only user who made transaction can view

---

### `pages/history.php` (Transaction History)
**Purpose:** Show all user purchases  
**Size:** ~60 lines  
**Features:**
- List all user transactions
- Show transaction number (zero-padded)
- Show date and total amount
- Show status badge
- Links to view invoices

**URL:** `/?page=history`  
**Requires Login:** Yes  
**Database Tables:** transaksi, users

**Validation:** Only shows logged-in user's transactions

---

## 👨‍💼 Admin Files

### `admin/products.php` (Product List)
**Purpose:** Admin view all products  
**Size:** ~80 lines  
**Features:**
- List all products in table
- Show name, price, stock, image
- Color-coded stock status (green/yellow/red)
- Edit button
- Delete button
- Add new product button

**URL:** `/?page=admin_products`  
**Requires Login:** Yes  
**Requires Role:** Admin  
**Database Tables:** produk

---

### `admin/add_product.php` (Create Product)
**Purpose:** Add new product  
**Size:** ~70 lines  
**Features:**
- Form for new product entry
- Nama, deskripsi, harga, stok, gambar
- File upload handling
- Validation on all fields
- Success/error messages

**URL:** `/?page=admin_add_product`  
**Requires Login:** Yes  
**Requires Role:** Admin  
**Database Tables:** produk (INSERT)

**Validation:**
- Nama: Required
- Harga: Must be > 0
- Stok: Must be >= 0
- File: JPG/PNG/GIF only, max 5MB

---

### `admin/edit_product.php` (Update Product)
**Purpose:** Modify existing product  
**Size:** ~100 lines  
**Features:**
- Load product data into form
- Edit all fields
- Replace image (optional)
- Delete old image if new one uploaded
- Save changes to database

**URL:** `/?page=admin_edit_product&id=1`  
**Requires Login:** Yes  
**Requires Role:** Admin  
**Database Tables:** produk (UPDATE)

---

## 🎨 Styling & JavaScript

### `assets/css/style.css` (Styling)
**Purpose:** Custom Bootstrap CSS overrides  
**Size:** 400+ lines  
**Includes:**
- Bootstrap CDN link
- Custom color scheme
- Card hover effects
- Form styling
- Table styling
- Print styles for invoice
- Responsive mobile adjustments
- Animation effects

**Key Color Variables:**
- Primary: #007bff (blue)
- Success: #28a745 (green)
- Danger: #dc3545 (red)
- Warning: #ffc107 (yellow)

**Customization:** Edit directly for color changes

---

### `assets/js/scripts.js` (Client-side Logic)
**Purpose:** JavaScript functionality  
**Size:** 100+ lines  
**Features:**
- Auto-dismiss alerts (5 seconds)
- Currency formatting
- Delete confirmation dialogs
- Quantity field validation
- Product search filtering
- Form submission handling

**Dependencies:**
- Bootstrap JS (included via CDN)
- jQuery (optional, using vanilla JS where possible)

---

## 📊 Database Files

### `database/database.sql` (SQL Schema)
**Purpose:** Complete database structure  
**Size:** 250+ lines  
**Contains:**
- CREATE DATABASE
- CREATE TABLEs (5 tables)
- CREATE INDEXes (8 indexes)
- INSERT dummy data (3 users, 8 products)
- Comments explaining each table/column

**Tables Created:**
1. users (3 records)
2. produk (8 records)
3. cart (empty)
4. transaksi (empty)
5. detail_transaksi (empty)

**Dummy Data:**
- Admin: admin@ecommerce.com (password: admin123 - bcrypt)
- Users: john@example.com, jane@example.com
- Products: Laptop, Monitor, Keyboard, Mouse, etc.

**Usage:**  
```bash
mysql -u root e_commerce < database/database.sql
```

---

## 📚 Template Files

### `includes/header.php` (Navigation Bar)
**Purpose:** Top navbar template  
**Size:** ~50 lines  
**Content:**
- Bootstrap navbar
- Shop logo/name
- Navigation links:
  - Products (everyone)
  - Cart link (shows count if logged in)
  - Login/Register (if not logged in)
  - Account menu (if logged in)
  - Admin Panel (if admin)
  - Logout button

**Used By:** All pages (included automatically)

---

### `includes/footer.php` (Footer Template)
**Purpose:** Bottom footer template  
**Size:** ~40 lines  
**Content:**
- Bootstrap footer
- Copyright info
- Link to Bootstrap JS
- Link to custom scripts.js
- Any custom scripts

**Used By:** All pages (included automatically)

---

### `includes/logout.php` (Logout Handler)
**Purpose:** Handle user logout  
**Size:** ~5 lines  
**Functions:**
- Destroy session
- Clear all session variables
- Redirect to login page

**Called By:** Logout link in header

---

## 📖 Documentation Files

### `README.md` (Main Documentation)
**Contains:**
- Project overview
- Feature list
- Folder structure
- Database schema explanation
- Installation steps
- Demo accounts
- **HOTS Analysis:**
  - Double order prevention technique
  - Input validation strategy
- Security features
- Maintenance tips
- Future features to add
- Learning resources

**Read First:** Yes! Start here to understand project

---

### `SETUP_GUIDE.md` (Installation Guide)
**Contains:**
- Prasyarat (requirements)
- Step-by-step setup (5 steps)
- Database import instructions (2 methods)
- Verification steps
- Testing workflows (6 test scenarios)
- Troubleshooting guide
- Database verification queries
- Performance tips
- Backup/restore procedures

**Use When:** Setting up for first time

---

### `QUICK_REFERENCE.md` (Quick Lookup)
**Contains:**
- All URLs/routes table
- Test accounts list
- Key files list
- List of all functions
- Common tasks with steps
- Common issues & solutions
- Code examples
- Security checklist
- Development tips

**Use When:** Need quick answer or function reference

---

### `DEPLOYMENT_CHECKLIST.md` (Status Verification)
**Contains:**
- Setup completion status
- Database statistics
- File statistics
- Next steps after setup
- Database verification queries
- Table record counts
- Admin features list
- User features list
- Security status
- Customization suggestions

**Use When:** Verifying everything is ready

---

### `FEATURES_VERIFICATION.md` (Test Scenarios)
**Contains:**
- 15 detailed test scenarios
- For each scenario:
  - Objective
  - Step-by-step instructions
  - Expected results
  - Validation points
  - Pass/Fail checklist

**Test Scenarios Include:**
1. Admin login & navigation
2. User registration
3. Browse products
4. Add to cart
5. Cart management
6. Checkout & stock deduction
7. Invoice display
8. Transaction history
9. Product create (CRUD-C)
10. Product update (CRUD-U)
11. Product delete (CRUD-D)
12. Input validation
13. SQL injection prevention
14. Password hashing verification
15. Concurrent checkout (race condition)

**Use When:** Testing application for bugs

---

### `FILE_MANIFEST.md` (This File)
**Purpose:** Overview of all files and folders  
**Use When:** Need to understand project structure

---

## 📊 File Statistics

| Category | Count | Total Lines |
|----------|-------|-------------|
| PHP Pages | 7 | 600+ |
| Admin Pages | 3 | 250+ |
| Config/Includes | 5 | 450+ |
| CSS | 1 | 400+ |
| JavaScript | 1 | 100+ |
| SQL | 1 | 250+ |
| Documentation | 7 | 2000+ |
| **TOTAL** | **25** | **4000+** |

---

## 🔄 File Dependencies

```
index.php (Entry Point)
  ├── config/db.php (Database connection)
  ├── includes/functions.php (All utilities)
  ├── includes/header.php (Navigation)
  ├── includes/footer.php (Footer)
  │
  ├── When page=login:   pages/login.php
  ├── When page=register: pages/register.php
  ├── When page=products: pages/products.php
  ├── When page=cart:     pages/cart.php
  ├── When page=checkout: pages/checkout.php
  ├── When page=invoice:  pages/invoice.php
  ├── When page=history:  pages/history.php
  │
  ├── When page=admin_products:     admin/products.php
  ├── When page=admin_add_product:  admin/add_product.php
  └── When page=admin_edit_product: admin/edit_product.php
  
All pages also load:
  └── assets/css/style.css (via HTML)
  └── assets/js/scripts.js (via footer.php)
  └── Bootstrap CDN (via footer.php)
```

---

## 🎯 Which File to Edit For...

| Task | Edit File |
|------|-----------|
| Change database credentials | `config/db.php` |
| Add new function | `includes/functions.php` |
| Change navbar items | `includes/header.php` |
| Change footer text | `includes/footer.php` |
| Add new page | Create in `pages/` folder + update `index.php` |
| Change colors | `assets/css/style.css` |
| Add JavaScript | `assets/js/scripts.js` |
| Modify login page | `pages/login.php` |
| Modify products display | `pages/products.php` |
| Add admin features | `admin/*.php` files |

---

## ✅ Completeness Checklist

- [x] Project structure complete
- [x] All 25 files created
- [x] Database schema created
- [x] Dummy data inserted
- [x] Authentication system working
- [x] Cart system implemented
- [x] Checkout system complete
- [x] Admin CRUD complete
- [x] Styling applied
- [x] JavaScript added
- [x] Security measures implemented
- [x] Documentation complete
- [x] Ready for testing

---

## 📞 Quick Support Guide

**Problem?**
1. Check `QUICK_REFERENCE.md` for quick answer
2. Check `SETUP_GUIDE.md` troubleshooting section
3. Check `FEATURES_VERIFICATION.md` for what you're testing
4. Check code comments in the specific file
5. Check browser console for JavaScript errors (F12)

**Want to understand the code?**
1. Start with `README.md` → Understand project
2. Read `QUICK_REFERENCE.md` → See function list
3. Open `includes/functions.php` → Read function comments
4. Open relevant page file → See how it uses functions

**Want to add a feature?**
1. Add function to `includes/functions.php`
2. Create/modify page file in `pages/` or `admin/`
3. Add route in `index.php` if new page
4. Add HTML/styling as needed
5. Test using `FEATURES_VERIFICATION.md` guide

---

**Generated:** 2024  
**Status:** ✅ COMPLETE
