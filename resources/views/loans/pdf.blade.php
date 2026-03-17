<!DOCTYPE html>
<html>

<head>
    <title>Loan Statement #LN-{{ str_pad($loan->account_id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2,
        .header h4 {
            margin: 5px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
        }

        table.schedule {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.schedule th,
        table.schedule td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        table.schedule th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Company Loan Management</h2>
        <h4>Loan Statement & Amortization Schedule</h4>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Loan No:</strong> LN-{{ str_pad($loan->account_id, 4, '0', STR_PAD_LEFT) }}</td>
            <td><strong>Principal:</strong> ₹{{ number_format($loan->loanAttributes->principal_amount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Customer ID:</strong> {{ $loan->employee->empCode ?? 'N/A' }}</td>
            <td><strong>Start Date:</strong>
                {{ $loan->loanAttributes->start_date ? $loan->loanAttributes->start_date->format('d M Y') : $loan->opened_date->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <td><strong>Interest Rate:</strong> {{ $loan->loanAttributes->interest_rate ?? 0 }}% pa</td>
            <td><strong>Tenure:</strong> {{ $loan->loanAttributes->tenure_months ?? 0 }} Months</td>
        </tr>
    </table>

    <table class="schedule">
        <thead>
            <tr>
                <th>No</th>
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
                    <td>{{ $inst->due_date->format('d/m/Y') }}</td>
                    <td>₹{{ number_format($inst->principal_due, 2) }}</td>
                    <td>₹{{ number_format($inst->interest_due, 2) }}</td>
                    <td>₹{{ number_format($inst->total_due, 2) }}</td>
                    <td>₹{{ number_format($inst->balance_after, 2) }}</td>
                    <td>{{ strtoupper($inst->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
