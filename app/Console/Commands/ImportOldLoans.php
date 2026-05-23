<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Account;
use App\Models\LoanAttribute;
use App\Models\LoanInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportOldLoans extends Command
{
    protected $signature = 'app:import-old-loans';

    protected $description = 'Import old loan data and create installment records without payment records';

    public function handle()
    {
        $data = [
            ['empCode' => '121457', 'amount' => 359956.00, 'emi_apr_2025' => 11250.00],
            ['empCode' => '132009', 'amount' => 208908.00, 'emi_apr_2025' => 6330.00],
            ['empCode' => '128540', 'amount' => 115909.00, 'emi_apr_2025' => 10630.00],
            ['empCode' => '126301', 'amount' => 224914.00, 'emi_apr_2025' => 14690.00],
            ['empCode' => '128090', 'amount' => 304862.00, 'emi_apr_2025' => 8440.00],
            ['empCode' => '130219', 'amount' => 406165.00, 'emi_apr_2025' => 11250.00],
            ['empCode' => '126318', 'amount' => 156533.00, 'emi_apr_2025' => 11000.00],
            ['empCode' => '130206', 'amount' => 182718.00, 'emi_apr_2025' => 3520.00],
            ['empCode' => '132008', 'amount' => 419316.00, 'emi_apr_2025' => 10410.00],
            ['empCode' => '130267', 'amount' => 535570.00, 'emi_apr_2025' => 13490.00],
            ['empCode' => '121485', 'amount' => 556442.00, 'emi_apr_2025' => 13490.00],
            ['empCode' => '121452', 'amount' => 320731.00, 'emi_apr_2025' => 13490.00],
            ['empCode' => '127136', 'amount' => 638218.00, 'emi_apr_2025' => 11250.00],
            ['empCode' => '121484', 'amount' => 654578.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '128072', 'amount' => 660368.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '128047', 'amount' => 470898.00, 'emi_apr_2025' => 15660.00],
            ['empCode' => '128060', 'amount' => 662552.00, 'emi_apr_2025' => 11180.00],
            ['empCode' => '132010', 'amount' => 250830.00, 'emi_apr_2025' => 4050.00],
            ['empCode' => '128062', 'amount' => 51420.00, 'emi_apr_2025' => 7610.00],
            ['empCode' => '121474', 'amount' => 596394.00, 'emi_apr_2025' => 15030.00],
            ['empCode' => '125048', 'amount' => 236724.00, 'emi_apr_2025' => 7530.00],
            ['empCode' => '136004', 'amount' => 424484.00, 'emi_apr_2025' => 12900.00],
            ['empCode' => '128051', 'amount' => 717002.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '126289', 'amount' => 269392.00, 'emi_apr_2025' => 16260.00],
            ['empCode' => '121461', 'amount' => 348148.00, 'emi_apr_2025' => 6750.00],
            ['empCode' => '129086', 'amount' => 26551.00, 'emi_apr_2025' => 9050.00],
            ['empCode' => '131061', 'amount' => 408341.00, 'emi_apr_2025' => 10120.00],
            ['empCode' => '130258', 'amount' => 464791.00, 'emi_apr_2025' => 6750.00],
            ['empCode' => '121473', 'amount' => 374952.00, 'emi_apr_2025' => 12810.00],
            ['empCode' => '122015', 'amount' => 747921.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '122016', 'amount' => 738093.00, 'emi_apr_2025' => 13000.00],
            ['empCode' => '125052', 'amount' => 756322.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '130228', 'amount' => 35134.00, 'emi_apr_2025' => 0],
            ['empCode' => '128073', 'amount' => 359637.00, 'emi_apr_2025' => 7830.00],
            ['empCode' => '130270', 'amount' => 129810.00, 'emi_apr_2025' => 9280.00],
            ['empCode' => '126328', 'amount' => 144080.00, 'emi_apr_2025' => 2030.00],
            ['empCode' => '128064', 'amount' => 760270.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '129105', 'amount' => 776419.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '122014', 'amount' => 792367.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '126300', 'amount' => 767797.00, 'emi_apr_2025' => 13600.00],
            ['empCode' => '127132', 'amount' => 796200.00, 'emi_apr_2025' => 10800.00],
            ['empCode' => '128061', 'amount' => 497025.00, 'emi_apr_2025' => 7350.00],
        ];

        $annualInterestRate = 10.5;
        $monthlyRate = $annualInterestRate / 12 / 100;

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                $employee = Employee::withoutGlobalScope('society_member')
                    ->where('empCode', $row['empCode'])
                    ->first();

                if (!$employee) {
                    $this->warn("Employee {$row['empCode']} not found. Skipping.");
                    continue;
                }

                if ($employee->is_society_member !== 'YES') {
                    $employee->update(['is_society_member' => 'YES']);
                }

                $principal = floatval($row['amount']);
                $emi = floatval($row['emi_apr_2025']);

                if ($emi <= 0) {
                    $this->warn("Employee {$row['empCode']} has zero EMI. Skipping.");
                    continue;
                }

                // Calculate tenure based on EMI
                $tenure = $this->calculateTenure($principal, $monthlyRate, $emi);

                if ($tenure <= 0) {
                    $this->warn("Invalid tenure calculated for {$row['empCode']}. Skipping.");
                    continue;
                }

                $loanDate = Carbon::create(2025, 3, 31);

                $account = Account::create([
                    'employee_id' => $employee->id,
                    'account_type' => 'LOAN',
                    'current_balance' => $principal,
                    'opened_date' => $loanDate,
                    'status' => 'ACTIVE'
                ]);

                LoanAttribute::create([
                    'loan_id' => $account->account_id,
                    'principal_amount' => $principal,
                    'interest_rate' => $annualInterestRate,
                    'tenure_months' => $tenure,
                    'emi_amount' => $emi,
                    'disbursal_date' => $loanDate,
                    'start_date' => $loanDate->copy()->startOfMonth()->addMonthNoOverflow(),
                    'penalty_rate' => 0,
                    'penalty_enabled' => false,
                    'is_topup' => false
                ]);

                // Create installments
                $balance = $principal;
                $dateIterator = $loanDate->copy()->startOfMonth()->addMonthNoOverflow();

                for ($i = 1; $i <= $tenure; $i++) {
                    $dueDate = $dateIterator->copy()->addDays(4);

                    $interest = round($balance * $monthlyRate);
                    $principalComponent = $emi - $interest;

                    // For last installment, if remaining balance is less than principal component
                    if ($i == $tenure) {
                        $principalComponent = $balance;
                        $totalDue = $principalComponent + $interest;
                    } else {
                        // If principal component is more than remaining balance (shouldn't happen normally)
                        if ($principalComponent > $balance) {
                            $principalComponent = $balance;
                            $totalDue = $principalComponent + $interest;
                        } else {
                            $totalDue = $emi;
                        }
                    }

                    $balanceAfter = max(0, $balance - $principalComponent);

                    LoanInstallment::create([
                        'loan_id' => $account->account_id,
                        'installment_no' => $i,
                        'due_date' => $dueDate,
                        'principal_due' => $principalComponent,
                        'interest_due' => $interest,
                        'total_due' => $totalDue,
                        'balance_after' => $balanceAfter,
                        'status' => 'pending'
                    ]);

                    $balance = $balanceAfter;
                    $dateIterator->addMonthNoOverflow();
                }

                $this->info(
                    "Imported | Emp: {$row['empCode']} | Amount: {$principal} | Tenure: {$tenure} months | EMI: {$emi}"
                );
            }

            DB::commit();
            $this->info("All loans imported successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
        }
    }

    /**
     * Calculate tenure based on principal, interest rate, and EMI
     * Formula: n = log(EMI / (EMI - P*r)) / log(1 + r)
     */
    private function calculateTenure($principal, $monthlyRate, $emi)
    {
        $monthlyInterest = $principal * $monthlyRate;

        if ($emi <= $monthlyInterest) {
            return 0;
        }

        $tenure = log($emi / ($emi - $monthlyInterest)) / log(1 + $monthlyRate);

        return ceil($tenure);
    }
}
