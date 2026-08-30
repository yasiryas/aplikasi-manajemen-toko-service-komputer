<?php

use App\Enums\DeviceType;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use App\Models\User;

function seedUserRole(UserRole $role): User
{
    return User::factory()->create(['role' => $role]);
}

test('role user dapat melihat halaman tetapi tidak dapat membuat data', function () {
    $user = seedUserRole(UserRole::User);
    $customer = Customer::create(['nama' => 'Budi', 'no_hp' => '08123']);

    $this->actingAs($user)->get('/dashboard')->assertOk();
    $this->actingAs($user)->get('/customers')->assertOk();
    $this->actingAs($user)->get('/devices')->assertOk();
    $this->actingAs($user)->get('/service-orders')->assertOk();
    $this->actingAs($user)->get('/dokumentasi')->assertOk();

    $this->actingAs($user)->postJson('/devices', [
        'customer_id' => $customer->id,
        'jenis' => DeviceType::Laptop->value,
        'merk' => 'Lenovo',
        'keluhan' => 'Blank',
    ])->assertForbidden();

    $this->actingAs($user)->postJson('/service-orders', [
        'device_id' => $customer->devices()->create(['jenis' => DeviceType::Laptop->value, 'merk' => 'Lenovo', 'keluhan' => 'Blank'])->id,
        'status' => ServiceOrderStatus::Antri->value,
        'tanggal_masuk' => now()->toDateString(),
    ])->assertForbidden();
});

test('role user tidak dapat mengubah status tiket', function () {
    $user = seedUserRole(UserRole::User);
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = Customer::create(['nama' => 'Siti', 'no_hp' => '08211']);
    $device = Device::create(['customer_id' => $customer->id, 'jenis' => 'pc', 'merk' => 'Dell', 'keluhan' => 'Hang']);
    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-7777',
        'status' => ServiceOrderStatus::Antri,
        'tanggal_masuk' => now()->toDateString(),
        'teknisi_id' => $admin->id,
    ]);

    $this->actingAs($user)
        ->patchJson("/service-orders/{$order->id}/status", ['status' => ServiceOrderStatus::Dikerjakan->value])
        ->assertForbidden();
});

test('teknisi tetap dapat membuat dan mengubah tiket', function () {
    $teknisi = seedUserRole(UserRole::Teknisi);
    $customer = Customer::create(['nama' => 'Rudi', 'no_hp' => '08345']);
    $device = Device::create(['customer_id' => $customer->id, 'jenis' => 'pc', 'merk' => 'HP', 'keluhan' => 'Lemot']);
    $order = ServiceOrder::create([
        'device_id' => $device->id,
        'no_tiket' => 'TS-8888',
        'status' => ServiceOrderStatus::Antri,
        'tanggal_masuk' => now()->toDateString(),
        'teknisi_id' => $teknisi->id,
    ]);

    $this->actingAs($teknisi)
        ->patchJson("/service-orders/{$order->id}/status", ['status' => ServiceOrderStatus::Dikerjakan->value])
        ->assertOk();
});
