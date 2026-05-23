<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\LoanPayment;

$emp = Employee::where('empCode', '130219')->first();
if (!$emp) {
    echo "Employee not found\n";
    exit;
}

$loanAcc = $emp->accounts()->where('account_type', 'LOAN')->orderBy('account_id', 'desc')->first();
if (!$loanAcc) {
    echo "Loan account not found\n";
    exit;
}

echo "Loan ID: " . $loanAcc->account_id . " Status: " . $loanAcc->status . "\n";

$payments = LoanPayment::where('loan_id', $loanAcc->account_id)
    ->orderBy('payment_date', 'asc')
    ->orderBy('id', 'asc')
    ->get(['id', 'payment_date', 'amount_paid', 'balance_after', 'principal_component', 'extra_payment', 'penalty_component']);

foreach ($payments as $p) {
    echo "ID: {$p->id} | Date: {$p->payment_date} | Paid: {$p->amount_paid} | Bal After: {$p->balance_after} | Prin: {$p->principal_component} | Extra: {$p->extra_payment}\n";
}
