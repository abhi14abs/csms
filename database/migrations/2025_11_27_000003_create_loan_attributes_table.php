<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_id')->primary();
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('tenure_months');
            $table->decimal('emi_amount', 15, 2);
            $table->date('disbursal_date');
            $table->boolean('is_topup')->default(false);
            $table->unsignedBigInteger('parent_loan_id')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('parent_loan_id')->references('loan_id')->on('loan_attributes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_attributes');
    }
};
