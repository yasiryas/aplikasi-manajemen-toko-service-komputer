<?php

use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('hanya admin yang bisa mengakses halaman pengaturan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);

    $this->actingAs($teknisi)->get('/settings')->assertForbidden();
    $this->actingAs($admin)->get('/settings')->assertOk()->assertSee('nama_toko');
});

test('admin dapat menyimpan pengaturan toko', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->put('/settings', [
        'nama_toko' => 'Toko Komputer Jaya',
        'tagline_toko' => 'Service Terbaik',
        'telepon_toko' => '081234567890',
        'alamat_toko' => 'Jl. Merdeka No. 1',
        'footer_invoice' => 'Terima kasih',
    ])->assertRedirect();

    expect(Setting::get('nama_toko'))->toBe('Toko Komputer Jaya');
    expect(Setting::get('telepon_toko'))->toBe('081234567890');
    expect(setting('nama_toko'))->toBe('Toko Komputer Jaya');
});

test('admin dapat mengupload logo toko', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)->put('/settings', [
        'nama_toko' => 'Service Computer',
        'logo' => UploadedFile::fake()->image('logo.png', 128, 128),
    ])->assertRedirect();

    expect(Setting::get('logo'))->toStartWith('logos/');
    Storage::disk('public')->assertExists(Setting::get('logo'));
});

test('seeder menghasilkan akun admin dan customer', function () {
    $this->seed();

    expect(User::where('email', 'admin@mail.com')->exists())->toBeTrue();
    expect(User::where('email', 'customer@mail.com')->exists())->toBeTrue();
    expect(User::where('email', 'admin@mil.com')->exists())->toBeFalse();
    expect(Setting::get('nama_toko'))->toBe('Service Computer');
});

test('halaman dokumentasi dapat diakses semua peran', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);

    $this->actingAs($teknisi)->get('/dokumentasi')->assertOk()->assertSee('Fitur Utama');
    $this->actingAs($admin)->get('/dokumentasi')->assertOk();
});

test('halaman print invoice menampilkan identitas toko dari pengaturan', function () {
    Setting::set('nama_toko', 'Service Computer');
    Setting::set('alamat_toko', 'Jl. Contoh No. 5');

    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = Customer::create(['nama' => 'Budi', 'no_hp' => '08123']);
    $device = Device::create(['customer_id' => $customer->id, 'jenis' => 'pc', 'merk' => 'Lenovo', 'keluhan' => 'Blank']);
    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-9001',
        'status' => ServiceOrderStatus::Diambil,
        'tanggal_masuk' => now()->toDateString(),
    ]);
    $invoice = Invoice::create([
        'service_order_id' => $order->id,
        'total_biaya' => 250000,
        'status_bayar' => PaymentStatus::Lunas,
    ]);
    $invoice->items()->create(['nama_item' => 'Jasa Perbaikan', 'tipe' => 'jasa', 'qty' => 1, 'harga' => 250000]);

    $response = $this->actingAs($admin)->get("/invoices/{$invoice->id}/print");

    $response->assertOk();
    $response->assertSee('Service Computer');
    $response->assertSee('Jl. Contoh No. 5');
    $response->assertSee('Rp 250.000');
    $response->assertSee('TS-9001');
});
