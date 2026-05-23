@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            /* Premium Dashboard Styles */
            .kpi-card {
                position: relative;
                overflow: hidden;
                border: none;
                border-radius: 16px;
                transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
                background: #fff;
                box-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                z-index: 1;
            }

            .kpi-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 30px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.06);
                z-index: 10;
            }

            .kpi-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 50%;
                height: 100%;
                background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
                transform: skewX(-25deg);
                transition: 0.8s;
            }

            .kpi-card:hover::after {
                left: 200%;
            }

            .icon-box {
                width: 54px;
                height: 54px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                transition: all 0.4s ease;
            }

            .icon-box.bg-primary-dim {
                background: rgba(133, 79, 255, 0.1);
                color: #854fff;
            }

            .kpi-card:hover .icon-box.bg-primary-dim {
                background: #854fff;
                color: #fff;
                transform: scale(1.1);
                rotate: 5deg;
            }

            .icon-box.bg-success-dim {
                background: rgba(30, 224, 172, 0.1);
                color: #1ee0ac;
            }

            .kpi-card:hover .icon-box.bg-success-dim {
                background: #1ee0ac;
                color: #fff;
                transform: scale(1.1);
                rotate: 5deg;
            }

            .icon-box.bg-info-dim {
                background: rgba(9, 194, 222, 0.1);
                color: #09c2de;
            }

            .kpi-card:hover .icon-box.bg-info-dim {
                background: #09c2de;
                color: #fff;
                transform: scale(1.1);
                rotate: -5deg;
            }

            .icon-box.bg-danger-dim {
                background: rgba(232, 83, 71, 0.1);
                color: #e85347;
            }

            .kpi-card:hover .icon-box.bg-danger-dim {
                background: #e85347;
                color: #fff;
                transform: scale(1.1);
                rotate: -5deg;
            }

            .icon-box.bg-warning-dim {
                background: rgba(244, 189, 14, 0.1);
                color: #f4bd0e;
            }

            .kpi-card:hover .icon-box.bg-warning-dim {
                background: #f4bd0e;
                color: #fff;
                transform: scale(1.1);
                rotate: 5deg;
            }

            .analysis-card {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.03);
            }

            .analysis-card:hover {
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            }

            .amount {
                font-size: 1.5rem;
                font-weight: 700;
                color: #364a63;
                letter-spacing: -0.02em;
            }
        </style>
    @endpush
    {{-- @dd($recentTransactions) --}}
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Member Profile & Overview</h3>
                <div class="nk-block-des text-soft">
                    <p class="fs-15px">
                        <strong>{{ $member->name }}</strong> ({{ $member->empCode }}) &nbsp;|&nbsp;
                        {{ $member->designation?->name ?? 'N/A' }} &nbsp;|&nbsp;
                        <span
                            class="badge badge-dim bg-{{ $member->status == 'EXISTING' ? 'success' : 'secondary' }} rounded-pill">{{ $member->status }}</span>
                        @if ($member->retirement_flag)
                            &nbsp;|&nbsp;
                            <span class="badge bg-{{ $member->retirement_flag['type'] }} bg-opacity-75 rounded-pill">
                                <em class="icon ni ni-clock me-1"></em> {{ $member->retirement_flag['text'] }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('admin.ledger.index') }}?member_id={{ $member->id }}"
                    class="btn btn-primary d-none d-sm-inline-flex shadow-sm me-2">
                    <em class="icon ni ni-reports"></em><span>Member Ledger & Audit</span>
                </a>
                <a href="{{ route('admin.members.index') }}"
                    class="btn btn-outline-light bg-white d-none d-sm-inline-flex btn-icon rounded-circle shadow-sm"
                    data-bs-toggle="tooltip" title="Back to List">
                    <em class="icon ni ni-arrow-left"></em>
                </a>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <!-- Asset Cards Level -->
        <div class="row g-gs mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle text-soft">Share Capital</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹
                                        {{ number_format($shareAccount->current_balance ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box bg-success-dim">
                                <em class="icon ni ni-wallet-in icon-main"></em>
                                <em class="icon ni ni-user-list-fill icon-hover"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle text-soft">Thrift Savings</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹
                                        {{ number_format($savingsAccount->current_balance ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box bg-primary-dim">
                                <em class="icon ni ni-wallet-saving icon-main"></em>
                                <em class="icon ni ni-coins icon-hover"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle text-soft">Fixed Deposits</div>
                                <div class="card-amount mt-1">
                                    <span class="amount text-info">₹
                                        {{ number_format($fdAccount->current_balance ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box bg-info-dim">
                                <em class="icon ni ni-briefcase icon-main"></em>
                                <em class="icon ni ni-wallet-saving icon-hover"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle text-soft">Total Withdrawable</div>
                                <div class="card-amount mt-1">
                                    <span class="amount text-primary">₹ {{ number_format($finalWithdrawable, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box bg-warning-dim">
                                <em class="icon ni ni-coins icon-main"></em>
                                <em class="icon ni ni-activity-round-fill icon-hover"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-gs">
            <!-- Details Panels -->
            <div class="col-md-6">
                <div class="card analysis-card h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-4">
                            <div class="card-title d-flex align-items-center">
                                <div class="icon-box bg-danger-dim" style="width: 40px; height: 40px; font-size: 20px;">
                                    <em class="icon ni ni-wallet-out icon-main"></em>
                                    <em class="icon ni ni-money icon-hover"></em>
                                </div>
                                <h5 class="title ms-2 mb-0">Loan & Liability Analysis</h5>
                            </div>
                            @if ($loanAccount)
                                <div class="card-tools">
                                    <a href="{{ route('loans.show', $loanAccount->account_id) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm transition-300">
                                        <em class="icon ni ni-eye me-1"></em> View Details
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if ($loanAccount)
                            <div class="row mt-4">
                                <div class="col-6 mb-3">
                                    <span class="d-block text-soft mb-1"><em class="icon ni ni-hash"></em> Loan ID</span>
                                    <h5 class="fw-bold mb-0">#{{ $loanAccount->account_id }} <span
                                            class="badge bg-success ms-1"
                                            style="font-size: 11px;">{{ $loanAccount->status }}</span></h5>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-soft mb-1"><em class="icon ni ni-reports"></em>
                                        Principal</span>
                                    <h5 class="fw-bold mb-0">₹
                                        {{ number_format($loanAccount->loanAttributes->principal_amount, 2) }}</h5>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-soft mb-1"><em class="icon ni ni-check-circle"></em> Total
                                        Paid</span>
                                    <h5 class="text-success mb-0">₹ {{ number_format($totalPaid, 2) }}</h5>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-soft mb-1"><em class="icon ni ni-alert-circle"></em>
                                        Outstanding</span>
                                    <h5 class="text-danger mb-0">₹ {{ number_format($totalPending, 2) }}</h5>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-bold text-soft"><em class="icon ni ni-calendar"></em> EMI: ₹
                                            {{ number_format($loanAccount->loanAttributes->emi_amount, 2) }}</span>
                                        <span class="small fw-bold text-primary">{{ number_format($repaidPercentage, 1) }}%
                                            Repaid</span>
                                    </div>
                                    <div class="progress progress-lg bg-light" style="border-radius: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: {{ $repaidPercentage }}%"
                                            aria-valuenow="{{ $repaidPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($individualHold > 0)
                                <div
                                    class="alert alert-danger mt-4 mb-0 py-2 border-0 bg-danger-dim rounded-pill d-flex align-items-center">
                                    <em class="icon ni ni-lock-fill me-2 fs-5"></em>
                                    <p class="small mb-0"><strong>₹ {{ number_format($individualHold, 2) }}</strong>
                                        security hold taken from savings (10% Principal).</p>
                                </div>
                            @endif
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                <div class="icon-box bg-success-dim mb-3"
                                    style="width: 72px; height: 72px; font-size: 36px;">
                                    <em class="icon ni ni-check-circle"></em>
                                </div>
                                <h5 class="text-secondary">Debt Free!</h5>
                                <p class="text-soft">This member has no active loan liabilities.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card analysis-card h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-4">
                            <div class="card-title d-flex align-items-center">
                                <div class="icon-box bg-warning-dim" style="width: 40px; height: 40px; font-size: 20px;">
                                    <em class="icon ni ni-shield-star"></em>
                                </div>
                                <h5 class="title ms-2 mb-0">Surety Commitments</h5>
                            </div>
                            <div class="card-tools">
                                <span class="badge badge-dim bg-warning rounded-pill">{{ $suretyCommitments->count() }}
                                    Active</span>
                            </div>
                        </div>

                        @if ($suretyCommitments->count() > 0)
                            <div class="table-responsive">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead class="bg-light" style="border-radius: 8px;">
                                        <tr>
                                            <th class="ps-3 rounded-start border-0">Loan ID</th>
                                            <th class="border-0">Borrower</th>
                                            <th class="border-0">Amount</th>
                                            <th class="pe-3 rounded-end border-0">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($suretyCommitments as $surety)
                                            <tr class="transition-300">
                                                <td class="ps-3 text-primary fw-bold">#{{ $surety->loan_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="user-avatar user-avatar-sm bg-primary text-white me-2"
                                                            style="width: 28px; height: 28px; font-size: 11px;">
                                                            {{ substr($surety->loan->employee->name, 0, 2) }}
                                                        </span>
                                                        <span
                                                            class="fw-bold text-dark">{{ substr($surety->loan->employee->name, 0, 15) }}..</span>
                                                    </div>
                                                </td>
                                                <td class="fw-bold">₹
                                                    {{ number_format($surety->loan->loanAttributes->principal_amount ?? 0, 0) }}
                                                </td>
                                                <td class="pe-3">
                                                    <span
                                                        class="badge badge-dot bg-{{ $surety->loan->status == 'Active' ? 'success' : 'secondary' }}">
                                                        {{ $surety->loan->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                <div class="icon-box bg-light text-secondary mb-3"
                                    style="width: 72px; height: 72px; font-size: 36px;">
                                    <em class="icon ni ni-shield-check"></em>
                                </div>
                                <h5 class="text-secondary">No Commitments</h5>
                                <p class="text-soft">This member is not a guarantor for any active loans.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="col-md-12">
                <div class="card analysis-card">
                    <div class="card-inner border-bottom">
                        <div class="card-title-group">
                            <div class="card-title d-flex align-items-center">
                                <div class="icon-box bg-primary-dim" style="width: 40px; height: 40px; font-size: 20px;">
                                    <em class="icon ni ni-repeat"></em>
                                </div>
                                <h5 class="title ms-2 mb-0">Recent Ledger Activity</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="datatable-init-export nowrap table" data-export-title="Export" data-order="[]">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Account</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th class="pe-4 text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $tx)
                                        <tr class="transition-300">
                                            <td class="ps-4"><span
                                                    class="text-soft">{{ \Carbon\Carbon::parse($tx->tx_date)->format('d M, Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dim bg-dark rounded-pill shadow-sm">
                                                    {{ $tx->account->account_type ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="fs-13px fw-bold text-{{ $tx->tx_type == 'CREDIT' ? 'success' : 'danger' }}">
                                                    @if ($tx->tx_type == 'CREDIT')
                                                        <em class="icon ni ni-arrow-down-left"></em> IN
                                                    @else
                                                        <em class="icon ni ni-arrow-up-right"></em> OUT
                                                    @endif
                                                </span>
                                            </td>
                                            <td><span class="text-dark fw-bold">{{ $tx->category }}</span></td>
                                            <td class="text-soft">
                                                {{ \Illuminate\Support\Str::limit($tx->description, 30) }}</td>
                                            <td
                                                class="pe-4 text-end text-{{ $tx->tx_type == 'CREDIT' ? 'success' : 'danger' }} fw-bold fs-15px">
                                                {{ $tx->tx_type == 'CREDIT' ? '+' : '-' }} ₹
                                                {{ number_format($tx->amount, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-soft">
                                                <em class="icon ni ni-inbox fs-1 mb-2"></em><br>
                                                No transactions found in ledger
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
