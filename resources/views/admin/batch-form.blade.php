@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="fas fa-layer-group"></i> Process Monthly Data Processing</h3>
        </div>

        @if (session('type'))
            <div class="alert alert-{{ session('type') == 'error' ? 'danger' : 'success' }} alert-dismissible fade show"
                role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- System Auto Batch Run -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100 border-left-primary">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-cogs"></i> System Auto-Batch Run</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            This option automatically loops through all active members and deducts the standard Share
                            configuration (₹2000) and any active EMI amounts. It distributes money above ₹10,000 to the
                            Savings account.
                        </p>
                        <form method="POST" action="{{ route('admin.batch.process') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Transaction Date</label>
                                <input type="date" name="transaction_date" class="form-control"
                                    value="{{ now()->format('Y-m-d') }}" required>
                                <small class="text-muted">The date on which these automatic transactions will be
                                    recorded.</small>
                            </div>
                            <button class="btn btn-primary fw-bold">
                                <i class="fas fa-play me-1"></i> Run Automated Batch
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Custom Excel Upload Batch Run -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100 border-left-success">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-file-excel"></i> Custom Excel/CSV Batch Run</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Use this option to upload a specific spreadsheet containing customized Share amounts, Fixed
                            Deposits (FDs), or custom EMI payments.
                        </p>

                        <div class="alert alert-info py-2 mb-3">
                            <strong><i class="fas fa-info-circle"></i> Requirements:</strong>
                            <ul class="mb-0 ps-3 small">
                                <li><strong>EmpCode</strong>: Required. Employee Identifier.</li>
                                <li><strong>PaymentDate</strong>: YYYY-MM-DD.</li>
                                <li><strong>ShareAmount</strong>: Minimum 2000. Must be a multiple of 1000.</li>
                                <li><strong>FDAmount</strong>: Credited to Fixed Deposit.</li>
                                <li><strong>EmiAmount</strong>: Deducted as EMI payment.</li>
                                <li><strong>PrepaymentMode</strong>: reduce_tenure or reduce_emi (Applies if EMI is
                                    overpaid).</li>
                            </ul>
                        </div>

                        <a href="{{ route('admin.batch.excel.template') }}" class="btn btn-outline-success mb-3">
                            <i class="fas fa-download me-1"></i> Download Excel (.xlsx) Template
                        </a>

                        <form action="{{ route('admin.batch.excel.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Select Filled Template (.xlsx, .xls,
                                    .csv)</label>
                                <input type="file" name="file" class="form-control border-success"
                                    accept=".xlsx,.xls,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-upload me-1"></i> Upload & Process Sheet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
