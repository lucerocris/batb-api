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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->string('status')->nullable()->after('order_number');
            $table->json('payment_instructions')->nullable()->after('payment_method');
            $table->decimal('tip', 8, 2)->nullable()->default(0.00)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'status', 'payment_instructions', 'tip']);
        });
    }
};
