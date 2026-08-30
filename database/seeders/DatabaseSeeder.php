<?php

namespace Database\Seeders;

use App\Enums\DeviceType;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\NotificationLog;
use App\Models\ServiceLog;
use App\Models\ServiceOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('nama_toko', 'Service Computer');
        Setting::set('alamat_toko', 'Jl. Merdeka No. 88, Yogyakarta');
        Setting::set('telepon_toko', '081234567890');
        Setting::set('tagline_toko', 'Service Komputer');
        Setting::set('footer_invoice', 'Terima kasih telah mempercayakan perbaikan kepada kami.');

        $admin = User::updateOrCreate(['email' => 'admin@mail.com'], [
            'name' => 'Admin Toko',
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
            'password' => 'admin123',
        ]);

        $teknisi = User::updateOrCreate(['email' => 'customer@mail.com'], [
            'name' => 'Pelayanan (Customer Service)',
            'role' => UserRole::Teknisi,
            'email_verified_at' => now(),
            'password' => 'teknisi123',
        ]);

        $legacyEmails = ['admin@mil.com', 'teknisi@mil.com', 'budi@mil.com'];
        $legacyIds = User::whereIn('email', $legacyEmails)->pluck('id');

        if ($legacyIds->isNotEmpty()) {
            ServiceOrder::whereIn('teknisi_id', $legacyIds)->update(['teknisi_id' => $teknisi->id]);
            ServiceLog::whereIn('changed_by', $legacyIds)->update(['changed_by' => $admin->id]);
            User::whereIn('email', $legacyEmails)->delete();
        }

        $sampleCustomers = [
            ['Budi Santoso', '081234567890', 'Jl. Merdeka No. 12, Jakarta'],
            ['Siti Aminah', '082112345678', 'Jl. Melati No. 5, Bandung'],
            ['Ahmad Fauzi', '083456789012', 'Jl. Anggrek No. 3, Surabaya'],
            ['Dewi Lestari', '085678901234', 'Jl. Kenanga No. 8, Yogyakarta'],
            ['Rudi Hartono', '087890123456', 'Jl. Mawar No. 21, Semarang'],
            ['Lina Marlina', '089345678901', 'Jl. Cempaka No. 9, Medan'],
        ];

        $statusFlow = [
            ServiceOrderStatus::Antri,
            ServiceOrderStatus::Dikerjakan,
            ServiceOrderStatus::MenungguSparepart,
            ServiceOrderStatus::Selesai,
            ServiceOrderStatus::Diambil,
            ServiceOrderStatus::MenungguSparepart,
        ];

        foreach ($sampleCustomers as $index => [$nama, $noHp, $alamat]) {
            $customer = Customer::firstOrCreate(['no_hp' => $noHp], [
                'nama' => $nama,
                'alamat' => $alamat,
            ]);

            if ($customer->devices()->exists()) {
                continue;
            }

            $deviceTypes = [DeviceType::Laptop, DeviceType::PC, DeviceType::Printer, DeviceType::Laptop, DeviceType::PC, DeviceType::Lainnya];
            $deviceBrands = ['ASUS', 'Dell', 'HP', 'Lenovo', 'Acer', 'Epson'];
            $complaints = [
                'Layar mati tidak tampil', 'Sering restart sendiri', 'Suara berisik, sering hang',
                'Tidak dapat booting', 'Printer macet jangan kertas', 'Keyboard rusak sebagian',
            ];

            $device = Device::create([
                'customer_id' => $customer->id,
                'jenis' => $deviceTypes[$index],
                'merk' => $deviceBrands[$index],
                'model' => ['VivoBook', 'OptiPlex', 'Pavilion', 'ThinkPad', 'Swift', 'L3110'][$index],
                'keluhan' => $complaints[$index],
            ]);

            $status = $statusFlow[$index];
            $isDone = in_array($status, [ServiceOrderStatus::Selesai, ServiceOrderStatus::Diambil], true);

            $order = ServiceOrder::create([
                'device_id' => $device->id,
                'no_tiket' => ServiceOrder::generateTicketNumber(),
                'status' => $status,
                'teknisi_id' => $teknisi->id,
                'estimasi_biaya' => ($index + 1) * 150000,
                'tanggal_masuk' => now()->subDays($index + 1)->toDateString(),
                'tanggal_selesai' => $isDone ? now()->subDays($index)->toDateString() : null,
            ]);

            ServiceLog::create([
                'service_order_id' => $order->id,
                'status' => $order->status->value,
                'catatan' => 'Tiket dibuat',
                'changed_by' => $admin->id,
                'created_at' => $order->tanggal_masuk,
            ]);

            if (! $isDone) {
                NotificationLog::create([
                    'service_order_id' => $order->id,
                    'channel' => NotificationChannel::WhatsApp,
                    'status' => NotificationStatus::Terkirim,
                    'pesan' => 'Halo '.$customer->nama.', perangkat Anda dalam status '.$status->label().'. - Service Computer',
                ]);
            }

            if ($isDone) {
                $invoice = Invoice::create([
                    'service_order_id' => $order->id,
                    'total_biaya' => ($order->estimasi_biaya ?? 0) + 250000,
                    'status_bayar' => fake()->boolean(70) ? PaymentStatus::Lunas : PaymentStatus::BelumLunas,
                    'metode_bayar' => PaymentMethod::Tunai,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'nama_item' => 'Jasa Perbaikan',
                    'tipe' => 'jasa',
                    'qty' => 1,
                    'harga' => $order->estimasi_biaya,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'nama_item' => 'Suku Cadang & Komponen',
                    'tipe' => 'sparepart',
                    'qty' => 1,
                    'harga' => 250000,
                ]);
            }
        }
    }
}
