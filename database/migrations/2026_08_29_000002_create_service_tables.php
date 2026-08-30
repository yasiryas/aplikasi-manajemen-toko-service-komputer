<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp')->unique();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis', ['laptop', 'pc', 'printer', 'lainnya']);
            $table->string('merk');
            $table->string('model')->nullable();
            $table->text('keluhan');
            $table->timestamps();
        });

        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('no_tiket')->unique();
            $table->string('status')->default('antri');
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimasi_biaya', 12, 2)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('service_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_biaya', 12, 2);
            $table->string('status_bayar')->default('belum_lunas');
            $table->string('metode_bayar')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('nama_item');
            $table->enum('tipe', ['jasa', 'sparepart']);
            $table->integer('qty');
            $table->decimal('harga', 12, 2);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['whatsapp', 'sms', 'email']);
            $table->enum('status', ['terkirim', 'gagal'])->default('terkirim');
            $table->text('pesan');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('service_logs');
        Schema::dropIfExists('service_orders');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('customers');
    }
};
