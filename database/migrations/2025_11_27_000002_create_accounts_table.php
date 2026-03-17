<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->unsignedBigInteger('member_id');
            $table->enum('account_type', ['SHARE', 'SAVINGS', 'LOAN'])->default('SAVINGS');
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->date('opened_date')->nullable();
            $table->enum('status', ['Active', 'Closed', 'Pending'])->default('Active');
            $table->timestamps();

            $table->foreign('member_id')->references('member_id')->on('members')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
