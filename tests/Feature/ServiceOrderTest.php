<?php

use App\Enums\NotificationStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use App\Models\User;

function createTechnician(): User
{
    return User::factory()->create(['role' => UserRole::Teknisi]);
}

function createCustomerWithDevice(): array
{
    $customer = Customer::create(['nama' => 'Budi Santoso', 'no_hp' => '081234567890', 'alamat' => 'Jl. Merdeka No. 1']);
    $device = Device::create(['customer_id' => $customer->id, 'jenis' => 'laptop', 'merk' => 'ASUS', 'model' => 'VivoBook', 'keluhan' => 'Layar mati']);

    return [$customer, $device];
}

test('teknisi dapat membuat tiket service', function () {
    $user = createTechnician();
    [, $device] = createCustomerWithDevice();

    $response = $this->actingAs($user)->postJson('/service-orders', [
        'device_id' => $device->id,
        'status' => ServiceOrderStatus::Antri->value,
        'teknisi_id' => $user->id,
        'estimasi_biaya' => 150000,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $response->assertOk()
        ->assertJsonPath('order.no_tiket', 'TS-0001')
        ->assertJsonPath('order.status', ServiceOrderStatus::Antri->value);

    $this->assertDatabaseHas('service_logs', [
        'service_order_id' => $response->json('order.id'),
        'catatan' => 'Tiket dibuat',
    ]);
});

test('teknisi non-admin hanya melihat tiket miliknya', function () {
    $user = createTechnician();
    $other = createTechnician();

    [$customer, $device] = createCustomerWithDevice();

    $own = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Antri,
        'teknisi_id' => $user->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $unassigned = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0002',
        'status' => ServiceOrderStatus::Dikerjakan,
        'teknisi_id' => $other->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get('/service-orders')
        ->assertOk()
        ->assertSee('TS-0001')
        ->assertDontSee('TS-0002');

    $this->actingAs($other)
        ->get('/service-orders')
        ->assertOk()
        ->assertDontSee('TS-0001')
        ->assertSee('TS-0002');
});

test('ubah status ke selesai memicu notifikasi WhatsApp', function () {
    $user = createTechnician();
    [$customer, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Dikerjakan,
        'teknisi_id' => $user->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->patchJson("/service-orders/{$order->id}/status", ['status' => ServiceOrderStatus::Selesai->value])
        ->assertOk();

    $this->assertDatabaseHas('notification_logs', [
        'service_order_id' => $order->id,
        'status' => NotificationStatus::Terkirim->value,
    ]);

    expect($order->fresh()->tanggal_selesai)->not->toBeNull();
});

test('status tiket diambil tidak bisa diubah lagi', function () {
    $user = createTechnician();
    [, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Diambil,
        'teknisi_id' => $user->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    // Endpoint tetap menolak status tidak valid
    $this->actingAs($user)
        ->patchJson("/service-orders/{$order->id}/status", ['status' => 'invalid-status'])
        ->assertStatus(422);
});

test('hanya admin yang dapat menghapus tiket', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $user = createTechnician();
    [, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Antri,
        'teknisi_id' => $user->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)->deleteJson("/service-orders/{$order->id}")->assertForbidden();
    $this->actingAs($admin)->deleteJson("/service-orders/{$order->id}")->assertOk();

    $this->assertDatabaseMissing('service_orders', ['id' => $order->id]);
});

test('teknisi tidak dapat melihat tiket teknisi lain', function () {
    $user = createTechnician();
    $other = createTechnician();
    [, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Dikerjakan,
        'teknisi_id' => $other->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)->getJson("/service-orders/{$order->id}")->assertForbidden();
});

test('teknisi tidak dapat mengubah status tiket teknisi lain', function () {
    $user = createTechnician();
    $other = createTechnician();
    [, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Dikerjakan,
        'teknisi_id' => $other->id,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->patchJson("/service-orders/{$order->id}/status", ['status' => ServiceOrderStatus::Selesai->value])
        ->assertForbidden();

    expect($order->fresh()->status)->toBe(ServiceOrderStatus::Dikerjakan);
});

test('teknisi dapat mengupdate tiket yang belum ditugaskan', function () {
    $user = createTechnician();
    [, $device] = createCustomerWithDevice();

    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-0001',
        'status' => ServiceOrderStatus::Antri,
        'teknisi_id' => null,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->putJson("/service-orders/{$order->id}", [
            'device_id' => $device->id,
            'status' => ServiceOrderStatus::Dikerjakan->value,
            'teknisi_id' => $user->id,
            'estimasi_biaya' => 100000,
            'tanggal_masuk' => $order->tanggal_masuk->toDateString(),
        ])
        ->assertOk();

    expect($order->fresh()->teknisi_id)->toBe($user->id);
});
