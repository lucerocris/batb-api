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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            $table->string('name', 255);
            $table->string('sku', 255)->unique();
            $table->decimal('price_adjustment', 8, 2)->default(0.00);
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->string('color')->nullable();

            // Clothing-specific variant fields
            $table->enum('type', [
                'size',
                'color',
                'style',
                'material'
            ])->nullable(); // What type of variant this is
            $table->string('value')->nullable(); // The actual value (e.g., 'XL', 'Blue', etc.)

            $table->json('attributes')->nullable(); // Made nullable for flexibility
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['product_id', 'type']);
            $table->index(['product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
