<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->string('applicant_name')->nullable();
            $table->string('aadhaar')->nullable();
            $table->string('mobile')->nullable();
            $table->string('designation')->nullable();
            $table->string('ro_hq')->nullable();
            $table->string('dept')->nullable();
            $table->integer('service_remaining')->nullable();
            $table->string('email')->nullable();

            $table->text('reason')->nullable();
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->integer('tenure_months')->nullable();
            $table->decimal('emi_desired', 15, 2)->nullable();
            $table->decimal('gross_salary', 15, 2)->nullable();
            $table->decimal('current_emi', 15, 2)->nullable();
            $table->decimal('net_salary', 15, 2)->nullable();
            $table->decimal('previous_loan', 15, 2)->nullable();
            $table->decimal('loan_outstanding', 15, 2)->nullable();

            $table->unsignedBigInteger('app_account_id')->nullable(); // link to accounts.account_id when created
            $table->enum('status', ['submitted', 'linked', 'rejected'])->default('submitted');

            $table->timestamps();

            $table->foreign('member_id')->references('member_id')->on('members')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_applications');
    }
};
