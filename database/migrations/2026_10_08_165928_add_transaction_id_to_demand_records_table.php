<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demand_records', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->after('product_id')
                ->constrained('transactions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('demand_records', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropColumn('transaction_id');
        });
    }
};

