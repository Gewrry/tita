<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('bulk_unit'); // e.g., 'case'
            $table->integer('bulk_quantity')->default(1);
            $table->string('retail_unit'); // e.g., 'piece'
            $table->integer('retail_quantity'); // e.g., 24 (1 case = 24 pieces)
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
