<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['global', 'category', 'product'])->default('global');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('sma_window_days')->default(30);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['scope', 'category_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_settings');
    }
};