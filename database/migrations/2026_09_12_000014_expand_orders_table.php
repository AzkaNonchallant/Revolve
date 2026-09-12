<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('shipping_rate_id')->constrained('vouchers')->onDelete('set null');
            $table->string('voucher_code', 50)->nullable()->after('voucher_id');
            $table->decimal('voucher_discount', 12, 2)->default(0)->after('discount');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('voucher_discount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'paid',
                'processed',
                'packing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'return_requested',
                'returned',
                'refund_requested',
                'refunded',
            ])->default('waiting_payment')->change();
        });

        Schema::table('order_tracking', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'paid',
                'processed',
                'packing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'return_requested',
                'returned',
                'refund_requested',
                'refunded',
            ])->default('waiting_payment')->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_tracking', function (Blueprint $table) {
            $table->enum('status', [
                'waiting_payment',
                'processed',
                'packing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('waiting_payment')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'waiting_payment',
                'processed',
                'packing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('waiting_payment')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn(['voucher_code', 'voucher_discount', 'commission_rate']);
        });
    }
};
