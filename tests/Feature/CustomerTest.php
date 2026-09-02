<?php

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;

test('hanya admin yang dapat menambah pelanggan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);

    $payload = ['nama' => 'Ahmad Fauzi', 'no_hp' => '083456789012', 'alamat' => 'Jl. Anggrek No. 3'];

    $this->actingAs($teknisi)->postJson('/customers', $payload)->assertForbidden();
    $this->actingAs($admin)->postJson('/customers', $payload)->assertOk();

    $this->assertDatabaseHas('customers', ['nama' => 'Ahmad Fauzi', 'no_hp' => '083456789012']);
});

test('menambahkan pelanggan dengan nomor HP duplikat ditolak', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    Customer::create(['nama' => 'Lama', 'no_hp' => '081234567890']);

    $this->actingAs($admin)
        ->postJson('/customers', ['nama' => 'Baru', 'no_hp' => '081234567890'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('no_hp');
});

test('teknisi dapat melihat data pelanggan', function () {
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);
    Customer::create(['nama' => 'Dewi Lestari', 'no_hp' => '085678901234']);

    $this->actingAs($teknisi)
        ->get('/customers')
        ->assertOk()
        ->assertSee('Dewi Lestari');
});

test('pencarian pelanggan untuk form tiket', function () {
    $teknisi = User::factory()->create(['role' => UserRole::Teknisi]);
    $customer = Customer::create(['nama' => 'Rudi Hartono', 'no_hp' => '087890123456']);

    $this->actingAs($teknisi)
        ->getJson('/api/customers/search?q=Aku%20Rudi')
        ->assertOk();

    $this->actingAs($teknisi)
        ->getJson('/api/customers/search?q=Hartono')
        ->assertJsonCount(1, 'customers');

    $this->actingAs($teknisi)
        ->getJson("/api/customers/{$customer->id}/devices")
        ->assertOk()
        ->assertJsonCount(0, 'devices');
});

test('detail pelanggan untuk modal mengembalikan data lengkap', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = Customer::create([
        'nama' => 'Uji Nama',
        'no_hp' => '081234567891',
        'alamat' => 'Jl. Ujian No. 1',
    ]);

    $this->actingAs($admin)
        ->getJson("/customers/{$customer->id}/detail")
        ->assertOk()
        ->assertJsonPath('customer.id', $customer->id)
        ->assertJsonPath('customer.nama', 'Uji Nama')
        ->assertJsonPath('customer.no_hp', '081234567891');
});
