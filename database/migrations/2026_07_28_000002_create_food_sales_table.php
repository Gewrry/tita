<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->date('sale_date');
            $table->decimal('quantity', 12, 2);
            $table->decimal('selling_price', 12, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_revenue', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('gross_profit', 12, 2);
            $table->string('channel')->default('manual');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'sale_date']);
            $table->index(['user_id', 'product_id', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_sales');
    }
};
