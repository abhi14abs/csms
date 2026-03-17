@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><a href="{{ route('loans.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Loans</a> /
                LN-{{ str_pad($loan->account_id, 4, '0', STR_PAD_LEFT) }}</h3>
            <h5 class="text-muted">Employee Name: {{ $loan->employee->name }}</h5>
            <div>
                <a href="{{ route('loans.export.excel', $loan->account_id) }}" class="btn btn-outline-success me-2"><i
                        class="fas fa-file-excel"></i> Excel</a>
                <a href="{{ route('loans.export.pdf', $loan->account_id) }}" class="btn btn-outline-danger me-2"><i
                        class="fas fa-file-pdf"></i> PDF</a>
                <button class="btn btn-success text-white fw-bold me-2" data-bs-toggle="modal"
                    data-bs-target="#paymentModal">
                    <i class="fas fa-rupee-sign me-1"></i> Make Payment
                </button>
            </div>
        </div>
        <!-- Loan Info Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Principal Amount</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹
                            {{ number_format($loan->loanAttributes->principal_amount ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Interest Rate / Tenure</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $loan->loanAttributes->interest_rate ?? 0 }}%
                            pa /
                            {{ $loan->loanAttributes->tenure_months ?? 0 }} Mo</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹ {{ number_format($totalPaid, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Outstanding Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹ {{ number_format($balance, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amortization Schedule -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Amortization Schedule</h6>
                <h6 class="m-0 font-weight-bold">EMI: ₹ {{ number_format($loan->loanAttributes->emi_amount ?? 0, 2) }}</h6>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init-export wrap table" data-export-title="Export">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Due Date</th>
                                <th>Principal Due</th>
                                <th>Interest Due</th>
                                <th>Total Due</th>
                                <th>Balance After</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->installments as $inst)
                                <tr>
                                    <td>{{ $inst->installment_no }}</td>
                                    <td>{{ $inst->due_date->format('d M, Y') }}</td>
                                    <td>₹ {{ number_format($inst->principal_due, 2) }}</td>
                                    <td>₹ {{ number_format($inst->interest_due, 2) }}</td>
                                    <td><strong>₹ {{ number_format($inst->total_due, 2) }}</strong></td>
                                    <td>₹ {{ number_format($inst->balance_after, 2) }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            if ($inst->status == 'paid') {
                                                $badgeClass = 'bg-success';
                                            }
                                            if ($inst->status == 'pending') {
                                                $badgeClass = 'bg-primary';
                                            }
                                            if ($inst->status == 'overdue') {
                                                $badgeClass = 'bg-danger';
                                            }
                                            if ($inst->status == 'partial') {
                                                $badgeClass = 'bg-warning text-dark';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ strtoupper($inst->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Payments History -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-1"></i> Recent Payments History</h6>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init-export wrap table" data-export-title="Export">
                        <thead class="table-light">
                            <tr>
                                <th>Payment Date</th>
                                <th>Amount Paid</th>
                                <th>Breakdown (Prin + Int + Pen)</th>
                                <th>Extra Paid</th>
                                <th>Balance After</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                    <td><strong>₹ {{ number_format($payment->amount_paid, 2) }}</strong></td>
                                    <td>
                                        <small class="text-muted">
                                            P: ₹{{ number_format($payment->principal_component, 2) }} <br>
                                            I: ₹{{ number_format($payment->interest_component, 2) }} <br>
                                            Pen: ₹{{ number_format($payment->penalty_component, 2) }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($payment->extra_payment > 0)
                                            <span class="badge bg-warning text-dark">₹
                                                {{ number_format($payment->extra_payment, 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary">None</span>
                                        @endif
                                    </td>
                                    <td>₹ {{ number_format($payment->balance_after, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="paymentForm">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Record Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="paymentAlert" class="alert d-none"></div>

                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount Paid (₹)</label>
                            <input type="number" step="0.01" name="amount_paid" class="form-control"
                                value="{{ round($loan->loanAttributes->emi_amount ?? 0, 2) }}" required>
                            <small class="text-muted">Standard EMI is ₹
                                {{ number_format($loan->loanAttributes->emi_amount ?? 0, 2) }}.
                                Overpaying will trigger prepayment handling.</small>
                        </div>

                        <div class="mb-3" id="prepaymentModeGroup">
                            <label class="form-label">Prepayment Mode (If Overpaying)</label>
                            <select name="prepayment_mode" class="form-select">
                                <option value="reduce_tenure">Reduce Tenure (Keep EMI same)</option>
                                <option value="reduce_emi">Reduce EMI (Keep Tenure same)</option>
                            </select>
                            <small class="text-muted">Determines how the remaining schedule is recalculated.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitPayment">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmitPayment');
            const alertBox = document.getElementById('paymentAlert');
            btn.disabled = true;
            btn.innerHTML = 'Processing... <i class="fas fa-spinner fa-spin"></i>';
            alertBox.classList.add('d-none');

            const formData = new FormData(this);

            const loanId = '{{ $loan->account_id }}';
            fetch('/admin/loans/' + loanId + '/pay', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message || 'Server error occurred');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alertBox.classList.remove('d-none', 'alert-danger');
                        alertBox.classList.add('alert-success');
                        alertBox.innerText = data.message;
                        setTimeout(() => {
                            location.reload(); // Reload to see new schedule
                        }, 1000);
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                    alertBox.innerText = error.message;
                    btn.disabled = false;
                    btn.innerHTML = 'Submit Payment';
                });
        });
    </script>
@endpush
