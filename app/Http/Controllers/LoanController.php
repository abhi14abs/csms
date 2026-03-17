<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Transaction;
use App\Services\LoanService;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanController extends Controller
{
    protected $loanService;
    protected $financialService;

    public function __construct(LoanService $loanService, FinancialService $financialService)
    {
        $this->loanService = $loanService;
        $this->financialService = $financialService;
    }

    public function dashboard()
    {
        // KPIs
        $activeLoans = Account::where('account_type', 'LOAN')->with('loanAttributes', 'installments')->get();
        // Calculate total loan amount from loan_attributes
        $totalLoanAmount = $activeLoans->sum(function ($account) {
            return $account->loanAttributes ? $account->loanAttributes->principal_amount : 0;
        });

        $principalPaid = LoanPayment::sum('principal_component');
        $interestPaid = LoanPayment::sum('interest_component');
        $outstandingBalance = $totalLoanAmount - $principalPaid;
        $overdueAmount = LoanInstallment::where('status', 'overdue')->sum('total_due');

        // Chart C: Installment Status
        $statuses = LoanInstallment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Chart B: Principal vs Interest Paid
        $pieData = [
            'Principal' => $principalPaid,
            'Interest' => $interestPaid,
        ];

        // Chart A: Balance vs Time (Simplified: Origination vs Paid out per month)
        $recentPayments = LoanPayment::select(
            DB::raw("DATE_FORMAT(payment_date, '%b %Y') as month"),
            DB::raw("SUM(amount_paid) as sum_paid")
        )
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderByRaw('MIN(payment_date) ASC')
            ->pluck('sum_paid', 'month')
            ->toArray();

        return view('loans.dashboard', compact(
            'totalLoanAmount',
            'principalPaid',
            'interestPaid',
            'outstandingBalance',
            'overdueAmount',
            'statuses',
            'pieData',
            'recentPayments'
        ));
    }

    public function index()
    {
        $loans = Account::where('account_type', 'LOAN')
            ->with(['employee', 'installments', 'payments', 'loanAttributes'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $membersList = Employee::all()->map(function ($emp) {
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

        $isAdminView = true;
        // In the view, if $isAdminView is true, we map $member = null to let them choose
        $member = null;

        return view('member.loan-application', compact('membersList', 'isAdminView', 'member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_amount' => 'required|numeric|min:1000|max:800000',
            'tenure_months' => 'required|integer|min:12|max:120',
            'surety_1' => 'required|exists:employees,id',
            'surety_2' => 'required|exists:employees,id',
            'surety_3' => 'required|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();
            $member = Employee::findOrFail($request->employee_id);

            // Call FinancialService (this sets it to 'Pending')
            $result = $this->financialService->createLoan(
                $member,
                $request->loan_amount,
                $request->tenure_months,
                [$request->surety_1, $request->surety_2, $request->surety_3]
            );

            if (!$result['success']) {
                throw new Exception($result['message']);
            }

            $loan = Account::findOrFail($result['loan_id']);
            $attr = $loan->loanAttributes;

            // Direct Disbursal for Admin
            $loan->status = 'Active';
            $loan->save();

            $attr->disbursal_date = Carbon::now();
            $attr->save();

            // Disbursal Transaction
            Transaction::create([
                'account_id' => $loan->account_id,
                'amount' => $attr->principal_amount,
                'tx_type' => 'DEBIT',
                'category' => 'DISBURSAL',
                'description' => 'Loan disbursed (Admin Direct)',
                'tx_date' => Carbon::now()
            ]);

            // Generate Amortization Schedule
            $this->loanService->generateAmortizationSchedule($loan);

            DB::commit();

            return redirect()->route('loans.show', $loan->account_id)->with('success', 'Loan created and disbursed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating loan: ' . $e->getMessage());
        }
    }

    public function show($loanId)
    {
        $loan = Account::with('employee')->where('account_id', $loanId)->where('account_type', 'LOAN')->firstOrFail();
        $loan->load(['installments' => function ($q) {
            $q->orderBy('installment_no', 'asc');
        }, 'payments' => function ($q) {
            $q->orderBy('payment_date', 'desc');
        }, 'loanAttributes']);

        // Calculate summary
        $totalDue = $loan->installments->where('status', '!=', 'paid')->sum('total_due');
        $totalPaid = $loan->payments->sum('amount_paid');
        $balance = $loan->current_balance;

        return view('loans.show', compact('loan', 'totalDue', 'totalPaid', 'balance'));
    }

    public function exportExcel($loanId)
    {
        $loan = Account::findOrFail($loanId);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LoanAmortizationExport($loan), 'Loan_Schedule_' . $loan->account_id . '.xlsx');
    }

    public function exportPdf($loanId)
    {
        $loan = Account::findOrFail($loanId);
        $loan->load('installments', 'employee');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('loans.pdf', compact('loan'));
        return $pdf->download('Loan_Statement_' . $loan->account_id . '.pdf');
    }
}
