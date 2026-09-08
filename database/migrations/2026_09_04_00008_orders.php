<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            $table->foreignId('address_id')
                  ->constrained('addresses')
                  ->onDelete('restrict');

            $table->foreignId('courier_id')
                  ->nullable()
                  ->constrained('couriers')
                  ->onDelete('set null');

            $table->foreignId('shipping_rate_id')
                  ->nullable()
                  ->constrained('shipping_rates')
                  ->onDelete('set null');

            $table->string('order_number', 50)->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('discount', 12, 2);
            $table->decimal('total', 12, 2);

            $table->string('payment_method', 100)->nullable();
            $table->dateTime('delivery_schedule')->nullable();

            $table->enum('status', [
                'waiting_payment', 
                'processed', 
                'packing', 
                'shipped', 
                'delivered', 
                'cancelled'
            ])->default('waiting_payment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};