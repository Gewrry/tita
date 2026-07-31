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
        $tables = ['customers', 'invoices', 'payments', 'expenses', 'audit_trails'];
        $firstUser = \DB::table('users')->first();

        foreach ($tables as $table) {
            // Add column if it doesn't exist
            if (!Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                });
            }

            // Assign data to all records
            if ($firstUser) {
                \DB::table($table)->whereNull('user_id')->update(['user_id' => $firstUser->id]);
            }

            // Make it required and add index
            try {
                \DB::statement("ALTER TABLE `{$table}` MODIFY `user_id` BIGINT UNSIGNED NOT NULL");
            } catch (\Exception $e) {}

            // Add foreign key
            try {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                });
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['customers', 'invoices', 'payments', 'expenses', 'audit_trails'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign([ 'user_id' ]);
                $table->dropColumn('user_id');
            });
        }
    }
};
