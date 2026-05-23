<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\BatchRun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function dashboard(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Ensure month is 2 digits for consistency (e.g. '03' instead of '3')
        $month = sprintf('%02d', $month);

        // Total Employees vs Society Members
        $totalEmployees = Employee::withoutGlobalScope('society_member')->count();
        $totalSocietyMembers = Employee::withoutGlobalScope('society_member')->where('is_society_member', 'YES')->count();

        // Target Date
        $targetDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Society Members Only (is_society_member = YES)
        $members = Employee::withoutGlobalScope('society_member')->where('is_society_member', 'YES')->get();
        $memberIds = $members->pluck('id');

        // Current Month Metrics
        $savingsData = $this->getHistoricalTotal($memberIds, 'SAVINGS', $targetDate);
        $totalSavings = $savingsData['total'];
        $savingsCount = $savingsData['count'];

        $sharesData = $this->getHistoricalTotal($memberIds, 'SHARE', $targetDate);
        $totalShares = $sharesData['total'];
        $sharesCount = $sharesData['count'];

        $fdData = $this->getHistoricalTotal($memberIds, 'FD', $targetDate);
        $totalFD = $fdData['total'];
        $fdCount = $fdData['count'];

        $loanData = $this->getHistoricalTotal($memberIds, 'LOAN', $targetDate);
        $loanExposure = $loanData['total'];
        $loanCount = $loanData['count'];

        $totalAssets = $totalSavings + $totalShares + $totalFD;

        // Last Month Metrics
        $lastMonthTarget = Carbon::createFromDate($year, $month, 1)->subMonth()->endOfMonth();

        $lastSavingsData = $this->getHistoricalTotal($memberIds, 'SAVINGS', $lastMonthTarget);
        $lastTotalSavings = $lastSavingsData['total'];

        $lastSharesData = $this->getHistoricalTotal($memberIds, 'SHARE', $lastMonthTarget);
        $lastTotalShares = $lastSharesData['total'];

        $lastFdData = $this->getHistoricalTotal($memberIds, 'FD', $lastMonthTarget);
        $lastTotalFD = $lastFdData['total'];

        $lastLoanData = $this->getHistoricalTotal($memberIds, 'LOAN', $lastMonthTarget);
        $lastLoanExposure = $lastLoanData['total'];

        // Refined Hold Logic: Loan Hold is 10% of Total Exposure
        $amountOnHold = $loanExposure * 0.10;

        // Remaining after loan hold
        $remainingAfterLoanHold = max(0, $totalAssets - $amountOnHold);

        // Bank Hold is 30% of the Remaining balance (Clarified requirement)
        $bankHold = $remainingAfterLoanHold * 0.30;

        $finalWithdrawable = $remainingAfterLoanHold - $bankHold;

        return view('admin.dashboard', compact(
            'year',
            'month',
            'totalEmployees',
            'totalSocietyMembers',
            'totalSavings',
            'savingsCount',
            'lastTotalSavings',
            'totalShares',
            'sharesCount',
            'lastTotalShares',
            'totalFD',
            'fdCount',
            'lastTotalFD',
            'loanExposure',
            'loanCount',
            'lastLoanExposure',
            'amountOnHold',
            'bankHold',
            'finalWithdrawable'
        ));
    }

    private function getHistoricalTotal($memberIds, $accountType, $endDate)
    {
        // Get all accounts of this type for the targeted members
        $accounts = Account::whereIn('employee_id', $memberIds)
            ->where('account_type', strtoupper($accountType))
            ->get();

        $totalBalance = 0;
        $activeAccountCount = 0;

        foreach ($accounts as $account) {
            // Find current balance
            $currentVal = (float)$account->current_balance;

            // Adjust by transactions after the targeted end date.
            // Using toDateTimeString() to ensure we compare against EOD accurately if tx_date is datetime.
            $adjustments = Transaction::where('account_id', $account->account_id)
                ->where('tx_date', '>', $endDate->toDateTimeString())
                ->get();

            foreach ($adjustments as $tx) {
                if (strtoupper($tx->tx_type) === 'CREDIT') {
                    $currentVal -= $tx->amount;
                } else {
                    $currentVal += $tx->amount;
                }
            }

            if ($currentVal > 0) {
                $totalBalance += $currentVal;
                $activeAccountCount++;
            }
        }

        return [
            'total' => $totalBalance,
            'count' => $activeAccountCount
        ];
    }

    public function monthlyDueReport()
    {
        // dd('Generating Monthly Due Report');
        $members = Employee::where('status', 'EXISTING')
            ->with(['accounts' => function ($q) {
                $q->where('status', 'Active');
            }])
            ->get();

        $reportData = [];
        $total_subscription = 0;
        $total_emi = 0;
        $total_deduction = 0;

        foreach ($members as $member) {
            $shareAccount = $member->accounts->where('account_type', 'SHARE')->first();
            $savingsAccount = $member->accounts->where('account_type', 'SAVINGS')->first();
            $loanAccount = $member->accounts->where('account_type', 'LOAN')->first();

            $subscriptionAmount = 0;
            if ($member->is_society_member === 'YES') {
                $subscriptionAmount = (int) ($member->monthly_subscription ?? 2000);
            }

            $emiAmount = 0;
            $loanBalance = 0;

            if ($loanAccount && $loanAccount->loanAttributes) {
                $emiAmount = $loanAccount->loanAttributes->emi_amount;
                $loanBalance = $loanAccount->current_balance;
            }

            $total_subscription += $subscriptionAmount;
            $total_emi += $emiAmount;
            $total_deduction += ($subscriptionAmount + $emiAmount);

            $reportData[] = [
                'id' => $member->id,
                'empCode' => $member->empCode,
                'name' => $member->name,
                'subscription' => $subscriptionAmount,
                'emi' => $emiAmount,
                'total_deduction' => $subscriptionAmount + $emiAmount,
                'loan_balance' => $loanBalance,
                'remarks' => $loanBalance > 0 ? 'Active Loan' : 'No Loan',
                'is_society_member' => $member->is_society_member
            ];
        }

        // Last month data comparison
        $lastMonth = Carbon::now()->subMonth();
        $last_month_subscription = DB::table('transactions')
            ->where('category', 'SUBSCRIPTION')
            ->whereMonth('tx_date', $lastMonth->month)
            ->whereYear('tx_date', $lastMonth->year)
            ->sum('amount');

        $last_month_emi = DB::table('transactions')
            ->where('category', 'EMI')
            ->whereMonth('tx_date', $lastMonth->month)
            ->whereYear('tx_date', $lastMonth->year)
            ->sum('amount');

        $last_month_deduction = $last_month_subscription + $last_month_emi;

        return view('admin.monthly-due-report', compact(
            'reportData',
            'total_subscription',
            'total_emi',
            'total_deduction',
            'last_month_subscription',
            'last_month_emi',
            'last_month_deduction'
        ));
    }

    public function updateMonthlySubscription(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'subscription' => 'required|integer|min:0'
        ]);

        if ($request->subscription > 0 && $request->subscription % 1000 !== 0) {
            return response()->json(['success' => false, 'message' => 'Subscription must be a multiple of 1000']);
        }

        $member = Employee::findOrFail($request->employee_id);

        if ($member->is_society_member !== 'YES') {
            return response()->json(['success' => false, 'message' => 'Only society members are liable to pay share/saving subscription']);
        }

        $member->monthly_subscription = $request->subscription;
        $member->save();

        return response()->json(['success' => true, 'message' => 'Subscription updated successfully']);
    }

    public function showBatchForm()
    {
        return view('admin.batch-form');
    }

    public function processBatchDeductions(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date'
        ]);
        $transactionDate = Carbon::parse($request->transaction_date);

        $year = $transactionDate->year;
        $month = $transactionDate->month;

        // Prevent duplicate successful runs for the same month/type unless explicitly forced
        $already = BatchRun::where('type', 'monthly_deductions')
            ->where('tx_year', $year)
            ->where('tx_month', $month)
            ->where('status', 'completed')
            ->exists();

        if ($already && ! $request->boolean('force')) {
            session()->flash('type', 'error');
            session()->flash('message', 'Monthly batch already processed for ' . $transactionDate->format('F Y') . ". To override, add 'force=1' to the request.");
            return redirect()->back();
        }

        // create a pending batch run record for auditing
        $batch = BatchRun::create([
            'user_id' => Auth::id(),
            'transaction_date' => $transactionDate->toDateString(),
            'type' => 'monthly_deductions',
            'status' => 'pending',
            'tx_year' => $year,
            'tx_month' => $month,
        ]);

        $members = Employee::where('status', 'EXISTING')->get();
        $results = [];

        try {
            foreach ($members as $member) {
                $loanAccount = $member->accounts()
                    ->where('account_type', 'LOAN')
                    ->where('status', 'Active')
                    ->first();

                $emiAmount = 0;
                if ($loanAccount && $loanAccount->loanAttributes) {
                    $emiAmount = $loanAccount->loanAttributes->emi_amount;
                }

                $subscriptionAmount = 0;
                if ($member->is_society_member === 'YES') {
                    $subscriptionAmount = (int) ($member->monthly_subscription ?? 2000);
                }

                $result = $this->financialService->processMonthlySubscription(
                    $member,
                    $subscriptionAmount,
                    $emiAmount,
                    $transactionDate
                );

                $results[] = [
                    'member' => $member->name,
                    'success' => $result['success'],
                    'message' => $result['message'] ?? 'Processed successfully'
                ];
            }

            $batch->status = 'completed';
            $batch->save();
        } catch (\Exception $e) {
            $batch->status = 'failed';
            $batch->notes = $e->getMessage();
            $batch->save();
            session()->flash('type', 'error');
            session()->flash('message', 'Batch processing failed: ' . $e->getMessage());
            return redirect()->back();
        }

        return view('admin.batch-results', compact('results'));
    }

    public function downloadBatchTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define Headers
        $headers = ['EmpCode', 'PaymentDate', 'ShareAmount', 'FDAmount', 'EmiAmount', 'PrepaymentMode'];
        $sheet->fromArray([$headers], NULL, 'A1');

        // Style the Header Row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1cc88a'] // Success green
            ]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Auto-Size Columns
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add Data Validation (Dropdown) to PrepaymentMode column
        $validation = $sheet->getCell('F2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please drop down and pick a mode.');
        $validation->setFormula1('"reduce_tenure,reduce_emi"');

        // Apply validations down the column F
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell('F' . $i)->setDataValidation(clone $validation);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $headersResponse = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="batch_upload_template.xlsx"',
        ];

        $callback = function () use ($writer) {
            $writer->save('php://output');
        };

        return response()->streamDownload($callback, 'batch_upload_template.xlsx', $headersResponse);
    }

    public function uploadBatchExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'Error loading file: ' . $e->getMessage()]);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $excelData = $sheet->toArray(null, true, true, false); // format values, calculate formulas, ignore empty cells

        if (empty($excelData)) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'The uploaded file is empty.']);
        }

        $header = array_shift($excelData); // Extract header

        if ($header !== ['EmpCode', 'PaymentDate', 'ShareAmount', 'FDAmount', 'EmiAmount', 'PrepaymentMode']) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'Invalid Excel format. Please download and use the official template.']);
        }

        $results = [];

        foreach ($excelData as $row) {
            // Check if row is completely empty
            if (empty(array_filter($row))) {
                continue;
            }

            // Ensure the correct number of columns exist
            if (count($header) !== count($row)) {
                $row = array_pad($row, count($header), null);
            }

            // Re-index array explicitly (0 to N) to ensure combine works
            $data = array_combine($header, array_values($row));

            $member = Employee::where('empCode', $data['EmpCode'])->first();
            if (!$member) {
                $results[] = [
                    'member' => $data['EmpCode'] . ' (Not Found)',
                    'success' => false,
                    'message' => 'Employee Code not found in database.'
                ];
                continue;
            }

            // Clean string inputs, particularly the date parsing from Excel formats if necessary
            $paymentDateStr = $data['PaymentDate'] ? trim($data['PaymentDate']) : now()->format('Y-m-d');
            try {
                // Try carbon parse to make sure format translates
                $parsedDate = Carbon::parse($paymentDateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                $parsedDate = now()->format('Y-m-d'); // fallback
            }

            $rowResult = $this->financialService->processBatchRow($member, [
                'payment_date' => $parsedDate,
                'share_amount' => $data['ShareAmount'],
                'fd_amount' => $data['FDAmount'],
                'emi_amount' => $data['EmiAmount'],
                'prepayment_mode' => $data['PrepaymentMode'] ?: 'reduce_tenure',
            ]);

            $results[] = [
                'member' => $member->name . ' (' . $member->empCode . ')',
                'success' => $rowResult['success'],
                'message' => $rowResult['message'] ?? 'Processed successfully'
            ];
        }

        return view('admin.batch-results', compact('results'));
    }

    public function showHistoricalImportForm()
    {
        return view('admin.historical-import');
    }

    public function processHistoricalExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $file = $request->file('file');

        $transactionDate = Carbon::create($request->year, $request->month, 1)->endOfMonth();

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'Error loading file: ' . $e->getMessage()]);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $excelData = $sheet->toArray(null, true, true, false);

        if (empty($excelData)) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'The uploaded file is empty.']);
        }

        $header = array_shift($excelData);

        // Find necessary column indexes
        $empCol = -1;
        $shareCol = -1;
        $emiCol = -1;

        foreach ($header as $index => $colName) {
            if (!$colName) continue;
            $lower = strtolower(trim($colName));
            if (str_contains($lower, 'emp no') || str_contains($lower, 'empcode') || str_contains($lower, 'emp code')) {
                $empCol = $index;
            } elseif (str_contains($lower, 'share')) {
                $shareCol = $index;
            } elseif (str_contains($lower, 'emi')) {
                $emiCol = $index;
            }
        }

        if ($empCol === -1) {
            return redirect()->back()->with(['type' => 'error', 'message' => 'Could not find Employee Number column (Emp No. or EmpCode).']);
        }

        $results = [];

        foreach ($excelData as $row) {
            if (empty(array_filter($row))) continue;

            $empCode = $row[$empCol] ?? null;
            if (!$empCode) continue;

            // Clean up empCode
            $empCode = trim($empCode);

            $shareAmount = $shareCol !== -1 ? (float)str_replace(',', '', $row[$shareCol] ?? 0) : 0;
            $emiAmount = $emiCol !== -1 ? (float)str_replace(',', '', $row[$emiCol] ?? 0) : 0;

            if ($shareAmount <= 0 && $emiAmount <= 0) {
                continue;
            }

            $member = Employee::withoutGlobalScope('society_member')->where('empCode', $empCode)->first();
            if (!$member) {
                $results[] = [
                    'member' => $empCode . ' (Not Found)',
                    'success' => false,
                    'message' => 'Employee Code not found in database.'
                ];
                continue;
            }

            $rowResult = $this->financialService->processHistoricalRow($member, [
                'share_amount' => $shareAmount,
                'emi_amount' => $emiAmount
            ], $transactionDate);

            $results[] = [
                'member' => $member->name . ' (' . $member->empCode . ')',
                'success' => $rowResult['success'],
                'message' => $rowResult['message'] ?? 'Historical processing successful'
            ];
        }

        return view('admin.batch-results', compact('results'));
    }

    public function pendingLoans()
    {
        $pendingLoans = Account::where('account_type', 'LOAN')
            ->where('status', 'Pending')
            ->with(['employee', 'loanAttributes', 'sureties.guarantor'])
            ->get();

        return view('admin.pending-loans', compact('pendingLoans'));
    }

    /**
     * List members
     */
    public function membersIndex()
    {
        $members = Employee::with('designation', 'department')->orderBy('id')->get();
        return view('admin.members.index', compact('members'));
    }

    public function createMember()
    {
        return view('admin.members.create');
    }

    public function storeMember(Request $request)
    {
        $data = $request->validate([
            'empCode' => 'required|unique:employees,empCode',
            'name' => 'required|string',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'category' => 'required|in:General,OBC,SC,ST,EWS',
            'dateOfBirth' => 'required|date',
            'dateOfRetirement' => 'required|date',
            'dateOfAppointment' => 'required|date',
            'designationAtAppointment' => 'required|string',
            'presentPosting' => 'required|string',
            'status' => 'required|in:EXISTING,RETIRED,TRANSFERRED',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'homeTown' => 'nullable|string',
            'residentialAddress' => 'nullable|string',
        ]);

        Employee::create($data);
        session()->flash('type', 'success');
        session()->flash('message', 'Member created');
        return redirect()->route('admin.members.index');
    }

    public function showMember($id)
    {
        $member = Employee::with(['accounts.loanAttributes', 'accounts.payments'])->findOrFail($id);

        $shareAccount = $member->accounts->where('account_type', 'SHARE')->first();
        $savingsAccount = $member->accounts->where('account_type', 'SAVINGS')->first();
        $fdAccount = $member->accounts->where('account_type', 'FD')->first();

        $loanAccount = $member->accounts
            ->where('account_type', 'LOAN')
            ->where('status', 'Active')
            ->first();

        $totalAssets = ($shareAccount->current_balance ?? 0) + ($savingsAccount->current_balance ?? 0) + ($fdAccount->current_balance ?? 0);

        $accountIds = $member->accounts->pluck('account_id')->toArray();
        $recentTransactions = Transaction::whereIn('account_id', $accountIds)
            ->orderBy('created_at', 'desc')
            ->orderBy('tx_id', 'desc')
            ->take(50)
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

            // Individual Hold: 10% of Loan Principal
            $individualHold = $principal * 0.10;
        }

        // Individual Bank Hold: 30% of Remaining Equity after Loan Hold
        $remainingAfterLoanHold = max(0, $totalAssets - $individualHold);
        // $individualBankHold = $remainingAfterLoanHold * 0.30;
        $finalWithdrawable = $remainingAfterLoanHold;

        $suretyCommitments = \App\Models\LoanSurety::where('employee_id', $member->id)
            ->with('loan.employee')
            ->get();

        return view('admin.members.show', compact(
            'member',
            'shareAccount',
            'savingsAccount',
            'fdAccount',
            'loanAccount',
            'totalAssets',
            'recentTransactions',
            'repaidPercentage',
            'totalPaid',
            'totalPending',
            'individualHold',
            'finalWithdrawable',
            'suretyCommitments'
        ));
    }

    public function editMember($id)
    {
        $member = Employee::findOrFail($id);
        return view('admin.members.edit', compact('member'));
    }

    public function updateMember(Request $request, $id)
    {
        $member = Employee::findOrFail($id);
        $data = $request->validate([
            'empCode' => 'required|unique:employees,empCode,' . $member->id . ',id',
            'name' => 'required|string',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'category' => 'required|in:General,OBC,SC,ST,EWS',
            'dateOfBirth' => 'required|date',
            'dateOfRetirement' => 'required|date',
            'dateOfAppointment' => 'required|date',
            'designationAtAppointment' => 'required|string',
            'presentPosting' => 'required|string',
            'status' => 'required|in:EXISTING,RETIRED,TRANSFERRED',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'homeTown' => 'nullable|string',
            'residentialAddress' => 'nullable|string',
        ]);

        $member->update($data);
        session()->flash('type', 'success');
        session()->flash('message', 'Member updated');
        return redirect()->route('admin.members.index');
    }

    public function createLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'password' => 'required|min:6',
            ]);

            $employee = \App\Models\Employee::findOrFail($request->employee_id);

            // Re-check email presence as we are taking it from employee
            if (empty($employee->email)) {
                session()->flash('type', 'error');
                session()->flash('message', 'Selected member does not have an email address set in their profile.');
                return redirect()->back();
            }

            // Check if user already exists
            if (\App\Models\User::where('employee_number', $employee->empCode)->exists()) {
                session()->flash('type', 'error');
                session()->flash('message', 'Login already exists for this member.');
                return redirect()->back();
            }

            // Check if email is already taken by another user
            if (\App\Models\User::where('email', $employee->email)->exists()) {
                session()->flash('type', 'error');
                session()->flash('message', 'The email address associated with this member is already used by another login.');
                return redirect()->back();
            }

            \App\Models\User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'employee_number' => $employee->empCode,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'is_admin' => false,
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'Member login created successfully.');
            return redirect()->route('admin.members.index');
        }

        // GET request
        // Get employees who don't have a user account
        $members = \App\Models\Employee::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotIn('empCode', function ($query) {
                $query->select('employee_number')->from('users')->whereNotNull('employee_number');
            })->get();

        return view('admin.members.create-login', compact('members'));
    }

    public function approveLoan(Request $request, $loanId)
    {
        $remarks = $request->input('remarks');

        try {
            $resultMessage = DB::transaction(function () use ($loanId, $remarks) {
                $loan = Account::where('account_id', $loanId)->lockForUpdate()->firstOrFail();
                $loan->load(['loanAttributes', 'employee']);

                if ($loan->status === 'Active') {
                    $alreadyDisbursed = Transaction::where('account_id', $loan->account_id)
                        ->where('category', 'DISBURSAL')
                        ->exists();
                    $hasSchedule = $loan->installments()->exists();

                    if ($alreadyDisbursed || $hasSchedule) {
                        return 'Loan was already approved earlier. No duplicate disbursal was created.';
                    }
                }

                if ($loan->status !== 'Pending') {
                    throw new \RuntimeException('Only pending loans can be approved.');
                }

                $attr = $loan->loanAttributes;
                $member = $loan->employee;

                if (! $attr) {
                    throw new \RuntimeException('Loan attributes missing. Cannot approve.');
                }

                $suretyCount = $loan->sureties()->count();
                if ($suretyCount < 3) {
                    throw new \RuntimeException('Loan must have three sureties before approval.');
                }

                if (! $this->financialService->checkCollateral($member, $attr->principal_amount)) {
                    throw new \RuntimeException('Insufficient collateral (10% required). Cannot approve loan.');
                }

                $loan->status = 'Active';
                $loan->save();

                $app = \App\Models\LoanApplication::where('app_account_id', $loan->account_id)->lockForUpdate()->first();
                if ($app) {
                    $app->status = 'approved';
                    $app->admin_remarks = $remarks;
                    $app->save();
                }

                if (! $attr->disbursal_date) {
                    $attr->disbursal_date = Carbon::now();
                    $attr->save();
                }

                $hasDisbursal = Transaction::where('account_id', $loan->account_id)
                    ->where('category', 'DISBURSAL')
                    ->exists();

                if (! $hasDisbursal) {
                    Transaction::create([
                        'account_id' => $loan->account_id,
                        'amount' => $attr->principal_amount,
                        'tx_type' => 'DEBIT',
                        'category' => 'DISBURSAL',
                        'description' => 'Loan disbursed',
                        'tx_date' => Carbon::now()
                    ]);
                }

                if (! $loan->installments()->exists()) {
                    app(\App\Services\LoanService::class)->generateAmortizationSchedule($loan);
                }

                return 'Loan approved and disbursed successfully';
            });

            session()->flash('type', 'success');
            session()->flash('message', $resultMessage);
        } catch (\Throwable $e) {
            session()->flash('type', 'error');
            session()->flash('message', $e->getMessage());
        }

        return redirect()->route('admin.pending-loans');
    }

    public function rejectLoan(Request $request, $loanId)
    {
        $remarks = $request->input('remarks');

        try {
            DB::transaction(function () use ($loanId, $remarks) {
                $loan = Account::where('account_id', $loanId)->lockForUpdate()->firstOrFail();

                if ($loan->status === 'Rejected') {
                    return;
                }

                if ($loan->status !== 'Pending') {
                    throw new \RuntimeException('Only pending loans can be rejected.');
                }

                $loan->status = 'Rejected';
                $loan->save();

                $app = \App\Models\LoanApplication::where('app_account_id', $loan->account_id)->lockForUpdate()->first();
                if ($app) {
                    $app->status = 'rejected';
                    $app->admin_remarks = $remarks;
                    $app->save();
                }
            });

            session()->flash('type', 'success');
            session()->flash('message', 'Loan rejected');
        } catch (\Throwable $e) {
            session()->flash('type', 'error');
            session()->flash('message', $e->getMessage());
        }

        return redirect()->route('admin.pending-loans');
    }

    public function createFD($id)
    {
        $member = Employee::findOrFail($id);
        return view('admin.members.fd-create', compact('member'));
    }

    public function storeFD(Request $request, $id)
    {
        $member = Employee::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'tx_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $fdAccount = $member->accounts()->where('account_type', 'FD')->lockForUpdate()->first();
            if (!$fdAccount) {
                $fdAccount = Account::create([
                    'employee_id' => $member->id,
                    'account_type' => 'FD',
                    'opened_date' => Carbon::now(),
                    'status' => 'Active',
                    'current_balance' => 0
                ]);
            }

            $amount = $request->input('amount');
            $txDate = Carbon::parse($request->input('tx_date'));

            Transaction::create([
                'account_id' => $fdAccount->account_id,
                'tx_date' => $txDate,
                'amount' => $amount,
                'tx_type' => 'CREDIT',
                'category' => 'FIXED_DEPOSIT',
                'description' => $request->input('description') ?? 'Manual FD Creation'
            ]);

            $fdAccount->increment('current_balance', $amount);

            DB::commit();
            session()->flash('type', 'success');
            session()->flash('message', 'FD created successfully for ' . $member->name);
            return redirect()->route('admin.members.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Error: ' . $e->getMessage());
        }
    }
}
