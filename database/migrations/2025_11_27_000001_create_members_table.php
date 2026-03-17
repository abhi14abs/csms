<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id('member_id');
            $table->string('employee_number', 20)->unique();
            $table->string('full_name', 100);
            $table->date('date_of_birth');
            $table->date('date_of_joining');
            $table->enum('status', ['Active', 'Suspended', 'Retired'])->default('Active');
            $table->string('password_hash', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
};
