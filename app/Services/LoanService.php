<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LoanAttribute;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanService
{
    /**
     * Generate standard amortization schedule for the loan.
     */
    public function generateAmortizationSchedule(Account $loan, $startDate = null, $startInstallmentNo = 1, $principalOutstanding = null)
    {
        $attr = $loan->loanAttributes;
        $monthlyInterestRate = ($attr->interest_rate / 12) / 100;
        $balance = $principalOutstanding ?? $attr->principal_amount;
        $emi = $attr->emi_amount;

        $currentDate = $startDate ? Carbon::parse($startDate) : Carbon::parse($attr->start_date ?? $attr->disbursal_date ?? now());

        for ($i = $startInstallmentNo; $i <= $attr->tenure_months; $i++) {
            $interest = round($balance * $monthlyInterestRate);
            $principalComponent = round($emi - $interest);

            // Adjust last installment for rounding differences
            if ($i == $attr->tenure_months) {
                $principalComponent = $balance;
                $emi = $principalComponent + $interest;
            }

            $balance -= $principalComponent;
            $balance = round($balance);

            LoanInstallment::create([
                'loan_id' => $loan->account_id,
                'installment_no' => $i,
                'due_date' => $currentDate->copy()->addMonths($i - $startInstallmentNo)->format('Y-m-d'),
                'principal_due' => $principalComponent,
                'interest_due' => $interest,
                'total_due' => round($principalComponent + $interest),
                'balance_after' => abs($balance), // Ensure no negative -0.00
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Process a payment against the loan
     */
    public function processPayment(Account $loan, array $paymentData)
    {
        return DB::transaction(function () use ($loan, $paymentData) {
            $attr = $loan->loanAttributes;
            $amountPaid = (float)$paymentData['amount_paid'];
            $paymentDate = Carbon::parse($paymentData['payment_date'] ?? now());
            $prepaymentMode = $paymentData['prepayment_mode'] ?? 'reduce_tenure';
            $forceRecalculate = $paymentData['force_recalculate'] ?? false;

            // Find earliest pending or partial installment(s)
            $installments = LoanInstallment::where('loan_id', $loan->account_id)
                ->whereIn('status', ['pending', 'overdue', 'partial'])
                ->orderBy('installment_no', 'asc')
                ->get();

            if ($installments->isEmpty()) {
                throw new Exception("No pending installments to pay.");
            }

            $remainingPayment = $amountPaid;
            $totalPenaltyPaid = 0;
            $totalInterestPaid = 0;
            $totalPrincipalPaid = 0;

            $currentInstallment = null;

            foreach ($installments as $installment) {
                // NEW: Stop paying installments if they are not due yet.
                // This preserves the remainder for Principal Prepayment (Extra), 
                // saving the user from paying future interest prematurely.
                if ($installment->due_date->gt($paymentDate) && $installment->status !== 'partial') {
                    break;
                }

                $currentInstallment = $installment;

                // What is technically due right now?
                // We prioritize: Penalty -> Interest -> Principal
                $penaltyDue = 0;
                if ($attr->penalty_enabled && $installment->status === 'overdue') {
                    $penaltyDue = round(($installment->total_due * $attr->penalty_rate) / 100);
                }

                $interestDue = $installment->interest_due;
                $principalDue = $installment->principal_due;

                // How much of the dues were covered by previous partial payments?
                $prevPenaltyPaid = LoanPayment::where('installment_no', $installment->installment_no)->where('loan_id', $loan->account_id)->sum('penalty_component');
                $prevInterestPaid = LoanPayment::where('installment_no', $installment->installment_no)->where('loan_id', $loan->account_id)->sum('interest_component');
                $prevPrincipalPaid = LoanPayment::where('installment_no', $installment->installment_no)->where('loan_id', $loan->account_id)->sum('principal_component');

                $remPenalty = max(0, $penaltyDue - $prevPenaltyPaid);
                $remInterest = max(0, $interestDue - $prevInterestPaid);
                $remPrincipal = max(0, $principalDue - $prevPrincipalPaid);

                $allocPenalty = 0;
                $allocInterest = 0;
                $allocPrincipal = 0;

                // 1. Pay Penalty
                if ($remainingPayment > 0 && $remPenalty > 0) {
                    $allocPenalty = min($remainingPayment, $remPenalty);
                    $remainingPayment -= $allocPenalty;
                }

                // 2. Pay Interest
                if ($remainingPayment > 0 && $remInterest > 0) {
                    $allocInterest = min($remainingPayment, $remInterest);
                    $remainingPayment -= $allocInterest;
                }

                // 3. Pay Principal
                if ($remainingPayment > 0 && $remPrincipal > 0) {
                    $allocPrincipal = min($remainingPayment, $remPrincipal);
                    $remainingPayment -= $allocPrincipal;
                }

                $totalPenaltyPaid += $allocPenalty;
                $totalInterestPaid += $allocInterest;
                $totalPrincipalPaid += $allocPrincipal;

                // Update installment status
                $paidOnInstallment = LoanPayment::where('installment_no', $installment->installment_no)
                    ->where('loan_id', $loan->account_id)
                    ->sum('amount_paid');

                $newPaidSoFar = $paidOnInstallment + $allocPenalty + $allocInterest + $allocPrincipal;
                $expectedTotalIncludingPenalty = $installment->total_due + $penaltyDue;

                if ($newPaidSoFar >= $expectedTotalIncludingPenalty) {
                    $installment->status = 'paid';
                } else if ($newPaidSoFar > 0) {
                    $installment->status = 'partial';
                }
                $installment->save();
            }

            // If there's STILL money left, it's an Extra Payment (Prepayment towards principal)
            $extraPayment = 0;
            if ($remainingPayment > 0) {
                $extraPayment = round($remainingPayment);
                $totalPrincipalPaid += $extraPayment;
                $remainingPayment = 0;
            }

            // Calculate new balance after this transaction
            $totalPrincipalPaidHistorically = LoanPayment::where('loan_id', $loan->account_id)->sum('principal_component');
            $newBalance = round($attr->principal_amount - ($totalPrincipalPaidHistorically + $totalPrincipalPaid));

            $paymentRecord = LoanPayment::create([
                'loan_id' => $loan->account_id,
                'installment_no' => $currentInstallment ? $currentInstallment->installment_no : null,
                'payment_date' => $paymentDate->format('Y-m-d'),
                'amount_paid' => $amountPaid,
                'interest_component' => $totalInterestPaid,
                'principal_component' => $totalPrincipalPaid,
                'penalty_component' => $totalPenaltyPaid,
                'extra_payment' => $extraPayment,
                'balance_after' => max(0, $newBalance)
            ]);

            // Sync global transactions
            \App\Models\Transaction::create([
                'account_id' => $loan->account_id,
                'tx_date' => $paymentDate->format('Y-m-d'),
                'amount' => $amountPaid,
                'tx_type' => 'Credit',
                'category' => 'EMI',
                'description' => 'Received Loan Payment ' . ($extraPayment > 0 ? '(Includes Extra Payment)' : '(EMI)')
            ]);

            // Handle Prepayment Schedule Regeneration
            if (($extraPayment > 0 || $forceRecalculate) && $newBalance > 0) {
                // Get the last installment number that is actually marked 'paid'
                $lastPaidInst = LoanInstallment::where('loan_id', $loan->account_id)
                    ->where('status', 'paid')
                    ->max('installment_no') ?? 0;

                $this->recalculateSchedule($loan, $newBalance, $lastPaidInst, $prepaymentMode, $paymentDate);
            }

            if ($newBalance <= 0) {
                $loan->status = 'Closed';
            }

            // Sync the main Account balance
            $loan->current_balance = max(0, $newBalance);
            $loan->save();

            return $paymentRecord;
        });
    }

    /**
     * Recalculate future installments without creating a payment record.
     */
    public function recalculateFutureSchedule(Account $loan, string $mode = 'reduce_tenure', $referenceDate = null): void
    {
        $loan->loadMissing('loanAttributes');

        $newBalance = (float) $loan->current_balance;
        if ($newBalance <= 0) {
            return;
        }

        $lastPaidInst = LoanInstallment::where('loan_id', $loan->account_id)
            ->where('status', 'paid')
            ->max('installment_no') ?? 0;

        $referenceDate = $referenceDate ? Carbon::parse($referenceDate) : now();

        $this->recalculateSchedule($loan, $newBalance, $lastPaidInst, $mode, $referenceDate);
    }

    /**
     * Recalculate and regenerate future schedule upon extra payments.
     */
    protected function recalculateSchedule(Account $loan, $newBalance, $lastPaidInstallmentNo, $mode, $recalculateDate)
    {
        $attr = $loan->loanAttributes;

        // Delete all future pending installments
        LoanInstallment::where('loan_id', $loan->account_id)
            ->where('installment_no', '>', $lastPaidInstallmentNo)
            ->whereIn('status', ['pending'])
            ->delete();

        if ($newBalance <= 0) {
            return; // Loan is fully paid off
        }

        $monthlyInterestRate = ($attr->interest_rate / 12) / 100;

        if ($mode === 'reduce_tenure') {
            // Keep EMI same, calculate new tenure
            $emi = $attr->emi_amount;
            if ($monthlyInterestRate > 0) {
                $base = $emi - $newBalance * $monthlyInterestRate;
                if ($base <= 0) {
                    $newRemainingTenure = 1; // Failsafe
                } else {
                    $n = log($emi / $base) / log(1 + $monthlyInterestRate);
                    $newRemainingTenure = ceil($n);
                }
            } else {
                $newRemainingTenure = ceil($newBalance / $emi);
            }
            $attr->tenure_months = $lastPaidInstallmentNo + $newRemainingTenure;
            \App\Models\LoanAttribute::where('loan_id', $loan->account_id)->update(['tenure_months' => $attr->tenure_months]);
        } else if ($mode === 'reduce_emi') {
            // Keep tenure same, calculate new EMI
            $remainingTenure = $attr->tenure_months - $lastPaidInstallmentNo;
            if ($remainingTenure > 0) {
                if ($monthlyInterestRate > 0) {
                    $emi = $newBalance * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $remainingTenure) / (pow(1 + $monthlyInterestRate, $remainingTenure) - 1);
                } else {
                    $emi = $newBalance / $remainingTenure;
                }
                $attr->emi_amount = round($emi);
                \App\Models\LoanAttribute::where('loan_id', $loan->account_id)->update(['emi_amount' => $attr->emi_amount]);
            }
        }

        // IMPORTANT: Unset the relation and refresh the loan with fresh attributes
        $loan->unsetRelation('loanAttributes');
        $loan->load('loanAttributes');
        $attr = $loan->loanAttributes;

        // Generate the rest of the schedule
        $lastPaid = LoanInstallment::where('loan_id', $loan->account_id)->where('installment_no', $lastPaidInstallmentNo)->first();
        if ($lastPaid) {
            $nextDueDate = Carbon::parse($lastPaid->due_date)->addMonth();
        } else {
            $nextDueDate = $recalculateDate->copy()->addMonth();
        }

        $this->generateAmortizationSchedule($loan, $nextDueDate, $lastPaidInstallmentNo + 1, $newBalance);
    }
}
