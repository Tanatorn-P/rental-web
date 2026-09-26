<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->json('image')->nullable();
        $table->string('category')->nullable();
        $table->text('description')->nullable();
        $table->decimal('rental_fee', 8, 2)->nullable();
        $table->decimal('deposit', 8, 2)->nullable();
        $table->integer('rental_duration_days')->nullable();
        $table->enum('status', ['available', 'preparing', 'rented', 'inspection', 'not_ready'])
            ->default('available');
        $table->string('product_name')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
};