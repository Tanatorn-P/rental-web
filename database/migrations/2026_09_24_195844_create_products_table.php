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
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id')->primary(); // String Primary Key
            $table->string('product_name');
            $table->string('category');
            $table->string('size');
            $table->text('description')->nullable();
            $table->float('rental_fee');
            $table->float('deposit');
            $table->integer('rental_duration_days');
            $table->boolean('status')->default(true); // true = available
            $table->json('image')->nullable();
            $table->json('review')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
