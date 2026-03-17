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
        // 1. Drop existing foreign keys that reference members
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
        });

        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->dropForeign(['guarantor_id']);
        });

        Schema::table('loan_application_sureties', function (Blueprint $table) {
            $table->dropForeign(['guarantor_id']);
            // If loan_application_sureties has a reference to loan_applications, it might need to be careful if we drop members
        });

        // 2. Drop the old members table
        Schema::dropIfExists('members');

        // 3. Create the new employees table based on the provided SQL
        Schema::create('employees', function (Blueprint $table) {
            $table->id('id'); // bigint unsigned NOT NULL AUTO_INCREMENT
            $table->string('empCode', 191)->unique();
            $table->text('name');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER']);
            $table->enum('category', ['General', 'OBC', 'SC', 'ST', 'EWS']);
            $table->text('education')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('email', 191)->unique()->nullable();
            $table->date('dateOfAppointment');
            $table->string('designationAtAppointment', 191);
            $table->string('presentPosting', 191);
            $table->string('personalFileNo', 191)->nullable();
            $table->string('officeLandline', 191)->nullable();
            $table->date('dateOfBirth');
            $table->date('dateOfRetirement');
            $table->string('homeTown', 191)->nullable();
            $table->text('residentialAddress')->nullable();
            $table->enum('status', ['EXISTING', 'RETIRED', 'TRANSFERRED'])->default('EXISTING');
            $table->string('current_posting', 191)->nullable();
            $table->date('last_transfer_date')->nullable();
            $table->unsignedBigInteger('current_transfer_id')->nullable();
            $table->date('last_promotion_date')->nullable();
            $table->unsignedBigInteger('current_promotion_id')->nullable();
            $table->boolean('promoted')->default(0);
            $table->string('profile_image', 191)->nullable();
            $table->boolean('office_in_charge')->default(0);
            $table->string('promotee_transferee', 191)->nullable();
            $table->string('pension_file_no', 191)->nullable();
            $table->boolean('nps')->default(0);
            $table->integer('increment_month')->nullable();
            $table->boolean('probation_period')->default(0);
            $table->string('status_of_post', 191)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->string('seniority_sequence_no', 191)->nullable();
            $table->string('sddlsection_incharge', 191)->nullable();
            $table->boolean('2021_2022')->default(0);
            $table->string('benevolent_member', 191)->nullable();
            $table->boolean('2022_2023')->default(0);
            $table->boolean('increment_individual_selc')->default(0);
            $table->string('office_landline_number', 191)->nullable();
            $table->boolean('increment_withheld')->default(0);
            $table->boolean('FR56J_2nd_batch')->default(0);
            $table->boolean('apar_hod')->default(0);
            $table->boolean('2023_2024')->default(0);
            $table->boolean('2024_2025')->default(0);
            $table->boolean('karmayogi_certificate_completed')->default(0);
            $table->timestamps();

            // Self-referencing indices if needed, currently leaving foreign keys open if they exist later
            $table->index('current_promotion_id', 'employees_current_promotion_id_foreign');
            $table->index('current_transfer_id', 'employees_current_transfer_id_foreign');
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('designation_id')->references('id')->on('designations')->nullOnDelete();
        });

        // 4. Update existing tables to use employee_id instead of member_id, guarantor_id
        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn('member_id', 'employee_id');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->renameColumn('member_id', 'employee_id');
        });
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->renameColumn('guarantor_id', 'employee_id');
        });
        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        Schema::table('loan_application_sureties', function (Blueprint $table) {
            $table->renameColumn('guarantor_id', 'employee_id');
        });
        Schema::table('loan_application_sureties', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For simplicity, we won't fully reconstruct members here, just drop employees
        // and detach foreign keys
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->renameColumn('employee_id', 'member_id');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->renameColumn('employee_id', 'member_id');
        });

        Schema::table('loan_sureties', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->renameColumn('employee_id', 'guarantor_id');
        });

        Schema::table('loan_application_sureties', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->renameColumn('employee_id', 'guarantor_id');
        });

        Schema::dropIfExists('employees');

        // Note: down() is destructive as it does not restore the exact 'members' schema.
    }
};
