# Referensi Resource Admin

Dokumen ini merangkum semua resource admin yang saat ini sudah dibuat di panel Filament. Fokusnya adalah menjelaskan:

- resource ini dipakai untuk apa
- admin bisa melakukan apa saja
- field atau input form apa saja yang tersedia
- tiap input dipakai untuk apa
- aturan atau perilaku penting yang perlu diketahui

## Ringkasan Resource

Saat ini resource admin yang sudah tersedia adalah:

- `MarketResource`
- `AreaResource`
- `PlotResource`
- `BookingRequestResource`
- `InvoiceResource`
- `PaymentResource`
- `LeaseResource`
- `UserResource`
- `ActivityLogResource`

---

## 1. MarketResource

Path utama:

- `app/Filament/Resources/Markets/MarketResource.php`
- `app/Filament/Resources/Markets/Pages/ManageMarkets.php`

### Fungsi resource

Resource ini dipakai untuk mengelola data pasar. Data pasar menjadi induk dari area dan plot/lahan.

### Admin bisa melakukan apa

- melihat daftar pasar
- menambah pasar baru
- mengubah data pasar
- menghapus pasar
- menghapus banyak pasar sekaligus

### Bentuk halaman

- hanya punya halaman list/manage
- create dan edit dilakukan lewat modal

### Kolom tabel yang ditampilkan

- `name` - nama pasar
- `city` - kota lokasi pasar
- `maps_url` - link maps
- `status` - status pasar
- `created_at` - waktu dibuat
- `updated_at` - waktu diubah

### Form input

#### Section: Informasi pasar

- `name`
  - label: `Nama pasar`
  - fungsi: identitas utama pasar di sistem
  - dipakai saat memilih pasar di area dan plot

- `city`
  - label: `Kota`
  - fungsi: menandai kota/kabupaten lokasi pasar

- `maps_url`
  - label: `Link maps`
  - fungsi: menyimpan tautan Google Maps atau tautan lokasi pasar

- `status`
  - label: `Status pasar`
  - opsi: `active`, `inactive`
  - fungsi: menandai apakah pasar masih dipakai aktif di operasional

- `address`
  - label: `Alamat`
  - fungsi: menyimpan alamat lengkap pasar

- `description`
  - label: `Deskripsi tambahan`
  - fungsi: catatan tambahan internal tentang pasar

### Catatan penting

- resource ini cocok untuk data master ringan
- modal dibuat lebar agar form tetap nyaman dibaca

---

## 2. AreaResource

Path utama:

- `app/Filament/Resources/Areas/AreaResource.php`
- `app/Filament/Resources/Areas/Pages/ManageAreas.php`

### Fungsi resource

Resource ini dipakai untuk mengelompokkan plot/lahan ke dalam area atau blok di tiap pasar.

### Admin bisa melakukan apa

- melihat daftar area
- menambah area baru
- mengubah area
- menghapus area
- menghapus banyak area sekaligus

### Bentuk halaman

- hanya punya halaman list/manage
- create dan edit dilakukan lewat modal

### Kolom tabel yang ditampilkan

- `name` - nama area
- `market.name` - pasar induk area
- `status` - status area
- `plots_count` - jumlah lahan di area itu
- `created_at`
- `updated_at`

### Filter yang tersedia

- `market_id`
  - fungsi: memfilter area berdasarkan pasar

### Form input

#### Section: Informasi area

- `market_id`
  - label: `Pasar`
  - fungsi: menentukan area ini milik pasar yang mana

- `status`
  - label: `Status area`
  - opsi: `active`, `inactive`
  - fungsi: menentukan apakah area aktif dipakai saat membuat plot

- `name`
  - label: `Nama area / blok`
  - fungsi: nama blok/area, misalnya `Blok A`

- `description`
  - label: `Deskripsi tambahan`
  - fungsi: catatan tambahan tentang area

### Catatan penting

- area selalu terkait ke market
- area dipakai sebagai grouping data plot/lahan

---

## 3. PlotResource

Path utama:

- `app/Filament/Resources/Plots/PlotResource.php`
- `app/Filament/Resources/Plots/Schemas/PlotForm.php`
- `app/Filament/Resources/Plots/Tables/PlotsTable.php`

### Fungsi resource

Resource ini dipakai untuk mengelola data lahan/plot yang akan disewakan.

### Admin bisa melakukan apa

- melihat daftar lahan
- menambah lahan
- mengubah lahan
- menghapus lahan
- mengelola galeri foto lahan

### Bentuk halaman

- full page resource
- ada list, create, dan edit page

### Kolom tabel yang ditampilkan

- `market.name`
- `area.name`
- `name`
- `type`
- `area_square_meters`
- `base_price_monthly`
- `status`
- `images_count`
- `created_at`
- `updated_at`

### Filter yang tersedia

- `market_id`
- `area_id`
- `status`

### Form input

#### Section: Informasi utama

- `name`
  - fungsi: nama lahan, misalnya `Freezer`

- `type`
  - opsi: `lahan`, `lapak`, `kios`
  - fungsi: tipe unit yang disewakan

- `status`
  - opsi: `available`, `occupied`, `inactive`
  - fungsi: status ketersediaan lahan

- `description`
  - fungsi: deskripsi tambahan tentang lahan

#### Section: Lokasi dan pengelompokan

- `market_id`
  - fungsi: menentukan lahan berada di pasar mana

- `area_id`
  - fungsi: menentukan lahan berada di area/blok mana
  - catatan: boleh kosong jika belum dimasukkan ke area tertentu

- `floor_level`
  - fungsi: menandai level/lantai lokasi lahan

- `location_note`
  - fungsi: catatan lokasi singkat, misalnya dekat pintu timur

#### Section: Ukuran dan luas

- `length`
  - fungsi: panjang lahan dalam meter

- `width`
  - fungsi: lebar lahan dalam meter

- `area_square_meters`
  - fungsi: luas lahan dalam meter persegi

#### Section: Harga dasar

- `base_price_monthly`
  - fungsi: harga dasar bulanan
  - catatan: boleh kosong jika harga tahunan diisi

- `base_price_yearly`
  - fungsi: harga dasar tahunan
  - catatan: boleh kosong jika harga bulanan diisi

Aturan validasi bagian harga:

- harga bulanan dan tahunan sekarang bersifat nullable di database
- tetapi pada form admin, minimal salah satu wajib diisi
- jika keduanya kosong, form akan menampilkan pesan:
  - `Minimal salah satu harga sewa harus diisi: bulanan atau tahunan.`

#### Section: Galeri foto

Field ini berbentuk repeater dengan relasi ke `plot_images`.

Setiap item foto punya input:

- `image_path`
  - upload file gambar lahan

- `is_primary`
  - menentukan apakah foto ini jadi gambar utama

- `sort_order`
  - menentukan urutan tampil foto

### Catatan penting

- `market_id` mempengaruhi pilihan `area_id`
- `area_id` difilter sesuai market yang dipilih
- foto disimpan sebagai relasi `hasMany`

---

## 4. BookingRequestResource

Path utama:

- `app/Filament/Resources/BookingRequests/BookingRequestResource.php`
- `app/Filament/Resources/BookingRequests/Schemas/BookingRequestForm.php`
- `app/Filament/Resources/BookingRequests/Tables/BookingRequestsTable.php`
- `app/Filament/Resources/BookingRequests/Pages/EditBookingRequest.php`

### Fungsi resource

Resource ini dipakai admin untuk mereview pengajuan booking dari customer.

### Admin bisa melakukan apa

- melihat daftar booking request
- membuka detail/review booking
- mengisi hasil review admin
- approve booking
- reject booking

### Bentuk halaman

- full page resource
- tidak ada create dari admin
- list page untuk monitoring
- edit page untuk review

### Kolom tabel yang ditampilkan

- `user.name`
- `plot.name`
- `term_type`
- `duration`
- `start_date`
- `end_date`
- `final_price`
- `status`
- `payment_status`
- `created_at`

### Filter yang tersedia

- `status`
- `payment_status`
- `market_id`
- `area_id`

### Form input

#### Section: Data customer

Readonly reference:

- `customer_name`
- `customer_email`
- `customer_phone`

Fungsi: membantu admin melihat siapa pengaju booking.

#### Section: Data pengajuan

Sebagian besar readonly:

- `plot_name`
- `market_name`
- `area_name`
- `term_type`
- `duration`
- `start_date`
- `end_date`
- `quoted_price`

Editable di bagian ini:

- `notes`
  - fungsi: catatan customer/admin yang berkaitan dengan review booking

#### Section: Keputusan admin

- `final_price`
  - fungsi: harga final hasil review admin
  - dipakai untuk membuat invoice

- `payment_due_at`
  - fungsi: batas waktu pembayaran setelah booking di-approve

#### Section: Status dan histori

Readonly summary:

- `booking_status`
- `payment_status_label`
- `approved_at_label`
- `rejected_at_label`
- `rejection_reason_label`
- `invoices_count`

### Action khusus

#### Approve

Yang dilakukan saat admin klik approve:

- validasi `final_price` dan `payment_due_at` wajib ada
- ubah booking menjadi `approved`
- set `approved_by` dan `approved_at`
- buat `BookingStatusEvent`
- otomatis buat `Invoice`
- otomatis buat `InvoiceItem`

#### Reject

Yang dilakukan saat admin klik reject:

- admin wajib isi `rejection_reason`
- ubah booking menjadi `rejected`
- set `rejected_at`
- buat `BookingStatusEvent`

### Catatan penting

- resource ini jadi titik awal flow admin untuk transaksi
- approve booking akan memicu invoice otomatis

---

## 5. InvoiceResource

Path utama:

- `app/Filament/Resources/Invoices/InvoiceResource.php`
- `app/Filament/Resources/Invoices/Schemas/InvoiceForm.php`
- `app/Filament/Resources/Invoices/Tables/InvoicesTable.php`
- `app/Filament/Resources/Invoices/Pages/EditInvoice.php`

### Fungsi resource

Resource ini dipakai admin untuk melihat dan mengelola invoice yang lahir dari booking approved.

### Admin bisa melakukan apa

- melihat daftar invoice
- membuka detail invoice
- mengubah invoice selama belum ada `payment_attempt`

### Bentuk halaman

- full page resource
- tidak ada create dari admin
- edit page juga berfungsi sebagai halaman detail/kelola invoice

### Kolom tabel yang ditampilkan

- `invoice_number`
- `user.name`
- `bookingRequest.plot.name`
- `total_amount`
- `due_date`
- `status`
- `paid_at`

### Filter yang tersedia

- `status`
- `market_id`
- `area_id`

### Form input

#### Section: Informasi invoice

Readonly reference:

- `invoice_number_label`
- `customer_name`
- `booking_reference`
- `invoice_status_label`
- `payment_attempt_count`
- `editing_lock_info`

Fungsi: memberi konteks invoice dan menunjukkan apakah invoice terkunci atau tidak.

#### Section: Pengaturan tagihan

- `due_date`
  - fungsi: mengatur jatuh tempo invoice

- `discount_amount`
  - fungsi: nominal diskon jika ada penyesuaian harga

- `penalty_amount`
  - fungsi: nominal penalti jika ada tambahan biaya

- `items` repeater
  - relasi ke `invoice_items`
  - tiap item punya:
    - `type`
    - `description`
    - `qty`
    - `unit_price`
    - `total`

Fungsi repeater: menyusun rincian tagihan di invoice.

#### Section: Ringkasan nominal

Readonly summary:

- `subtotal`
- `total_amount`
- `payment_summary`

### Perilaku penting

- jika sudah ada `payment_attempt`, invoice terkunci
- admin tidak bisa mengubah invoice setelah invoice dipakai untuk proses payment gateway
- saat save, subtotal dan total dihitung ulang dari item, diskon, dan penalti

---

## 6. PaymentResource

Path utama:

- `app/Filament/Resources/Payments/PaymentResource.php`
- `app/Filament/Resources/Payments/Schemas/PaymentForm.php`
- `app/Filament/Resources/Payments/Tables/PaymentsTable.php`
- `app/Filament/Resources/Payments/Pages/EditPayment.php`

### Fungsi resource

Resource ini dipakai admin untuk monitoring payment final dan melihat ringkasan hasil integrasi gateway.

### Admin bisa melakukan apa

- melihat daftar payment
- membuka detail payment
- melihat ringkasan payment attempt
- melihat ringkasan payment event
- menjalankan action `cek ulang status` placeholder

### Bentuk halaman

- full page resource
- tidak ada create dari admin
- edit page bertindak sebagai halaman detail payment

### Kolom tabel yang ditampilkan

- `invoice.invoice_number`
- `user.name`
- `provider`
- `provider_payment_method`
- `amount`
- `status`
- `provider_status`
- `paid_at`

### Filter yang tersedia

- `status`
- `provider`
- `provider_payment_method`
- `market_id`
- `area_id`

### Form input

#### Section: Informasi payment

Readonly detail:

- `invoice_number`
- `customer_name`
- `provider`
- `provider_order_id`
- `provider_payment_method`
- `provider_payment_number`
- `amount`
- `status_label`
- `provider_status_label`

Fungsi: menampilkan ringkasan utama payment dan data dari gateway.

#### Section: Ringkasan payment attempt

Readonly summary:

- `attempt_count`
- `latest_attempt_status`
- `latest_attempt_amount`
- `latest_checkout_url`
- `latest_attempt_expired_at`

Fungsi: menunjukkan percobaan payment yang terhubung ke invoice.

#### Section: Ringkasan payment event

Readonly summary:

- `events_count`
- `latest_event_source`
- `latest_event_status`
- `latest_event_verified`
- `latest_event_received_at`

Fungsi: menunjukkan event gateway/webhook/status check terakhir.

#### Section: Failure info

Readonly summary:

- `failure_code`
- `failure_message`

Fungsi: membantu investigasi jika payment gagal.

### Action khusus

#### Cek ulang status

Saat ini action ini:

- tersedia di halaman detail payment
- belum tersambung ke service Pakasir
- masih berupa placeholder aman

Tujuan akhirnya nanti:

- sinkronisasi status payment dari gateway
- tanpa mengubah payment manual oleh admin

---

## 7. LeaseResource

Path utama:

- `app/Filament/Resources/Leases/LeaseResource.php`
- `app/Filament/Resources/Leases/Schemas/LeaseForm.php`
- `app/Filament/Resources/Leases/Tables/LeasesTable.php`

### Fungsi resource

Resource ini dipakai admin untuk memonitor kontrak/lease yang sudah terbentuk setelah payment sukses.

### Admin bisa melakukan apa

- melihat daftar lease
- membuka detail lease
- melihat periode-periode lease

### Bentuk halaman

- full page resource
- tidak ada create dari admin
- detail page readonly
- tidak ada action ubah status atau ubah tanggal

### Kolom tabel yang ditampilkan

- `lease_number`
- `tenant.name`
- `plot.name`
- `term_type`
- `duration`
- `start_date`
- `end_date`
- `status`
- `activated_at`

### Filter yang tersedia

- `status`
- `tenant_id`
- `market_id`
- `area_id`

### Form input

#### Section: Informasi kontrak

Readonly detail:

- `lease_number`
- `booking_reference`
- `invoice_number`
- `tenant_name`
- `plot_name`
- `market_name`

#### Section: Periode sewa

Readonly detail:

- `term_type`
- `duration`
- `start_date`
- `end_date`
- `activated_at`

#### Section: Nilai kontrak

Readonly detail:

- `agreed_price`
- `deposit_amount`
- `status`

#### Section: Ringkasan lease periods

Repeater readonly relasi `periods` dengan field:

- `period_no`
- `period_start`
- `period_end`
- `due_date`
- `amount`
- `status`

Fungsi: menampilkan semua periode kontrak langsung di halaman detail lease.

---

## 8. UserResource

Path utama:

- `app/Filament/Resources/Users/UserResource.php`
- `app/Filament/Resources/Users/Schemas/UserForm.php`
- `app/Filament/Resources/Users/Tables/UsersTable.php`
- `app/Filament/Resources/Users/Pages/CreateUser.php`
- `app/Filament/Resources/Users/Pages/EditUser.php`

### Fungsi resource

Resource ini dipakai admin untuk mengelola akun admin dan customer.

### Admin bisa melakukan apa

- melihat daftar user
- membuat user baru
- mengubah user
- mengganti role user
- mengganti status user
- mengganti password user
- menghapus user lain

### Bentuk halaman

- full page resource
- ada list, create, edit

### Kolom tabel yang ditampilkan

- `name`
- `email`
- `phone`
- `roles.name`
- `status`
- `email_verified_at`
- `created_at`

### Filter yang tersedia

- `status`
- `roles`

### Form input

#### Section: Informasi akun

- `name`
  - fungsi: nama user

- `email`
  - fungsi: email login user

- `phone`
  - fungsi: nomor telepon user

#### Section: Hak akses dan status

- `roles`
  - fungsi: menentukan role user (`admin` atau `customer`)

- `status`
  - opsi: `active`, `inactive`, `blocked`
  - fungsi: menentukan status akun user

- `email_verified_at_label`
  - readonly summary status verifikasi email

#### Section: Password

- `password`
  - wajib saat create
  - opsional saat edit
  - fungsi: password login user

- `password_confirmation`
  - fungsi: konfirmasi password agar input tidak salah

### Perilaku penting

- admin bisa membuat user baru dari panel
- password diisi manual saat create
- admin tidak bisa menghapus role admin dari akun yang sedang dipakai login
- admin tidak bisa menghapus akun yang sedang dipakai login

---

## 9. ActivityLogResource

Path utama:

- `app/Filament/Resources/ActivityLogs/ActivityLogResource.php`
- `app/Filament/Resources/ActivityLogs/Schemas/ActivityLogForm.php`
- `app/Filament/Resources/ActivityLogs/Tables/ActivityLogsTable.php`

### Fungsi resource

Resource ini dipakai untuk melihat audit trail aksi penting yang dilakukan admin.

### Admin bisa melakukan apa

- melihat daftar activity log
- membuka detail activity log

### Bentuk halaman

- readonly resource
- tidak ada create
- tidak ada edit
- tidak ada delete
- hanya menampilkan log milik actor yang punya role `admin`

### Kolom tabel yang ditampilkan

- `actor.name`
- `action`
- `target_type`
- `target_id`
- `description`
- `created_at`

### Filter yang tersedia

- `actor_id`
- `action`
- `target_type`

### Form input / detail readonly

#### Section: Informasi activity log

- `actor_name`
- `action`
- `target_type`
- `target_id`
- `created_at`

#### Section: Deskripsi

- `description`
  - fungsi: penjelasan singkat aksi admin

#### Section: Properties

- `properties`
  - fungsi: data tambahan JSON untuk audit/investigasi

### Catatan penting

- log ini dipakai untuk audit internal
- hanya aksi penting admin yang relevan untuk operasional

---

## Kesimpulan Kondisi Panel Admin Saat Ini

Saat ini panel admin sudah bisa dipakai untuk:

- kelola data master pasar, area, dan lahan
- review booking customer
- approve/reject booking
- otomatis membuat invoice saat booking di-approve
- kelola invoice selama belum ada payment attempt
- monitor payment dan ringkasan gateway
- monitor lease aktif dan periode kontrak
- kelola akun user
- melihat audit trail admin

Yang belum masuk ke logic bisnis penuh:

- kirim email invoice ke customer
- integrasi create payment ke Pakasir
- sinkronisasi webhook / status payment gateway
- auto-create lease dari payment sukses nyata
- automation expire booking/invoice
