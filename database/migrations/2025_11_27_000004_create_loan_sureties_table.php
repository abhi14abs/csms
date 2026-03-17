<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_sureties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('guarantor_id');
            $table->decimal('guarantee_amount', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['loan_id', 'guarantor_id']);
            $table->foreign('loan_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('guarantor_id')->references('member_id')->on('members')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_sureties');
    }
};
