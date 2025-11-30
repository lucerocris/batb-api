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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User relationship (nullable for guest carts)
            // Use uuid to match your users table
            $table->uuid('user_id')->nullable();

            // Session identifier for guest users
            $table->string('session_id')->nullable();

            // Product relationship
            $table->uuid('product_id');

            // Cart item details
            $table->integer('quantity')->default(1);
            $table->decimal('price', 7, 2); // Price at time of adding to cart

            // Optional: Size/variant info if applicable
            $table->string('size')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_id');

            // Unique constraint to prevent duplicate cart items
            $table->unique(['user_id', 'product_id', 'size']);
            $table->unique(['session_id', 'product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
