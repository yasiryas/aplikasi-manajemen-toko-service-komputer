<?php

use App\Enums\UserRole;
use App\Models\User;

test('customer dialihkan ke progres servis setelah login', function () {
    $customer = User::factory()->create(['role' => UserRole::User]);

    $this->post('/login', ['email' => $customer->email, 'password' => 'password'])
        ->assertRedirect(route('service-orders.progress'));
});

test('admin tetap dialihkan ke dashboard setelah login', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
        ->assertRedirect('/dashboard');
});

test('customer yang membuka dashboard diarahkan ke progres servis', function () {
    $customer = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertRedirect(route('service-orders.progress'));
});

test('paket pwa tersedia', function () {
    expect(file_exists(public_path('manifest.webmanifest')))->toBeTrue();
    expect(file_exists(public_path('sw.js')))->toBeTrue();
    expect(file_exists(public_path('offline.html')))->toBeTrue();
    expect(file_exists(public_path('icons/icon-192.png')))->toBeTrue();
    expect(file_exists(public_path('icons/icon-512.png')))->toBeTrue();
    expect(file_exists(public_path('icons/maskable-512.png')))->toBeTrue();
});
