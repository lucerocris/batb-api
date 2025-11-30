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
            $table->uuid('id')->primary();

            //foreign key
            $table->unsignedBigInteger('category_id');

            //basic information
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('sku');
            $table->string('color')->nullable();

            //clothing store specific fields
            $table->string('brand')->nullable();
            $table->enum('condition', [
                'Brand New',
                'Excellent',
                'Very Good',
                'Good',
                'Fair'
            ])->nullable();
            $table->enum('source', [
                'Mall Pullout',
                'Thrift Store',
                'Consignment',
                'Direct Purchase'
            ])->nullable();

            //pricing
            $table->decimal('base_price', 7, 2);
            $table->decimal('sale_price', 7, 2)->nullable();
            $table->decimal('cost_price', 7, 2)->nullable();
            $table->decimal('original_price', 7, 2)->nullable();
            $table->enum('stock_status', [
                'available',
                'unavailable'
            ])->default('available');


            //type
            $table->string('type')->default('shirt');
            //images & meta
            $table->string('image_path')->nullable();
            $table->string('stock_quantity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);



            //dimensions
            // $table->decimal('weight', 5, 2)->nullable();
            // $table->decimal('height', 5, 2)->nullable();
            // $table->decimal('length', 5, 2)->nullable();
            // $table->decimal('width', 5, 2)->nullable();

            $table->json('tags')->nullable();

            //stats
            // $table->integer('view_count')->default(0);
            // $table->decimal('average_rating', 3, 2)->default(0);
            // $table->integer('review_count')->default(0);

            //soft delete
            $table->softDeletes();

            $table->timestamps();

            //foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');

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
