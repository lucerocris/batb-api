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


            $table->enum('payment_status', [
                'pending',                  // first dawat sa order
                'awaiting_confirmation',    // payment proof uploaded awaiting admin
                'paid',                     // verified payment
                'failed',                   // rejected payment
                'refunded',
            ])->default('pending');

            $table->enum('fulfillment_status', [
                'pending',      // order created
                'processing',   // payment verifiec
                'fulfilled',    // bracelet completed
                'shipped',      // out for delivery
                'delivered',    // received
                'cancelled',    // cancelled
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
            $table->string('payment_reference', 100)->nullable();

            //idempotency-key
            $table->string('idempotency_key');

            //admin confirmation
            $table->foreignUuid('payment_verified_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');

            //pricing
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->decimal('tax_amount', 8, 2)->default(0.00); // not used for now
            $table->decimal('shipping_amount', 8, 2)->default(0.00)->nullable();
            $table->decimal('discount_amount', 8, 2)->default(0.00);
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->decimal('refunded_amount', 8, 2)->default(0.00);

            $table->string('currency', 3)->default('PHP');

            //general information


            $table->json('shipping_address');
            $table->json('billing_address')->nullable(); // not used for now
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->tinyInteger('reminder_sent_count')->default(0);
            $table->timestamp('last_reminder_sent')->nullable();
            $table->timestamp('order_date')->useCurrent();


            //soft delete
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        Schema::dropIfExists('orders');
    }
};
