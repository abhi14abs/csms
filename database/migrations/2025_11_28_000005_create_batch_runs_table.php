<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('transaction_date');
            $table->string('type')->default('monthly_deductions');
            $table->string('status')->default('completed');
            $table->integer('tx_year');
            $table->integer('tx_month');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['type', 'tx_year', 'tx_month'], 'uniq_batch_type_year_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_runs');
    }
};
