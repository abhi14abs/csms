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
            
            .trend-indicator {
                font-size: 0.85rem;
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 8px;
            }
            .trend-up { color: #1ee0ac; }
            .trend-down { color: #e85347; }
        </style>
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
                                    <span class="amount" id="global-total-sub">₹ {{ number_format($total_subscription ?? 0, 2) }}</span>
                                </div>
                                <div class="trend-indicator {{ $total_subscription >= ($last_month_subscription ?? 0) ? 'trend-up' : 'trend-down' }}">
                                    <em class="icon ni ni-arrow-long-{{ $total_subscription >= ($last_month_subscription ?? 0) ? 'up' : 'down' }}"></em>
                                    <span>vs last month (₹ {{ number_format($last_month_subscription ?? 0, 2) }})</span>
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
                                <div class="trend-indicator {{ $total_emi >= ($last_month_emi ?? 0) ? 'trend-up' : 'trend-down' }}">
                                    <em class="icon ni ni-arrow-long-{{ $total_emi >= ($last_month_emi ?? 0) ? 'up' : 'down' }}"></em>
                                    <span>vs last month (₹ {{ number_format($last_month_emi ?? 0, 2) }})</span>
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
                                    <span class="amount text-primary fw-bold" id="global-total-deduction">₹
                                        {{ number_format($total_deduction ?? 0, 2) }}</span>
                                </div>
                                <div class="trend-indicator {{ $total_deduction >= ($last_month_deduction ?? 0) ? 'trend-up' : 'trend-down' }}" id="global-total-trend">
                                    <em class="icon ni ni-arrow-long-{{ $total_deduction >= ($last_month_deduction ?? 0) ? 'up' : 'down' }}"></em>
                                    <span>vs last month (₹ {{ number_format($last_month_deduction ?? 0, 2) }})</span>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $index => $row)
                            <tr data-emp-id="{{ $row['id'] }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['empCode'] }}</td>
                                <td>{{ $row['name'] }}
                                    @if($row['is_society_member'] !== 'YES')
                                        <span class="badge badge-dim bg-warning ms-1">Non-Society</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['is_society_member'] === 'YES')
                                        <span class="sub-text" data-val="{{ $row['subscription'] }}">₹ {{ number_format($row['subscription'], 2) }}</span>
                                    @else
                                        <span class="sub-text" data-val="0">₹ 0.00</span>
                                    @endif
                                </td>
                                <td class="emi-cell" data-val="{{ $row['emi'] }}">₹ {{ number_format($row['emi'], 2) }}</td>
                                <td><strong class="row-total-deduction">₹ {{ number_format($row['total_deduction'], 2) }}</strong></td>
                                <td>₹ {{ number_format($row['loan_balance'], 2) }}</td>
                                <td>{{ $row['remarks'] }}</td>
                                <td>
                                    @if($row['is_society_member'] === 'YES')
                                        <button class="btn btn-sm btn-outline-primary edit-sub-btn"><em class="icon ni ni-edit"></em><span>Edit Sub</span></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- .card-preview -->
    </div> <!-- nk-block -->

    <!-- Subscription Edit Modal -->
    <div class="modal fade" id="editSubModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subscription</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="editSubForm">
                        <input type="hidden" id="edit_emp_id">
                        <div class="form-group">
                            <label class="form-label" for="edit_sub_amount">Subscription Amount</label>
                            <div class="form-control-wrap">
                                <input type="number" class="form-control" id="edit_sub_amount" step="1000" min="0" required>
                            </div>
                            <div class="form-note">Must be a multiple of 1000.</div>
                        </div>
                        <div class="form-group mt-3 text-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveSubBtn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Number formatter
            const formatCurrency = (val) => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR'
            }).format(val).replace('INR', '₹').trim();

            const globalLastMonthSub = {{ $last_month_subscription ?? 0 }};
            const globalLastMonthDed = {{ $last_month_deduction ?? 0 }};

            // Setup CSRF setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Update UI totals function
            function updateGlobalTotals() {
                let newTotalSub = 0;
                let newTotalDed = 0;
                
                $('table tbody tr').each(function() {
                    let subVal = parseFloat($(this).find('.sub-text').data('val')) || 0;
                    let emiVal = parseFloat($(this).find('.emi-cell').data('val')) || 0;
                    
                    newTotalSub += subVal;
                    newTotalDed += (subVal + emiVal);
                });

                // Update text
                $('#global-total-sub').text(formatCurrency(newTotalSub));
                $('#global-total-deduction').text(formatCurrency(newTotalDed));

                // Update sub trend
                let subTrend = $('#global-total-sub').siblings('.trend-indicator');
                if (newTotalSub >= globalLastMonthSub) {
                    subTrend.removeClass('trend-down').addClass('trend-up');
                    subTrend.find('em').removeClass('ni-arrow-long-down').addClass('ni-arrow-long-up');
                } else {
                    subTrend.removeClass('trend-up').addClass('trend-down');
                    subTrend.find('em').removeClass('ni-arrow-long-up').addClass('ni-arrow-long-down');
                }

                // Update ded trend
                let dedTrend = $('#global-total-trend');
                if (newTotalDed >= globalLastMonthDed) {
                    dedTrend.removeClass('trend-down').addClass('trend-up');
                    dedTrend.find('em').removeClass('ni-arrow-long-down').addClass('ni-arrow-long-up');
                } else {
                    dedTrend.removeClass('trend-up').addClass('trend-down');
                    dedTrend.find('em').removeClass('ni-arrow-long-up').addClass('ni-arrow-long-down');
                }
            }

            // Modal Logic
            var currentRow = null;
            
            $(document).on('click', '.edit-sub-btn', function() {
                currentRow = $(this).closest('tr');
                var empId = currentRow.data('emp-id');
                var currentSub = parseFloat(currentRow.find('.sub-text').data('val')) || 0;
                
                $('#edit_emp_id').val(empId);
                $('#edit_sub_amount').val(currentSub);
                $('#editSubModal').modal('show');
            });

            $('#editSubForm').on('submit', function(e) {
                e.preventDefault();
                var empId = $('#edit_emp_id').val();
                var newVal = parseInt($('#edit_sub_amount').val()) || 0;
                
                if (newVal % 1000 !== 0) {
                    NioApp.Toast('error', 'Subscription must be a multiple of 1000');
                    return;
                }

                var btn = $('#saveSubBtn');
                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: '{{ route("admin.update-subscription") }}',
                    method: 'POST',
                    data: {
                        employee_id: empId,
                        subscription: newVal
                    },
                    success: function(response) {
                        btn.prop('disabled', false).text('Save');
                        if (response.success) {
                            $('#editSubModal').modal('hide');
                            
                            // Update row UI
                            var subSpan = currentRow.find('.sub-text');
                            subSpan.data('val', newVal);
                            subSpan.text(formatCurrency(newVal));
                            
                            var emi = parseFloat(currentRow.find('.emi-cell').data('val')) || 0;
                            currentRow.find('.row-total-deduction').text(formatCurrency(newVal + emi));
                            
                            updateGlobalTotals();
                            NioApp.Toast('success', response.message);
                        } else {
                            NioApp.Toast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Save');
                        NioApp.Toast('error', 'Error saving sub. Please try again.');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
