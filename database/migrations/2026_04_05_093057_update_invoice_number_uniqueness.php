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
        Schema::table('invoices', function (Blueprint $table) {
            // Drop current global unique constraint
            $table->dropUnique(['invoice_number']);
            
            // Add user-scoped unique constraint
            $table->unique(['user_id', 'invoice_number'], 'invoices_user_invoice_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_user_invoice_unique');
            $table->unique('invoice_number');
        });
    }
};
