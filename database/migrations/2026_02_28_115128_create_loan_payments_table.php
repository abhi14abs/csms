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
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('installment_no')->nullable();
            $table->date('payment_date');
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('interest_component', 15, 2)->default(0);
            $table->decimal('principal_component', 15, 2)->default(0);
            $table->decimal('penalty_component', 15, 2)->default(0);
            $table->boolean('extra_payment')->default(false);
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
