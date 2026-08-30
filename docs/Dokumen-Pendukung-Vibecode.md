# Dokumen Pendukung Teknis (Vibecode Reference)
## Aplikasi Manajemen Service Computer — RepairStation

Dokumen ini adalah pelengkap PRD, ditujukan sebagai konteks teknis untuk proses pengembangan (vibe coding) — baik dikerjakan sendiri maupun dibantu AI coding assistant (Claude Code, Cursor, dll). Berisi detail stack, struktur project, skema database, daftar endpoint, dan konvensi yang perlu diikuti agar hasil generate konsisten dengan desain yang sudah dibuat.

---

## 1. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel (versi terbaru LTS) |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS |
| Interaktivitas | AJAX (fetch/jQuery) untuk CRUD & update status; Alpine.js untuk state UI (modal, tab, dropdown) |
| Notifikasi | Provider WhatsApp API lokal (Fonnte/Wablas) sebagai kanal utama, SMS/Email sebagai fallback |
| Autentikasi | Laravel Breeze/Fortify (role: admin, teknisi) |
| Font | Lexend (heading), Inter (body), JetBrains Mono (kode tiket) |
| Tema Warna | Indigo sebagai warna utama (`indigo-600` #4F46E5), amber/emerald/rose sebagai indikator status |

## 2. Struktur Folder Laravel (Acuan)

```
app/
  Http/
    Controllers/
      CustomerController.php
      DeviceController.php
      ServiceOrderController.php
      InvoiceController.php
      NotificationController.php
      DashboardController.php
    Requests/
      StoreServiceOrderRequest.php
      StoreInvoiceRequest.php
  Models/
    Customer.php
    Device.php
    ServiceOrder.php
    ServiceLog.php
    Invoice.php
    InvoiceItem.php
    NotificationLog.php
    User.php
  Services/
    WhatsAppNotificationService.php
resources/
  views/
    dashboard.blade.php
    customers/
    service-orders/
      index.blade.php
      partials/_modal-form.blade.php
      partials/_ticket-row.blade.php
    invoices/
  js/
    dashboard.js
    service-order-ajax.js
routes/
  web.php
  api.php (opsional, untuk endpoint AJAX)
database/
  migrations/
```

## 3. Skema Database (Migration Reference)

### customers
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| nama | varchar |
| no_hp | varchar, unique |
| alamat | text, nullable |
| timestamps | |

### devices
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| customer_id | FK → customers |
| jenis | enum('laptop','pc','printer','lainnya') |
| merk | varchar |
| model | varchar, nullable |
| keluhan | text |
| timestamps | |

### service_orders
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| device_id | FK → devices |
| no_tiket | varchar, unique (format: TS-0001) |
| status | enum('antri','dikerjakan','menunggu_sparepart','selesai','diambil') default 'antri' |
| teknisi_id | FK → users, nullable |
| estimasi_biaya | decimal(12,2), nullable |
| tanggal_masuk | date |
| tanggal_selesai | date, nullable |
| timestamps | |

### service_logs
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| service_order_id | FK → service_orders |
| status | varchar |
| catatan | text, nullable |
| changed_by | FK → users |
| created_at | |

### invoices
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| service_order_id | FK → service_orders |
| total_biaya | decimal(12,2) |
| status_bayar | enum('belum_lunas','lunas') default 'belum_lunas' |
| metode_bayar | enum('tunai','transfer','lainnya'), nullable |
| timestamps | |

### invoice_items
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| invoice_id | FK → invoices |
| nama_item | varchar |
| tipe | enum('jasa','sparepart') |
| qty | integer |
| harga | decimal(12,2) |

### notification_logs
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| service_order_id | FK → service_orders |
| channel | enum('whatsapp','sms','email') |
| status | enum('terkirim','gagal') |
| pesan | text |
| created_at | |

### users
| Kolom | Tipe |
|---|---|
| id | bigint, PK |
| nama | varchar |
| email | varchar, unique |
| role | enum('admin','teknisi') |
| password | varchar |
| timestamps | |

## 4. Daftar Endpoint AJAX (Acuan Route)

| Method | Endpoint | Fungsi | Trigger di UI |
|---|---|---|---|
| GET | `/api/customers/search?q=` | Autocomplete cari pelanggan by no HP/nama | Input pencarian di form tiket baru |
| POST | `/service-orders` | Simpan tiket baru | Submit modal "Tiket Baru" |
| PUT | `/service-orders/{id}` | Update data tiket | Submit modal "Edit Tiket" |
| PATCH | `/service-orders/{id}/status` | Update status saja | Dropdown status di tabel/kanban |
| DELETE | `/service-orders/{id}` | Hapus tiket | Tombol hapus |
| GET | `/service-orders?status=` | Filter tiket by status | Tab filter Antri/Dikerjakan/Selesai |
| POST | `/invoices` | Buat invoice dari tiket selesai | Submit modal invoice |
| POST | `/service-orders/{id}/notify` | Kirim ulang notifikasi manual | Tombol "Kirim Update" |
| GET | `/dashboard/stats` | Ambil data stat cards (polling/refresh) | Auto-refresh dashboard tiap beberapa detik |
| GET | `/dashboard/activity` | Ambil log aktivitas terbaru | Panel "Aktivitas Realtime" |

**Catatan realtime:** Untuk MVP, "realtime" cukup disimulasikan dengan polling AJAX (`setInterval` fetch tiap 5–10 detik) ke endpoint stats/activity — tidak perlu WebSocket/Pusher kecuali nanti dirasa perlu update instan lintas user.

## 5. Konvensi Frontend (Menyesuaikan Mockup yang Sudah Dibuat)

- Semua form tambah/edit data (tiket, pelanggan, invoice) **wajib** dalam modal, tidak ada halaman create/edit terpisah.
- Setiap submit modal harus AJAX (`fetch`/`$.ajax`) — response JSON, lalu update DOM/state tanpa reload.
- Badge status pakai mapping warna konsisten:
  - `antri` → amber
  - `dikerjakan` → indigo
  - `menunggu_sparepart` → rose
  - `selesai` / `diambil` → emerald
- Layout mobile: sidebar berubah jadi bottom navigation + drawer (bukan sidebar collapse biasa).
- Elemen tiket di tampilan mobile menggunakan gaya "ticket stub" (kartu dengan notch/perforasi) sesuai mockup dashboard yang sudah dibuat sebelumnya.
- Gunakan Tailwind CDN atau build (Vite) — sesuaikan dengan setup Laravel yang dipakai; kalau pakai Vite, pastikan warna custom (indigo varian, amber, emerald, rose) tetap konsisten dengan token di mockup.

## 6. Prompt Awal untuk AI Coding Assistant (Contoh)

Jika dokumen ini dipakai sebagai konteks untuk vibe coding, berikut contoh prompt pembuka yang bisa dipakai:

> "Saya sedang membangun aplikasi manajemen service computer untuk toko sendiri, berbasis Laravel + AJAX + Tailwind (tema indigo). Berikut PRD dan dokumen teknisnya [lampirkan]. Tolong buatkan migration, model, dan controller untuk modul Service Order terlebih dahulu, sesuai skema database yang sudah ditentukan. Semua CRUD harus AJAX-based dan mendukung integrasi dengan modal di frontend."

## 7. Urutan Pengembangan yang Disarankan

1. Setup project Laravel + Tailwind + Alpine.js
2. Migration & model untuk semua tabel di atas
3. Modul Customer & Device (CRUD dasar via modal AJAX)
4. Modul Service Order (tiket, status, log riwayat)
5. Dashboard dengan stat cards + polling realtime sederhana
6. Modul Invoice & pembayaran
7. Integrasi notifikasi WhatsApp (Fonnte/Wablas)
8. Role & permission (admin vs teknisi)
9. Polish UI mobile & testing end-to-end
