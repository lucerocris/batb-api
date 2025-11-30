<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update payment_status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending'");
        
        // Update fulfillment_status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN fulfillment_status ENUM('pending', 'fulfilled', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
        
        // Update existing records: convert 'awaiting_confirmation' to 'pending' if any exist
        DB::table('orders')
            ->where('payment_status', 'awaiting_confirmation')
            ->update(['payment_status' => 'pending']);
        
        // Update existing records: convert 'processing' to 'pending' if any exist
        DB::table('orders')
            ->where('fulfillment_status', 'processing')
            ->update(['fulfillment_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment_status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'awaiting_confirmation', 'paid', 'failed', 'refunded') DEFAULT 'pending'");
        
        // Revert fulfillment_status enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN fulfillment_status ENUM('pending', 'processing', 'fulfilled', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
