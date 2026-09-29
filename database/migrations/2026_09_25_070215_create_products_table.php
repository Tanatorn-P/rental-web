<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id')->primary();
            $table->string('product_name');
            $table->string('category');
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->decimal('rental_fee', 8, 2);
            $table->decimal('deposit', 8, 2);
            $table->integer('rental_duration_days');
            $table->enum('status', ['available', 'preparing', 'rented', 'inspection', 'not_ready'])
                ->default('available');
            $table->json('image')->nullable();
            $table->json('review')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
