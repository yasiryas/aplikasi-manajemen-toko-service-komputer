# Aplikasi Manajemen Toko Service Komputer

Sistem manajemen toko service komputer berbasis web. Mencatat **pelanggan**, **perangkat**, **tiket service**, hingga **invoice** secara terintegrasi — lengkap dengan notifikasi WhatsApp, dashboard statistik, dan pengaturan branding toko yang dinamis (nama, alamat, telepon, hingga upload logo).

## Fitur

- **Dashboard** — ringkasan tiket aktif, menunggu sparepart, selesai hari ini, dan pendapatan hari ini, plus aktivitas terbaru.
- **Manajemen Pelanggan** — CRUD pelanggan, pencarian, dan riwayat perangkat per pelanggan.
- **Manajemen Perangkat** — catat jenis (laptop, PC, printer, dll.), merk, model, dan keluhan.
- **Tiket Service** — alur status `Antri → Dikerjakan → Menunggu Sparepart → Selesai → Diambil`, nomor tiket otomatis, estimasi biaya, riwayat status, dan penugasan teknisi.
- **Notifikasi WhatsApp** — kirim ulang pemberitahuan progres ke pelanggan dari detail tiket.
- **Invoice** — buat dari tiket selesai, cetak (print-friendly), dan tandai lunas (tiket otomatis menjadi *Diambil*).
- **Pengaturan Toko (admin)** — nama, tagline, alamat, telepon, catatan footer invoice, dan **upload logo/ikon** yang langsung tampil di seluruh aplikasi, favicon, halaman login, hingga cetak invoice.
- **Dokumentasi** — menu bantuan bawaan berisi fitur, peran, alur penggunaan, dan cara instalasi.
- **Multi-peran** — Admin (semua akses) dan Teknisi (kelola tiket, lihat data).
- **Antarmuka responsif & lokal** — sidebar kolaps ala SB Admin, navigasi bawah di mobile, ikon FontAwesome, SweetAlert2, semua aset (font + ikon) di-bundle lokal sehingga cepat tanpa dependensi CDN.

## Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | Laravel 13, PHP 8.2+ |
| Database | MySQL / MariaDB |
| Frontend | Blade, Tailwind CSS v4, Alpine.js |
| Aset | Vite, FontAwesome Free, Google Fonts (Lexend/Inter, lokal) |
| Interaksi | SweetAlert2, Fetch API (AJAX) |
| Pengujian | Pest (Pest + PHPUnit) |

## Persyaratan

- PHP ≥ 8.2 dengan ekstensi: `pdo_mysql`, `fileinfo`, `gd` (untuk upload gambar), `mbstring`.
- Composer 2.
- Node.js ≥ 20 dan npm.
- MySQL / MariaDB.

## Instalasi

### 1. Clone & instal dependensi

```bash
git clone https://github.com/yasiryas/aplikasi-manajemen-toko-service-komputer.git
cd aplikasi-manajemen-toko-service-komputer

composer install
npm install
```

### 2. Konfigurasi lingkungan

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan kredensial database pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=service_computer
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=database
```

### 3. Migrasi, seeder, dan aset

```bash
php artisan migrate --seed
php artisan storage:link     # wajib agar logo hasil upload dapat diakses
npm run build                # atau npm run dev saat pengembangan
php artisan serve
```

Kunjungi `http://localhost:8000`.

> **Catatan Laragon/XAMPP (Windows):** letakkan folder di `.../www`, buat database via phpMyAdmin, lalu jalankan bagian 3. Arahkan DocumentRoot vhost ke `public/` pada proyek.

## Akun Demo

Seeder membuat tiga akun dan menghubungkan tiga pelanggan sampel ke akun Customer:

| Peran | Email | Kata Sandi |
| --- | --- | --- |
| Admin | `admin@mail.com` | `admin123` |
| Teknisi | `teknisi@mail.com` | `teknisi123` |
| Customer (User) | `customer@mail.com` | `user123` |

Seeder juga menyertakan 6 pelanggan, perangkat, tiket service dalam berbagai status, invoice, dan riwayat.

## Peran & Hak Akses

- **Admin** — semua fitur: kelola pelanggan, perangkat, tiket, invoice, dan menu Pengaturan.
- **Teknisi** — membuat & memperbarui tiket service, mengubah status, kirim notifikasi, serta melihat dashboard, pelanggan, dan perangkat.
- **Customer (User)** — halaman **Progres Servis**: melihat status, estimasi, dan riwayat pengerjaan tiket perangkatnya sendiri; dashboard juga dibatasi pada data miliknya.

Pembatasan akses diterapkan dua lapis: middleware `role:admin` pada rute dan pemeriksaan otorisasi pada Form Request serta `ServiceOrderPolicy`.

## Branding Toko

Menu **Pengaturan** (admin) mengontrol identitas yang tampil di seluruh aplikasi:

- Nama toko, tagline, alamat, telepon — dipakai di judul halaman, sidebar, halaman login, dan invoice.
- **Logo/ikon** — unggah PNG/JPG/WebP/SVG (maks. 2 MB). Logo otomatis dipakai sebagai favicon, logo sidebar, logo halaman login, dan logo pada cetak invoice. File tersimpan di `storage/app/public/logos`.
- Catatan footer invoice — pada bagian bawah invoice.

Nilai disimpan pada tabel `settings` dan **di-cache** (helper global `setting()`) sehingga pembacaan ringan.

## Alur Penggunaan

1. Daftarkan **Pelanggan**, lalu catat **Perangkat** beserta keluhannya.
2. Buat **Tiket Service** — nomor tiket dihasilkan otomatis.
3. Perbarui status seiring pengerjaan; kirim notifikasi WhatsApp bila perlu.
4. Saat tiket **Selesai**, admin membuat **Invoice** (jasa + sparepart).
5. Cetak invoice untuk pelanggan; tandai **Lunas** — tiket otomatis menjadi **Diambil**.

## Struktur Penting

```
app/
  Enums/               # ServiceOrderStatus, PaymentStatus, dll.
  Http/Controllers/    # Dashboard, Customer, Device, ServiceOrder, Invoice, Settings
  Http/Requests/       # Form Request + otorisasi
  Models/              # Eloquent models (memakai cache untuk Setting)
  Policies/            # ServiceOrderPolicy
  Services/            # WhatsAppNotificationService
  Support/helpers.php  # setting(), rupiah(), tanggal(), logo_url()
database/seeders/      # Seeder idempoten + akun demo
resources/views/       # Blade (layouts, partials, halaman)
resources/js/          # Alpine + modul JS per halaman (common, customers, devices, invoices)
tests/                 # Pest feature tests
```

## Struktur Database

- `users` — akun (role admin/teknisi).
- `customers`, `devices` — data pelanggan dan perangkat.
- `service_orders`, `service_logs` — tiket dan riwayat status.
- `notification_logs` — log notifikasi WhatsApp.
- `invoices`, `invoice_items` — invoice dan item (jasa/sparepart).
- `settings` — pengaturan toko (key-value) yang di-cache.

## Pengembangan

### Menjalankan tes

```bash
vendor/bin/pest
```

### Lint & format

```bash
vendor/bin/pint
```

### Aset frontend

```bash
npm run dev       # saat pengembangan (hot reload)
npm run build     # produksi
```

## Troubleshooting

- **Gambar logo tidak muncul** → pastikan `php artisan storage:link` sudah dijalankan dan folder link `public/storage` ada.
- **Perubahan pengaturan tidak tampil** → jalankan `php artisan cache:clear` (settings di-cache).
- **Halaman login tidak berubah styling** → jalankan `npm run build`.
- **421/419 CSRF** → pastikan cookie `XSRF-TOKEN` dan meta `csrf-token` utuh; hapus cache browser.
- **Seeder gagal karena user lama** → seeder otomatis mengalihkan data log/tiket dari akun lama dan menghapusnya; jalankan `php artisan db:seed --force`.

## Lisensi

Proyek sumber terbuka — silakan dipakai, dimodifikasi, dan disebarluaskan sesuai kebutuhan.