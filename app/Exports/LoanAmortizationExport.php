<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LoanAmortizationExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $loan;

    public function __construct(Account $loan)
    {
        $this->loan = $loan;
    }

    public function collection()
    {
        return $this->loan->installments()->orderBy('installment_no', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Installment No',
            'Due Date',
            'Principal Due (INR)',
            'Interest Due (INR)',
            'Total Due (INR)',
            'Balance After (INR)',
            'Status'
        ];
    }

    public function map($installment): array
    {
        return [
            $installment->installment_no,
            $installment->due_date->format('d M, Y'),
            $installment->principal_due,
            $installment->interest_due,
            $installment->total_due,
            $installment->balance_after,
            strtoupper($installment->status)
        ];
    }
}
