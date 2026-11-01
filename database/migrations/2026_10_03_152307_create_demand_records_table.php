<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demand_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->date('record_date');
            $table->integer('quantity');
            $table->string('source')->default('pos'); // pos, manual, imported
            $table->timestamps();

            $table->index(['product_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_records');
    }
};