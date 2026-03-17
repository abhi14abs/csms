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
        Schema::table('loan_attributes', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('emi_amount');
            $table->decimal('penalty_rate', 5, 2)->nullable()->after('start_date');
            $table->boolean('penalty_enabled')->default(false)->after('penalty_rate');
        });

        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->foreign('loan_id')->references('account_id')->on('accounts')->onDelete('cascade');
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->foreign('loan_id')->references('account_id')->on('accounts')->onDelete('cascade');
        });

        Schema::dropIfExists('loans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not supporting rollback fully to avoid creating dummy 'loans' table manually and recovering data
    }
};
