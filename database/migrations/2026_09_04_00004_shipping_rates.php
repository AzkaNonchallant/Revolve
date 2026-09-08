<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke kurir
            $table->foreignId('courier_id')
                  ->constrained('couriers')
                  ->onDelete('cascade');

            // Cakupan wilayah dan harga
            $table->string('province', 100);
            $table->string('city', 100)->nullable();
            $table->decimal('rate_per_kg', 12, 2); // Tarif per kg
            $table->integer('etd_days')->nullable(); // Estimasi hari (misal: 2-3 hari)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};