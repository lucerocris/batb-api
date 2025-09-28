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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            // ENUM for type
            $table->enum('type', ['shipping', 'billing', 'both'])->default('both');

            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('company', 255)->nullable();
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255)->nullable();
            $table->string('city', 255);
            $table->string('state_province', 255);
            $table->string('postal_code', 20);
            $table->string('country_code', 2);
            $table->string('phone', 20)->nullable();

            // Booleans instead of tinyint(1)
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_validated')->default(false);

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
