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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            $table->foreignUuid('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('restrict');

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');

            // ENUM type in Postgres
            $table->enum('type', [
                'restock',
                'lost',
                'damaged',
                'correction',
                'other',
                'creation',
                'purchase'
            ]);


            $table->integer('quantity');
            $table->integer('initial_quantity')->nullable();

            $table->text('notes')->nullable();
            $table->string('reference', 255)->nullable();
            $table->json('meta_data')->nullable();

            $table->softDeletes();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
