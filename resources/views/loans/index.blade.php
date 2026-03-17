@extends('layouts.app')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center mb-4">
            <h4 class="nk-block-title">Loan Applications</h4>
            <a href="{{ route('loans.create') }}" class="btn btn-primary"><em class="icon ni ni-plus-circle"></em>&nbsp; New
                Loan</a>
        </div>

        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init-export wrap table" data-export-title="Export">
                    <thead>
                        <tr>
                            <th>Loan No.</th>
                            <th>Customer ID</th>
                            <th>Amount</th>
                            <th>Interest Rate</th>
                            <th>Tenure</th>
                            <th>Start Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>LN-{{ str_pad($loan->account_id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $loan->employee->name ?? 'N/A' }}</td>
                                <td>₹ {{ number_format($loan->loanAttributes->principal_amount ?? 0, 2) }}</td>
                                <td>{{ $loan->loanAttributes->interest_rate ?? 0 }}%</td>
                                <td>{{ $loan->loanAttributes->tenure_months ?? 0 }} Months</td>
                                <td>{{ $loan->loanAttributes?->start_date ? $loan->loanAttributes?->start_date->format('d M, Y') : $loan?->opened_date->format('d M, Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('loans.show', $loan->account_id) }}" class="btn btn-sm btn-info">
                                        <em class="icon ni ni-eye"></em>&nbsp; View details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
