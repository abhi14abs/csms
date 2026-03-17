@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Loan Management Dashboard</h2>
                <a href="{{ route('loans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> New Loan
                </a>
            </div>
        </div>

        <!-- KPIs -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Outstanding Balance</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹
                                    {{ number_format($outstandingBalance, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Principal Paid</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹ {{ number_format($principalPaid, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Loan Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹
                                    {{ number_format($totalLoanAmount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Overdue Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₹ {{ number_format($overdueAmount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Area Chart: Payments -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Payments Overview (Last 6 Months)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentLineChart"></canvas>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Installment Status Distributions</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Principal vs Interest Recovered</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="piPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const lineCtx = document.getElementById('paymentLineChart');
        const labelsLine = {!! json_encode(array_keys($recentPayments)) !!};
        const dataLine = {!! json_encode(array_values($recentPayments)) !!};
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: labelsLine,
                datasets: [{
                    label: 'Amount Paid (₹)',
                    data: dataLine,
                    borderColor: 'rgb(78, 115, 223)',
                    tension: 0.1,
                    fill: true,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                }]
            },
            options: {
                responsive: true
            }
        });

        const pieCtx = document.getElementById('piPieChart');
        const labelsPie = {!! json_encode(array_keys($pieData)) !!};
        const dataPie = {!! json_encode(array_values($pieData)) !!};
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: labelsPie,
                datasets: [{
                    data: dataPie,
                    backgroundColor: ['rgb(28, 200, 138)', 'rgb(246, 194, 62)']
                }]
            },
            options: {
                responsive: true
            }
        });

        const barCtx = document.getElementById('statusBarChart');
        const labelsBar = {!! json_encode(array_keys($statuses)) !!};
        const dataBar = {!! json_encode(array_values($statuses)) !!};

        // Assign colors based on status
        const bgColors = labelsBar.map(status => {
            if (status === 'paid') return 'rgb(28, 200, 138)'; // green
            if (status === 'pending') return 'rgb(54, 162, 235)'; // blue
            if (status === 'overdue') return 'rgb(231, 74, 59)'; // red
            if (status === 'partial') return 'rgb(246, 194, 62)'; // yellow
            return 'rgb(133, 135, 150)'; // gray
        });

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labelsBar,
                datasets: [{
                    label: 'Count',
                    data: dataBar,
                    backgroundColor: bgColors,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
