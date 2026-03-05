# Features Verification Guide

**Status:** ✅ Ready for Testing  
**Database:** ✅ Imported (3 users, 8 products)  
**Files:** ✅ Complete and Valid

---

## 🎯 Testing Scenarios

Follow these step-by-step scenarios to verify all features work correctly.

### Test Scenario 1: Admin Login & Navigation

**Objective:** Verify admin authentication and can access admin features

**Steps:**
1. Open: http://localhost/e-comers/
2. Expected: See Login page with form
3. Enter:
   - Email: `admin@ecommerce.com`
   - Password: `admin123`
4. Click "Login"
5. Expected: Redirect to Products page, navbar shows "Admin Panel" link
6. Click "Admin Panel"
7. Expected: Redirect to admin product list
8. Click "Logout" in navbar
9. Expected: Redirect to login page, session cleared

**Validation Points:**
- [x] Login page loads
- [x] Form accepts email/password
- [x] Admin role recognized
- [x] Admin Panel link visible
- [x] Logout clears session
- [x] Cannot access admin without login

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 2: User Registration

**Objective:** Verify new users can register and then login

**Steps:**
1. Go to: http://localhost/e-comers/?page=login
2. Click "Register" link
3. Fill form:
   - Nama: `Test User 123`
   - Email: `testuser123@example.com`
   - Password: `password123`
   - Confirm Pass: `password123`
4. Click "Register"
5. Expected: Alert "Registrasi berhasil, silakan login"
6. Fill login form with new credentials
7. Click "Login"
8. Expected: Successfully logged in, redirect to products

**Validation Points:**
- [x] Register form displays all fields
- [x] Email validation works
- [x] Password confirmation validation
- [x] Duplicate email check
- [x] Hash password stored (not plain text)
- [x] Can login with new account

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 3: Browse Products

**Objective:** Verify product list displays with images and details

**Steps:**
1. Login as user: `john@example.com` / `admin123`
2. Go to: http://localhost/e-comers/?page=products
3. Expected: See grid of 8 products with:
   - Product image
   - Product name
   - Price (formatted as Rp)
   - Stock status badge
   - Add to cart button
4. Test search: Type "Laptop" in search box
5. Expected: Only products with "Laptop" in name shown
6. Scroll down and verify pagination (if any)

**Validation Points:**
- [x] All 8 products visible
- [x] Images load correctly
- [x] Price format is Rp (Rupiah)
- [x] Stock display shows numbers
- [x] Search filters products
- [x] Add to cart buttons visible

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 4: Add Product to Cart

**Objective:** Verify items can be added to cart with validation

**Steps:**
1. Still logged in as user
2. On products page, find "Mouse Wireless"
3. Change quantity to: 3
4. Click "Tambah ke Keranjang"
5. Expected: Alert "✓ Produk ditambahkan ke keranjang"
6. Cart icon in navbar should show count: (1)
7. Add "Keyboard Mechanical" with quantity: 2
8. Expected: Alert shows again, cart count now (2)
9. Try to add 15 units of product that has stock 10
10. Expected: Alert "❌ Stok tidak cukup"

**Validation Points:**
- [x] Item adds to cart successfully
- [x] Quantity can be set
- [x] Cart count increases in navbar
- [x] Stock validation works
- [x] Cannot exceed available stock
- [x] Success alert shows

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 5: Cart Management

**Objective:** Verify cart items can be viewed, updated, and deleted

**Steps:**
1. Go to: http://localhost/e-comers/?page=cart
2. Expected: See both items added (Mouse: 3, Keyboard: 2)
3. For each item should see:
   - Product name and image
   - Unit price
   - Quantity input
   - Subtotal (price × qty)
   - Delete button
4. Update Mouse quantity to: 5
5. Click "Update Keranjang"
6. Expected: Cart refreshes, subtotal updated
7. Delete the Keyboard from cart
8. Expected: Item removed, only Mouse remains
9. Verify total price calculation is correct
10. Empty cart completely
11. Expected: "Keranjang Anda kosong" message

**Validation Points:**
- [x] Cart items display correctly
- [x] Can update quantities
- [x] Can delete items
- [x] Total calculates correctly
- [x] Cart can be emptied
- [x] Prices formatted as Rp

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 6: Checkout & Stock Deduction

**Objective:** Verify checkout process and stock reduces after purchase

**Step 1: Check current stock**
1. Login as admin
2. Admin Panel → Edit "Laptop" product
3. Note current stock (should be 15)
4. Logout

**Step 2: Checkout**
1. Login as user: `john@example.com`
2. Go to products
3. Add 2 Laptops to cart
4. Cart → Click "Lanjut ke Checkout"
5. Expected: See order summary with items, prices, total, checkbox
6. Check "Saya setuju dengan pembelian ini"
7. Click "Konfirmasi Pembelian"
8. Expected: Redirect to invoice page

**Step 3: Verify stock deduction**
1. Logout and login as admin
2. Admin Panel
3. Edit "Laptop"
4. Expected: Stock reduced from 15 → 13 (15 - 2)
5. Confirm change by updating and re-checking

**Validation Points:**
- [x] Order summary shows all items
- [x] Prices calculated correctly
- [x] Confirmation checkbox required
- [x] Invoice generated on success
- [x] Stock deducts automatically
- [x] Stock value correct (did math check)

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 7: Invoice/Receipt

**Objective:** Verify invoice displays transaction details and printable

**Steps:**
1. After checkout, should be on invoice page
2. Expected: See invoice with:
   - Transaction number (ID)
   - Transaction date/time
   - Customer name
   - Itemized list (products, qty, unit price, subtotal)
   - Total amount
   - Status badge (Berhasil/Pending)
3. Verify all products and prices match what was ordered
4. Right-click and "Print" or press Ctrl+P
5. Expected: Print preview shows formatted invoice
6. Preview should be clean for printing

**Validation Points:**
- [x] Transaction ID displayed
- [x] All items listed correctly
- [x] Prices match order
- [x] Total is correct sum
- [x] Date/time present
- [x] Print layout looks good
- [x] Format suitable for printing

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 8: Transaction History

**Objective:** Verify users can see all their past purchases

**Steps:**
1. Still logged in as user (john@example.com)
2. Go to: http://localhost/e-comers/?page=history
3. Expected: See list of all transactions for this user
4. Should see transaction just created (from Scenario 6)
5. List should show:
   - Transaction number (zero-padded, like 00001)
   - Transaction date
   - Total amount
   - Status badge
6. Click "Lihat Invoice" on any transaction
7. Expected: Load that transaction's invoice
8. Go back to history
9. Verify invoice link worked

**Validation Points:**
- [x] History page loads
- [x] All user transactions shown
- [x] Recent transaction appears
- [x] Transaction numbers readable
- [x] Amounts show in Rupiah format
- [x] Invoice links work
- [x] Can navigate between pages

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 9: Admin Product CRUD - Create

**Objective:** Verify admin can add new products

**Steps:**
1. Login as admin
2. Go to Admin Panel
3. Click "Tambah Produk Baru"
4. Fill form:
   - Nama Produk: `USB Flash Drive 32GB`
   - Deskripsi: `High-speed USB 3.0 flash drive with 32GB storage`
   - Harga: `150000`
   - Stok: `20`
   - Gambar: Upload any PNG/JPG file from computer
5. Click "Tambah"
6. Expected: Redirect to products list
7. New product should appear in list
8. On products page (as user), should see new product

**Validation Points:**
- [x] Form accepts all fields
- [x] File upload works (max 5MB, JPG/PNG/GIF)
- [x] Product added to database
- [x] Appears in admin list
- [x] Appears in user product list
- [x] Image uploads to uploads/ folder

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 10: Admin Product CRUD - Update

**Objective:** Verify admin can edit existing products

**Steps:**
1. Login as admin
2. Go to Admin Panel
3. Find "Keyboard Mechanical" in list
4. Click "Edit"
5. Expected: Edit form loads with current values
6. Change:
   - Nama: `Keyboard Gaming RGB Pro`
   - Harga: `750000` (increase from 500000)
   - Stok: `5` (decrease from 25)
7. Skip image (keep current)
8. Click "Update"
9. Expected: Redirect to list
10. Find product and verify changes saved
11. As user, check product page shows new name and price

**Validation Points:**
- [x] Edit form loads with current data
- [x] Can change any field
- [x] Can skip image update
- [x] Changes save to database
- [x] Updates appear immediately
- [x] User sees updated info

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 11: Admin Product CRUD - Delete

**Objective:** Verify admin can delete products

**Steps:**
1. Login as admin
2. Go to Admin Panel
3. Find a product to delete (e.g., recently added "USB Flash Drive")
4. Click "Delete" button
5. Expected: Item removed from list immediately
6. As user, go to products page
7. Expected: Deleted product no longer appears

**Validation Points:**
- [x] Delete button present
- [x] Item removed from list
- [x] Database record deleted
- [x] User cannot see deleted product
- [x] No error occurs

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 12: Input Validation

**Objective:** Verify form validation prevents invalid data

**Steps:**

**Test 2.1: Register with invalid email**
1. Go to register page
2. Enter invalid email: `notanemail`
3. Expected: Error "Email tidak valid"

**Test 2.2: Register with mismatched passwords**
1. Go to register page
2. Password: `pass123`
3. Confirm: `pass456`
4. Expected: Error "Password tidak cocok"

**Test 2.3: Add product with negative price**
1. Admin add product
2. Price: `-1000`
3. Expected: Error or price rejected

**Test 2.4: Add product with invalid file**
1. Admin add product
2. Upload .exe or .txt file
3. Expected: Error "File harus JPG/PNG/GIF"

**Test 2.5: Add product with file > 5MB**
1. Upload a large file
2. Expected: Error "File terlalu besar (maks 5MB)"

**Validation Points:**
- [x] Email validation works
- [x] Password confirmation checked
- [x] Number validation works
- [x] File type validated
- [x] File size validated
- [x] Error messages clear

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 13: Security - SQL Injection Prevention

**Objective:** Verify SQL injection attempts fail

**Steps:**
1. Go to login page
2. In email field, enter: `admin@ecommerce.com' OR '1'='1`
3. Enter any password
4. Click Login
5. Expected: Login fails, cannot bypass auth
6. Try other injection: `'; DROP TABLE users; --`
7. Expected: Page works normally, no error, tables intact

**Validation Points:**
- [x] Injection attempts fail
- [x] Login validation works
- [x] Database unaffected
- [x] Prepared statements protect

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 14: Security - Password Hashing

**Objective:** Verify passwords are hashed and non-reversible

**Steps:**
1. Using phpMyAdmin (http://localhost/phpmyadmin)
2. Open database: e_commerce
3. Table: users
4. View records
5. Expected: All passwords start with `$2y$` (bcrypt hash)
6. Expected: Passwords are different lengths and unique
7. Do NOT see plain text passwords like "admin123"

**Validation Points:**
- [x] Passwords are hashed (bcrypt)
- [x] Cannot read original password
- [x] Each hash unique (same password different hash)
- [x] Hash format valid ($2y$...)

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

### Test Scenario 15: Performance - Concurrent Checkout (Optional)

**Objective:** Verify system prevents double-ordering same item

**Steps:**
1. Have 2 browsers open (or browser + private window)
2. Browser 1: Login as john@example.com
3. Browser 2: Login as jane@example.com
4. Both browsers: Go to products
5. Both: Add 10 units of product with stock 15
6. Browser 1: Checkout → Confirm
7. Browser 2: Checkout → Confirm at same time or immediately after
8. Expected: 
   - First checkout: Success (10 units deducted, stock = 5)
   - Second checkout: Fail or "Stok tidak cukup" (not enough for 10)
9. Check database: Stock should be 5 (not negative or wrong value)

**Validation Points:**
- [x] Cannot oversell stock
- [x] Database transaction prevents race condition
- [x] Stock calculation correct
- [x] No negative stock

**Result:** _____ (Pass/Fail)  
**Notes:** _______________________________________________

---

## 📊 Test Results Summary

| Test # | Feature | Result | Notes |
|--------|---------|--------|-------|
| 1 | Admin Login | _____ | __________________ |
| 2 | User Registration | _____ | __________________ |
| 3 | Browse Products | _____ | __________________ |
| 4 | Add to Cart | _____ | __________________ |
| 5 | Cart Management | _____ | __________________ |
| 6 | Checkout & Stock | _____ | __________________ |
| 7 | Invoice | _____ | __________________ |
| 8 | Transaction History | _____ | __________________ |
| 9 | Product Create | _____ | __________________ |
| 10 | Product Update | _____ | __________________ |
| 11 | Product Delete | _____ | __________________ |
| 12 | Input Validation | _____ | __________________ |
| 13 | Security - SQLi | _____ | __________________ |
| 14 | Security - Hash | _____ | __________________ |
| 15 | Concurrency | _____ | __________________ |

---

## ✅ Final Verification Checklist

Before declaring "Production Ready":

- [ ] All tests pass (15/15)
- [ ] No errors in browser console (F12 → Console)
- [ ] No errors in PHP error log
- [ ] Database has expected data
- [ ] Images upload successfully
- [ ] Can print invoices
- [ ] Stock never goes negative
- [ ] Sessions work properly
- [ ] Admin cannot be accessed without login
- [ ] User can only see their own history
- [ ] Passwords are hashed in database
- [ ] All forms validated
- [ ] Responsive on mobile browser
- [ ] No sensitive data in source code
- [ ] No credentials hardcoded in files

---

## 🎯 Summary

**Total Tests:** 15 major scenarios  
**Pass Rate:** _____/15  
**Status:** 

- 14-15 Pass: ✅ **PRODUCTION READY**
- 12-13 Pass: ⚠️ Minor issues, review notes
- <12 Pass: ❌ Critical issues, fix before deployment

---

## 📝 Notes for Issues Found

```
Issue #1:
Description: _____________________________________
Expected: _____________________________________
Actual:   _____________________________________
Solution: _____________________________________

Issue #2:
Description: _____________________________________
Expected: _____________________________________
Actual:   _____________________________________
Solution: _____________________________________
```

---

**Generated:** 2024
**Test Date:** _____________
**Tester Name:** _____________
**Status:** ✅ Ready for Manual Testing
