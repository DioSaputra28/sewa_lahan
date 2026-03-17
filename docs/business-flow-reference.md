# Referensi Alur Bisnis

Dokumen ini menjelaskan alur bisnis project sewa lahan pasar yang sedang dibangun, beserta hubungan antara:

- proses bisnis
- tabel database yang dipakai
- resource admin atau user yang terlibat

Dokumen ini fokus ke alur sistem secara menyeluruh, bukan hanya ke detail field per resource.

---

## 1. Gambaran Besar Sistem

Project ini adalah sistem booking lahan pasar dengan alur utama:

1. customer melihat lahan yang tersedia
2. customer mengajukan booking lahan
3. admin mereview booking
4. admin approve atau reject booking
5. jika approve, sistem membuat invoice
6. customer melakukan pembayaran
7. sistem sinkron ke Pakasir
8. jika payment sukses, sistem membuat lease atau kontrak

Prinsip bisnis utama yang sedang dipakai adalah:

- `bayar dulu, baru kontrak`

Artinya:

- lease tidak dibuat saat booking diajukan
- lease baru terbentuk setelah payment valid dan sukses

---

## 2. Alur Master Data

Sebelum transaksi bisa berjalan, admin harus menyiapkan data master.

### 2.1. Admin membuat market

Tujuan:

- membuat data pasar sebagai induk area dan lahan

Database:

- `markets`

Resource terkait:

- `MarketResource`

### 2.2. Admin membuat area atau blok

Tujuan:

- mengelompokkan lahan berdasarkan blok atau area di dalam pasar

Database:

- `areas`

Relasi:

- `areas.market_id -> markets.id`

Resource terkait:

- `AreaResource`

### 2.3. Admin membuat plot atau lahan

Tujuan:

- membuat unit lahan yang bisa disewakan oleh customer

Database:

- `plots`
- `plot_images`

Relasi:

- `plots.market_id -> markets.id`
- `plots.area_id -> areas.id`
- `plot_images.plot_id -> plots.id`

Resource terkait:

- `PlotResource`

Catatan bisnis:

- lahan hanya bisa diajukan customer jika `status = available`
- harga bulanan dan tahunan sekarang nullable di database
- tetapi di form admin, minimal salah satu harus diisi

---

## 3. Alur User dan Akses Panel

Sistem saat ini memiliki dua jenis role utama:

- `admin`
- `customer`

Database:

- `users`
- `roles`
- `role_user`
- `otp_verifications`

Relasi:

- `role_user.user_id -> users.id`
- `role_user.role_id -> roles.id`

Pembagian panel:

- admin hanya bisa masuk panel admin
- customer hanya bisa masuk panel user

Implementasi akses role ini berada di:

- `App\Models\User::canAccessPanel()`

Resource yang terkait manajemen user di admin:

- `UserResource`

---

## 4. Alur User Melihat dan Mengajukan Booking

### 4.1. User browse lahan

Tujuan:

- customer melihat daftar lahan yang tersedia untuk diajukan booking

Database yang dibaca:

- `plots`
- `plot_images`
- `areas`
- `markets`

Panel / halaman terkait:

- user panel `Bookings`
- custom page browse plots
- custom page detail plot

Implementasi terkait:

- `App\Filament\User\Resources\Bookings\Pages\BrowsePlots`
- `App\Filament\User\Resources\Bookings\Pages\ViewPlot`

### 4.2. User melihat detail lahan

Tujuan:

- customer memahami detail lahan sebelum mengajukan booking

Yang ditampilkan:

- nama lahan
- pasar
- area
- ukuran
- luas
- harga bulanan/tahunan
- galeri gambar
- deskripsi

Database yang dibaca:

- `plots`
- `plot_images`

### 4.3. User membuat booking request

Tujuan:

- customer mengirim pengajuan sewa lahan ke admin

Database yang ditulis:

- `booking_requests`
- `booking_status_events`

Resource / action terkait:

- user `BookingResource`
- `CreateUserBookingRequest` action

Field bisnis penting yang terbentuk saat create:

- `user_id`
- `plot_id`
- `term_type`
- `duration`
- `start_date`
- `end_date`
- `quoted_price`
- `status = pending`
- `payment_status = unpaid`

Catatan bisnis:

- `quoted_price` dihitung otomatis dari harga dasar lahan
- `end_date` dihitung otomatis dari `term_type` dan `duration`
- status awal booking adalah `pending`

---

## 5. Alur Admin Review Booking

### 5.1. Admin melihat booking request masuk

Tujuan:

- admin memonitor booking baru dari customer

Database yang dibaca:

- `booking_requests`
- `users`
- `plots`
- `areas`
- `markets`

Resource terkait:

- `BookingRequestResource`

### 5.2. Admin approve booking

Tujuan:

- admin menyetujui pengajuan dan menetapkan harga final serta due date pembayaran

Database yang diupdate:

- `booking_requests`
- `booking_status_events`
- `invoices`
- `invoice_items`

Action terkait:

- action `approve` di `BookingRequestResource`

Perubahan bisnis saat approve:

- `booking_requests.status = approved`
- `booking_requests.payment_status = unpaid`
- `approved_by` diisi
- `approved_at` diisi
- `final_price` diisi oleh admin
- `payment_due_at` diisi oleh admin
- sistem membuat invoice otomatis

### 5.3. Admin reject booking

Tujuan:

- menolak pengajuan customer dengan alasan yang jelas

Database yang diupdate:

- `booking_requests`
- `booking_status_events`

Perubahan bisnis:

- `booking_requests.status = rejected`
- `rejected_at` diisi
- `rejection_reason` wajib diisi

---

## 6. Alur Invoice

### 6.1. Invoice dibuat otomatis setelah booking approved

Tujuan:

- membuat tagihan resmi yang akan dibayar customer

Database:

- `invoices`
- `invoice_items`

Relasi:

- `invoices.booking_request_id -> booking_requests.id`
- `invoice_items.invoice_id -> invoices.id`

Resource terkait:

- `InvoiceResource`

Field bisnis penting:

- `invoice_number`
- `subtotal`
- `discount_amount`
- `penalty_amount`
- `total_amount`
- `due_date`
- `status`

### 6.2. Admin mengelola invoice

Admin bisa:

- melihat invoice
- mengubah due date
- mengubah item invoice
- mengubah diskon/penalti

Tetapi ada aturan penting:

- jika sudah ada `payment_attempt`, invoice dikunci

Kenapa:

- supaya nominal invoice tidak berubah setelah dipakai membuat transaksi payment

---

## 7. Alur Payment dan Pakasir

### 7.1. Payment attempt dibuat dari invoice

Tujuan:

- membuat transaksi payment ke gateway Pakasir

Database yang ditulis:

- `payments`
- `payment_attempts`

Service dan action terkait:

- `PakasirService`
- `CreatePakasirPaymentAttempt`

Resource terkait:

- `InvoiceResource`
- `PaymentResource`

Data yang disimpan dari Pakasir:

- `provider_order_id`
- `payment_method`
- `payment_number`
- `fee`
- `total_payment`
- `expired_at`

Catatan bisnis:

- `provider_order_id` dibuat konsisten dengan `invoice_number`
- satu invoice bisa punya lebih dari satu `payment_attempt`

### 7.2. Pakasir mengirim webhook

Tujuan:

- memberitahu sistem bahwa ada update status transaksi payment

Database yang ditulis:

- `payment_events`

Endpoint terkait:

- `POST /webhooks/pakasir`

Controller terkait:

- `PaymentWebhookController`

Validasi dasar yang dilakukan:

- `project` cocok
- `order_id` cocok
- `amount` cocok dengan invoice

### 7.3. Sistem melakukan recheck via transaction detail API

Tujuan:

- jangan langsung percaya webhook mentah
- status akhir diambil dari detail API Pakasir

Database yang diupdate:

- `payments`
- `payment_attempts`
- `payment_events`
- `invoices`
- `booking_requests`

Action terkait:

- `SyncPakasirPaymentStatus`

Resource terkait:

- `PaymentResource` action `Cek ulang status`

### 7.4. Status payment lokal

Status bisnis yang sinkron ke sistem lokal:

- `pending`
- `paid`
- `failed`
- `expired`
- `cancelled`

Catatan penting:

- `payment_attempt expired` berbeda dengan `invoice expired`
- `payment_attempt expired` berarti transaksi gateway kadaluarsa
- `invoice expired` berarti tagihan bisnis di sistem sudah tidak berlaku lagi

---

## 8. Alur Lease atau Kontrak

### 8.1. Lease dibuat setelah payment sukses

Tujuan:

- membuat kontrak sewa aktif hanya untuk booking yang benar-benar sudah dibayar

Database yang ditulis:

- `leases`
- `lease_periods`

Action terkait:

- `CreateLeaseFromPaidBooking`

Relasi:

- `leases.booking_request_id -> booking_requests.id`
- `leases.invoice_id -> invoices.id`
- `lease_periods.lease_id -> leases.id`

Catatan bisnis:

- project ini memakai rule `bayar dulu, baru kontrak`
- jadi `lease` tidak pernah dibuat sebelum payment tervalidasi sukses

### 8.2. Admin memonitor lease

Tujuan:

- admin melihat kontrak aktif dan periode-periode sewanya

Resource terkait:

- `LeaseResource`

Database yang dibaca:

- `leases`
- `lease_periods`

---

## 9. Alur Activity Log

Tujuan:

- mencatat aksi penting admin untuk audit

Database:

- `activity_logs`

Resource terkait:

- `ActivityLogResource`

Catatan bisnis:

- hanya log admin yang ditampilkan di panel admin
- log dipakai untuk audit internal dan debugging operasional

---

## 10. Alur Expire Otomatis

Konsep bisnisnya:

- booking approved punya `payment_due_at`
- invoice punya `due_date`
- jika tidak dibayar sampai melewati batas waktu, transaksi bisa di-expire otomatis

Database yang terlibat:

- `booking_requests`
- `invoices`
- `payments`
- `payment_attempts`

Status yang bisa terpengaruh:

- `booking_requests.status = expired`
- `booking_requests.payment_status = expired`
- `invoices.status = expired`

Catatan:

- logic expire otomatis penuh belum selesai diimplementasikan
- tapi struktur database dan arah business flow-nya sudah disiapkan

---

## 11. Ringkasan Mapping Bisnis ke Database dan Resource

### Master data

- Database:
  - `markets`
  - `areas`
  - `plots`
  - `plot_images`
- Resource:
  - `MarketResource`
  - `AreaResource`
  - `PlotResource`

### User dan auth

- Database:
  - `users`
  - `roles`
  - `role_user`
  - `otp_verifications`
- Resource:
  - `UserResource` (admin)

### Booking

- Database:
  - `booking_requests`
  - `booking_status_events`
- Resource:
  - `BookingRequestResource` (admin)
  - `BookingResource` (user)

### Invoice

- Database:
  - `invoices`
  - `invoice_items`
- Resource:
  - `InvoiceResource`

### Payment

- Database:
  - `payments`
  - `payment_attempts`
  - `payment_events`
- Resource:
  - `PaymentResource`

### Lease

- Database:
  - `leases`
  - `lease_periods`
- Resource:
  - `LeaseResource`

### Audit

- Database:
  - `activity_logs`
- Resource:
  - `ActivityLogResource`
