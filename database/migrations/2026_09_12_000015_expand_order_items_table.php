<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('product_id')->constrained('users')->onDelete('set null');
            $table->foreignId('variant_id')->nullable()->after('seller_id')->constrained('product_variants')->onDelete('set null');
            $table->decimal('discount', 12, 2)->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_id');
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn('discount');
        });
    }
};
