<!-- member dashboard view -->
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h5>Dashboard</h5>
                <p><strong>Member Since:</strong> {{ \Carbon\Carbon::parse($member->dateOfAppointment)->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Asset Summary</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Share Capital</th>
                                <td>₹ {{ number_format($shareAccount->current_balance ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Thrift Savings</th>
                                <td>₹ {{ number_format($savingsAccount->current_balance ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Fixed Deposits (FD)</th>
                                <td class="text-primary">₹ {{ number_format($fdAccount->current_balance ?? 0, 2) }}</td>
                            </tr>
                            @if ($individualHold > 0)
                                <tr class="table-warning">
                                    <th><em class="icon ni ni-lock-fill text-danger"></em> Loan Security (10% Principal)
                                    </th>
                                    <td class="text-danger">₹ {{ number_format($individualHold, 2) }}</td>
                                </tr>
                            @endif
                            {{-- <tr class="table-info">
                                <th><em class="icon ni ni-building-fill text-primary"></em> Bank Hold (30% of Net Equity)
                                </th>
                                <td class="text-primary">₹
                                    {{ number_format(max(0, $totalAssets - $individualHold) * 0.3, 2) }}
                                </td>
                            </tr> --}}
                            <tr class="table-primary border-top">
                                <th><strong>Final Withdrawable Amount</strong></th>
                                <td><strong>₹ {{ number_format($finalWithdrawable, 2) }}</strong></td>
                            </tr>
                        </table>
                        <div class="alert alert-dim alert-info py-1 small">
                            <em class="icon ni ni-info-fill"></em> Your withdrawable balance is calculated after a 10% loan
                            security hold.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4>Liability Summary</h4>
                    </div>
                    <div class="card-body">
                        @if ($loanAccount)
                            <table class="table">
                                <tr>
                                    <th>Loan ID</th>
                                    <td>#{{ $loanAccount->account_id }}</td>
                                </tr>
                                <tr>
                                    <th>Principal Amount</th>
                                    <td>₹ {{ number_format($loanAccount->loanAttributes->principal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Paid</th>
                                    <td class="text-success">₹ {{ number_format($totalPaid, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Outstanding Balance</th>
                                    <td class="text-danger">₹ {{ number_format($totalPending, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Monthly EMI</th>
                                    <td>₹ {{ number_format($loanAccount->loanAttributes->emi_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Repayment Progress</th>
                                    <td>
                                        <div class="progress"
                                            style="height: 15px; border-radius: 5px; background: #d4d8db;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                role="progressbar"
                                                style="width: {{ $repaidPercentage }}%; font-weight:bold; border-radius: 5px;"
                                                aria-valuenow="{{ $repaidPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">

                                                {{ number_format($repaidPercentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </table>

                            @if ($topUpEligible)
                                <div class="alert alert-success">
                                    <strong>Top-Up Eligible!</strong> You have repaid
                                    {{ number_format($repaidPercentage, 1) }}% of your loan.
                                    <a href="{{ route('member.topup') }}" class="btn btn-sm btn-success">Apply for
                                        Top-Up</a>
                                </div>
                            @endif
                        @else
                            <p class="text-muted">No active loan</p>
                            <a href="{{ route('member.loan.apply') }}" class="btn btn-primary">Apply for Loan</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Transactions</h4>
                    </div>
                    <div class="card-body">
                        <table class="datatable-init-export nowrap table" data-export-title="Export">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentTransactions as $tx)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($tx->tx_date)->format('d M Y') }}</td>
                                        <td>
                                            <span
                                                class="fw-bold text-{{ $tx->tx_type == 'CREDIT' ? 'success' : 'danger' }}">
                                                {{ $tx->tx_type }}
                                            </span>
                                        </td>
                                        <td>{{ $tx->category }}</td>
                                        <td>{{ $tx->description }}</td>
                                        <td>₹ {{ number_format($tx->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if ($suretyCommitments->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h4>Surety Commitments</h4>
                        </div>
                        <div class="card-body">
                            <table class="datatable-init-export nowrap table" data-export-title="Export">
                                <thead>
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Borrower</th>
                                        <th>Loan Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suretyCommitments as $surety)
                                        <tr>
                                            <td>#{{ $surety->loan_id }}</td>
                                            <td>{{ $surety->loan->employee->name }}</td>
                                            <td>₹ {{ number_format($surety->loan->loanAttributes->principal_amount, 2) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-bold text-{{ $surety->loan->status == 'Active' ? 'success' : 'secondary' }}">
                                                    {{ $surety->loan->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
