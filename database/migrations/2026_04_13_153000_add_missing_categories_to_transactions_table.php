<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'OPENING BALANCE' and 'FIXED_DEPOSIT' to the category enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN category ENUM('SUBSCRIPTION', 'EMI', 'DISBURSAL', 'DIVIDEND', 'SETTLEMENT', 'REPAY', 'OPENING BALANCE', 'FIXED_DEPOSIT') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'OPENING BALANCE' and 'FIXED_DEPOSIT'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN category ENUM('SUBSCRIPTION', 'EMI', 'DISBURSAL', 'DIVIDEND', 'SETTLEMENT', 'REPAY') NULL");
    }
};
