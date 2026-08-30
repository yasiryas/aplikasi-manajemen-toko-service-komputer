# Product Requirements Document (PRD)
## Aplikasi Manajemen Service Computer — RepairStation

| | |
|---|---|
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 28 Agustus 2026 |
| **Pemilik Produk** | Yasir |
| **Status** | Draft |

---

## 1. Latar Belakang & Tujuan

Toko service computer milik sendiri saat ini belum memiliki sistem digital untuk mengelola alur kerja service — mulai dari pendataan pelanggan, tracking progres perbaikan, hingga invoice dan komunikasi ke pelanggan. Proses manual berisiko menyebabkan data hilang, status service tidak jelas, dan pelanggan tidak mendapat update tepat waktu.

**Tujuan produk:**
- Mendigitalkan seluruh alur kerja service computer dari masuk hingga selesai.
- Memberi visibilitas realtime atas antrian dan progres service kepada admin/teknisi.
- Mempercepat proses invoicing dan pembayaran.
- Meningkatkan kepuasan pelanggan lewat notifikasi otomatis.

## 2. Target Pengguna

| Peran | Kebutuhan Utama |
|---|---|
| **Admin/Pemilik Toko** | Melihat ringkasan bisnis, mengelola semua data, membuat invoice |
| **Teknisi** | Melihat tiket yang ditugaskan, update status pekerjaan |
| **Pelanggan** (penerima, tidak login) | Menerima notifikasi status service via WA/SMS/Email |

## 3. Lingkup Produk (Scope)

Aplikasi berbasis **web internal**, digunakan oleh admin dan teknisi toko sendiri (bukan produk yang dijual ke klien lain). Dibangun dengan **Laravel** (backend) dan **AJAX** untuk interaksi tanpa reload halaman, tampilan **Tailwind CSS** bertema indigo, mobile-friendly.

### Termasuk dalam scope (MVP):
- Manajemen data pelanggan & perangkat
- Tracking status tiket service (antri → dikerjakan → menunggu sparepart → selesai → diambil)
- Invoice & pencatatan pembayaran
- Notifikasi otomatis ke pelanggan (WhatsApp/SMS/Email)
- Dashboard realtime dengan CRUD berbasis modal

### Di luar scope (fase berikutnya):
- Aplikasi mobile native
- Portal login untuk pelanggan memantau service sendiri
- Manajemen stok sparepart & supplier
- Multi-cabang/multi-toko
- Payment gateway online (pembayaran tetap dicatat manual/tunai/transfer)

## 4. Alur Pengguna Utama (User Flow)

1. Pelanggan datang membawa perangkat bermasalah.
2. Admin input data pelanggan (baru/lama via pencarian) + data perangkat + keluhan → sistem membuat tiket dengan status **Antri**.
3. Teknisi mengecek perangkat, mengubah status menjadi **Dikerjakan**.
4. Jika perlu sparepart, status diubah ke **Menunggu Sparepart**; sistem mencatat riwayat perubahan.
5. Setelah selesai, teknisi/admin ubah status ke **Selesai**.
6. Sistem otomatis mengirim notifikasi ke pelanggan bahwa perangkat siap diambil.
7. Admin membuat invoice (jasa + sparepart), mencatat pembayaran.
8. Status akhir diubah menjadi **Diambil/Lunas**.

## 5. Fitur & Requirement Fungsional

### 5.1 Manajemen Data Pelanggan & Perangkat
- Tambah/edit/hapus data pelanggan (nama, no. HP, alamat) via modal.
- Autocomplete pencarian pelanggan lama berdasarkan nomor HP saat membuat tiket baru (AJAX).
- Riwayat semua perangkat & tiket per pelanggan.
- Satu pelanggan dapat memiliki banyak perangkat.

### 5.2 Tracking Status Service
- Status tiket: `Antri`, `Dikerjakan`, `Menunggu Sparepart`, `Selesai`, `Diambil`.
- Update status melalui dropdown/modal, dikirim via AJAX tanpa reload.
- Setiap perubahan status tercatat di riwayat log (siapa, kapan, status apa).
- Filter daftar tiket berdasarkan status.
- Penugasan tiket ke teknisi tertentu.

### 5.3 Invoice & Pembayaran
- Buat invoice dari tiket yang sudah selesai.
- Item invoice dinamis (jasa & sparepart) dengan perhitungan total otomatis di sisi client sebelum disimpan.
- Status pembayaran: `Belum Lunas` / `Lunas`.
- Metode pembayaran: tunai, transfer, lainnya.
- Cetak/unduh invoice dalam format yang bisa diprint.

### 5.4 Notifikasi ke Pelanggan
- Notifikasi otomatis saat status berubah menjadi **Selesai** (perangkat siap diambil).
- Notifikasi terkirim melalui WhatsApp (prioritas utama, via provider seperti Fonnte/Wablas), dengan SMS/Email sebagai kanal alternatif.
- Admin dapat mengirim ulang notifikasi secara manual melalui tombol di halaman tiket.
- Log riwayat notifikasi yang sudah terkirim.

### 5.5 Dashboard Realtime
- Kartu ringkasan: tiket aktif, menunggu sparepart, selesai hari ini, pendapatan hari ini.
- Panel aktivitas realtime menampilkan perubahan status/tiket terbaru.
- Ringkasan status dalam bentuk progress bar per kategori.
- Semua operasi CRUD (tiket, pelanggan) dilakukan melalui modal tanpa reload halaman.
- Desain mobile-friendly: sidebar di desktop, bottom navigation + drawer di mobile.

## 6. Requirement Non-Fungsional

| Aspek | Requirement |
|---|---|
| **Teknologi** | Laravel (backend & routing), MySQL (database), AJAX (jQuery/vanilla/Alpine.js untuk interaksi dinamis), Tailwind CSS (UI) |
| **Desain UI** | Tema warna indigo, mobile-friendly, seluruh CRUD melalui modal |
| **Performa** | Update status dan pencarian pelanggan merespons dalam <1 detik |
| **Keamanan** | Autentikasi login untuk admin/teknisi, role-based access (admin vs teknisi) |
| **Ketersediaan Data** | Backup database berkala |
| **Kompatibilitas** | Berjalan baik di browser mobile (Chrome/Safari Android & iOS) dan desktop |

## 7. Struktur Data Inti (Ringkasan)

- `customers` — data pelanggan
- `devices` — perangkat milik pelanggan
- `service_orders` — tiket service (status, teknisi, estimasi biaya)
- `service_logs` — riwayat perubahan status tiket
- `invoices` & `invoice_items` — invoice dan rincian item
- `users` — akun admin/teknisi dengan role
- `notifications_log` — riwayat notifikasi yang terkirim

## 8. Metrik Keberhasilan

- Rata-rata waktu penyelesaian service per tiket dapat dipantau (dari status Antri hingga Selesai).
- Berkurangnya kesalahan pencatatan data pelanggan/perangkat dibanding proses manual.
- Persentase notifikasi terkirim otomatis tanpa intervensi manual admin.
- Waktu yang dibutuhkan admin untuk membuat invoice setelah tiket selesai.

## 9. Risiko & Asumsi

- **Asumsi:** Toko memiliki koneksi internet stabil untuk pengiriman notifikasi WA/SMS/Email.
- **Risiko:** Ketergantungan pada API pihak ketiga (provider WA) — perlu fallback ke SMS/Email bila gagal kirim.
- **Risiko:** Karena aplikasi hanya dipakai internal, perlu dipastikan role admin vs teknisi dibatasi dengan benar agar data tidak disalahgunakan.

## 10. Roadmap Bertahap

| Fase | Fokus |
|---|---|
| **Fase 1 (MVP)** | Manajemen pelanggan/perangkat, tiket service, dashboard dasar |
| **Fase 2** | Invoice & pembayaran, notifikasi otomatis WA/SMS/Email |
| **Fase 3** | Dashboard realtime lengkap, laporan/analitik, penyempurnaan UI mobile |
| **Fase 4 (opsional)** | Manajemen stok sparepart, portal pemantauan untuk pelanggan |
