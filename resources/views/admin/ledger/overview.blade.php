@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Member Ledger Overview</h3>
            <div class="nk-block-des text-soft">
                <p>Overview of each member's accounts based on financial year.</p>
            </div>
        </div>
    </div>
</div>

<div class="nk-block">
    <div class="card card-bordered shadow-sm">
        <div class="card-inner">
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="form-label" for="filter_fy">Select Financial Year</label>
                        <div class="form-control-wrap">
                            <select class="form-select form-control form-control-lg js-select2" id="filter_fy" data-placeholder="Select Financial Year">
                                <option value=""></option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered mt-4 shadow-sm" id="overview-card" style="display: none;">
        <div class="card-inner">
            <div class="table">
                <table class="table table-hover table-bordered" id="overviewTable" data-export-title="Overview Data">
                    <thead class="table-light">
                        <tr>
                            <th>Emp No</th>
                            <th>Name</th>
                            <th class="text-end text-success">Total Shares</th>
                            <th class="text-end text-info">Opening Sav</th>
                            <th class="text-end text-info">Total Sav</th>
                            <th class="text-end">Running</th>
                            <th class="text-end text-warning">Loan</th>
                            <th class="text-end text-danger">Remaining</th>
                            <th class="text-end fw-bold">EMI</th>
                            <th class="text-end">FD</th>
                            <th class="text-end text-danger">Withdrawals</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="overview-body">
                        <!-- Content loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if($('.js-select2').length > 0) {
            $('.js-select2').select2({
                theme: "bootstrap-5"
            });
        }

        // Initialize DataTable once with export features
        NioApp.DataTable('#overviewTable', {
            responsive: { details: true },
            buttons: ['copy', 'excel', 'csv', 'pdf'],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });

        $('#filter_fy').on('change', function() {
            let fy = $(this).val();
            if (fy) {
                $('#overview-card').show();
                loadOverviewData(fy);
            } else {
                $('#overview-card').hide();
            }
        });

        function loadOverviewData(fy) {
            Swal.fire({
                title: 'Loading Data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route('admin.ledger.overview.data') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    financial_year: fy
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        renderTable(response.data);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch data', 'error');
                }
            });
        }

        function renderTable(data) {
            let table = $('#overviewTable').DataTable();
            table.clear(); // Clear existing data

            let rows = [];

            data.forEach(function(row) {
                let statusBadge = row.status === 'FY Audited' 
                    ? '<span class="badge badge-dim bg-success rounded-pill"><em class="icon ni ni-lock-alt-fill"></em> Audited</span>'
                    : (row.status.includes('Locked') ? '<span class="badge badge-dim bg-info rounded-pill">' + row.status + '</span>' : '<span class="badge badge-dim bg-warning rounded-pill">Unlocked</span>');
                
                rows.push([
                    `<span class="fw-bold">${row.empCode}</span>`,
                    row.name,
                    `<div class="text-end text-success">₹ ${parseFloat(row.total_shares).toFixed(2)}</div>`,
                    `<div class="text-end text-info">₹ ${parseFloat(row.opening_savings).toFixed(2)}</div>`,
                    `<div class="text-end text-info fw-bold"> ${parseFloat(row.total_savings).toFixed(2)}</div>`,
                    `<div class="text-end"> ${parseFloat(row.monthly_contribution).toFixed(2)}</div>`,
                    `<div class="text-end text-warning"> ${parseFloat(row.loan_taken).toFixed(2)}</div>`,
                    `<div class="text-end text-danger fw-bold"> ${parseFloat(row.loan_remaining).toFixed(2)}</div>`,
                    `<div class="text-end fw-bold"> ${parseFloat(row.emi).toFixed(2)}</div>`,
                    `<div class="text-end"> ${parseFloat(row.fd).toFixed(2)}</div>`,
                    `<div class="text-end text-danger"> ${parseFloat(row.withdrawals).toFixed(2)}</div>`,
                    `<div class="text-center">${statusBadge}</div>`
                ]);
            });

            table.rows.add(rows).draw();
        }
    });
</script>
@endpush
