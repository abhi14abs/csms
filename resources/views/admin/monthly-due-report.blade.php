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
        </style>
    @endpush

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center">

            <div class="nk-block-head-content">
                <h4 class="nk-block-title mb-1">Monthly Due Report - Payroll Deduction</h4>
                <div class="nk-block-des">
                    <p class="mb-0">Generated on: {{ now()->format('d M Y H:i') }}</p>
                </div>
            </div>

            <button onclick="hideLoader(true); window.print();" class="btn btn-primary no-loader">
                Print Report
            </button>

        </div>

        <div class="row g-gs mb-4">
            <!-- Total Subscription Card -->
            <div class="col-md-4">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total Subscription</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($total_subscription ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-wallet-in icon-main text-success"></em>
                                <em class="icon ni ni-coins icon-hover text-info"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Loan EMI Card -->
            <div class="col-md-4">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total Loan EMI</div>
                                <div class="card-amount mt-1">
                                    <span class="amount">₹ {{ number_format($total_emi ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-wallet-out icon-main text-primary"></em>
                                <em class="icon ni ni-money icon-hover text-warning"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Deduction Card -->
            <div class="col-md-4">
                <div class="card kpi-card">
                    <div class="card-inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title subtitle">Total Deduction</div>
                                <div class="card-amount mt-1">
                                    <span class="amount text-primary fw-bold">₹
                                        {{ number_format($total_deduction ?? 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="icon-box">
                                <em class="icon ni ni-reports icon-main text-danger"></em>
                                <em class="icon ni ni-file-docs icon-hover text-success"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init-export nowrap table" data-export-title="Export">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Employee No.</th>
                            <th>Member Name</th>
                            <th>Share/Sav Sub</th>
                            <th>Loan EMI</th>
                            <th>Total Deduction</th>
                            <th>Loan Balance</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['empCode'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>₹ {{ number_format($row['subscription'], 2) }}</td>
                                <td>₹ {{ number_format($row['emi'], 2) }}</td>
                                <td><strong>₹ {{ number_format($row['total_deduction'], 2) }}</strong></td>
                                <td>₹ {{ number_format($row['loan_balance'], 2) }}</td>
                                <td>{{ $row['remarks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- .card-preview -->
    </div> <!-- nk-block -->
@endsection
