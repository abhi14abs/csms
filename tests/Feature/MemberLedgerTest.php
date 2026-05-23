<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\MemberAudit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class MemberLedgerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);
    }

    protected function createTestEmployee(string $empCode = 'EMP999'): Employee
    {
        return Employee::create([
            'empCode' => $empCode,
            'name' => 'John Doe',
            'gender' => 'MALE',
            'category' => 'General',
            'dateOfAppointment' => '2020-01-01',
            'designationAtAppointment' => 'Clerk',
            'designationAtPresent' => 'Clerk',
            'presentPosting' => 'Head Office',
            'dateOfBirth' => '1990-01-01',
            'dateOfRetirement' => '2050-01-01',
            'is_society_member' => 'YES',
        ]);
    }

    public function test_ledger_routes_require_admin(): void
    {
        // 1. Guest
        $response = $this->get(route('admin.ledger.index'));
        $response->assertRedirect(route('login'));

        // 2. Regular User
        $response = $this->actingAs($this->regularUser)->get(route('admin.ledger.index'));
        $response->assertRedirect('/');

        // 3. Admin User
        $response = $this->actingAs($this->adminUser)->get(route('admin.ledger.index'));
        $response->assertStatus(200);
    }

    public function test_get_member_data_groups_by_exact_date(): void
    {
        // Create employee
        $employee = $this->createTestEmployee('EMP001');

        // Create accounts
        $shareAccount = Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'SHARE',
            'current_balance' => 1000,
            'status' => 'Active',
        ]);

        $savingsAccount = Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'SAVINGS',
            'current_balance' => 2000,
            'status' => 'Active',
        ]);

        // Create two transactions on different dates
        Transaction::create([
            'account_id' => $shareAccount->account_id,
            'tx_date' => '2025-04-15',
            'amount' => 500,
            'tx_type' => 'CREDIT',
            'category' => 'SUBSCRIPTION',
            'description' => 'Share Sub 1',
        ]);

        Transaction::create([
            'account_id' => $savingsAccount->account_id,
            'tx_date' => '2025-04-15',
            'amount' => 300,
            'tx_type' => 'CREDIT',
            'category' => 'SUBSCRIPTION',
            'description' => 'Savings Cont 1',
        ]);

        // Transaction on a different date in the same financial year
        Transaction::create([
            'account_id' => $savingsAccount->account_id,
            'tx_date' => '2025-05-20',
            'amount' => 400,
            'tx_type' => 'CREDIT',
            'category' => 'SUBSCRIPTION',
            'description' => 'Savings Cont 2',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.ledger.data'), [
            'employee_id' => $employee->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        $data = $response->json('data');
        $this->assertCount(2, $data); // Should have exactly 2 grouped date entries

        // Validate first record (2025-04-15)
        $this->assertEquals('2025-04-15', $data[0]['date']);
        $this->assertEquals('15 Apr 2025', $data[0]['display_date']);
        $this->assertEquals('2025-2026', $data[0]['fy']);
        $this->assertEquals(500, $data[0]['share_sub']);
        $this->assertEquals(300, $data[0]['savings_cont']);

        // Validate second record (2025-05-20)
        $this->assertEquals('2025-05-20', $data[1]['date']);
        $this->assertEquals('20 May 2025', $data[1]['display_date']);
        $this->assertEquals('2025-2026', $data[1]['fy']);
        $this->assertEquals(0, $data[1]['share_sub']);
        $this->assertEquals(400, $data[1]['savings_cont']);
    }

    public function test_update_month_data_updates_exact_date(): void
    {
        $employee = $this->createTestEmployee('EMP002');

        $shareAccount = Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'SHARE',
            'current_balance' => 1000,
            'status' => 'Active',
        ]);

        $savingsAccount = Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'SAVINGS',
            'current_balance' => 2000,
            'status' => 'Active',
        ]);

        // Create transaction to be updated
        Transaction::create([
            'account_id' => $shareAccount->account_id,
            'tx_date' => '2025-04-15',
            'amount' => 500,
            'tx_type' => 'CREDIT',
            'category' => 'SUBSCRIPTION',
            'description' => 'Original Share Sub',
        ]);

        // Update the record via the endpoint
        $response = $this->actingAs($this->adminUser)->post(route('admin.ledger.update-month'), [
            'employee_id' => $employee->id,
            'month' => '2025-04-15', // Passed exact date
            'share_sub' => 600,
            'savings_cont' => 250,
            'emi_principal' => 0,
            'emi_interest' => 0,
            'emi_extra' => 0,
            'remark' => 'Test Adjustment',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify databases
        $updatedShareTx = Transaction::where('account_id', $shareAccount->account_id)
            ->whereDate('tx_date', '2025-04-15')
            ->first();
        $this->assertEquals(600, $updatedShareTx->amount);
        $this->assertStringContainsString('Test Adjustment', $updatedShareTx->description);

        $newSavingsTx = Transaction::where('account_id', $savingsAccount->account_id)
            ->whereDate('tx_date', '2025-04-15')
            ->first();
        $this->assertNotNull($newSavingsTx);
        $this->assertEquals(250, $newSavingsTx->amount);
    }

    public function test_lock_month_locks_containing_month_and_blocks_edits(): void
    {
        $employee = $this->createTestEmployee('EMP003');

        $shareAccount = Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'SHARE',
            'current_balance' => 1000,
            'status' => 'Active',
        ]);

        // 1. Lock the month containing 2025-04-15 (April 2025)
        $response = $this->actingAs($this->adminUser)->post(route('admin.ledger.lock-month'), [
            'employee_id' => $employee->id,
            'month' => '2025-04-15',
            'remark' => 'Locking April 2025',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify MemberAudit entry was created for 2025-04
        $audit = MemberAudit::where('employee_id', $employee->id)
            ->where('month', '2025-04')
            ->first();
        $this->assertNotNull($audit);
        $this->assertEquals('Locking April 2025', $audit->remark);

        // 2. Try to update a transaction on a different date but within the same locked month (2025-04-20)
        $response = $this->actingAs($this->adminUser)->post(route('admin.ledger.update-month'), [
            'employee_id' => $employee->id,
            'month' => '2025-04-20',
            'share_sub' => 100,
            'savings_cont' => 100,
            'emi_principal' => 0,
            'emi_interest' => 0,
            'emi_extra' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Cannot edit data for a locked/audited month or financial year.');
    }
}
