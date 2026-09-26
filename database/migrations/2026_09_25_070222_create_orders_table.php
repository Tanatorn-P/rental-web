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
    $table->id();
    $table->foreignId('customer_id')->constrained('customers');
    $table->json('item'); // [{"product_id":"C001","size":"M"}, {"product_id":"F002","size":"L"}]
    $table->enum('status', ['pending', 'approved', 'rejected', 'rented', 'returned', 'completed'])
        ->default('pending');
    $table->boolean('order_status')->nullable();
    $table->text('reject_reason')->nullable();
    $table->date('event_date');
    $table->date('pickup_date');
    $table->string('pickup_time')->nullable();
    $table->date('return_date');
    $table->string('return_time')->nullable();
    $table->decimal('total_price', 10, 2)->nullable();
    $table->decimal('security_price', 10, 2)->nullable();
    $table->decimal('damage_price', 10, 2)->nullable();
    $table->timestamps();
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
