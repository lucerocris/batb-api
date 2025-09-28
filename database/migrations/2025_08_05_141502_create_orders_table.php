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
            $table->uuid('id')->primary();

            //fk field
            $table->foreignUuid('user_id')->nullable()->constrained('users');

            //separate order_number to show
            $table->string('order_number', 20)->unique();
            $table->enum('status', [
                'for_verification',
                'payment_verified',
                'shipped',
                'delivered',
                'cancelled',
                'refunded',
                'expired'
            ])->default('for_verification');

            $table->enum('fulfillment_status', [
                'pending',
                'processing',
                'partial',
                'fulfilled',
                'cancelled'
            ])->default('pending');



            $table->enum('payment_status', [
                'pending',
                'awaiting_confirmation',
                'partial',
                'paid',
                'failed',
                'refunded',
            ])->default('pending');

            $table->enum('payment_method', [
                'gcash',
                'bank_transfer'
            ])->nullable();

            $table->string('email');
            //payment details
            $table->timestamp('payment_due_date')->nullable();
            $table->timestamp('payment_sent_date')->nullable();
            $table->timestamp('payment_verified_date')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('payment_instructions')->nullable();
            $table->string('payment_reference', 100)->nullable();

            //image
            $table->string('image_path')->nullable();

            //admin confirmation
            $table->foreignUuid('payment_verified_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');

            //pricing
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->decimal('tax_amount', 8, 2)->default(0.00);
            $table->decimal('shipping_amount', 8, 2)->default(0.00);
            $table->decimal('discount_amount', 8, 2)->default(0.00);
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->decimal('refunded_amount', 8, 2)->default(0.00);

            $table->string('currency', 3)->default('PHP');

            //general information


            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->tinyInteger('reminder_sent_count')->default(0);
            $table->timestamp('last_reminder_sent')->nullable();
            $table->timestamp('order_date');
            $table->decimal('tip', 7, 2)->nullable();


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
        Schema::dropIfExists('orders');
    }
};
