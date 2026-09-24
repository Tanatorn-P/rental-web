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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id')->primary();
            $table->string('customer_id');
            $table->json('item');
            $table->date('event_date');
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->date('return_date');
            $table->time('return_time');
            $table->float('total_price');
            $table->float('deposit_prices');
            $table->float('damage_price')->default(0);
            $table->string('reject_reason')->nullable();
            $table->string('order_status')->default('pending');
            $table->timestamps();

            // Foreign key เชื่อมกับตาราง customers
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
