<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * USERS
         */
        Schema::table('users', function (Blueprint $table) {
            if (! $this->indexExists('users', 'users_username_idx')) {
                $table->index('username', 'users_username_idx');
            }
            if (! $this->indexExists('users', 'users_created_at_idx')) {
                $table->index('created_at', 'users_created_at_idx');
            }
        });


        /**
         * CATEGORIES
         */
        Schema::table('categories', function (Blueprint $table) {
            if (! $this->indexExists('categories', 'categories_name_idx')) {
                $table->index('name', 'categories_name_idx');
            }
            if (! $this->indexExists('categories', 'categories_active_idx')) {
                $table->index('is_active', 'categories_active_idx');
            }
        });


        /**
         * PRODUCTS
         */
        Schema::table('products', function (Blueprint $table) {
            if (! $this->indexExists('products', 'products_category_idx')) {
                $table->index('category_id', 'products_category_idx');
            }
            if (! $this->indexExists('products', 'products_is_active_idx')) {
                $table->index('is_active', 'products_is_active_idx');
            }
            if (! $this->indexExists('products', 'products_stockstatus_idx')) {
                $table->index('stock_status', 'products_stockstatus_idx');
            }
            if (! $this->indexExists('products', 'products_created_at_idx')) {
                $table->index('created_at', 'products_created_at_idx');
            }
            if (! $this->indexExists('products', 'products_deleted_at_idx')) {
                $table->index('deleted_at', 'products_deleted_at_idx');
            }
            if (! $this->indexExists('products', 'products_slug_idx')) {
                $table->index('slug', 'products_slug_idx');
            }
            if (! $this->indexExists('products', 'products_sku_idx')) {
                $table->index('sku', 'products_sku_idx');
            }
        });

        // FULLTEXT INDEX (MySQL InnoDB 5.6+)
        // Only create if server supports it
        try {
            DB::statement('ALTER TABLE products ADD FULLTEXT products_fulltext (name, description)');
        } catch (\Throwable $e) {
            // Ignore if FULLTEXT already exists or unsupported
        }


        /**
         * ORDERS
         */
        Schema::table('orders', function (Blueprint $table) {
            if (! $this->indexExists('orders', 'orders_user_idx')) {
                $table->index('user_id', 'orders_user_idx');
            }
            if (! $this->indexExists('orders', 'orders_payment_status_idx')) {
                $table->index('payment_status', 'orders_payment_status_idx');
            }
            if (! $this->indexExists('orders', 'orders_payment_method_idx')) {
                $table->index('payment_method', 'orders_payment_method_idx');
            }
            if (! $this->indexExists('orders', 'orders_fulfillment_status_idx')) {
                $table->index('fulfillment_status', 'orders_fulfillment_status_idx');
            }
            if (! $this->indexExists('orders', 'orders_order_date_idx')) {
                $table->index('order_date', 'orders_order_date_idx');
            }
        });


        /**
         * ADDRESSES
         */
        Schema::table('addresses', function (Blueprint $table) {
            if (! $this->indexExists('addresses', 'addresses_user_idx')) {
                $table->index('user_id', 'addresses_user_idx');
            }
            if (! $this->indexExists('addresses', 'addresses_postal_idx')) {
                $table->index('postal_code', 'addresses_postal_idx');
            }
            if (! $this->indexExists('addresses', 'addresses_country_idx')) {
                $table->index('country_code', 'addresses_country_idx');
            }
        });


        /**
         * ORDER ITEMS
         */
        Schema::table('order_items', function (Blueprint $table) {
            if (! $this->indexExists('order_items', 'order_items_order_idx')) {
                $table->index('order_id', 'order_items_order_idx');
            }
            if (! $this->indexExists('order_items', 'order_items_product_idx')) {
                $table->index('product_id', 'order_items_product_idx');
            }
            if (! $this->indexExists('order_items', 'order_items_sku_idx')) {
                $table->index('product_sku', 'order_items_sku_idx');
            }
        });


        /**
         * SESSIONS
         */
        Schema::table('sessions', function (Blueprint $table) {
            if (! $this->indexExists('sessions', 'sessions_user_idx')) {
                $table->index('user_id', 'sessions_user_idx');
            }
            if (! $this->indexExists('sessions', 'sessions_last_activity_idx')) {
                $table->index('last_activity', 'sessions_last_activity_idx');
            }
        });
    }


    public function down(): void
    {
        $indexes = [
            // users
            ['users', 'users_username_idx'],
            ['users', 'users_created_at_idx'],

            // categories
            ['categories', 'categories_name_idx'],
            ['categories', 'categories_active_idx'],

            // products
            ['products', 'products_category_idx'],
            ['products', 'products_is_active_idx'],
            ['products', 'products_stockstatus_idx'],
            ['products', 'products_created_at_idx'],
            ['products', 'products_deleted_at_idx'],
            ['products', 'products_slug_idx'],
            ['products', 'products_sku_idx'],

            // orders
            ['orders', 'orders_user_idx'],
            ['orders', 'orders_payment_status_idx'],
            ['orders', 'orders_payment_method_idx'],
            ['orders', 'orders_fulfillment_status_idx'],
            ['orders', 'orders_order_date_idx'],

            // addresses
            ['addresses', 'addresses_user_idx'],
            ['addresses', 'addresses_postal_idx'],
            ['addresses', 'addresses_country_idx'],

            // order_items
            ['order_items', 'order_items_order_idx'],
            ['order_items', 'order_items_product_idx'],
            ['order_items', 'order_items_sku_idx'],

            // sessions
            ['sessions', 'sessions_user_idx'],
            ['sessions', 'sessions_last_activity_idx'],
        ];

        foreach ($indexes as [$table, $index]) {
            if ($this->indexExists($table, $index)) {
                Schema::table($table, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            }
        }

        // Drop FULLTEXT safely
        try {
            DB::statement('ALTER TABLE products DROP INDEX products_fulltext');
        } catch (\Throwable $e) {}
    }


    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select("
            SELECT 1
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND INDEX_NAME = ?
            LIMIT 1
        ", [$table, $index]);

        return !empty($result);
    }
};
