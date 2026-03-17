<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoanInstallment;
use Carbon\Carbon;

class UpdateOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates pending loan installments to overdue if past due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $updatedCount = LoanInstallment::where('status', 'pending')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        // Also check for partial payments that are past their due date
        // Note: business logic might keep partial as partial or change to overdue. 
        // We will change strictly pending to overdue, but let's do partial -> overdue too
        // if the balance is still not paid off.
        $partialCount = LoanInstallment::where('status', 'partial')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        $total = $updatedCount + $partialCount;

        $this->info("Successfully updated {$total} loan installments to overdue status.");
    }
}
