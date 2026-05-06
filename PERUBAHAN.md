## Overview

Secara garis besar ada 5 area utama yang diperbaiki:

1. **Settlement discipline + auto stock deduction**
2. **Admin-side data integrity fixes** (multi-tenant store_id, bug naming, shift logic)
3. **Stock validation + race-condition prevention**
4. **Inventory management** — min_stock threshold + form penerimaan bahan + low-stock alert
5. **Customer-side lengkap dari setengah jadi ke fungsional** (QR auth + cart lifecycle + multi-customer handling)

---

## Perubahan Konsep / Design

### 1. Chair-as-Authentication

**Sebelum:** Chair model sudah extend `Authenticatable` tapi belum ada flow login via QR (setengah jadi).

**Sesudah:** Chair benar-benar bisa login via QR scan:

- Dibuatkan QR generator untuk tiap chair
- AuthController terima `qrToken` di URL signin
- Guard Laravel ditambah (`chair` guard + `chairs` provider)
- Customer routes di-split: pakai `auth:chair` (admin tetap `auth:sanctum`)

### 2. Multi-tenant enforcement

**Sebelum:** Banyak controller tidak set `store_id` saat create record, beberapa pakai `Model::all()` tanpa scope per store.

**Sesudah:** `store_id` konsisten di-set di semua record, query scoped per `$userStore`.

### 3. Cart lifecycle explicit

**Sebelum:** `$user->carts()->latest()->first()` di-pakai di mana-mana — ignore state (sudah bayar? expired? empty?).

**Sesudah:** Cart punya scope `active()` + helper `getActiveOrCreateForChair()` + kolom `expires_at` + sliding expiration. Cart berstate jelas: in-progress, committed, expired.

### 4. Stock deduction strategy

**Sebelum:** Tidak ada auto deduction, tidak ada validation.

**Sesudah:** Stock deduction otomatis saat settlement (cash + Midtrans). Pakai `SELECT ... FOR UPDATE` row lock untuk race-safe. Cash = strict (refuse kalau kurang), Midtrans = allow negative (sesuai keputusan: customer-facing errors dialihkan ke kasir intervention).

### 5. Midtrans dibuat optional di dev

**Sebelum:** Hardcoded `\Midtrans\Snap::getSnapToken()` — crash kalau key tidak ada.

**Sesudah:** Wrap dalam `if (config('midtrans.server_key'))` — skip snap token kalau key kosong. Cash payment tetap jalan. Customer-side juga.

### 6. Inventory events via StockMovement

**Sebelum:** Tidak ada audit trail.

**Sesudah:** Setiap perubahan stok dicatat di tabel `stock_movements` (polymorphic reference ke Order) — type `order_consume`, `order_restore`, `receive`.

---

## Detail Per Area

### AREA 1 — Settlement Discipline

**Before:** Shift bisa double-open, posttotal pakai `latest()` bisa tutup shift yang sudah ditutup, archive auto-create shift kalau tidak ada.

**After:**

- Scope `Settlement::active()` (filter `end_time IS NULL`)
- Guard double-shift di `poststart` — blokir kalau ada shift aktif
- `posttotal` pakai `active()` — cuma bisa tutup yang benar-benar aktif
- `archive` blokir kalau tidak ada shift aktif (hapus auto-create)
- `expected` diinisialisasi = `start_amount` saat buka shift (dulu default 0)

**Files:**

- `app/Models/Settlement.php` — scope active()
- `app/Http/Controllers/SettlementController.php`
- `app/Http/Controllers/OrderController.php` — archive()

### AREA 2 — Stock Movements + InventoryService

**Before:** Stok tidak berkurang saat order selesai.

**After:** Service class + model + tabel baru untuk track semua perubahan stok.

**Files baru:**

- `database/migrations/2026_04_21_030000_create_stock_movements_table.php`
- `app/Models/StockMovement.php`
- `app/Services/InventoryService.php` — method `consumeForOrder($order, strict)`, `restoreForOrder`, `canFulfillCart`

**Integrasi:**

- `OrderController@cashpayment` — consume dengan `strict: true`
- `OrderController@index` + `Customer/PagesController@antrian` — polling Midtrans, consume dengan default (allow negative)

### AREA 3 — Stock Validation + Race Condition Fix

**Before:** Tidak ada validasi stok di cart; ada risk oversell saat 2 transaksi simultan.

**After:**

- `InventoryService::canFulfillCart(Cart, ?Menu, ?qty)` — validate aggregate per-ingredient
- `InsufficientStockException` — exception class dengan list bahan kurang
- `consumeForOrder` refactored: agregasi per invent, `lockForUpdate()` row lock, mode `strict` untuk cash
- Admin CartController@store & OrderController@cashpayment pakai validation ini
- Customer CartController@postcart juga (di Phase 2)

**Files baru:**

- `app/Exceptions/InsufficientStockException.php`

**Files diubah:**

- `app/Services/InventoryService.php`
- `app/Http/Controllers/CartController.php` (admin)
- `app/Http/Controllers/OrderController.php` — cashpayment wrapped DB::transaction + try-catch

### AREA 4 — Multi-tenant Store_id Fixes

**Before:** Record-record baru banyak tidak set `store_id`, trigger error `Field 'store_id' doesn't have a default value`.

**After:** `store_id` di-set di semua creation.

**Files admin:**

- `app/Http/Controllers/CartController.php` — Cart + CartMenu
- `app/Http/Controllers/ChatController.php` — saveChat
- `app/Http/Controllers/ExpenseController.php` — full multi-tenant rewrite (juga scope index/update/destroy + validation)
- `app/Http/Controllers/IngredientController.php` — InventMenu di store/update
- `app/Http/Controllers/OrderController.php` — Order + cart baru + History di archive + Cart di cashpayment
- `app/Http/Controllers/ProductController.php` — Menu di store

**Files customer:**

- `app/Http/Controllers/Customer/ProductController.php`
- `app/Http/Controllers/Customer/CartController.php`
- `app/Http/Controllers/Customer/OrderController.php`
- `app/Http/Controllers/Customer/ServeController.php`

**Model:**

- `app/Models/Store.php` — tambah relationship `expenses()`

### AREA 5 — Bug Fixes Admin-side

**Before:**

- Kolom `kursi` di code tapi tidak ada di migration, menyebabkan archive error
- Column `akun` NOT NULL tidak diset
- Field typo "Nomine" di expense view
- Menu table pakai `desc` tapi code pakai `description`

**After:**

- `kursi` ke `akun` konsisten di 3 tempat (OrderController@archive, Pagescontroller search, search.blade.php)
- Expense "Nomine" ke "Nominal"
- Migration baru rename `menus.desc` ke `menus.description`

**Files:**

- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/Pagescontroller.php`
- `resources/views/search.blade.php`
- `resources/views/expense.blade.php`
- `database/migrations/2026_04_21_040000_rename_desc_to_description_in_menus_table.php` — new

### AREA 6 — Migration Sweep (Bulk Fix)

**Before:** Banyak migration pakai pattern broken `->constrained()->onUpdate(...)->onDelete(...)->nullable()` — `->nullable()` ineffective karena dipanggil setelah chain constrained, jadi kolom tetap NOT NULL.

**After:** Bersihkan pattern broken dari 14+ migration. Kolom `store_id` konsisten NOT NULL. `user_id` dan `chair_id` di `carts` dibuat nullable dengan pattern yang benar (`->nullable()` sebelum `->constrained()`). `total_amount` default 0. `expires_at` baru ditambah.

**Files (semua migration yang disapu):**

- `database/migrations/2024_01_21_00003_create_chairs_table.php`
- `database/migrations/2024_01_21_182121_create_categories_table.php`
- `database/migrations/2024_01_21_194132_create_menus_table.php`
- `database/migrations/2024_02_04_075221_create_carts_table.php` (user_id/chair_id nullable + expires_at + total_amount default)
- `database/migrations/2024_02_04_075712_create_discounts_table.php`
- `database/migrations/2024_02_04_075713_create_cart_menus_table.php`
- `database/migrations/2024_02_14_092508_create_orders_table.php`
- `database/migrations/2024_03_16_221005_create_settlements_table.php`
- `database/migrations/2024_10_17_030955_create_expenses_table.php`
- `database/migrations/2024_11_20_125201_create_showcases_table.php`
- `database/migrations/2025_09_18_091128_create_invents_table.php` (+ min_stock column)
- `database/migrations/2025_09_24_100334_create_chats_table.php`
- `database/migrations/2025_09_24_103424_create_invent_menus_table.php`

**Migration DIHAPUS:**

- `database/migrations/2024_03_16_221006_create_histoys_table.php` — typo "Histoy", diganti dengan `histories`

**Model file DIHAPUS:**

- `app/Models/Histoy.php` — typo

### AREA 7 — Inventory Management Lengkap

**Before:** Tidak ada form penerimaan bahan, tidak ada alert stock rendah.

**After:**

- Kolom `min_stock` di invents table (threshold per bahan, 0 = no alert)
- Method baru `InventController@receive` — catat penerimaan, increment stock, create stock_movement type='receive'
- View invent: tombol hijau "Terima Bahan" + modal form (bahan + jumlah + catatan supplier)
- Row highlight kuning + warning icon kalau `stock <= min_stock`
- Banner di atas tabel: "X bahan stoknya rendah"

**Files:**

- `database/migrations/2025_09_18_091128_create_invents_table.php` — kolom min_stock
- `app/Models/Invent.php` — fillable, scopeLowStock(), isLowStock()
- `app/Http/Controllers/InventController.php` — receive() + store/update handle min_stock
- `resources/views/invent.blade.php` — banner + receive modal + kolom min_stock + highlight low-stock
- `routes/web.php` — route receive

### AREA 8 — UI Bug Fixes (Settlement + Product pages)

**Before:** `settlement.blade.php` dan `product.blade.php` di-fork dari template category tapi belum di-adapt — tombol Start/Add/Set Ingredients masih buka modal add category.

**After:**

- `settlement.blade.php` — modal start/end shift di-rewrite proper, action column dengan View + Delete
- `product.blade.php` — tombol Add dan Set Ingredients diganti `<a>` ke route yang tepat, hapus unused modal
- `bootstrap/app.php` — register middleware `ToSweetAlert` (untuk error messages bisa muncul)

### AREA 9 — Customer-side Redesign (Major)

**Before (colleague's unfinished state):**

- Chair extends Authenticatable tapi tidak ada login flow
- Customer routes all require login tapi tidak ada cara untuk customer login
- Password chair hardcoded `'123456'`
- View files di folder `cust/` tapi controller reference `user.*` (broken render)
- Model `Profil` tidak exist tapi dipakai di 2 controller
- Store_id tidak di-set di semua customer creation
- Midtrans hardcoded isProduction=true
- Cart resolution pakai `latest()->first()` (fragile)
- Multi-customer per chair bocor (Customer B lihat cart Customer A)
- Tidak ada stock validation di customer side

**After (fungsional end-to-end):**

#### 9a. Auth foundation

- `config/auth.php` — guard `chair` + provider `chairs`
- `AuthController@signin` — terima `qrToken` parameter, `Auth::guard('chair')->login($chair)` + session_started_at
- Chair-aware redirect setelah login (chair ke /customer, user ke /dashboard)
- Logout clear kedua guard
- `routes/web.php` — split middleware: admin `auth:sanctum`, customer `auth:chair`, logout `auth:sanctum,chair`

#### 9b. QR generation

- `ChairController@qr($id)` — method baru, validate ownership store, return view dengan signin URL
- `resources/views/chairqr.blade.php` — view print-friendly (QR besar + nama chair + cara pakai + tombol Print/Kembali)
- `resources/views/chair.blade.php` — tombol kuning "Show QR" di tabel

#### 9c. View path fix

Folder `resources/views/cust/` ke `resources/views/user/` (rename manual).

#### 9d. Orphan cleanup

Model `Profil` dihapus dari controller, replaced with `$chair->store` (reuse Store model). View `home.blade.php` `$item->alamat` ke `$item->location`.

#### 9e. Store_id fixes customer

Semua create Cart/CartMenu/Order di customer controllers set `store_id = $chair->store_id`.

#### 9f. Midtrans optional customer

`Customer/OrderController@postorder` wrap snap token generation dalam `if (config('midtrans.server_key'))`.

#### 9g. Cart lifecycle

- Migration: `expires_at` di carts
- `Cart` model: scope `active()`, `getActiveOrCreateForChair()`, `bumpExpiration()`
- Customer/ProductController + Customer/CartController pakai helper
- Sliding expiration: setiap postcart/removecart/reset bump expires_at 30 menit

#### 9h. Banner "Lanjutkan/Mulai Baru"

- `Customer/PagesController@home` — query pendingCart dengan filter: belum acknowledged, has items, not expired, not committed, created_at < session_started_at (sehingga banner tidak muncul untuk cart sendiri)
- `user/home.blade.php` — UI banner
- 2 endpoints baru di `Customer/CartController`: `acknowledge()` (tetap pakai cart) dan `reset()` (delete items + start fresh)
- Routes: `user-cart-acknowledge`, `user-cart-reset`

#### 9i. Stock validation customer

`Customer/CartController@postcart` panggil `canFulfillCart` sebelum add item.

**Files customer:**

- `config/auth.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/ChairController.php`
- `app/Http/Controllers/Customer/PagesController.php`
- `app/Http/Controllers/Customer/ProductController.php`
- `app/Http/Controllers/Customer/CartController.php`
- `app/Http/Controllers/Customer/OrderController.php`
- `app/Http/Controllers/Customer/ServeController.php`
- `app/Models/Cart.php`
- `app/Models/Chair.php`
- `resources/views/chairqr.blade.php` (new)
- `resources/views/chair.blade.php`
- `resources/views/user/home.blade.php`
- `resources/views/checkout.blade.php` (Midtrans optional)
- `routes/web.php`

---

## Files Summary

### NEW Files

- `app/Exceptions/InsufficientStockException.php`
- `app/Models/StockMovement.php`
- `app/Services/InventoryService.php`
- `database/migrations/2026_04_21_030000_create_stock_movements_table.php`
- `database/migrations/2026_04_21_040000_rename_desc_to_description_in_menus_table.php`
- `resources/views/chairqr.blade.php`
- `PERUBAHAN.md` (dokumen ini)

### DELETED Files

- `app/Models/Histoy.php`
- `database/migrations/2024_03_16_221006_create_histoys_table.php`

### RENAMED Files

- `resources/views/cust/*` ke `resources/views/user/*`

### MODIFIED (major)

**Config:** `config/auth.php`, `bootstrap/app.php`

**Models:** `Cart.php`, `Chair.php`, `Invent.php`, `Settlement.php`, `Store.php`

**Controllers (admin):** `AuthController`, `CartController`, `ChairController`, `ChatController`, `ExpenseController`, `IngredientController`, `InventController`, `OrderController`, `Pagescontroller`, `ProductController`, `SettlementController`

**Controllers (customer):** `CartController`, `OrderController`, `PagesController`, `ProductController`, `ServeController`

**Migrations:** hampir semua (migration sweep)

**Views:** `chair.blade.php`, `checkout.blade.php`, `expense.blade.php`, `invent.blade.php`, `product.blade.php`, `search.blade.php`, `settlement.blade.php`, `user/home.blade.php`

**Routes:** `routes/web.php`
