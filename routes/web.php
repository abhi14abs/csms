<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\Middleware\Authenticate;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (simple custom handlers)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Member routes - require authentication
Route::middleware([\Illuminate\Auth\Middleware\Authenticate::class])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/loan/apply', [MemberController::class, 'showLoanApplication'])->name('member.loan.apply');
    Route::post('/loan/apply', [MemberController::class, 'submitLoanApplication'])->name('member.loan.submit');
    Route::get('/loan/topup', [MemberController::class, 'showTopupApplication'])->name('member.topup');
    Route::post('/loan/topup', [MemberController::class, 'submitTopupApplication'])->name('member.topup.submit');
});

// Admin routes - require auth and admin middleware
Route::middleware([Authenticate::class, AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/monthly-due-report', [AdminController::class, 'monthlyDueReport'])->name('admin.monthly-due');
    Route::post('/monthly-due-report/update-subscription', [AdminController::class, 'updateMonthlySubscription'])->name('admin.update-subscription');
    Route::get('/process-batch', [AdminController::class, 'showBatchForm'])->name('admin.batch.form');
    Route::post('/process-batch', [AdminController::class, 'processBatchDeductions'])->name('admin.batch.process');
    Route::get('/process-batch/template', [AdminController::class, 'downloadBatchTemplate'])->name('admin.batch.excel.template');
    Route::post('/process-batch/upload', [AdminController::class, 'uploadBatchExcel'])->name('admin.batch.excel.upload');

    Route::get('/historical-import', [AdminController::class, 'showHistoricalImportForm'])->name('admin.historical-import');
    Route::post('/historical-import', [AdminController::class, 'processHistoricalExcel'])->name('admin.historical-import.process');

    Route::get('/pending-loans', [AdminController::class, 'pendingLoans'])->name('admin.pending-loans');
    Route::post('/approve-loan/{id}', [AdminController::class, 'approveLoan'])->name('admin.approve-loan');
    Route::post('/reject-loan/{id}', [AdminController::class, 'rejectLoan'])->name('admin.reject-loan');
    // Member management
    Route::get('/members', [AdminController::class, 'membersIndex'])->name('admin.members.index');
    Route::get('/members/create', [AdminController::class, 'createMember'])->name('admin.members.create');
    Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
    Route::match(['get', 'post'], '/members/create-login', [AdminController::class, 'createLogin'])->name('admin.members.create-login');
    Route::get('/members/{id}/show', [AdminController::class, 'showMember'])->name('admin.members.show');
    Route::get('/members/{id}/edit', [AdminController::class, 'editMember'])->name('admin.members.edit');
    Route::post('/members/{id}', [AdminController::class, 'updateMember'])->name('admin.members.update');
    Route::get('/members/{id}/fd', [AdminController::class, 'createFD'])->name('admin.members.fd.create');
    Route::post('/members/{id}/fd', [AdminController::class, 'storeFD'])->name('admin.members.fd.store');

    // Loan Management Module
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/dashboard', [LoanController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::get('/{loan}/export/excel', [LoanController::class, 'exportExcel'])->name('export.excel');
        Route::get('/{loan}/export/pdf', [LoanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{loan}', [LoanController::class, 'show'])->name('show');
        Route::post('/{loan}/pay', [LoanPaymentController::class, 'store'])->name('pay');
    });

    // Member Ledger & Audit
    Route::prefix('ledger')->name('admin.ledger.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MemberLedgerController::class, 'index'])->name('index');
        Route::post('/data', [\App\Http\Controllers\MemberLedgerController::class, 'getMemberData'])->name('data');
        Route::post('/update-month', [\App\Http\Controllers\MemberLedgerController::class, 'updateMonthData'])->name('update-month');
        Route::post('/settle-loan', [\App\Http\Controllers\MemberLedgerController::class, 'settleLoan'])->name('settle-loan');
        Route::post('/audit-year', [\App\Http\Controllers\MemberLedgerController::class, 'auditFinancialYear'])->name('audit-year');
        Route::post('/lock-month', [\App\Http\Controllers\MemberLedgerController::class, 'lockMonth'])->name('lock-month');
    });
});
