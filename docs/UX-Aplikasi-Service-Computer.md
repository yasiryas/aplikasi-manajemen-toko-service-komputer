# Dokumen UX (User Experience)
## Aplikasi Manajemen Service Computer — RepairStation

| | |
|---|---|
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 28 Agustus 2026 |
| **Terkait** | PRD-Aplikasi-Service-Computer.md, ERD-Aplikasi-Service-Computer.mermaid |

---

## 1. Persona Pengguna

### Persona 1 — Admin/Pemilik Toko
- **Kebutuhan:** Melihat kondisi bisnis sekilas (tiket aktif, pendapatan hari ini), mengelola data pelanggan & tiket, membuat invoice.
- **Konteks pemakaian:** Sering di meja kasir/komputer utama toko (desktop), kadang cek dari HP saat di luar toko.
- **Frustrasi yang ingin dihindari:** Berpindah halaman terlalu banyak untuk hal sederhana seperti update status.

### Persona 2 — Teknisi
- **Kebutuhan:** Tahu tiket mana yang jadi tanggung jawabnya, update status pekerjaan dengan cepat.
- **Konteks pemakaian:** Sering di area bengkel, bisa jadi menggunakan HP/tablet sambil tangan kotor — interaksi harus simpel, tombol besar.
- **Frustrasi yang ingin dihindari:** Form yang rumit hanya untuk mengubah status "Dikerjakan" → "Selesai".

### Persona 3 — Pelanggan (penerima notifikasi, tidak mengakses aplikasi)
- **Kebutuhan:** Tahu kapan perangkatnya siap diambil tanpa harus menelepon/datang ke toko.
- **Titik kontak:** Notifikasi WhatsApp/SMS/Email otomatis.

## 2. Prinsip Desain (Design Principles)

1. **Minim klik, minim reload** — semua aksi CRUD selesai dalam satu modal, tanpa pindah halaman.
2. **Status selalu terlihat jelas** — warna badge konsisten di semua tempat (tabel, kartu, ringkasan) agar admin/teknisi tidak perlu berpikir dua kali.
3. **Mobile-first untuk teknisi, desktop-first untuk admin** — layout menyesuaikan konteks pemakaian tanpa kehilangan fungsi di kedua perangkat.
4. **Realtime terasa hidup, tidak mengganggu** — update otomatis (polling) tidak boleh membuat tampilan "lompat-lompat" atau mengganggu saat pengguna sedang mengetik/klik.
5. **Identitas visual bengkel** — elemen seperti "ticket stub" (kartu tiket bergaya struk servis) dipakai agar aplikasi terasa relevan dengan konteks bengkel, bukan template generik.

## 3. Peta Perjalanan Pengguna (User Journey)

### Journey: Admin memproses tiket dari masuk sampai selesai
| Tahap | Aksi Admin | Titik Sentuh UI | Emosi yang Diharapkan |
|---|---|---|---|
| 1. Pelanggan datang | Cari data pelanggan (autocomplete no. HP) | Search bar di modal "Tiket Baru" | Cepat, tidak perlu input ulang data lama |
| 2. Buat tiket | Isi perangkat, keluhan, estimasi | Modal "Tiket Baru" | Sederhana, field minimal |
| 3. Pantau progres | Lihat status di dashboard/tabel | Dashboard, filter status | Percaya diri, semua terlihat jelas |
| 4. Update status | Ubah status via dropdown/modal | Tabel tiket, kartu mobile | Instan, tanpa reload |
| 5. Tiket selesai | Sistem kirim notifikasi otomatis | (background, tanpa aksi UI) | Lega, tidak perlu ingat kirim manual |
| 6. Buat invoice | Tambah item jasa/sparepart, simpan | Modal invoice | Total otomatis terhitung, tidak salah hitung |
| 7. Tutup transaksi | Tandai lunas & diambil | Modal/aksi cepat | Selesai, rapi |

### Journey: Teknisi update status pekerjaan
| Tahap | Aksi Teknisi | Titik Sentuh UI |
|---|---|---|
| 1. Buka aplikasi di HP | Lihat daftar tiket miliknya | Bottom nav → Tiket |
| 2. Pilih tiket | Tap kartu tiket (ticket stub) | Kartu tiket mobile |
| 3. Update status | Pilih status baru dari modal | Modal edit tiket |
| 4. Simpan | Konfirmasi | Tombol "Simpan Perubahan" |

## 4. Struktur Navigasi

```
Dashboard (halaman utama)
├── Pelanggan
│   └── Detail Pelanggan → riwayat perangkat & tiket
├── Perangkat
├── Tiket Service
│   └── Modal: Tambah/Edit Tiket
├── Invoice
│   └── Modal: Buat Invoice dari Tiket Selesai
└── Laporan (fase lanjutan)
```

- **Desktop:** sidebar kiri tetap, konten utama di kanan.
- **Mobile:** bottom navigation (4 menu utama + tombol tambah mengambang), sidebar penuh diakses lewat drawer/hamburger.

## 5. Deskripsi Layar Utama (Wireframe Naratif)

### 5.1 Dashboard
- Header: judul halaman, tanggal, search, notifikasi, tombol "Tiket Baru".
- Indikator realtime kecil ("Data realtime · diperbarui...") di atas kartu statistik.
- 4 kartu statistik: Tiket Aktif, Menunggu Sparepart, Selesai Hari Ini, Pendapatan Hari Ini.
- Dua kolom: (kiri, lebih lebar) daftar tiket terbaru dengan filter status; (kanan) feed aktivitas realtime bergaya timeline.
- Baris bawah: ringkasan status dalam bentuk progress bar per kategori.

### 5.2 Daftar Tiket Service
- Desktop: tabel dengan kolom Tiket, Pelanggan, Perangkat, Status, Estimasi, Aksi.
- Mobile: kartu bergaya "ticket stub" (ada notch kiri-kanan meniru sobekan tiket fisik), status badge di pojok kanan atas kartu.
- Filter status berbentuk tab pill di atas daftar.

### 5.3 Modal Tambah/Edit Tiket
- Header modal memakai warna indigo gradasi (identitas brand).
- Field: Nama Pelanggan (dengan autocomplete), Perangkat & Keluhan, Status (dropdown), Estimasi Biaya, Teknisi.
- Tombol aksi: "Batal" (netral) dan "Buat Tiket"/"Simpan Perubahan" (indigo, penekanan utama).

### 5.4 Modal Invoice
- Daftar item dinamis (jasa/sparepart) yang bisa ditambah/hapus baris.
- Total otomatis terhitung saat qty/harga berubah.
- Pilihan metode pembayaran & status lunas/belum di bagian bawah.

## 6. Pola Interaksi (Interaction Patterns)

| Pola | Penerapan |
|---|---|
| **Modal-based CRUD** | Semua tambah/edit data dilakukan di modal, tidak ada halaman form terpisah |
| **Inline status update** | Dropdown status langsung di baris tabel/kartu, tersimpan otomatis via AJAX |
| **Autocomplete** | Pencarian pelanggan berdasarkan no. HP saat membuat tiket baru |
| **Polling realtime** | Statistik dan feed aktivitas diperbarui otomatis tiap beberapa detik tanpa aksi pengguna |
| **Optimistic UI** | Perubahan status/CRUD langsung terlihat di UI sebelum konfirmasi server selesai, untuk kesan cepat |
| **Konfirmasi hapus** | Aksi hapus tiket/pelanggan perlu konfirmasi singkat agar tidak terhapus tidak sengaja |

## 7. Sistem Warna & Tipografi (Design Tokens)

| Elemen | Nilai |
|---|---|
| Warna utama | Indigo `#4F46E5` (indigo-600), hover `#4338CA` (indigo-700) |
| Status Antri | Amber `#F59E0B` |
| Status Dikerjakan | Indigo `#6366F1` |
| Status Menunggu Sparepart | Rose `#F43F5E` |
| Status Selesai/Diambil | Emerald `#10B981` |
| Font judul | Lexend (600–800) |
| Font isi | Inter (400–600) |
| Font kode tiket | JetBrains Mono |

## 8. Pertimbangan Aksesibilitas & Mobile

- Ukuran tombol aksi di mobile minimal 40x40px agar mudah disentuh (relevan untuk teknisi yang mungkin memakai sarung tangan/tangan kotor).
- Kontras warna badge status dan teks dijaga sesuai standar WCAG AA.
- Semua ikon aksi (edit/hapus) disertai area tap yang cukup besar, tidak hanya ikon kecil polos.
- Form modal di mobile muncul dari bawah (bottom sheet style) agar familiar dengan pola UI mobile pada umumnya.

## 9. Referensi Visual

Mockup dashboard interaktif (HTML/Tailwind) sudah dibuat sebelumnya sebagai referensi implementasi pola-pola di atas — lihat file `dashboard-service-computer.html`.
