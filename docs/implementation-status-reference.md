# Referensi Status Implementasi

Dokumen ini menjelaskan apa saja yang **sudah diimplementasikan** di project saat ini, apa yang **sudah sebagian jadi**, dan apa yang **belum dibuat**.

Dokumen ini berguna untuk mengetahui posisi project sekarang tanpa harus membaca semua code satu per satu.

---

## 1. Fondasi Database

Sudah diimplementasikan:

- tabel auth dan role
  - `users`
  - `roles`
  - `role_user`
  - `otp_verifications`
- tabel master data
  - `markets`
  - `areas`
  - `plots`
  - `plot_images`
- tabel transaksi
  - `booking_requests`
  - `booking_status_events`
  - `invoices`
  - `invoice_items`
  - `payments`
  - `payment_attempts`
  - `payment_events`
  - `leases`
  - `lease_periods`
  - `activity_logs`

Catatan penting:

- skema bisnis memakai alur `bayar dulu, baru kontrak`
- harga plot bulanan dan tahunan sudah nullable di database
- tetapi form admin memaksa minimal salah satu harga diisi

Status: `selesai`

---

## 2. Model Eloquent

Sudah diimplementasikan:

- `User`
- `Role`
- `RoleUser`
- `OtpVerification`
- `Market`
- `Area`
- `Plot`
- `PlotImage`
- `BookingRequest`
- `BookingStatusEvent`
- `Invoice`
- `InvoiceItem`
- `Payment`
- `PaymentAttempt`
- `PaymentEvent`
- `Lease`
- `LeasePeriod`
- `ActivityLog`

Yang sudah tersedia di model:

- `fillable`
- `casts()`
- relasi antar model

Status: `selesai`

---

## 3. Seeder Demo

Sudah diimplementasikan:

- `RoleSeeder`
- `AdminUserSeeder`
- `CustomerDemoSeeder`
- `MarketDemoSeeder`
- `BookingFlowDemoSeeder`
- `InvoiceDemoSeeder`
- `PaymentDemoSeeder`
- `LeaseDemoSeeder`
- `ActivityLogDemoSeeder`

Hasil setelah seed:

- admin default
- customer demo
- market, area, plot, image
- booking dengan beberapa status
- invoice dengan beberapa status
- payment dengan beberapa status
- lease aktif
- activity log admin

Status: `selesai`

---

## 4. Panel Admin: Resource yang Sudah Ada

### 4.1. MarketResource

Status: `selesai`

Bisa:

- create market
- edit market
- delete market

Tabel terkait:

- `markets`

### 4.2. AreaResource

Status: `selesai`

Bisa:

- create area
- edit area
- delete area

Tabel terkait:

- `areas`

### 4.3. PlotResource

Status: `selesai`

Bisa:

- create plot
- edit plot
- delete plot
- upload banyak gambar
- set gambar utama

Tabel terkait:

- `plots`
- `plot_images`

### 4.4. BookingRequestResource

Status: `selesai`

Bisa:

- lihat daftar booking request
- review booking
- isi `final_price`
- isi `payment_due_at`
- approve booking
- reject booking

Saat approve:

- update booking request
- buat status event
- auto-create invoice
- auto-create invoice item

Tabel terkait:

- `booking_requests`
- `booking_status_events`
- `invoices`
- `invoice_items`

### 4.5. InvoiceResource

Status: `selesai`

Bisa:

- lihat invoice
- edit due date
- edit item invoice
- edit diskon dan penalti

Rule penting:

- invoice terkunci kalau sudah ada `payment_attempt`

Tabel terkait:

- `invoices`
- `invoice_items`
- `payment_attempts`

### 4.6. PaymentResource

Status: `selesai`

Bisa:

- lihat payment
- lihat ringkasan attempt
- lihat ringkasan event
- cek ulang status payment ke Pakasir

Tabel terkait:

- `payments`
- `payment_attempts`
- `payment_events`

### 4.7. LeaseResource

Status: `selesai`

Bisa:

- lihat lease
- lihat detail kontrak
- lihat ringkasan lease periods

Tabel terkait:

- `leases`
- `lease_periods`

### 4.8. UserResource

Status: `selesai`

Bisa:

- create user
- edit user
- ganti role
- ganti status
- ganti password

Rule penting:

- admin tidak bisa mencabut role admin miliknya sendiri
- admin tidak bisa menghapus akunnya sendiri

Tabel terkait:

- `users`
- `roles`
- `role_user`

### 4.9. ActivityLogResource

Status: `selesai`

Bisa:

- lihat log admin
- lihat detail log

Readonly:

- tidak bisa create
- tidak bisa edit
- tidak bisa delete

Tabel terkait:

- `activity_logs`

---

## 5. Panel User: Yang Sudah Ada

### 5.1. User panel access

Status: `selesai`

Sudah ada pembatasan role:

- role `admin` masuk panel admin
- role `customer` masuk panel user

Implementasi ada di:

- `App\Models\User::canAccessPanel()`

### 5.2. Booking flow user

Status: `selesai`

Yang sudah ada:

- `My Bookings`
- `Browse Plots`
- `Plot Detail`
- `Create Booking Request`

Flow yang sudah hidup:

- customer login ke panel user
- customer browse lahan tersedia
- customer buka detail lahan
- customer lihat galeri gambar dan info lahan
- customer ajukan booking
- sistem membuat `booking_request`
- sistem membuat `booking_status_event` awal

Tabel terkait:

- `plots`
- `plot_images`
- `booking_requests`
- `booking_status_events`

Implementasi penting:

- `CreateUserBookingRequest`

Status: `selesai`

---

## 6. Integrasi Pakasir Sisi Admin/Backend

### 6.1. Config Pakasir

Status: `selesai`

Sudah ada di:

- `.env.example`
- `config/services.php`

Config yang tersedia:

- `PAKASIR_PROJECT_SLUG`
- `PAKASIR_API_KEY`
- `PAKASIR_SANDBOX`
- `PAKASIR_BASE_URL`
- daftar method Pakasir

### 6.2. PakasirService

Status: `selesai`

Sudah ada di:

- `App\Services\PakasirService`

Method yang tersedia:

- `createTransaction()`
- `getTransactionDetail()`
- `cancelTransaction()`
- `simulatePayment()`

### 6.3. Create payment attempt

Status: `selesai`

Sudah ada di:

- `CreatePakasirPaymentAttempt`

Yang dilakukan:

- call `transactioncreate/{method}`
- update/create `payments`
- update/create `payment_attempts`

### 6.4. Webhook Pakasir

Status: `selesai`

Sudah ada di:

- route `POST /webhooks/pakasir`
- `PaymentWebhookController`

Yang dilakukan:

- validasi project/order_id/amount
- simpan raw webhook ke `payment_events`
- lanjut sync status ke transaction detail API

### 6.5. Sync status payment

Status: `selesai`

Sudah ada di:

- `SyncPakasirPaymentStatus`

Yang dilakukan:

- panggil `transactiondetail`
- update `payments`
- update `payment_attempts`
- update `payment_events`
- update `invoices`
- update `booking_requests`

### 6.6. Auto-create lease setelah paid

Status: `selesai`

Sudah ada di:

- `CreateLeaseFromPaidBooking`

Yang dilakukan:

- buat `lease`
- buat `lease_periods`

### 6.7. Integrasi ke resource admin

Status: `selesai`

Sudah ada:

- action `Buat payment attempt` di `InvoiceResource`
- action `Simulasi pembayaran` di `InvoiceResource`
- action `Cek ulang status` di `PaymentResource`

---

## 7. Yang Sudah Sebagian Jadi

### 7.1. Customer payment flow

Status: `sebagian jadi`

Sudah ada:

- backend Pakasir
- create payment attempt dari invoice admin
- sync status payment

Belum ada:

- halaman invoice untuk user/customer
- halaman bayar invoice dari panel user
- pilihan method bayar dari sisi customer

### 7.2. Email notification

Status: `belum jadi`

Belum ada:

- email invoice dibuat
- email booking rejected
- email payment sukses

### 7.3. Automation expire booking dan invoice

Status: `belum jadi`

Belum ada:

- command/scheduler untuk expire otomatis booking dan invoice

---

## 8. Yang Belum Dibuat

Bagian yang masih belum ada atau belum final:

- panel user untuk invoice
- panel user untuk payment / bayar invoice
- panel user untuk lease saya
- email notification
- scheduler expire otomatis
- retry policy bisnis final untuk payment gagal/expired

---

## 9. Posisi Project Saat Ini

Kalau diringkas, kondisi project sekarang adalah:

- `database`: sudah kuat
- `models`: sudah lengkap
- `admin panel`: sudah hampir lengkap
- `user booking flow`: sudah hidup
- `backend Pakasir`: sudah hidup
- `customer payment UI`: belum ada
- `automation bisnis`: sebagian belum ada

Artinya, project sekarang sudah punya fondasi kuat untuk lanjut ke:

1. invoice/payment flow di panel user
2. email notification
3. automation expire booking dan invoice
