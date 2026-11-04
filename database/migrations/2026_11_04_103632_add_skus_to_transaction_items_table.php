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
        Schema::table('transaction_items', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('transaction_items', 'item_sku')) {
                $table->string('item_sku')->nullable()->after('product_sku');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['product_sku', 'item_sku']);
        });
    }
};
