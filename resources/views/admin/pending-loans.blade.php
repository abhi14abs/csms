@extends('layouts.app')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between align-items-center">

            <div class="nk-block-head-content">
                <h4 class="nk-block-title mb-1">Pending Loan Applications</h4>
                <div class="nk-block-des">
                    <p class="mb-0">Review and approve or reject pending member loan requests.</p>
                </div>
            </div>

        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init-export nowrap table" data-export-title="Export">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingLoans as $loan)
                            <tr>
                                <td>#{{ $loan->account_id }}</td>
                                <td>{{ $loan->employee->name }}</td>
                                <td>₹ {{ number_format($loan->loanAttributes?->principal_amount, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.approve-loan', $loan->account_id) }}"
                                        style="display:inline" class="action-form">
                                        @csrf
                                        <button type="button" class="btn btn-success btn-sm"
                                            onclick="handleLoanAction(event, 'approve', this.closest('form'))">
                                            <em class="icon ni ni-check"></em><span>Approve</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reject-loan', $loan->account_id) }}"
                                        style="display:inline" class="action-form">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="handleLoanAction(event, 'reject', this.closest('form'))">
                                            <em class="icon ni ni-cross"></em><span>Reject</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- .card-preview -->
    </div> <!-- nk-block -->

    <script>
        function handleLoanAction(e, action, form) {
            e.preventDefault();
            let title = action === 'approve' ? 'Approve Loan' : 'Reject Loan';
            let confirmBtn = action === 'approve' ? 'Yes, Approve' : 'Yes, Reject';
            let confirmColor = action === 'approve' ? '#1ee0ac' : '#e85347';

            Swal.fire({
                title: title,
                text: "Please enter your administrative remarks below:",
                input: 'textarea',
                inputPlaceholder: 'Type your remarks here...',
                inputAttributes: {
                    'aria-label': 'Type your remarks here'
                },
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#854fff',
                confirmButtonText: confirmBtn,
                preConfirm: (remarks) => {
                    if (!remarks && action === 'reject') {
                        Swal.showValidationMessage('Remarks are required for rejection')
                    }
                    return remarks;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", "remarks");
                    input.setAttribute("value", result.value);
                    form.appendChild(input);
                    form.submit();
                }
            });
        }
    </script>
@endsection
