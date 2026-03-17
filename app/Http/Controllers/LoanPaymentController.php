<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreLoanPaymentRequest;
use App\Services\LoanService;
use Exception;

class LoanPaymentController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function store(StoreLoanPaymentRequest $request, $loanId)
    {
        try {
            $loan = Account::where('account_id', $loanId)->where('account_type', 'LOAN')->firstOrFail();
            $payment = $this->loanService->processPayment($loan, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully.',
                'payment_id' => $payment->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
