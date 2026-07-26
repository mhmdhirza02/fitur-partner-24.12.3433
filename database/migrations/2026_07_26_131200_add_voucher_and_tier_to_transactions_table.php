<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('ticket_tier_id')->nullable()->after('event_id')->constrained('ticket_tiers')->nullOnDelete();
            $table->string('ticket_tier_name')->nullable()->after('ticket_tier_id');
            $table->string('voucher_code')->nullable()->after('total_price');
            $table->integer('discount_amount')->default(0)->after('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['ticket_tier_id']);
            $table->dropColumn(['ticket_tier_id', 'ticket_tier_name', 'voucher_code', 'discount_amount']);
        });
    }
};
