<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_id', 100)->nullable()->after('order_id');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'refunded_at']);
        });
    }
};
