<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("
            ALTER TABLE accounts
            MODIFY account_type ENUM('SHARE', 'SAVINGS', 'LOAN', 'FD') NOT NULL DEFAULT 'SAVINGS'
        ");

        DB::statement("
            ALTER TABLE accounts
            MODIFY status ENUM('Active', 'Closed', 'Pending', 'Rejected') NOT NULL DEFAULT 'Active'
        ");

        DB::statement("
            ALTER TABLE loan_applications
            MODIFY status ENUM('submitted', 'linked', 'approved', 'rejected') NOT NULL DEFAULT 'submitted'
        ");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("
            ALTER TABLE loan_applications
            MODIFY status ENUM('submitted', 'linked', 'rejected') NOT NULL DEFAULT 'submitted'
        ");

        DB::statement("
            ALTER TABLE accounts
            MODIFY status ENUM('Active', 'Closed', 'Pending') NOT NULL DEFAULT 'Active'
        ");

        DB::statement("
            ALTER TABLE accounts
            MODIFY account_type ENUM('SHARE', 'SAVINGS', 'LOAN') NOT NULL DEFAULT 'SAVINGS'
        ");
    }
};
