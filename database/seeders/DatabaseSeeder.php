<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Account;
use App\Models\LoanAttribute;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.test'
        ], [
            'name' => 'Admin User',
            'employee_number' => null,
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create default department and designation
        $dept = Department::firstOrCreate(['code' => 'D001'], ['name' => 'General Department']);
        $desig = Designation::firstOrCreate(['code' => 'DSG001'], ['name' => 'Clerk']);

        // Create 12 members and users for testing
        $members = [];
        for ($i = 1; $i <= 12; $i++) {
            $emp = sprintf('EMP%03d', $i);
            $m = Employee::firstOrCreate([
                'empCode' => $emp
            ], [
                'name' => 'Member ' . $i,
                'gender' => 'MALE',
                'category' => 'General',
                'dateOfBirth' => Carbon::now()->subYears(25 + $i)->toDateString(),
                'dateOfAppointment' => Carbon::now()->subYears(1)->toDateString(),
                'dateOfRetirement' => Carbon::now()->addYears(30)->toDateString(),
                'designationAtAppointment' => 'Clerk',
                'department_id' => $dept->id,
                'designation_id' => $desig->id,
                'presentPosting' => 'Head Office',
                'status' => 'EXISTING',
                'mobile' => '900000' . str_pad($i, 4, '0', STR_PAD_LEFT)
            ]);

            $members[] = $m;

            // create a user for each member
            User::firstOrCreate([
                'email' => strtolower($emp) . '@example.test'
            ], [
                'name' => $m->name,
                'employee_number' => $m->empCode,
                'password' => Hash::make('password')
            ]);
        }

        // For each member ensure share and savings accounts exist
        foreach ($members as $idx => $m) {
            $share = Account::firstOrCreate([
                'employee_id' => $m->id,
                'account_type' => 'SHARE'
            ], [
                'current_balance' => 2000 + ($idx * 500),
                'opened_date' => Carbon::now(),
                'status' => 'Active'
            ]);

            $savings = Account::firstOrCreate([
                'employee_id' => $m->id,
                'account_type' => 'SAVINGS'
            ], [
                'current_balance' => 1000 + ($idx * 200),
                'opened_date' => Carbon::now(),
                'status' => 'Active'
            ]);

            // Seed subscription transactions
            Transaction::firstOrCreate([
                'account_id' => $share->account_id,
                'category' => 'SUBSCRIPTION',
                'description' => 'Initial Share Balance'
            ], [
                'tx_date' => Carbon::now(),
                'amount' => $share->current_balance,
                'tx_type' => 'CREDIT'
            ]);

            Transaction::firstOrCreate([
                'account_id' => $savings->account_id,
                'category' => 'SUBSCRIPTION',
                'description' => 'Initial Savings Balance'
            ], [
                'tx_date' => Carbon::now(),
                'amount' => $savings->current_balance,
                'tx_type' => 'CREDIT'
            ]);
        }

        // Create loans with varying repaid percentages
        // members 1-4: no loan
        // members 5-8: active loan, 20% repaid
        // members 9-11: active loan, 50% repaid
        // member 12: closed/settled loan
        $createLoanFor = [
            '20pct' => array_slice($members, 4, 4),
            '50pct' => array_slice($members, 8, 3),
            'closed' => [$members[11]]
        ];

        foreach ($createLoanFor['20pct'] as $m) {
            $principal = 200000.00;
            $repaid = $principal * 0.20; // 20% repaid
            $outstanding = $principal - $repaid;

            $loan = Account::create([
                'employee_id' => $m->id,
                'account_type' => 'LOAN',
                'current_balance' => $outstanding,
                'opened_date' => Carbon::now()->subMonths(10),
                'status' => 'Active'
            ]);

            LoanAttribute::create([
                'loan_id' => $loan->account_id,
                'principal_amount' => $principal,
                'interest_rate' => 10.5,
                'tenure_months' => 60,
                'emi_amount' => round(($principal * (10.5 / 12 / 100) * pow(1 + (10.5 / 12 / 100), 60)) / (pow(1 + (10.5 / 12 / 100), 60) - 1), 2),
                'disbursal_date' => Carbon::now()->subMonths(10)
            ]);

            // disbursal transaction
            Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => Carbon::now()->subMonths(10),
                'amount' => $principal,
                'tx_type' => 'DEBIT',
                'category' => 'DISBURSAL',
                'description' => 'Loan disbursed'
            ]);

            // create a repayment transaction representing the repaid portion
            Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => Carbon::now()->subMonths(2),
                'amount' => $repaid,
                'tx_type' => 'CREDIT',
                'category' => 'REPAY',
                'description' => 'Partial repayment'
            ]);
        }

        foreach ($createLoanFor['50pct'] as $m) {
            $principal = 150000.00;
            $repaid = $principal * 0.50; // 50% repaid
            $outstanding = $principal - $repaid;

            $loan = Account::create([
                'employee_id' => $m->id,
                'account_type' => 'LOAN',
                'current_balance' => $outstanding,
                'opened_date' => Carbon::now()->subMonths(24),
                'status' => 'Active'
            ]);

            LoanAttribute::create([
                'loan_id' => $loan->account_id,
                'principal_amount' => $principal,
                'interest_rate' => 10.5,
                'tenure_months' => 60,
                'emi_amount' => round(($principal * (10.5 / 12 / 100) * pow(1 + (10.5 / 12 / 100), 60)) / (pow(1 + (10.5 / 12 / 100), 60) - 1), 2),
                'disbursal_date' => Carbon::now()->subMonths(24)
            ]);

            Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => Carbon::now()->subMonths(24),
                'amount' => $principal,
                'tx_type' => 'DEBIT',
                'category' => 'DISBURSAL',
                'description' => 'Loan disbursed'
            ]);

            Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => Carbon::now()->subMonths(3),
                'amount' => $repaid,
                'tx_type' => 'CREDIT',
                'category' => 'REPAY',
                'description' => 'Partial repayment'
            ]);
        }

        // closed loan for member 12
        foreach ($createLoanFor['closed'] as $m) {
            $principal = 100000.00;
            $loan = Account::create([
                'employee_id' => $m->id,
                'account_type' => 'LOAN',
                'current_balance' => 0.00,
                'opened_date' => Carbon::now()->subYears(3),
                'status' => 'Closed'
            ]);

            LoanAttribute::create([
                'loan_id' => $loan->account_id,
                'principal_amount' => $principal,
                'interest_rate' => 10.5,
                'tenure_months' => 36,
                'emi_amount' => round(($principal * (10.5 / 12 / 100) * pow(1 + (10.5 / 12 / 100), 36)) / (pow(1 + (10.5 / 12 / 100), 36) - 1), 2),
                'disbursal_date' => Carbon::now()->subYears(3)
            ]);

            Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => Carbon::now()->subYears(3),
                'amount' => $principal,
                'tx_type' => 'DEBIT',
                'category' => 'DISBURSAL',
                'description' => 'Loan disbursed'
            ]);
        }
    }
}
