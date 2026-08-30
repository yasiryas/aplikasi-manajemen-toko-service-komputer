<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\User;

function seedReadyOrder(): ServiceOrder
{
    $customer = Customer::create(['nama' => 'Siti Aminah', 'no_hp' => '082112345678']);
    $device = Device::create(['customer_id' => $customer->id, 'jenis' => 'pc', 'merk' => 'Dell', 'keluhan' => 'Hang']);

    return ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Selesai,
        'tanggal_masuk' => now()->toDateString(),
        'tanggal_selesai' => now()->toDateString(),
    ]);
}

test('hanya admin yang bisa melihat halaman invoice', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);

    $this->actingAs($teknisi)->get('/invoices')->assertForbidden();
    $this->actingAs($admin)->get('/invoices')->assertOk();
});

test('admin dapat membuat invoice untuk tiket selesai', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $order = seedReadyOrder();

    $response = $this->actingAs($admin)->postJson('/invoices', [
        'service_order_id' => $order->id,
        'status_bayar' => PaymentStatus::Lunas->value,
        'metode_bayar' => PaymentMethod::Tunai->value,
        'items' => [
            ['nama_item' => 'Jasa Perbaikan', 'tipe' => 'jasa', 'qty' => 1, 'harga' => 150000],
            ['nama_item' => 'Sparepart', 'tipe' => 'sparepart', 'qty' => 2, 'harga' => 50000],
        ],
    ]);

    $response->assertOk()->assertJsonPath('invoice.total_biaya', 250000);

    $this->assertDatabaseHas('invoice_items', [
        'invoice_id' => $response->json('invoice.id'),
        'nama_item' => 'Jasa Perbaikan',
    ]);
});

test('invoice lunas menutup tiket menjadi diambil', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $order = seedReadyOrder();

    $this->actingAs($admin)->postJson('/invoices', [
        'service_order_id' => $order->id,
        'status_bayar' => PaymentStatus::Lunas->value,
        'metode_bayar' => PaymentMethod::Transfer->value,
        'items' => [
            ['nama_item' => 'Jasa Perbaikan', 'tipe' => 'jasa', 'qty' => 1, 'harga' => 100000],
        ],
    ])->assertOk();

    expect($order->fresh()->status)->toBe(ServiceOrderStatus::Diambil);
});

test('tiket dengan invoice tidak muncul di daftar siap invoice', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $order = seedReadyOrder();

    Invoice::create(['service_order_id' => $order->id, 'total_biaya' => 100000, 'status_bayar' => PaymentStatus::BelumLunas]);

    $response = $this->actingAs($admin)->getJson('/api/invoices/ready-orders');

    $response->assertOk();
    expect($response->json('orders'))->toHaveCount(0);
});

test('admin dapat menandai invoice lunas melalui endpoint pembayaran', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $order = seedReadyOrder();
    $invoice = Invoice::create([
        'service_order_id' => $order->id,
        'total_biaya' => 100000,
        'status_bayar' => PaymentStatus::BelumLunas,
    ]);

    $this->actingAs($admin)
        ->patchJson("/invoices/{$invoice->id}/payment", ['metode_bayar' => PaymentMethod::Tunai->value])
        ->assertOk();

    expect($invoice->fresh()->status_bayar)->toBe(PaymentStatus::Lunas);
    expect($order->fresh()->status)->toBe(ServiceOrderStatus::Diambil);
});
