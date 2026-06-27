@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Member Financial Ledger & Audit</h3>
            <div class="nk-block-des text-soft">
                <p>View historical financial data, edit monthly contributions, settle loans, and audit financial years.</p>
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
                        <label class="form-label" for="member_id">Select Society Member</label>
                        <div class="form-control-wrap">
                            <select class="form-select form-control form-control-lg js-select2" id="member_id" data-search="on" data-placeholder="Choose a member to load ledger">
                                <option value=""></option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->empCode }} - {{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="fy-filter-container" style="display: none;">
                    <div class="form-group mb-0">
                        <label class="form-label" for="filter_fy">Financial Year Filter</label>
                        <div class="form-control-wrap">
                            <select class="form-select form-control form-control-lg" id="filter_fy">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-end" id="action-buttons" style="display: none;">
                    <button type="button" class="btn btn-warning shadow-sm me-2" id="btn-settle-loan" style="display: none;" data-bs-toggle="modal" data-bs-target="#settleModal">
                        <em class="icon ni ni-wallet-out"></em><span>Settle / Pay Loan</span>
                    </button>
                    <button type="button" class="btn btn-secondary shadow-sm me-2" id="btn-withdraw-savings" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <em class="icon ni ni-money"></em><span>Withdraw Savings</span>
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#auditModal">
                        <em class="icon ni ni-check-circle-cut"></em><span>Mark FY as Audited</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered mt-4 shadow-sm" id="ledger-card" style="display: none;">
        <div class="card-inner">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="ledgerTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end text-primary">Principal</th>
                            <th class="text-end text-danger">Interest</th>
                            <th class="text-end text-warning">Extra</th>
                            <th class="text-end fw-bold">Total EMI</th>
                            <th class="text-end text-success">Share Sub</th>
                            <th class="text-end text-info">Sav Cont</th>
                            <th class="text-end">Rem Loan</th>
                            <th>Remark</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ledger-body">
                        <!-- Content loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Month Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Record & Adjustments</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_employee_id" name="employee_id">
                    <input type="hidden" id="edit_month" name="month">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Processed Date</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control fw-bold" id="display_edit_month" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_emi_principal">Principal Component</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_emi_principal" name="emi_principal" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_emi_interest">Interest Component</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_emi_interest" name="emi_interest" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_emi_extra">Extra Payment</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_emi_extra" name="emi_extra" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_share_sub">Share Subscription</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_share_sub" name="share_sub" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_savings_cont">Savings Contrib</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_savings_cont" name="savings_cont" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="edit_savings_withdrawal">Savings Withdraw</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="edit_savings_withdrawal" name="savings_withdrawal" step="0.01" required value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="edit_remark">Adjustment Remark</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" id="edit_remark" name="remark" rows="2" placeholder="Reason for change..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <em class="icon ni ni-alert-circle"></em>
                        <span>Saving changes will retroactively adjust loan balances for all subsequent months.</span>
                    </div>
                    <div class="form-group mt-3 text-end">
                        <button type="submit" class="btn btn-lg btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Settle Loan Modal -->
<div class="modal fade" id="settleModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Settle or Pay Loan Database</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="settleForm">
                    <div class="row mb-3">
                        <div class="col-6">
                            <span class="text-soft">Current Loan Balance:</span>
                            <h4 class="text-danger" id="display_loan_balance">₹ 0.00</h4>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-soft">Available Savings:</span>
                            <h4 class="text-success" id="display_savings_balance">₹ 0.00</h4>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="amount_from_savings">Amount using Savings</label>
                        <div class="form-control-wrap">
                            <input type="number" class="form-control" id="amount_from_savings" name="amount_from_savings" step="0.01" value="0" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="extra_amount">Extra Amount (Cash/Bank)</label>
                        <div class="form-control-wrap">
                            <input type="number" class="form-control" id="extra_amount" name="extra_amount" step="0.01" value="0" min="0" required>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        Total Payment: <strong id="display_total_payment">₹ 0.00</strong>
                    </div>

                    <div class="form-group mt-3 text-end">
                        <button type="submit" class="btn btn-lg btn-warning">Process Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Savings Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Savings</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="withdrawForm">
                    <div class="row mb-3">
                        <div class="col-12">
                            <span class="text-soft">Available Savings:</span>
                            <h4 class="text-success" id="display_withdraw_savings_balance">₹ 0.00</h4>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="withdrawal_date">Withdrawal Date</label>
                        <div class="form-control-wrap">
                            <input type="date" class="form-control" id="withdrawal_date" name="withdrawal_date" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="withdrawal_amount">Withdrawal Amount</label>
                        <div class="form-control-wrap">
                            <input type="number" class="form-control" id="withdrawal_amount" name="withdrawal_amount" step="0.01" min="0.01" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="withdraw_remark">Remark (Optional)</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="withdraw_remark" name="remark" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-group mt-3 text-end">
                        <button type="submit" class="btn btn-lg btn-secondary">Process Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Audit FY Modal -->
<div class="modal fade" id="auditModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Financial Year</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="auditForm">
                    <div class="form-group">
                        <label class="form-label" for="audit_fy">Select Financial Year</label>
                        <div class="form-control-wrap">
                            <select class="form-select form-control" id="audit_fy" name="financial_year" required>
                                <!-- Populated dynamically based on available FYs -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="audit_remark">Auditor Remark (Optional)</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="audit_remark" name="remark" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <em class="icon ni ni-alert-circle"></em>
                        <strong>Warning:</strong> Once a financial year is marked as audited, its records are permanently locked and cannot be edited.
                    </div>
                    <div class="form-group mt-3 text-end">
                        <button type="submit" class="btn btn-lg btn-primary">Confirm & Audit Setup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentEmployeeId = null;
        let ledgerData = [];
        let currentLoanBal = 0;
        let currentSavingsBal = 0;

        // Initialize Select2 if not automatically done by layout
        if($('.js-select2').length > 0) {
            $('.js-select2').select2({
                theme: "bootstrap-5"
            });
        }

        $('#member_id').on('change', function() {
            currentEmployeeId = $(this).val();
            if (currentEmployeeId) {
                loadMemberData(currentEmployeeId);
                // Update URL without reload
                const newUrl = window.location.pathname + '?member_id=' + currentEmployeeId;
                window.history.pushState({path: newUrl}, '', newUrl);
            } else {
                $('#ledger-card').hide();
                $('#action-buttons').hide();
                $('#fy-filter-container').hide();
            }
        });

        // Auto-load if member_id is in URL
        const urlParams = new URLSearchParams(window.location.search);
        const memberIdParam = urlParams.get('member_id');
        if (memberIdParam) {
            $('#member_id').val(memberIdParam).trigger('change');
        }

        function loadMemberData(employeeId) {
            Swal.fire({
                title: 'Loading Data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route('admin.ledger.data') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    employee_id: employeeId
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        ledgerData = response.data;
                        currentLoanBal = response.loan_balance;
                        currentSavingsBal = response.savings_balance;
                        
                        populateFyFilter(ledgerData);
                        renderTable(ledgerData);
                        
                        $('#ledger-card').show();
                        $('#action-buttons').show();
                        $('#fy-filter-container').show();
                        
                        if (response.loan_active) {
                            $('#btn-settle-loan').show();
                        } else {
                            $('#btn-settle-loan').hide();
                        }

                        populateAuditDropdown(ledgerData);
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
            const tbody = $('#ledger-body');
            tbody.empty();

            const selectedFy = $('#filter_fy').val() || 'ALL';
            let filteredData = data;
            if (selectedFy !== 'ALL') {
                filteredData = data.filter(row => row.fy === selectedFy);
            }

            if (filteredData.length === 0) {
                tbody.append('<tr><td colspan="11" class="text-center p-4">No financial records found for the selected financial year</td></tr>');
                return;
            }

            filteredData.forEach(function(row) {
                let statusBadge = row.is_locked 
                    ? '<span class="badge badge-dim bg-success rounded-pill"><em class="icon ni ni-lock-alt-fill"></em> Locked</span>'
                    : (row.is_audited ? '<span class="badge badge-dim bg-info rounded-pill"><em class="icon ni ni-lock-alt-fill"></em> FY Audited</span>' : '<span class="badge badge-dim bg-warning rounded-pill">Unlocked</span>');
                
                let isReadOnly = row.is_locked || row.is_audited;

                let actionBtns = '';
                if (!isReadOnly) {
                    actionBtns = `
                        <button class="btn btn-sm btn-icon btn-primary edit-month-btn me-1" 
                            data-month="${row.date}" 
                            data-display="${row.display_date}" 
                            data-prin="${row.emi_principal}" 
                            data-int="${row.emi_interest}"
                            data-extra="${row.emi_extra}"
                            data-share="${row.share_sub}"
                            data-sav="${row.savings_cont}"
                            data-withdraw="${row.savings_withdrawal}"
                            data-remark="${row.remark || ''}"
                            title="Edit Record"><em class="icon ni ni-edit"></em></button>
                        <button class="btn btn-sm btn-icon btn-warning lock-month-btn" 
                            data-month="${row.date}" 
                            data-display="${row.display_date}"
                            title="Lock Row"><em class="icon ni ni-lock-alt-fill"></em></button>
                    `;
                } else {
                    actionBtns = '<span class="text-soft"><em class="icon ni ni-lock"></em></span>';
                }

                let tr = `
                    <tr class="${row.is_locked ? 'table-light' : ''}">
                        <td class="fw-bold">${row.display_date}</td>
                        <td class="text-end text-primary">₹ ${parseFloat(row.emi_principal).toFixed(2)}</td>
                        <td class="text-end text-danger">₹ ${parseFloat(row.emi_interest).toFixed(2)}</td>
                        <td class="text-end text-warning">₹ ${parseFloat(row.emi_extra).toFixed(2)}</td>
                        <td class="text-end fw-bold">₹ ${parseFloat(row.emi_total).toFixed(2)}</td>
                        <td class="text-end text-success">₹ ${parseFloat(row.share_sub).toFixed(2)}</td>
                        <td class="text-end text-info">
                            ${parseFloat(row.savings_cont) > 0 ? `<div>₹ ${parseFloat(row.savings_cont).toFixed(2)}</div>` : ''}
                            ${parseFloat(row.savings_withdrawal) > 0 ? `<div class="text-danger">-₹ ${parseFloat(row.savings_withdrawal).toFixed(2)}</div>` : ''}
                            ${parseFloat(row.savings_cont) == 0 && parseFloat(row.savings_withdrawal) == 0 ? '₹ 0.00' : ''}
                        </td>
                        <td class="text-end fw-bold">₹ ${parseFloat(row.remaining_loan).toFixed(2)}</td>
                        <td class="small text-soft">${row.remark || '-'}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-end">${actionBtns}</td>
                    </tr>
                `;
                tbody.append(tr);
            });
        }

        function populateFyFilter(data) {
            const select = $('#filter_fy');
            select.empty();
            
            let uniqueFys = [...new Set(data.map(item => item.fy))];
            uniqueFys.sort(function(a, b) {
                return b.localeCompare(a);
            });

            select.append('<option value="ALL">All Years</option>');
            uniqueFys.forEach(fy => {
                select.append(`<option value="${fy}">${fy}</option>`);
            });

            if (uniqueFys.length > 0) {
                select.val(uniqueFys[0]);
            } else {
                select.val('ALL');
            }
        }

        $('#filter_fy').on('change', function() {
            renderTable(ledgerData);
        });

        function populateAuditDropdown(data) {
            const select = $('#audit_fy');
            select.empty();
            let uniqueFys = [...new Set(data.map(item => item.fy))];
            
            uniqueFys.forEach(fy => {
                let isAlreadyAudited = data.some(item => item.fy === fy && item.is_audited);
                if (!isAlreadyAudited) {
                    select.append(`<option value="${fy}">${fy}</option>`);
                }
            });

            if(select.children('option').length === 0) {
                select.append('<option value="">All active years audited</option>');
                $('#btn-audit-year').prop('disabled', true);
            } else {
                $('#btn-audit-year').prop('disabled', false);
            }
        }

        // Handle Edit Click
        $(document).on('click', '.edit-month-btn', function() {
            let month = $(this).data('month');
            let ds = $(this).data('display');
            let prin = $(this).data('prin');
            let int = $(this).data('int');
            let extra = $(this).data('extra');
            let share = $(this).data('share');
            let sav = $(this).data('sav');
            let withdraw = $(this).data('withdraw');
            let remark = $(this).data('remark');

            $('#edit_employee_id').val(currentEmployeeId);
            $('#edit_month').val(month);
            $('#display_edit_month').val(ds);
            $('#edit_emi_principal').val(prin);
            $('#edit_emi_interest').val(int);
            $('#edit_emi_extra').val(extra);
            $('#edit_share_sub').val(share);
            $('#edit_savings_cont').val(sav);
            $('#edit_savings_withdrawal').val(withdraw);
            $('#edit_remark').val(remark);

            $('#editModal').modal('show');
        });

        // Submit Edit Form
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: '{{ route('admin.ledger.update-month') }}',
                method: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    if(res.success) {
                        $('#editModal').modal('hide');
                        Swal.fire('Updated!', res.message, 'success');
                        loadMemberData(currentEmployeeId);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update Record');
                }
            });
        });

        // Handle Lock Click
        $(document).on('click', '.lock-month-btn', function() {
            let month = $(this).data('month');
            let ds = $(this).data('display');

            Swal.fire({
                title: 'Lock Monthly Record?',
                text: `Are you sure you want to lock the record for ${ds}? Once locked, it cannot be edited again.`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Enter lock remark (optional)...',
                showCancelButton: true,
                confirmButtonColor: '#e85347',
                confirmButtonText: 'Yes, Lock it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.ledger.lock-month') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: currentEmployeeId,
                            month: month,
                            remark: result.value
                        },
                        success: function(res) {
                            if(res.success) {
                                Swal.fire('Locked!', res.message, 'success');
                                loadMemberData(currentEmployeeId);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Setup Settle Modal
        $('#settleModal').on('show.bs.modal', function() {
            $('#display_loan_balance').text('₹ ' + parseFloat(currentLoanBal).toFixed(2));
            $('#display_savings_balance').text('₹ ' + parseFloat(currentSavingsBal).toFixed(2));
            
            // Auto calculate a default suggestion
            let suggestedSavings = Math.min(currentLoanBal, currentSavingsBal);
            let suggestedExtra = currentLoanBal - suggestedSavings;
            if(suggestedExtra < 0) suggestedExtra = 0;

            $('#amount_from_savings').attr('max', currentSavingsBal).val(suggestedSavings.toFixed(2));
            $('#extra_amount').val(suggestedExtra.toFixed(2));
            updateTotalPayment();
        });

        $('#amount_from_savings, #extra_amount').on('input', updateTotalPayment);

        function updateTotalPayment() {
            let sav = parseFloat($('#amount_from_savings').val()) || 0;
            let ext = parseFloat($('#extra_amount').val()) || 0;
            $('#display_total_payment').text('₹ ' + (sav + ext).toFixed(2));
        }

        // Submit Settle Form
        $('#settleForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            
            let sav = parseFloat($('#amount_from_savings').val()) || 0;
            let ext = parseFloat($('#extra_amount').val()) || 0;
            let total = sav + ext;

            if (total <= 0) {
                Swal.fire('Warning', 'Total payment must be greater than zero.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Confirm Payment',
                text: `You are processing a payment of ₹${total.toFixed(2)}. This will deduct ₹${sav.toFixed(2)} from savings and accept ₹${ext.toFixed(2)} in external funds.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Process it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    
                    $.ajax({
                        url: '{{ route('admin.ledger.settle-loan') }}',
                        method: 'POST',
                        data: $(this).serialize() + '&_token={{ csrf_token() }}&employee_id=' + currentEmployeeId,
                        success: function(res) {
                            if(res.success) {
                                $('#settleModal').modal('hide');
                                Swal.fire('Successful!', res.message, 'success');
                                loadMemberData(currentEmployeeId);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server Error', 'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).text('Process Payment');
                        }
                    });
                }
            });
        });

        // Setup Withdraw Modal
        $('#withdrawModal').on('show.bs.modal', function() {
            $('#display_withdraw_savings_balance').text('₹ ' + parseFloat(currentSavingsBal).toFixed(2));
            $('#withdrawal_amount').attr('max', currentSavingsBal).val('');
            $('#withdrawal_date').val(new Date().toISOString().split('T')[0]); // Default to today
            $('#withdraw_remark').val('');
        });

        // Submit Withdraw Form
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            
            let amt = parseFloat($('#withdrawal_amount').val()) || 0;

            if (amt <= 0 || amt > currentSavingsBal) {
                Swal.fire('Warning', 'Invalid withdrawal amount or insufficient balance.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Confirm Withdrawal',
                text: `You are withdrawing ₹${amt.toFixed(2)} from savings.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Withdraw!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    
                    $.ajax({
                        url: '{{ route('admin.ledger.withdraw-savings') }}',
                        method: 'POST',
                        data: $(this).serialize() + '&_token={{ csrf_token() }}&employee_id=' + currentEmployeeId,
                        success: function(res) {
                            if(res.success) {
                                $('#withdrawModal').modal('hide');
                                Swal.fire('Successful!', res.message, 'success');
                                loadMemberData(currentEmployeeId);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server Error', 'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).text('Process Withdrawal');
                        }
                    });
                }
            });
        });

        // Submit Audit Form
        $('#auditForm').on('submit', function(e) {
            e.preventDefault();
            if (!$('#audit_fy').val()) return;

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Auditing...');

            $.ajax({
                url: '{{ route('admin.ledger.audit-year') }}',
                method: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}&employee_id=' + currentEmployeeId,
                success: function(res) {
                    if(res.success) {
                        $('#auditModal').modal('hide');
                        Swal.fire('Audited!', res.message, 'success');
                        loadMemberData(currentEmployeeId);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server Error', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Confirm & Audit Setup');
                }
            });
        });

    });
</script>
@endpush
