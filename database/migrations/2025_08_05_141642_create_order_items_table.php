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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Foreign key to orders
            $table->foreignUuid('order_id')
                  ->constrained('orders')
                  ->onDelete('restrict');

            // Foreign key to products
            $table->foreignUuid('product_id')
                  ->constrained('products')
                  ->onDelete('restrict');

            $table->string('product_name', 255)->nullable();
            $table->string('product_sku', 255)->nullable();
            $table->json('product_attributes')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 6, 2);
            $table->decimal('line_total', 9, 2);
            $table->decimal('discount_amount', 8, 2)->default(0.00);
            $table->json('customization')->nullable();
            $table->text('customization_notes')->nullable();

            //soft delete
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
