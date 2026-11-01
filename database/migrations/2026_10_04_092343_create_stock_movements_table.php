<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id')->nullable(); // groups multiple movements into one restocking session
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->enum('type', [
                'restock_store',
                'restock_warehouse',
                'assembly',
                'transfer_to_store',
                'transfer_to_warehouse',
                'adjustment',
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->integer('quantity_before')->default(0);
            $table->integer('quantity_after')->default(0);
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index('status');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};