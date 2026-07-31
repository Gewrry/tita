<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('savings_transactions', function (Blueprint $table) {
            $table->foreignId('savings_goal_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down() {
        Schema::table('savings_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('savings_goal_id');
        });
    }
};
