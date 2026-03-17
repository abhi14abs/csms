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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_no')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2); // annual %
            $table->integer('tenure_months');
            $table->decimal('emi_amount', 15, 2);
            $table->date('start_date');
            $table->decimal('penalty_rate', 5, 2)->nullable();
            $table->boolean('penalty_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
