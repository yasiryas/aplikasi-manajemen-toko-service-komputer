@extends('layouts.app', ['page' => 'dokumentasi'])

@section('page-title', 'Dokumentasi')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Tentang Aplikasi</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    <strong>{{ setting('nama_toko', 'Service Computer') }}</strong> adalah sistem manajemen toko service komputer
                    berbasis web. Aplikasi mencatat pelanggan, perangkat, tiket service, hingga invoicing secara terintegrasi
                    sehingga alur kerja servis lebih rapi dan mudah dipantau.
                </p>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Nama toko, tagline, alamat, telepon, dan logo ditampilkan
                    otomatis di seluruh aplikasi sesuai pengaturan pada menu <span class="font-medium">Pengaturan</span> (khusus admin).
                </p>
            </div>

            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Fitur Utama</h2>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex gap-2"><i class="fa-solid fa-user-plus mt-0.5 text-indigo-500"></i><span><strong>Manajemen Pelanggan</strong> — data pelanggan, nomor HP, alamat, dan riwayat perangkat.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-hard-drive mt-0.5 text-indigo-500"></i><span><strong>Manajemen Perangkat</strong> — jenis, merk, model, dan keluhan yang dicatat.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-list-check mt-0.5 text-indigo-500"></i><span><strong>Tiket Service</strong> — alur status <em>Antri → Dikerjakan → Menunggu Sparepart → Selesai → Diambil</em>, dengan riwayat status dan estimasi biaya.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-bell mt-0.5 text-indigo-500"></i><span><strong>Notifikasi WhatsApp</strong> — kirim ulang pemberitahuan progres kepada pelanggan.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-file-invoice-dollar mt-0.5 text-indigo-500"></i><span><strong>Invoice</strong> — buat invoice dari tiket selesai, cetak, dan tandai lunas.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-gauge-high mt-0.5 text-indigo-500"></i><span><strong>Dashboard</strong> — ringkasan statistik dan aktivitas terbaru.</span></li>
                    <li class="flex gap-2"><i class="fa-solid fa-gear mt-0.5 text-indigo-500"></i><span><strong>Pengaturan Toko</strong> — nama, alamat, telepon, catatan invoice, dan upload logo/ikon.</span></li>
                </ul>
            </div>

            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Akun &amp; Peran</h2>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="rounded-l-lg px-4 py-2.5 font-semibold">Peran</th>
                                <th class="px-4 py-2.5 font-semibold">Hak Akses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">Admin</td>
                                <td class="px-4 py-3">Semua fitur: kelola pelanggan, perangkat, tiket, invoice, dan pengaturan toko.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">Teknisi</td>
                                <td class="px-4 py-3">Membuat &amp; memperbarui tiket service, mengubah status, melihat dashboard, pelanggan, dan perangkat.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">User</td>
                                <td class="px-4 py-3">Hanya melihat (read-only): dashboard, data pelanggan, perangkat, dan tiket service.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-slate-400">Akun demo: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">admin@mail.com</code> / <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">admin123</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">customer@mail.com</code> / <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">teknisi123</code>, dan <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">user@mail.com</code> / <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600">user123</code>.</p>
            </div>

            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Alur Penggunaan</h2>
                <ol class="mt-3 list-inside list-decimal space-y-2 text-sm text-slate-600">
                    <li>Registrasi <strong>Pelanggan</strong> lalu catat <strong>Perangkat</strong> beserta keluhannya.</li>
                    <li>Buat <strong>Tiket Service</strong> — nomor tiket dibuat otomatis.</li>
                    <li>Perbarui status seiring pengerjaan; kirim notifikasi WhatsApp ke pelanggan bila perlu.</li>
                    <li>Saat tiket <strong>Selesai</strong>, admin membuat <strong>Invoice</strong> (jasa + sparepart).</li>
                    <li>Cetak invoice untuk pelanggan; tandai <strong>Lunas</strong> setelah dibayar — tiket otomatis menjadi <strong>Diambil</strong>.</li>
                </ol>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Teknologi</h2>
                <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
                    <li>Laravel {{ app()->version() }}</li>
                    <li>PHP {{ PHP_VERSION }}</li>
                    <li>MySQL / MariaDB</li>
                    <li>Tailwind CSS + Alpine.js</li>
                    <li>FontAwesome + SweetAlert2</li>
                </ul>
            </div>

            <div class="card p-6">
                <h2 class="font-heading text-lg font-bold text-slate-900">Instalasi Ringkas</h2>
                <pre class="mt-3 overflow-x-auto rounded-lg bg-slate-900 p-3 text-xs leading-relaxed text-slate-200"><code>cp .env.example .env
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve</code></pre>
                <p class="mt-2 text-xs text-slate-400">Pastikan konfigurasi database di <code>.env</code> sesuai lingkungan Anda.</p>
            </div>

            <div class="card bg-indigo-600 p-6 text-white">
                <h2 class="font-heading text-lg font-bold">Butuh bantuan?</h2>
                <p class="mt-1 text-sm text-indigo-100">Kelola branding toko Anda dari menu Pengaturan (khusus admin). Perubahan nama, logo, atau alamat langsung tampil di seluruh aplikasi dan invoice.</p>
            </div>
        </div>
    </div>
@endsection