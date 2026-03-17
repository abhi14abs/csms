<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Account;
use App\Models\LoanAttribute;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportOldLoans extends Command
{
    protected $signature = 'app:import-old-loans';

    protected $description = 'Import old loan data from transcribed image into the system';

    public function handle()
    {

        $data = [
            ['empCode' => '130270', 'date' => '07-12-2024', 'amount' => 200000, 'tenure' => 24],
            ['empCode' => '126318', 'date' => '01-09-2021', 'amount' => 500000, 'tenure' => 60],
            ['empCode' => '126289', 'date' => '27-09-2023', 'amount' => 500000, 'tenure' => 36],
            ['empCode' => '127111', 'date' => '10-09-2025', 'amount' => 150000, 'tenure' => 21],
            ['empCode' => '126328', 'date' => '08-08-2024', 'amount' => 150000, 'tenure' => 120],
            ['empCode' => '132009', 'date' => '26-10-2018', 'amount' => 450000, 'tenure' => 120],
            ['empCode' => '130206', 'date' => '10-11-2021', 'amount' => 250000, 'tenure' => 71],
            ['empCode' => '125048', 'date' => '11-05-2023', 'amount' => 350000, 'tenure' => 60],
            ['empCode' => '128090', 'date' => '02-03-2019', 'amount' => 600000, 'tenure' => 120],
            ['empCode' => '128061', 'date' => '01-03-2025', 'amount' => 500000, 'tenure' => 104],
            ['empCode' => '121473', 'date' => '01-02-2024', 'amount' => 500000, 'tenure' => 48],
            ['empCode' => '129107', 'date' => '29-12-2025', 'amount' => 300000, 'tenure' => 24],
            ['empCode' => '128073', 'date' => '07-12-2024', 'amount' => 400000, 'tenure' => 68],
            ['empCode' => '121461', 'date' => '10-01-2024', 'amount' => 400000, 'tenure' => 84],
            ['empCode' => '136004', 'date' => '07-04-2023', 'amount' => 600000, 'tenure' => 60],
            ['empCode' => '131061', 'date' => '10-01-2024', 'amount' => 500000, 'tenure' => 65],
            ['empCode' => '132008', 'date' => '07-12-2021', 'amount' => 646000, 'tenure' => 90],
            ['empCode' => '127132', 'date' => '01-03-2025', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '130267', 'date' => '29-04-2022', 'amount' => 800000, 'tenure' => 84],
            ['empCode' => '121485', 'date' => '14-07-2022', 'amount' => 800000, 'tenure' => 84],
            ['empCode' => '131074', 'date' => '04-09-2025', 'amount' => 500000, 'tenure' => 60],
            ['empCode' => '121474', 'date' => '19-04-2023', 'amount' => 800000, 'tenure' => 72],
            ['empCode' => '127136', 'date' => '01-08-2022', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '121484', 'date' => '04-08-2022', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '128060', 'date' => '14-11-2022', 'amount' => 800000, 'tenure' => 113],
            ['empCode' => '128072', 'date' => '17-08-2022', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '122016', 'date' => '26-04-2024', 'amount' => 800000, 'tenure' => 120], // Assuming 26-04-2024 due to other slash format
            ['empCode' => '128051', 'date' => '06-09-2023', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '130258', 'date' => '19-09-2025', 'amount' => 725000, 'tenure' => 103],
            ['empCode' => '122015', 'date' => '07-03-2024', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '128064', 'date' => '07-12-2024', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '125052', 'date' => '05-03-2024', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '129105', 'date' => '12-11-2024', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '126300', 'date' => '11-02-2025', 'amount' => 800000, 'tenure' => 83],
            ['empCode' => '130271', 'date' => '16-04-2025', 'amount' => 800000, 'tenure' => 84],
            ['empCode' => '128047', 'date' => '14-08-2025', 'amount' => 800000, 'tenure' => 60],
            ['empCode' => '121472', 'date' => '17-10-2025', 'amount' => 800000, 'tenure' => 48],
            ['empCode' => '122014', 'date' => '28-01-2025', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '121449', 'date' => '20-05-2025', 'amount' => 800000, 'tenure' => 120],
            ['empCode' => '121457', 'date' => '08-01-2026', 'amount' => 800000, 'tenure' => 72],
            ['empCode' => '130219', 'date' => '29-01-2026', 'amount' => 800000, 'tenure' => 60],
        ];

        $annualInterestRate = 10.5;
        $monthlyRate = $annualInterestRate / 12 / 100;
        $cutoffDate = Carbon::create(2026, 2, 28);

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

                $loanDate = Carbon::createFromFormat(
                    'd-m-Y',
                    str_replace('/', '-', $row['date'])
                );

                $principal = (int) $row['amount'];
                $tenure = (int) $row['tenure'];

                $emi = $this->calculateEmi($principal, $monthlyRate, $tenure);

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
                    'start_date' => $loanDate->copy()->addMonth()->startOfMonth(),
                    'penalty_rate' => 0,
                    'penalty_enabled' => false,
                    'is_topup' => false
                ]);

                $balance = $principal;
                $dateIterator = $loanDate->copy()->addMonth();

                for ($i = 1; $i <= $tenure; $i++) {

                    $dueDate = $dateIterator->copy()->startOfMonth()->addDays(4);

                    $interest = round($balance * $monthlyRate);

                    $principalComponent = $emi - $interest;

                    if ($i === $tenure) {
                        $principalComponent = $balance;
                        $emi = $principalComponent + $interest;
                    }

                    $balanceAfter = max(0, $balance - $principalComponent);

                    $status = 'pending';

                    if ($dueDate->lte($cutoffDate)) {

                        $status = 'paid';

                        LoanPayment::create([
                            'loan_id' => $account->account_id,
                            'installment_no' => $i,
                            'payment_date' => $dueDate,
                            'amount_paid' => $emi,
                            'interest_component' => $interest,
                            'principal_component' => $principalComponent,
                            'penalty_component' => 0,
                            'extra_payment' => false,
                            'balance_after' => $balanceAfter
                        ]);
                    }

                    LoanInstallment::create([
                        'loan_id' => $account->account_id,
                        'installment_no' => $i,
                        'due_date' => $dueDate,
                        'principal_due' => $principalComponent,
                        'interest_due' => $interest,
                        'total_due' => $emi,
                        'balance_after' => $balanceAfter,
                        'status' => $status
                    ]);

                    $balance = $balanceAfter;

                    $dateIterator->addMonth();
                }

                $totalPrincipalPaid = LoanPayment::where(
                    'loan_id',
                    $account->account_id
                )->sum('principal_component');

                $actualBalance = max(0, $principal - $totalPrincipalPaid);

                $account->update([
                    'current_balance' => $actualBalance,
                    'status' => $actualBalance <= 0 ? 'CLOSED' : 'ACTIVE'
                ]);

                $this->info(
                    "Loan Imported | Employee: {$row['empCode']} | Amount: {$principal} | Account ID: {$account->account_id}"
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
     * Calculate EMI
     */
    private function calculateEmi($principal, $rate, $tenure)
    {
        if ($rate == 0) {
            return ceil($principal / $tenure);
        }

        $emi = $principal * $rate *
            pow((1 + $rate), $tenure) /
            (pow((1 + $rate), $tenure) - 1);

        return ceil($emi);
    }
}
