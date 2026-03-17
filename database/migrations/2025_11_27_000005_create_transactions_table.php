<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('tx_id');
            $table->unsignedBigInteger('account_id');
            $table->dateTime('tx_date')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('tx_type', ['CREDIT', 'DEBIT']);
            $table->enum('category', ['SUBSCRIPTION', 'EMI', 'DISBURSAL', 'DIVIDEND', 'SETTLEMENT', 'REPAY'])->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->index(['account_id', 'tx_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
