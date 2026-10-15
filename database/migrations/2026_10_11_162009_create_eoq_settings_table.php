<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eoq_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_ordering_cost', 10, 2)->nullable(); // pwede palitan, takes priority if set
            $table->decimal('ordering_cost_percentage', 5, 2)->default(10.00); // default:10 % of purchase cost
            $table->decimal('holding_cost_percentage', 5, 2)->default(20.00); // defaullt:20 % of purchase cost per year
            $table->decimal('holding_cost_per_cbm', 10, 2)->default(0); // same sa ordering costtakes priority if product has storage_volume_cbm set
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eoq_settings');
    }
};