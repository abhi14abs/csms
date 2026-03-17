<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('region')->nullable()->after('date_of_joining');
            $table->string('phone')->nullable()->after('region');
            $table->string('designation')->nullable()->after('phone');
            $table->string('email')->nullable()->after('designation');
            $table->string('section')->nullable()->after('email');
            $table->text('residential_address')->nullable()->after('section');
            $table->text('permanent_address')->nullable()->after('residential_address');
            $table->string('nominee_name')->nullable()->after('permanent_address');
            $table->string('nominee_relationship')->nullable()->after('nominee_name');
        });
    }

    public function down()
    {
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn([
                    'region',
                    'phone',
                    'designation',
                    'email',
                    'section',
                    'residential_address',
                    'permanent_address',
                    'nominee_name',
                    'nominee_relationship'
                ]);
            });
        }
    }
};
