<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->onDelete('set null');
            $table->decimal('discount_price', 12, 2)->nullable()->after('price');
            $table->string('material', 150)->nullable()->after('nutrition');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('status');
            $table->text('approval_note')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approval_note');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_id');
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn(['discount_price', 'material', 'approval_status', 'approval_note', 'approved_at']);
        });
    }
};
