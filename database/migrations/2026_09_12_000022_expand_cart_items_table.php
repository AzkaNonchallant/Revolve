<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumns('cart_items', ['variant_id', 'is_selected'])) {
                $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('set null');
                $table->boolean('is_selected')->default(true)->after('quantity');
            }
        });

        $indexes = collect(DB::select('SHOW INDEX FROM cart_items'))->pluck('Key_name');

        if (! $indexes->contains('cart_items_cart_id_index')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->index(['cart_id'], 'cart_items_cart_id_index');
            });
        }

        if ($indexes->contains('cart_items_cart_id_product_id_unique')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropUnique(['cart_id', 'product_id']);
            });
        }

        if (! $indexes->contains('cart_items_cart_product_variant_unique')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->unique(['cart_id', 'product_id', 'variant_id'], 'cart_items_cart_product_variant_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $indexes = collect(DB::select('SHOW INDEX FROM cart_items'))->pluck('Key_name');

            if ($indexes->contains('cart_items_cart_product_variant_unique')) {
                $table->dropUnique('cart_items_cart_product_variant_unique');
            }

            if (! $indexes->contains('cart_items_cart_id_product_id_unique')) {
                $table->unique(['cart_id', 'product_id']);
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumns('cart_items', ['variant_id', 'is_selected'])) {
                $table->dropConstrainedForeignId('variant_id');
                $table->dropColumn('is_selected');
            }
        });
    }
};
