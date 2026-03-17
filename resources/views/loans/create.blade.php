@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="mb-4">
            <h2><a href="{{ route('loans.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Loans</a> /
                New Loan</h2>
        </div>

        <div class="card shadow col-lg-8 mx-auto">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Create Loan & Generate Schedule</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Loan No. (Optional)</label>
                            <input type="text" name="loan_no" class="form-control" value="{{ old('loan_no') }}"
                                placeholder="Auto-generated if blank">
                        </div>
                        <div class="col-md-6">
                            <label>Customer/Employee ID</label>
                            <input type="number" name="customer_id" class="form-control" value="{{ old('customer_id') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Loan Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="loan_amount" class="form-control" required
                                value="{{ old('loan_amount') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Interest Rate (Annual %) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="interest_rate" class="form-control" required
                                value="{{ old('interest_rate') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Tenure (Months) <span class="text-danger">*</span></label>
                            <input type="number" name="tenure_months" class="form-control" required
                                value="{{ old('tenure_months') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required
                                value="{{ old('start_date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Penalty Rate (% of due amount)</label>
                            <input type="number" step="0.01" name="penalty_rate" class="form-control"
                                value="{{ old('penalty_rate', 2) }}">
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" id="penalty_enabled" name="penalty_enabled"
                                    value="1" {{ old('penalty_enabled') ? 'checked' : '' }}>
                                <label class="form-check-label" for="penalty_enabled">Enable auto-penalty on overdue</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Generate Loan
                            Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
