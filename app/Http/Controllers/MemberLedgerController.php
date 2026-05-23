<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\LoanPayment;
use App\Models\MemberAudit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MemberLedgerController extends Controller
{
    public function index()
    {
        $members = Employee::where('is_society_member', 'YES')
            ->orderBy('name')
            ->get(['id', 'name', 'empCode']);
        return view('admin.ledger.index', compact('members'));
    }

    public function getMemberData(Request $request)
    {
        $employeeId = $request->input('employee_id');
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Member ID required.']);
        }

        $member = Employee::with(['accounts' => function ($q) {
            $q->whereIn('account_type', ['SHARE', 'SAVINGS', 'LOAN']);
        }])->find($employeeId);

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found.']);
        }

        $accountIds = $member->accounts->pluck('account_id')->toArray();
        $shareAccountId = $member->accounts->where('account_type', 'SHARE')->first()?->account_id;
        $savingsAccountId = $member->accounts->where('account_type', 'SAVINGS')->first()?->account_id;

        // Fetch the active loan, or fallback to the most recent closed loan to preserve history
        $loanAcc = $member->accounts->where('account_type', 'LOAN')->where('status', 'Active')->first()
            ?? $member->accounts->where('account_type', 'LOAN')->sortByDesc('account_id')->first();
        $loanAccountId = $loanAcc?->account_id;
        $isLoanActive = $loanAcc && $loanAcc->status === 'Active';

        // Fetch all transactions involving Share and Savings
        $transactions = Transaction::whereIn('account_id', [$shareAccountId, $savingsAccountId])
            ->orderBy('tx_date', 'asc')
            ->get();

        // Fetch all loan payments for this active/closed loan
        $loanPayments = collect();
        if ($loanAccountId) {
            $loanPayments = LoanPayment::where('loan_id', $loanAccountId)
                ->orderBy('payment_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        // Fetch audited years and months for this member
        $audits = MemberAudit::where('employee_id', $employeeId)->get();
        $auditedFys = $audits->whereNull('month')->pluck('financial_year')->toArray();
        $auditedMonths = $audits->whereNotNull('month')->keyBy('month');

        // Group by date Y-m-d
        $ledgerData = [];
        $runningSavings = 0;
        $runningShares = 0;
        $runningLoanBalance = $loanAcc?->loanAttributes?->principal_amount ?? 0;

        $allDates = collect();
        foreach ($transactions as $tx) {
            $allDates->push(Carbon::parse($tx->tx_date)->format('Y-m-d'));
        }
        foreach ($loanPayments as $lp) {
            $allDates->push(Carbon::parse($lp->payment_date)->format('Y-m-d'));
        }

        $uniqueDates = $allDates->unique()->sort()->values();

        foreach ($uniqueDates as $dateStr) {
            $dateCarbon = Carbon::parse($dateStr);
            $dateTxs = $transactions->filter(function ($tx) use ($dateStr) {
                return Carbon::parse($tx->tx_date)->format('Y-m-d') === $dateStr;
            });
            $dateLps = $loanPayments->filter(function ($lp) use ($dateStr) {
                return Carbon::parse($lp->payment_date)->format('Y-m-d') === $dateStr;
            });

            if ($dateTxs->isEmpty() && $dateLps->isEmpty()) {
                continue;
            }

            // Calculate amounts for the date
            $dateShareSub = 0;
            $dateSavSub = 0;

            foreach ($dateTxs as $tx) {
                if ($tx->tx_type === 'CREDIT' || $tx->tx_type === 'Credit') {
                    if ($tx->account_id == $shareAccountId) {
                        $dateShareSub += $tx->amount;
                        $runningShares += $tx->amount;
                    } elseif ($tx->account_id == $savingsAccountId) {
                        $dateSavSub += $tx->amount;
                        $runningSavings += $tx->amount;
                    }
                } elseif ($tx->tx_type === 'DEBIT' || $tx->tx_type === 'Debit') {
                    if ($tx->account_id == $shareAccountId) {
                        $runningShares -= $tx->amount;
                    } elseif ($tx->account_id == $savingsAccountId) {
                        $runningSavings -= $tx->amount;
                    }
                }
            }

            $datePrincipal = $dateLps->sum('principal_component');
            $dateInterest = $dateLps->sum('interest_component');
            $dateTotalEmi = $dateLps->sum('amount_paid');

            if ($dateLps->isNotEmpty()) {
                $runningLoanBalance = $dateLps->sortBy('id')->last()->balance_after;
            }

            // Determine financial year (April to March)
            $year = $dateCarbon->year;
            $fyStr = $dateCarbon->month >= 4 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;

            $monthStr = $dateCarbon->format('Y-m');
            $monthAudit = $auditedMonths->get($monthStr);
            $isLocked = $monthAudit ? true : false;
            $monthRemark = $monthAudit ? $monthAudit->remark : '';

            $ledgerData[] = [
                'date' => $dateStr,
                'display_date' => $dateCarbon->format('d M Y'),
                'fy' => $fyStr,
                'is_audited' => in_array($fyStr, $auditedFys),
                'is_locked' => $isLocked,
                'remark' => $monthRemark,
                'emi_principal' => round($datePrincipal),
                'emi_interest' => round($dateInterest),
                'emi_extra' => round($dateLps->sum('extra_payment')),
                'emi_total' => round($dateTotalEmi),
                'share_sub' => round($dateShareSub),
                'savings_cont' => round($dateSavSub),
                'total_saving' => round($runningSavings),
                'total_shares' => round($runningShares),
                'remaining_loan' => round($runningLoanBalance)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $ledgerData,
            'loan_active' => $isLoanActive,
            'loan_balance' => $loanAcc ? $loanAcc->current_balance : 0,
            'savings_balance' => $savingsAccountId ? $member->accounts->where('account_type', 'SAVINGS')->first()->current_balance : 0
        ]);
    }

    public function updateMonthData(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'month' => 'required', // This is now the exact processing date (Y-m-d)
            'emi_principal' => 'required|numeric',
            'emi_interest' => 'required|numeric',
            'emi_extra' => 'required|numeric',
            'share_sub' => 'required|numeric',
            'savings_cont' => 'required|numeric',
            'remark' => 'nullable|string'
        ]);

        $employeeId = $request->input('employee_id');
        $dateStr = $request->input('month');
        $newPrin = (float) $request->input('emi_principal');
        $newInt = (float) $request->input('emi_interest');
        $newExtra = (float) $request->input('emi_extra');
        $newShare = (float) $request->input('share_sub');
        $newSav = (float) $request->input('savings_cont');
        $remark = $request->input('remark');

        $member = Employee::with('accounts')->find($employeeId);
        if (!$member) return response()->json(['success' => false, 'message' => 'Member not found']);

        // Check if year or month is audited/locked
        $dateCarbon = Carbon::parse($dateStr);
        $formattedDate = $dateCarbon->format('Y-m-d');
        $monthKey = $dateCarbon->format('Y-m');
        $year = $dateCarbon->year;
        $fyStr = $dateCarbon->month >= 4 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;

        $isAudited = MemberAudit::where('employee_id', $employeeId)
            ->where(function($q) use ($fyStr, $monthKey) {
                $q->where('financial_year', $fyStr)->whereNull('month')
                  ->orWhere('month', $monthKey);
            })->exists();

        if ($isAudited) {
            return response()->json(['success' => false, 'message' => 'Cannot edit data for a locked/audited month or financial year.']);
        }

        DB::beginTransaction();
        try {
            $shareAcc = $member->accounts->where('account_type', 'SHARE')->first();
            $savAcc = $member->accounts->where('account_type', 'SAVINGS')->first();
            $loanAcc = $member->accounts->where('account_type', 'LOAN')->where('status', 'Active')->first();

            // 1. Update Share Subscription
            if ($shareAcc) {
                $dateTxs = Transaction::where('account_id', $shareAcc->account_id)
                    ->where('category', 'SUBSCRIPTION')
                    ->where('tx_type', 'CREDIT')
                    ->whereDate('tx_date', $formattedDate)
                    ->get();

                $oldShare = $dateTxs->sum('amount');
                $diffShare = $newShare - $oldShare;

                if ($diffShare != 0) {
                    if ($dateTxs->isNotEmpty()) {
                        $tx = $dateTxs->first();
                        $tx->amount += $diffShare;
                        if ($remark) $tx->description .= " | Edit: $remark";
                        $tx->save();
                    } else {
                        Transaction::create([
                            'account_id' => $shareAcc->account_id,
                            'tx_date' => $formattedDate,
                            'amount' => $newShare,
                            'tx_type' => 'CREDIT',
                            'category' => 'SUBSCRIPTION',
                            'description' => 'Manual Share Entry: ' . $remark
                        ]);
                    }
                    $shareAcc->current_balance += $diffShare;
                    $shareAcc->save();
                }
            }

            // 2. Update Savings Contribution
            if ($savAcc) {
                $dateTxs = Transaction::where('account_id', $savAcc->account_id)
                    ->where('category', 'SUBSCRIPTION')
                    ->where('tx_type', 'CREDIT')
                    ->whereDate('tx_date', $formattedDate)
                    ->get();

                $oldSav = $dateTxs->sum('amount');
                $diffSav = $newSav - $oldSav;

                if ($diffSav != 0) {
                    if ($dateTxs->isNotEmpty()) {
                        $tx = $dateTxs->first();
                        $tx->amount += $diffSav;
                        if ($remark) $tx->description .= " | Edit: $remark";
                        $tx->save();
                    } else {
                        Transaction::create([
                            'account_id' => $savAcc->account_id,
                            'tx_date' => $formattedDate,
                            'amount' => $newSav,
                            'tx_type' => 'CREDIT',
                            'category' => 'SUBSCRIPTION',
                            'description' => 'Manual Savings Entry: ' . $remark
                        ]);
                    }
                    $savAcc->current_balance += $diffSav;
                    $savAcc->save();
                }
            }

            // 3. Update Loan Payment (EMI components)
            if ($loanAcc) {
                $dateLps = LoanPayment::where('loan_id', $loanAcc->account_id)
                    ->whereDate('payment_date', $formattedDate)
                    ->get();

                if ($dateLps->isNotEmpty()) {
                    $lp = $dateLps->first();
                    $oldTotal = $lp->amount_paid;
                    
                    $lp->principal_component = $newPrin;
                    $lp->interest_component = $newInt;
                    $lp->extra_payment = $newExtra;
                    $lp->amount_paid = $newPrin + $newInt + $newExtra;
                    
                    $prevLp = LoanPayment::where('loan_id', $loanAcc->account_id)
                        ->where('payment_date', '<', $lp->payment_date)
                        ->orderBy('payment_date', 'desc')
                        ->first();
                    
                    $startBalance = $prevLp ? $prevLp->balance_after : $loanAcc->loanAttributes->principal_amount;
                    $lp->balance_after = $startBalance - ($newPrin + $newExtra);
                    $lp->save();

                    // Adjust subsequent historical payments
                    $subsequentLps = LoanPayment::where('loan_id', $loanAcc->account_id)
                        ->where('payment_date', '>', $lp->payment_date)
                        ->orderBy('payment_date', 'asc')
                        ->get();

                    $runningBal = $lp->balance_after;
                    foreach ($subsequentLps as $slp) {
                        $slp->balance_after = $runningBal - ($slp->principal_component + $slp->extra_payment);
                        $slp->save();
                        $runningBal = $slp->balance_after;
                    }

                    // Update loan balance
                    $loanAcc->current_balance = $runningBal;
                    $loanAcc->save();

                    // Update Transaction
                    $tx = Transaction::where('account_id', $loanAcc->account_id)
                        ->where('category', 'EMI')
                        ->whereDate('tx_date', $formattedDate)
                        ->first();
                    if ($tx) {
                        $tx->amount = $lp->amount_paid;
                        if ($remark) $tx->description .= " | Edit: $remark";
                        $tx->save();
                    }

                    // Recalculate future schedule
                    $loanService = app(\App\Services\LoanService::class);
                    $loanService->recalculateFutureSchedule($loanAcc, 'reduce_tenure', now());
                } else {
                    if ($newPrin > 0 || $newInt > 0 || $newExtra > 0) {
                        $prevLp = LoanPayment::where('loan_id', $loanAcc->account_id)
                            ->where('payment_date', '<', $formattedDate)
                            ->orderBy('payment_date', 'desc')
                            ->first();
                        
                        $startBalance = $prevLp ? $prevLp->balance_after : $loanAcc->loanAttributes->principal_amount;
                        $amountPaid = $newPrin + $newInt + $newExtra;
                        $balanceAfter = $startBalance - ($newPrin + $newExtra);

                        $lp = LoanPayment::create([
                            'loan_id' => $loanAcc->account_id,
                            'payment_date' => $formattedDate,
                            'principal_component' => $newPrin,
                            'interest_component' => $newInt,
                            'extra_payment' => $newExtra,
                            'amount_paid' => $amountPaid,
                            'balance_after' => $balanceAfter
                        ]);

                        // Adjust subsequent historical payments
                        $subsequentLps = LoanPayment::where('loan_id', $loanAcc->account_id)
                            ->where('payment_date', '>', $formattedDate)
                            ->orderBy('payment_date', 'asc')
                            ->get();

                        $runningBal = $balanceAfter;
                        foreach ($subsequentLps as $slp) {
                            $slp->balance_after = $runningBal - ($slp->principal_component + $slp->extra_payment);
                            $slp->save();
                            $runningBal = $slp->balance_after;
                        }

                        // Update loan balance
                        $loanAcc->current_balance = $runningBal;
                        $loanAcc->save();

                        // Create Transaction
                        Transaction::create([
                            'account_id' => $loanAcc->account_id,
                            'tx_date' => $formattedDate,
                            'amount' => $amountPaid,
                            'tx_type' => 'CREDIT',
                            'category' => 'EMI',
                            'description' => 'Manual EMI Entry' . ($remark ? " | Edit: $remark" : '')
                        ]);

                        // Recalculate future schedule
                        $loanService = app(\App\Services\LoanService::class);
                        $loanService->recalculateFutureSchedule($loanAcc, 'reduce_tenure', now());
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Record updated and subsequent balances adjusted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function lockMonth(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'month' => 'required', // This will now be the exact date (Y-m-d)
            'remark' => 'nullable|string'
        ]);

        $monthCarbon = Carbon::parse($request->month);
        $monthKey = $monthCarbon->format('Y-m');

        $exists = MemberAudit::where('employee_id', $request->employee_id)
            ->where('month', $monthKey)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'This month is already locked.']);
        }

        $year = $monthCarbon->year;
        $fyStr = $monthCarbon->month >= 4 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;

        MemberAudit::create([
            'employee_id' => $request->employee_id,
            'financial_year' => $fyStr,
            'month' => $monthKey,
            'audited_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'remark' => $request->remark
        ]);

        return response()->json(['success' => true, 'message' => 'Month record locked successfully.']);
    }

    public function settleLoan(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'amount_from_savings' => 'required|numeric|min:0',
            'extra_amount' => 'required|numeric|min:0'
        ]);

        $employeeId = $request->input('employee_id');
        $amtFromSavings = (float) $request->input('amount_from_savings');
        $extraAmt = (float) $request->input('extra_amount');
        $totalPayment = $amtFromSavings + $extraAmt;

        if ($totalPayment <= 0) {
            return response()->json(['success' => false, 'message' => 'Total payment amount must be greater than zero.']);
        }

        $member = Employee::with('accounts')->find($employeeId);
        if (!$member) return response()->json(['success' => false, 'message' => 'Member not found']);

        $savAcc = $member->accounts->where('account_type', 'SAVINGS')->first();
        $loanAcc = $member->accounts->where('account_type', 'LOAN')->where('status', 'Active')->first();

        if (!$loanAcc) {
            return response()->json(['success' => false, 'message' => 'Missing active loan account.']);
        }

        if ($amtFromSavings > 0) {
            if (!$savAcc || $savAcc->current_balance < $amtFromSavings) {
                return response()->json(['success' => false, 'message' => 'Insufficient savings to cover the requested amount.']);
            }
        }

        DB::beginTransaction();
        try {
            // Debit Savings if used
            if ($amtFromSavings > 0) {
                Transaction::create([
                    'account_id' => $savAcc->account_id,
                    'tx_date' => now()->format('Y-m-d'),
                    'amount' => $amtFromSavings,
                    'tx_type' => 'DEBIT',
                    'category' => 'SETTLEMENT',
                    'description' => 'Transfer from savings towards loan'
                ]);
                $savAcc->current_balance -= $amtFromSavings;
                $savAcc->save();
            }

            // Create Transaction for Extra Cash if used
            // The processPayment inside LoanService usually creates a global transaction for the full amount_paid.
            // Wait, LoanService->processPayment creates a single Transaction for the whole amount paid:
            // "description' => 'Received Loan Payment (Includes Extra Payment)'".
            // If we also create a transaction here for the "extra cash", it might double count unless we consider how it's structurally logged.
            // Actually, `LoanService->processPayment` will create the CREDIT transaction to the LOAN account for `$totalPayment`.
            // We only needed to manually DEBIT the savings. The credit to the loan is handled by processPayment.

            // Process the payment towards the loan
            $loanService = app(\App\Services\LoanService::class);
            $loanService->processPayment($loanAcc, [
                'payment_date' => now()->format('Y-m-d'),
                'amount_paid' => $totalPayment,
                'prepayment_mode' => 'reduce_tenure'
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Loan payment/settlement applied successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function auditFinancialYear(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'financial_year' => 'required',
            'remark' => 'nullable|string'
        ]);

        $exists = MemberAudit::where('employee_id', $request->employee_id)
            ->where('financial_year', $request->financial_year)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'This financial year is already audited for the member.']);
        }

        MemberAudit::create([
            'employee_id' => $request->employee_id,
            'financial_year' => $request->financial_year,
            'audited_by' => \Illuminate\Support\Facades\Auth::id() ?? 1, // Fallback for dev if needed
            'remark' => $request->remark
        ]);

        return response()->json(['success' => true, 'message' => 'Financial year marked as audited.']);
    }
}
