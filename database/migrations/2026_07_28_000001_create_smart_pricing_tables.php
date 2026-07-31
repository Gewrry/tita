<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('packaging_cost', 12, 2)->default(0);
            $table->decimal('labor_allowance', 12, 2)->default(0);
            $table->decimal('utility_allowance', 12, 2)->default(0);
            $table->decimal('transportation_cost', 12, 2)->default(0);
            $table->decimal('delivery_fees', 12, 2)->default(0);
            $table->decimal('waste_percentage', 5, 2)->default(0);
            $table->decimal('minimum_margin', 5, 2)->default(25);
            $table->decimal('desired_margin', 5, 2)->default(35);
            $table->decimal('complete_cost_per_serving', 12, 2)->default(0);
            $table->decimal('previous_cost_per_serving', 12, 2)->nullable();
            $table->boolean('smart_rounding')->default(true);
            $table->json('last_recommendation')->nullable();
            $table->timestamp('last_recommended_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->index(['product_id', 'last_recommended_at']);
        });

        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('quantity_per_serving', 12, 4)->default(0);
            $table->string('unit')->default('unit');
            $table->decimal('cost_per_unit', 12, 4)->default(0);
            $table->boolean('is_estimated')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'product_id']);
        });

        Schema::create('pricing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('previous_price', 12, 2)->default(0);
            $table->decimal('recommended_price', 12, 2)->nullable();
            $table->decimal('approved_price', 12, 2)->default(0);
            $table->decimal('previous_cost_per_serving', 12, 2)->nullable();
            $table->decimal('updated_cost_per_serving', 12, 2)->default(0);
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->default('approved_recommendation');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'product_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_histories');
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('product_pricing_profiles');
    }
};
