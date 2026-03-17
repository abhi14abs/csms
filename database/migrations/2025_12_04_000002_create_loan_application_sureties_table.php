<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_application_sureties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->unsignedBigInteger('guarantor_id');
            $table->string('employee_number')->nullable();
            $table->integer('service_left')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();

            $table->foreign('loan_application_id')->references('id')->on('loan_applications')->cascadeOnDelete();
            $table->foreign('guarantor_id')->references('member_id')->on('members')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_application_sureties');
    }
};
