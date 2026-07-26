<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique(); // Contoh: MAHASISWA50, EARLYBIRD
            $table->string('name'); // Contoh: Diskon Mahasiswa 50%
            $table->enum('discount_type', ['percent', 'nominal'])->default('percent');
            $table->integer('discount_value'); // Contoh: 50 (persen) atau 15000 (nominal)
            $table->integer('max_discount')->nullable(); // Maksimal diskon (dalam Rupiah) jika tipe percent
            $table->integer('min_purchase')->default(0); // Minimal pembelian
            $table->integer('quota')->default(100); // Kuota penggunaan voucher
            $table->integer('used_count')->default(0); // Jumlah yang sudah terpakai
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
