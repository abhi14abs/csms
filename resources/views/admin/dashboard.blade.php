@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            /* Premium Dashboard Styles */
            .kpi-card {
                position: relative;
                overflow: hidden;
                border: none;
                border-radius: 20px;
                transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
                background: #fff;
                box-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
                z-index: 1;
            }

            .kpi-card:hover {
                transform: translateY(-12px) scale(1.03);
                box-shadow: 0 30px 45px -5px rgba(0, 0, 0, 0.12), 0 15px 15px -5px rgba(0, 0, 0, 0.06);
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

            /* Glass Analysis Card */
            .analysis-card {
                background: #fff;
                border-radius: 24px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            }

            .hold-badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
            }

            .pulse-animation {
                animation: pulse-red 2s infinite;
            }

            @keyframes pulse-red {
                0% {
                    box-shadow: 0 0 0 0 rgba(238, 82, 83, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(238, 82, 83, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(238, 82, 83, 0);
                }
            }

            .trend-indicator {
                font-size: 0.85rem;
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 8px;
            }

            .trend-up {
                color: #1ee0ac;
            }

            .trend-down {
                color: #e85347;
            }
        </style>
    @endpush

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Dashboard</h3>
                <div class="nk-block-des text-soft">
                    <p>Welcome to Society Management System. Financial summary for active members.</p>
                    <div class="mt-2 text-dark fw-bold">
                        <span class="badge badge-dim bg-primary rounded-pill">Total Employees:
                            {{ $totalEmployees ?? 0 }}</span>
                        <span class="badge badge-dim bg-secondary rounded-pill">
                            <a href="{{ route('admin.members.index') }}" class="text-dark text-decoration-none">
                                Society Members: {{ $totalSocietyMembers ?? 0 }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <form action="{{ route('admin.dashboard') }}" method="GET"
                        class="form-inline bg-white p-2 rounded-pill shadow-sm border">
                        <div class="form-group mb-0 me-2">
                            <select name="month" class="form-select form-select-sm border-0 bg-transparent fw-bold">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}"
                                        {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group mb-0 me-2 border-start ps-2">
                            <select name="year" class="form-select form-select-sm border-0 bg-transparent fw-bold">
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                            <em class="icon ni ni-filter"></em> <span>Filter</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="row g-gs">
            <!-- KPI Cards -->
            <div class="col-md-3">
                <div class="card kpi-card card-savings">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total Savings</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($totalSavings, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-wallet-fill icon-main"></em>
                                <em class="icon ni ni-coins icon-hover"></em>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div
                                class="trend-indicator mt-0 {{ $totalSavings >= ($lastTotalSavings ?? 0) ? 'trend-up' : 'trend-down' }}">
                                <em
                                    class="icon ni ni-arrow-long-{{ $totalSavings >= ($lastTotalSavings ?? 0) ? 'up' : 'down' }}"></em>
                                <span>vs last month (₹ {{ number_format($lastTotalSavings ?? 0, 2) }})</span>
                            </div>
                            <div class="text-hard ms-2" style="font-size: 0.85rem; white-space: nowrap;">
                                {{ $savingsCount ?? 0 }} Accounts</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card card-shares">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total Shares</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($totalShares, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-wallet-in icon-main"></em>
                                <em class="icon ni ni-user-list-fill icon-hover"></em>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div
                                class="trend-indicator mt-0 {{ $totalShares >= ($lastTotalShares ?? 0) ? 'trend-up' : 'trend-down' }}">
                                <em
                                    class="icon ni ni-arrow-long-{{ $totalShares >= ($lastTotalShares ?? 0) ? 'up' : 'down' }}"></em>
                                <span>vs last month (₹ {{ number_format($lastTotalShares ?? 0, 2) }})</span>
                            </div>
                            <div class="text-hard ms-2" style="font-size: 0.85rem; white-space: nowrap;">
                                {{ $sharesCount ?? 0 }} Accounts</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card card-fd">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total FD</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($totalFD, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-briefcase icon-main"></em>
                                <em class="icon ni ni-wallet-saving icon-hover"></em>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div
                                class="trend-indicator mt-0 {{ $totalFD >= ($lastTotalFD ?? 0) ? 'trend-up' : 'trend-down' }}">
                                <em
                                    class="icon ni ni-arrow-long-{{ $totalFD >= ($lastTotalFD ?? 0) ? 'up' : 'down' }}"></em>
                                <span>vs last month (₹ {{ number_format($lastTotalFD ?? 0, 2) }})</span>
                            </div>
                            <div class="text-hard ms-2" style="font-size: 0.85rem; white-space: nowrap;">
                                {{ $fdCount ?? 0 }} Accounts</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card card-exposure">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Loan Exposure</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($loanExposure, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-wallet-out icon-main"></em>
                                <em class="icon ni ni-money icon-hover"></em>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div
                                class="trend-indicator mt-0 {{ $loanExposure > ($lastLoanExposure ?? 0) ? 'trend-up' : ($loanExposure < ($lastLoanExposure ?? 0) ? 'trend-down' : '') }}">
                                @if ($loanExposure > ($lastLoanExposure ?? 0))
                                    <em class="icon ni ni-arrow-long-up"></em>
                                @elseif($loanExposure < ($lastLoanExposure ?? 0))
                                    <em class="icon ni ni-arrow-long-down"></em>
                                @endif
                                <span>vs last month (₹ {{ number_format($lastLoanExposure ?? 0, 2) }})</span>
                            </div>
                            <div class="text-hard ms-2" style="font-size: 0.85rem; white-space: nowrap;">
                                {{ $loanCount ?? 0 }} Loans</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liability & Hold Metrics -->
            <div class="col-md-6">
                <div class="card card-bordered card-full bg-white shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-inner">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <em class="icon ni ni-lock-fill icon-main"></em>
                                <em class="icon ni ni-coins icon-hover"></em>
                            </div>
                            <div class="ms-1">
                                <h6 class="subtitle text-danger mb-0">Amount on Hold</h6>
                                <small class="text-soft">10% of Loan Exposure</small>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount text-dark" style="font-size: 1.8rem;">₹
                                {{ number_format($amountOnHold, 2) }}</span>
                        </div>
                        <div class="alert alert-danger mt-3 mb-0 py-2 border-0 bg-danger-dim rounded-pill">
                            <p class="small mb-0"><em class="icon ni ni-info-fill me-1"></em> Security hold against member
                                equity.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-bordered card-full bg-white shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-inner">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box">
                                <em class="icon ni ni-building-fill icon-main"></em>
                                <em class="icon ni ni-coins icon-hover"></em>
                            </div>
                            <div class="ms-1">
                                <h6 class="subtitle text-warning mb-0">Bank Hold</h6>
                                <small class="text-soft">30% of Net Remaining Equity</small>
                            </div>
                        </div>
                        <div class="card-amount">
                            <span class="amount text-dark" style="font-size: 1.8rem;">₹
                                {{ number_format($bankHold, 2) }}</span>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0 py-2 border-0 bg-warning-dim rounded-pill">
                            <p class="small mb-0"><em class="icon ni ni-shield-check-fill me-1"></em> Mandatory liquidity
                                reserve.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawable Analysis -->
            <div class="col-md-12">
                <div class="card analysis-card border-0 shadow-lg">
                    <div class="card-inner">
                        <div class="nk-block-between g-3">
                            <div class="g">
                                <div class="card-title-group">
                                    <div class="card-title d-flex align-items-center">
                                        <div class="icon-box">
                                            <em class="icon ni ni-activity-round-fill icon-main"></em>
                                            <em class="icon ni ni-coins icon-hover"></em>
                                        </div>
                                        <div class="ms-1">
                                            <h5 class="title text-primary">Withdrawable Capital
                                                Analysis</h5>
                                            <p class="text-soft">Hold Utilization Priority: <span
                                                    class="badge badge-dim bg-info rounded-pill">1. Shares</span> <em
                                                    class="icon ni ni-arrow-right"></em> <span
                                                    class="badge badge-dim bg-primary rounded-pill">2. Savings</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $holdFromShares = min($totalShares, $amountOnHold);
                            $remainingHoldVal = max(0, $amountOnHold - $holdFromShares);
                            $holdFromSavings = min($totalSavings, $remainingHoldVal);

                            $withdrawableShares = $totalShares - $holdFromShares;
                            $withdrawableSavings = $totalSavings - $holdFromSavings;
                            $totalWithdrawableDeposits = $withdrawableShares + $withdrawableSavings + $totalFD;
                        @endphp

                        <div class="row g-gs mt-3">
                            <div class="col-sm-3">
                                <div class="card card-bordered py-3 px-4 bg-light border-0">
                                    <div class="text-soft overline-title mb-1">Total Assets</div>
                                    <div class="h5">₹ {{ number_format($totalSavings + $totalShares + $totalFD, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card card-bordered py-3 px-4 bg-light border-0">
                                    <div class="text-danger overline-title mb-1">Total Hold</div>
                                    <div class="h5 text-danger">₹ {{ number_format($amountOnHold + $bankHold, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card card-bordered py-3 px-4 bg-primary text-white border-0 shadow pulse-animation"
                                    style="border-radius: 12px;">
                                    <div class="overline-title mb-1" style="color: rgba(255,255,255,0.7)">Final
                                        Withdrawable Amount</div>
                                    <div class="d-flex align-items-center">
                                        <div class="h3 mb-0 me-3">₹ {{ number_format($finalWithdrawable, 2) }}</div>
                                        <div class="badge bg-white text-primary rounded-pill">Live Liquidity
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <h6 class="overline-title text-soft">Hold Utilization Details</h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-soft">Hold taken from Shares</span>
                                        <span class="fw-bold">₹ {{ number_format($holdFromShares, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-soft">Hold taken from Savings</span>
                                        <span class="fw-bold">₹ {{ number_format($holdFromSavings, 2) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-soft">Remaining Withdrawable Shares</span>
                                        <span class="text-success fw-bold">₹
                                            {{ number_format($withdrawableShares, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-primary fw-bold">Total Withdrawable before Bank Hold</span>
                                        <span class="text-primary fw-bold">₹
                                            {{ number_format($totalWithdrawableDeposits, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
