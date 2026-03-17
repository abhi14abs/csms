<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LoanApplication;
use App\Models\LoanApplicationSurety;
use App\Models\LoanSurety;
use App\Models\Transaction;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function dashboard()
    {
        // Support both authenticated and demo mode: fall back to first Member if no auth scaffolding is present
        if (Auth::check()) {
            $member = Auth::user()->employee;
        } else {
            $member = Employee::first();
        }
        if (!$member) {
            abort(404, 'No member found. Run the seeder.');
        }

        $shareAccount = $member->accounts()->where('account_type', 'SHARE')->first();
        $savingsAccount = $member->accounts()->where('account_type', 'SAVINGS')->first();
        $fdAccount = $member->accounts()->where('account_type', 'FD')->first();

        $loanAccount = $member->accounts()
            ->where('account_type', 'LOAN')
            ->where('status', 'Active')
            ->with('loanAttributes')
            ->first();

        $totalAssets = ($shareAccount->current_balance ?? 0) + ($savingsAccount->current_balance ?? 0) + ($fdAccount->current_balance ?? 0);

        $accountIds = $member->accounts()->pluck('account_id')->toArray();
        $recentTransactions = Transaction::whereIn('account_id', $accountIds)
            ->orderBy('tx_date', 'desc')
            ->orderBy('tx_id', 'desc')
            ->get();

        $repaidPercentage = 0;
        $totalPaid = 0;
        $totalPending = 0;
        $individualHold = 0;

        if ($loanAccount && $loanAccount->loanAttributes) {
            $principal = $loanAccount->loanAttributes->principal_amount;
            $outstanding = $loanAccount->current_balance;
            $totalPaid = $loanAccount->payments()->sum('amount_paid');
            $totalPending = $outstanding;
            $repaidPercentage = $principal > 0 ? (($principal - $outstanding) / $principal) * 100 : 0;
            $topUpEligible = $repaidPercentage >= 50;

            // Individual Hold: 10% of Loan Principal
            $individualHold = $principal * 0.10;
        }

        // Individual Bank Hold: 30% of Remaining Equity after Loan Hold
        $remainingAfterLoanHold = max(0, $totalAssets - $individualHold);
        $individualBankHold = $remainingAfterLoanHold * 0.30;
        $finalWithdrawable = $remainingAfterLoanHold - $individualBankHold;

        $suretyCommitments = LoanSurety::where('employee_id', $member->id)
            ->with('loan.employee')
            ->get();

        return view('member.dashboard', compact(
            'member',
            'shareAccount',
            'savingsAccount',
            'fdAccount',
            'loanAccount',
            'totalAssets',
            'recentTransactions',
            'topUpEligible',
            'repaidPercentage',
            'totalPaid',
            'totalPending',
            'individualHold',
            'finalWithdrawable',
            'suretyCommitments'
        ));
    }

    public function showLoanApplication()
    {
        if (Auth::check()) {
            $member = Auth::user()->employee;
            if ($member) {
                $member->load(['designation', 'department']);
            }
        } else {
            $member = Employee::with(['designation', 'department'])->first();
        }

        $hasActiveLoan = $member->accounts()
            ->where('account_type', 'LOAN')
            ->where('status', 'Active')
            ->exists();

        if ($hasActiveLoan) {
            session()->flash('type', 'error');
            session()->flash('message', 'You already have an active loan. Please repay 50% to apply for top-up.');
            return redirect()->route('member.dashboard');
        }

        $membersList = Employee::where('id', '!=', $member->id)->get()->map(function ($emp) {
            $emp->service_left_months = 0;
            if ($emp->dateOfRetirement) {
                $retireDate = Carbon::parse($emp->dateOfRetirement);
                $now = Carbon::now();
                if ($retireDate->greaterThan($now)) {
                    $emp->service_left_months = number_format($now->floatDiffInMonths($retireDate), 2, '.', '');
                }
            }
            return $emp;
        });

        return view('member.loan-application', compact('membersList', 'member'));
    }

    public function showTopupApplication()
    {
        if (Auth::check()) {
            $member = Auth::user()->employee;
        } else {
            $member = Employee::first();
        }
        $loanAccount = $member->accounts()->where('account_type', 'LOAN')->where('status', 'Active')->first();

        if (!$loanAccount) {
            session()->flash('type', 'error');
            session()->flash('message', 'No active loan to top-up');
            return redirect()->route('member.dashboard');
        }

        return view('member.topup', compact('loanAccount'));
    }

    public function submitTopupApplication(Request $request)
    {
        $request->validate([
            'new_amount' => 'required|numeric|min:1000|max:800000'
        ]);

        if (Auth::check()) {
            $member = Auth::user()->employee;
        } else {
            $member = Employee::first();
        }

        $result = $this->financialService->processTopUp($member, (float)$request->input('new_amount'));
        if ($result['success']) {
            session()->flash('type', 'success');
            session()->flash('message', 'Top-up processed. Net disbursement: ' . ($result['net_disbursement'] ?? 0));
            return redirect()->route('member.dashboard');
        }

        session()->flash('type', 'error');
        session()->flash('message', $result['message'] ?? 'Top-up failed');
        return back();
    }

    public function submitLoanApplication(Request $request)
    {
        $request->validate([
            'loan_amount' => 'required|numeric|min:1000|max:800000',
            'tenure_months' => 'required|integer|min:12|max:120',
            'surety_1' => 'required|exists:employees,id',
            'surety_2' => 'required|exists:employees,id',
            'surety_3' => 'required|exists:employees,id',
            'reason' => 'nullable|string',
            'aadhaar' => 'nullable|string',
            'mobile' => 'nullable|string',
            'designation' => 'nullable|string',
            'ro_hq' => 'nullable|string',
            'dept' => 'nullable|string',
            'service_remaining' => 'nullable|integer',
            'gross_salary' => 'nullable|numeric',
            'current_emi' => 'nullable|numeric',
            'previous_loan' => 'nullable|numeric',
            'loan_outstanding' => 'nullable|numeric'
        ]);

        if (Auth::check()) {
            $member = Auth::user()->employee;
        } else {
            $member = Employee::first();
        }

        try {
            // Persist the application details for auditing and review
            $app = LoanApplication::create([
                'employee_id' => $member->id,
                'applicant_name' => $member->name,
                'aadhaar' => $request->aadhaar,
                'mobile' => $request->mobile,
                'designation' => $request->designation,
                'ro_hq' => $request->ro_hq,
                'dept' => $request->dept,
                'service_remaining' => $request->service_remaining,
                'email' => $request->email,
                'reason' => $request->reason,
                'loan_amount' => $request->loan_amount,
                'tenure_months' => $request->tenure_months,
                'emi_desired' => $request->emi_desired,
                'gross_salary' => $request->gross_salary,
                'current_emi' => $request->current_emi,
                'net_salary' => $request->net_salary,
                'previous_loan' => $request->previous_loan,
                'loan_outstanding' => $request->loan_outstanding,
            ]);

            // store application sureties snapshot
            for ($i = 1; $i <= 3; $i++) {
                $sid = $request->input('surety_' . $i);
                if ($sid) {
                    LoanApplicationSurety::create([
                        'loan_application_id' => $app->id,
                        'employee_id' => $sid,
                        'employee_number' => $request->input('surety_' . $i . '_emp'),
                        'service_left' => $request->input('surety_' . $i . '_service'),
                        'signature' => $request->input('surety_' . $i . '_sign')
                    ]);
                }
            }

            // Now call service to create a pending loan account and loan attributes
            $result = $this->financialService->createLoan(
                $member,
                $request->loan_amount,
                $request->tenure_months,
                [$request->surety_1, $request->surety_2, $request->surety_3]
            );

            if ($result['success']) {
                // link application to account created by service
                if (! empty($result['loan_id'])) {
                    $app->app_account_id = $result['loan_id'];
                    $app->status = 'linked';
                    $app->save();
                }

                session()->flash('type', 'success');
                session()->flash('message', 'Loan application submitted successfully!');
                return redirect()->route('member.dashboard');
            } else {
                // mark application as rejected if loan creation failed
                $app->status = 'rejected';
                $app->save();

                \Illuminate\Support\Facades\Log::error('Loan application service failed.', ['member' => $member->id, 'error' => $result['message'] ?? 'Unknown error']);
                session()->flash('type', 'error');
                session()->flash('message', $result['message'] ?? 'Loan application failed');
                return back()->withInput();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception during loan application.', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('type', 'error');
            session()->flash('message', 'An unexpected error occurred while processing your application. Please try again.');
            return back()->withInput();
        }
    }
}
