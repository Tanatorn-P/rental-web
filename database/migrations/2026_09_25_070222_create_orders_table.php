<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id')->primary();
            $table->string('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->json('item');
            $table->date('event_date');
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->date('return_date');
            $table->time('return_time');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('deposit_prices', 10, 2)->nullable();
            $table->decimal('damage_price', 10, 2)->default(0);
            $table->text('reject_reason')->nullable();
            $table->string('order_status');
            $table->string('slip_image')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
