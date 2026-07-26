<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: Early Bird, Presale 1, Regular
            $table->integer('price');
            $table->integer('stock');
            $table->dateTime('start_date')->nullable(); // Kapan penjualan tiket ini dibuka
            $table->dateTime('end_date')->nullable(); // Kapan penjualan tiket ini ditutup
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_tiers');
    }
};
