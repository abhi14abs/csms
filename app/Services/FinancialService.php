<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LoanAttribute;
use App\Models\LoanSurety;
use App\Models\Employee;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function calculateEMI(float $principal, float $monthlyRate, int $n)
    {
        if ($monthlyRate <= 0) {
            return $principal / $n;
        }

        $r = $monthlyRate;
        $num = $principal * $r * pow(1 + $r, $n);
        $den = pow(1 + $r, $n) - 1;
        return round($num / $den, 2);
    }

    public function checkCollateral(Employee $member, float $requestedAmount): bool
    {
        $sum = Account::where('employee_id', $member->id)
            ->whereIn('account_type', ['SHARE', 'SAVINGS'])
            ->sum('current_balance');

        return $sum >= (0.10 * $requestedAmount);
    }

    public function processMonthlySubscription(Employee $member, float $subscriptionAmount, float $emiAmount, \DateTime $txDate)
    {
        DB::beginTransaction();
        try {
            $share = $member->accounts()->where('account_type', 'SHARE')->lockForUpdate()->first();
            $savings = $member->accounts()->where('account_type', 'SAVINGS')->lockForUpdate()->first();

            if (!$share) {
                $share = Account::create(["employee_id" => $member->id, 'account_type' => 'SHARE', 'opened_date' => Carbon::now(), 'status' => 'Active']);
            }
            if (!$savings) {
                $savings = Account::create(["employee_id" => $member->id, 'account_type' => 'SAVINGS', 'opened_date' => Carbon::now(), 'status' => 'Active']);
            }

            $shareBalance = $share->current_balance;

            if ($shareBalance >= 10000) {
                // all to savings
                Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $txDate, 'amount' => $subscriptionAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Monthly Sub (Diversion)']);
                $savings->increment('current_balance', $subscriptionAmount);
            } else {
                $gap = 10000 - $shareBalance;
                if ($subscriptionAmount <= $gap) {
                    Transaction::create(['account_id' => $share->account_id, 'tx_date' => $txDate, 'amount' => $subscriptionAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Monthly Sub']);
                    $share->increment('current_balance', $subscriptionAmount);
                } else {
                    // split
                    Transaction::create(['account_id' => $share->account_id, 'tx_date' => $txDate, 'amount' => $gap, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Monthly Sub (Split-Share)']);
                    $share->increment('current_balance', $gap);

                    $rest = $subscriptionAmount - $gap;
                    Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $txDate, 'amount' => $rest, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Monthly Sub (Split-Sav)']);
                    $savings->increment('current_balance', $rest);
                }
            }

            // EMI processing natively through LoanService
            $loan = $member->accounts()->where('account_type', 'LOAN')->where('status', 'Active')->lockForUpdate()->first();
            if ($loan && $emiAmount > 0) {
                // Ensure we hit the amortization schedule updater
                $loanService = app(\App\Services\LoanService::class);
                $loanService->processPayment($loan, [
                    'payment_date' => $txDate,
                    'amount_paid' => $emiAmount,
                    'prepayment_mode' => 'reduce_tenure'
                ]);
            }

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function processBatchRow(Employee $member, array $data)
    {
        $shareAmount = (float)($data['share_amount'] ?? 0);
        $fdAmount = (float)($data['fd_amount'] ?? 0);
        $emiAmount = (float)($data['emi_amount'] ?? 0);
        $paymentDate = Carbon::parse($data['payment_date'] ?? now());
        $prepaymentMode = $data['prepayment_mode'] ?? 'reduce_tenure';

        DB::beginTransaction();
        try {
            // 1. Process Share
            if ($shareAmount > 0) {
                if ($shareAmount < 2000 || fmod($shareAmount, 1000) !== 0.0) {
                    throw new \Exception("Share amount must be at least 2000 and a multiple of 1000.");
                }

                $share = $member->accounts()->where('account_type', 'SHARE')->lockForUpdate()->first();
                $savings = $member->accounts()->where('account_type', 'SAVINGS')->lockForUpdate()->first();

                if (!$share) {
                    $share = Account::create(["employee_id" => $member->id, 'account_type' => 'SHARE', 'opened_date' => Carbon::now(), 'status' => 'Active']);
                }
                if (!$savings) {
                    $savings = Account::create(["employee_id" => $member->id, 'account_type' => 'SAVINGS', 'opened_date' => Carbon::now(), 'status' => 'Active']);
                }

                $shareBalance = $share->current_balance;

                if ($shareBalance >= 10000) {
                    Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $paymentDate, 'amount' => $shareAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Batch Sub (Diversion)']);
                    $savings->increment('current_balance', $shareAmount);
                } else {
                    $gap = 10000 - $shareBalance;
                    if ($shareAmount <= $gap) {
                        Transaction::create(['account_id' => $share->account_id, 'tx_date' => $paymentDate, 'amount' => $shareAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Batch Share Sub']);
                        $share->increment('current_balance', $shareAmount);
                    } else {
                        Transaction::create(['account_id' => $share->account_id, 'tx_date' => $paymentDate, 'amount' => $gap, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Batch Share (Split)']);
                        $share->increment('current_balance', $gap);

                        $rest = $shareAmount - $gap;
                        Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $paymentDate, 'amount' => $rest, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Batch Share (Split-Sav)']);
                        $savings->increment('current_balance', $rest);
                    }
                }
            }

            // 2. Process FD (Fixed Deposit)
            if ($fdAmount > 0) {
                $fd = $member->accounts()->where('account_type', 'FD')->lockForUpdate()->first();
                if (!$fd) {
                    $fd = Account::create(["employee_id" => $member->id, 'account_type' => 'FD', 'opened_date' => Carbon::now(), 'status' => 'Active']);
                }
                Transaction::create(['account_id' => $fd->account_id, 'tx_date' => $paymentDate, 'amount' => $fdAmount, 'tx_type' => 'CREDIT', 'category' => 'FIXED_DEPOSIT', 'description' => 'Batch Fixed Deposit']);
                $fd->increment('current_balance', $fdAmount);
            }

            // 3. Process EMI
            if ($emiAmount > 0) {
                $loan = $member->accounts()->where('account_type', 'LOAN')->where('status', 'Active')->lockForUpdate()->first();
                if ($loan) {
                    $loanService = app(\App\Services\LoanService::class);
                    $loanService->processPayment($loan, [
                        'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                        'amount_paid' => $emiAmount,
                        'prepayment_mode' => $prepaymentMode
                    ]);
                } else {
                    throw new \Exception("No active loan found for EMI payment.");
                }
            }

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function createLoan(Employee $member, float $amount, int $tenureMonths, array $suretyIds)
    {
        // Basic validations
        if ($amount > 800000) {
            return ['success' => false, 'message' => 'Amount exceeds maximum allowed'];
        }
        if ($tenureMonths > 120) {
            return ['success' => false, 'message' => 'Tenure exceeds maximum allowed'];
        }

        // Collateral and other underwriting checks are performed at approval/disbursal time.
        // Allow application to be created (status = Pending) so admin can review and approve later.

        if (count($suretyIds) !== 3 || in_array($member->id, $suretyIds)) {
            return ['success' => false, 'message' => 'Provide three valid sureties (cannot include applicant)'];
        }

        DB::beginTransaction();
        try {
            // Create loan account (Pending status)
            $loan = Account::create([
                'employee_id' => $member->id,
                'account_type' => 'LOAN',
                'current_balance' => $amount,
                'opened_date' => Carbon::now(),
                'status' => 'Pending'
            ]);

            $monthlyRate = 10.5 / 12 / 100;
            $emi = $this->calculateEMI($amount, $monthlyRate, $tenureMonths);

            LoanAttribute::create([
                'loan_id' => $loan->account_id,
                'principal_amount' => $amount,
                'interest_rate' => 10.5,
                'tenure_months' => $tenureMonths,
                'emi_amount' => $emi,
                // disbursal_date should be set when the loan is approved/disbursed by admin
                'disbursal_date' => null
            ]);

            foreach ($suretyIds as $sid) {
                LoanSurety::create(['loan_id' => $loan->account_id, 'employee_id' => $sid]);
            }

            DB::commit();
            return ['success' => true, 'loan_id' => $loan->account_id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function processTopUp(Employee $member, float $newLoanAmount)
    {
        DB::beginTransaction();
        try {
            $existingLoan = $member->accounts()->where('account_type', 'LOAN')->where('status', 'Active')->lockForUpdate()->first();
            if (!$existingLoan) {
                throw new \Exception('No active loan found');
            }

            $loanDetails = $existingLoan->loanAttributes;
            $repaidPercentage = (($loanDetails->principal_amount - $existingLoan->current_balance) / $loanDetails->principal_amount) * 100;
            if ($repaidPercentage < 50) {
                throw new \Exception('Loan must be at least 50% repaid for top-up');
            }

            if (!$this->checkCollateral($member, $newLoanAmount)) {
                throw new \Exception('Insufficient collateral (10% required)');
            }

            $outstanding = $existingLoan->current_balance;
            $net = $newLoanAmount - $outstanding;

            // Close existing loan
            $existingLoan->status = 'Closed';
            $existingLoan->current_balance = 0;
            $existingLoan->save();

            Transaction::create(['account_id' => $existingLoan->account_id, 'amount' => $outstanding, 'tx_type' => 'CREDIT', 'category' => 'SETTLEMENT', 'description' => 'Loan settled via top-up', 'tx_date' => Carbon::now()]);

            $newLoan = Account::create(['employee_id' => $member->id, 'account_type' => 'LOAN', 'current_balance' => $newLoanAmount, 'opened_date' => Carbon::now(), 'status' => 'Active']);

            $monthlyRate = 10.5 / 12 / 100;
            $tenure = 120;
            $emi = $this->calculateEMI($newLoanAmount, $monthlyRate, $tenure);

            LoanAttribute::create(['loan_id' => $newLoan->account_id, 'principal_amount' => $newLoanAmount, 'interest_rate' => 10.5, 'tenure_months' => $tenure, 'emi_amount' => $emi, 'disbursal_date' => Carbon::now(), 'is_topup' => true, 'parent_loan_id' => $existingLoan->account_id]);

            Transaction::create(['account_id' => $newLoan->account_id, 'amount' => $newLoanAmount, 'tx_type' => 'DEBIT', 'category' => 'DISBURSAL', 'description' => 'Top-up loan disbursed (Net: ' . $net . ')', 'tx_date' => Carbon::now()]);

            DB::commit();
            return ['success' => true, 'new_loan_id' => $newLoan->account_id, 'net_disbursement' => $net];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function processHistoricalRow(Employee $member, array $data, Carbon $transactionDate)
    {
        $shareAmount = (float)($data['share_amount'] ?? 0);
        $emiAmount = (float)($data['emi_amount'] ?? 0);
        
        DB::beginTransaction();
        try {
            // 1. Process Historical Share Deduction
            if ($shareAmount > 0) {
                $share = $member->accounts()->where('account_type', 'SHARE')->lockForUpdate()->first();
                $savings = $member->accounts()->where('account_type', 'SAVINGS')->lockForUpdate()->first();

                if (!$share) {
                    $share = Account::create(["employee_id" => $member->id, 'account_type' => 'SHARE', 'opened_date' => $transactionDate, 'status' => 'Active']);
                }
                if (!$savings) {
                    $savings = Account::create(["employee_id" => $member->id, 'account_type' => 'SAVINGS', 'opened_date' => $transactionDate, 'status' => 'Active']);
                }

                $shareBalance = $share->current_balance;

                if ($shareBalance >= 10000) {
                    Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $transactionDate, 'amount' => $shareAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Historical Share (Diversion)']);
                    $savings->increment('current_balance', $shareAmount);
                } else {
                    $gap = 10000 - $shareBalance;
                    if ($shareAmount <= $gap) {
                        Transaction::create(['account_id' => $share->account_id, 'tx_date' => $transactionDate, 'amount' => $shareAmount, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Historical Share Deduction']);
                        $share->increment('current_balance', $shareAmount);
                    } else {
                        Transaction::create(['account_id' => $share->account_id, 'tx_date' => $transactionDate, 'amount' => $gap, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Historical Share (Split)']);
                        $share->increment('current_balance', $gap);

                        $rest = $shareAmount - $gap;
                        Transaction::create(['account_id' => $savings->account_id, 'tx_date' => $transactionDate, 'amount' => $rest, 'tx_type' => 'CREDIT', 'category' => 'SUBSCRIPTION', 'description' => 'Historical Share (Split-Sav)']);
                        $savings->increment('current_balance', $rest);
                    }
                }
            }

            // 2. Process Historical EMI Deduction
            if ($emiAmount > 0) {
                $loan = $member->accounts()->where('account_type', 'LOAN')->where('status', 'Active')->lockForUpdate()->first();
                if ($loan) {
                    $attr = $loan->loanAttributes;
                    if ($attr && $attr->emi_amount != $emiAmount) {
                        $attr->emi_amount = $emiAmount;
                        $attr->save();
                    }

                    $loanService = app(\App\Services\LoanService::class);
                    $loanService->processPayment($loan, [
                        'payment_date' => $transactionDate->format('Y-m-d'),
                        'amount_paid' => $emiAmount,
                        'prepayment_mode' => 'reduce_tenure',
                        'force_recalculate' => true
                    ]);
                } else {
                    // It's possible historical records don't have an active loan. We can just log it or skip.
                    // For now, if there is an EMI but no active loan, we skip or we can throw. Throwing might break the batch.
                    throw new \Exception("No active loan found for Historical EMI payment.");
                }
            }

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
