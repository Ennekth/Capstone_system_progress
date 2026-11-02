<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'cart_discount_type')) {
                $table->string('cart_discount_type')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'cart_discount_value')) {
                $table->decimal('cart_discount_value', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('transactions', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_items', 'item_discount_type')) {
                $table->string('item_discount_type')->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'item_discount_value')) {
                $table->decimal('item_discount_value', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('transaction_items', 'item_discount_scope')) {
                $table->string('item_discount_scope')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['cart_discount_type', 'cart_discount_value', 'customer_name']);
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['item_discount_type', 'item_discount_value', 'item_discount_scope']);
        });
    }
};