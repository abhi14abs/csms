<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportOldShares extends Command
{
	protected $signature = 'app:import-old-shares';
	protected $description = 'Import old share data till April 2025';

	public function handle()
	{
		// Fixed format: EMPCODE NAME AMOUNT (not NAME AMOUNT EMPCODE)
		$rawData = <<<DATA
121412 RAMANUJAM SIVAKUMAR 10000.00
121413 VELUCHAMY K 10000.00
121415 SUBIAH P 10000.00
121419 PALANICHAMY V 10000.00
121423 RAVI A 10000.00
121424 BARMAN JAYADRATHA DEV 10000.00
121426 KAUSHAL K.C. 10000.00
121428 PONNUSWAMY V. 10000.00
121435 KORAVI SANJAY V 10000.00
121438 N J MHETRAS 10000.00
121440 BANGAR GANESH P 10000.00
121442 GAGAN DEEP 10000.00
121443 PANKAJ KUMAR 10000.00
121445 SHKLA BRIJESH KUMAR 10000.00
121447 A NITYAKUMAR 10000.00
121448 AMBRISH SINGH 10000.00
121449 J PARAMESWARAN 10000.00
121450 SAINI SHYAMLAL 10000.00
121451 SHANMUGA SUNDARAM C 10000.00
121452 ROUTRAY SAGAR KUMAR 10000.00
121453 GOWRISANKAR S 10000.00
121454 ABHISHEKH SHARMA 10000.00
121455 CHIRAG DHINGRA 10000.00
121457 SHINGARE LAXMAN M 10000.00
121460 VARMA RAHUL SWARUP 10000.00
121461 DHAVAL DINESH KUMAR 10000.00
121462 PATIL NAMRATA G 10000.00
121463 PRADEEPKUMAR S. 10000.00
121464 MONIRAL ISLAM 10000.00
121465 K R DURAIRAJ 10000.00
121467 DEVENDAR SINGH MAHURA 10000.00
121468 ANKUR KUMAR 10000.00
121472 SHIVRAJ MARGAM PRAKASH 10000.00
121473 CHALLA SATHIESH KUMAR 10000.00
121474 SAGAR KUMAR BEHRA 10000.00
121476 MOHAMMED. TARIQ ISHTIAQ 10000.00
121478 RAVINDRA NETAJI KORE 10000.00
121479 SHRADDHA KISHORRAO AKAR 10000.00
121480 DAMAYANTI MEHER 10000.00
121481 VIKAS KUMAR GAUTAM 10000.00
121482 NITESH RAWAT 10000.00
121483 RINITH K P 10000.00
121484 KESHAVA MURTHY 10000.00
121485 RAMNARESH MEENA 10000.00
122013 CHAUHAN SUNIL S. 10000.00
122014 KALPANA M SOLANKI 60000.00
122015 VENKATA RAMANA GEDELA 10000.00
122016 TUSHAR LAXMIKANT BENDRE 10000.00
124058 BHUVANESHWARI J 10000.00
124059 SELVAM DEEPIKA 10000.00
124060 SINGH NARENDER 10000.00
124061 POONAM MALIK 10000.00
125048 KOLI SUDHAKAR PANDIT 10000.00
125049 CHAVAN MANGESH A 10000.00
125050 SANAP SANTOSH KHANDU 10000.00
125052 GANGURE SIDDHARTH C 10000.00
125053 KHOTARE GANESH SHANKAR 10000.00
125054 WORLIKAR VEDANT NAVNIT 10000.00
126222 ADVANI LAJWANTI B -700.00
126242 KUMAR SHIV 10000.00
126260 CHINDARKAR VANDANA VILAS 10000.00
126261 HIREMATH V.C. 10000.00
126284 PILLAI USHADEVI 10000.00
126286 VINOD KUMAR 10000.00
126289 ASHOK KUMAR SHARMA 10000.00
126291 SINGH JAIPRAKASH 10000.00
126300 TAMHANE SAVITRI S 10000.00
126301 TUNGATKAR DATTATRAY B 10000.00
126302 NAVGHARE GAUTAMI ROHIDAS 10000.00
126312 PATIL SUNITA R 10000.00
126316 KOLI JAYA KISHORE 10000.00
126318 TIWARI RAVINDERKUMAR 10000.00
126320 MUGDHA M.CHAVAN (KHANVILKAR) 10000.00
126322 PATKAR PRANALI VIJAY 10000.00
126323 USHA MAHADEV GAONKAR 10000.00
126324 A NALINI 10000.00
126328 PRATIK M BHAGAL 10000.00
127074 NIKHALJE PRALHAD ANANDA -500.00
127110 NYAYNIT ARVIND KISAN 10000.00
127111 PANDEY SUBHASH M 10000.00
127116 NAIK ASHOK CHANDRA. 10000.00
127124 SAH BALESHWAR 10000.00
127126 SAWANT VISHAKHA M 10000.00
127127 CHANDRASHEKHARAN A 10000.00
127129 JADHAV SACHIN T 10000.00
127130 DODDI PRASAD BALAPPA 10000.00
127131 NIKHALJE SUBHASH A. 10000.00
127132 KRUSHNAKANT B. TATTU 10000.00
127133 SAVITA P 10000.00
127136 SAWANT MANISH ARJUN 10000.00
127139 SUSHMA DEVI 10000.00
128042 SEKHAR S 10000.00
128047 JADHAV SANTOSH B 10000.00
128051 PARTE RAMCHANDRA G. 10000.00
128052 SANJAY VASANT JADHAV 10000.00
128060 LAKASHMI NARAYAN GATTU 10000.00
128061 SANDIS NARESH DHUNDA 10000.00
128062 SAMIULLA A 10000.00
128063 SHARMA BADRI NARAYAN 10000.00
128064 NYAYNIT SANDESH DYANU 10000.00
128072 JADHAV VISHWANATH J. 10000.00
128073 LINGALA JYOTHI 10000.00
128090 MOHITE PRASHANT R. 10000.00
128540 VITHAL B PARTE( JADHAV ) 10000.00
129075 KUDALGIKAR M.C. 10000.00
129080 PONNUSAMY P 10000.00
129086 SINGH SHRINIWAS 10000.00
129087 KATHIRAVAN M 10000.00
129089 RAO Y.V. 10000.00
129093 SHARMA VIVEK KUMAR 10000.00
129096 DASH PRADIP 10000.00
129099 AHAMED SHAIKH SHAKIL 10000.00
129102 DWIVEDI RAKESH 10000.00
129105 M MADASAMY 10000.00
129107 SUSANTA BEHERA 10000.00
130085 SINGH DHANIRAM 10000.00
130144 TORVI KISHORE V 10000.00
130189 WASNIK PRAKASH NANAJI 10000.00
130206 KHANDARE PRASENNAJIT PUNDALI 10000.00
130207 KAMBLE SANDHYARANI A 10000.00
130208 DHIMAAN S K 10000.00
130211 SIREESHA K 10000.00
130212 SAMUEL J 10000.00
130214 GOPALAKRISHNAN L. 10000.00
130215 SUNDARI R 10000.00
130217 GAIKWAD SANGEETA RAJESH 10000.00
130218 MAJI JHARNA 10000.00
130219 BALAJI S 10000.00
130224 BASKARAN K 1000.00
130225 SAXENA SANJEEV 10000.00
130227 KRISHNAKUMAR J N 10000.00
130228 MURLIDHARA K S 10000.00
130229 MAPDAR SAUMEN 10000.00
130230 SENTHIL KUMAR K 10000.00
130235 KAMBHAM HARI BABU 10000.00
130237 ELLUR RAMESH 10000.00
130239 SHINDE SANTOSH PANDURANG 10000.00
130243 JOSHI ANITA S. 10000.00
130245 THAKUR SACHIN D. 10000.00
130251 RAMTEKKAR SUDHIR K. 10000.00
130252 KULKARNI J.U. 10000.00
130258 GHOSH PRADIPTENDRA NARAYAN 10000.00
130262 PUSHPA 10000.00
130264 SONTAKKE RAJKUMAR 10000.00
130266 MAMTA JHA 10000.00
130267 PROSENJIT KARMARKAR 10000.00
130269 GEELANI A 10000.00
130270 R JAYARAMAN 10000.00
130271 U BALAKUMAR 10000.00
130273 DHANYA E V 10000.00
130274 VIJAY NANDKUMAR MENKUDALE 10000.00
130275 VIJAYASLKSHMI S 10000.00
130276 ANGAT KUMAR 10000.00
130282 JAYANTI HEMBRAM 10000.00
131055 HARIKRISHNAN A 10000.00
131056 ANITHA P 10000.00
131058 LOPEZ LOVELIN 10000.00
131061 KUMAR RAJEEV 10000.00
131068 DAHIYA RAHUL 10000.00
131073 DAS RUMA 10000.00
131074 MISHRA BAIJUKUMAR 10000.00
132008 PASTE SANDESH G 10000.00
132009 MOHITE PRAVIN P 10000.00
132010 BAGHALE RITESH B 10000.00
135006 DHANDA KARTIKAY 10000.00
135007 CHAUHAN SHILPI 10000.00
135008 R CHANDRAN 10000.00
136004 ROUT TAPAN KUMAR 10000.00
DATA;

		$lines = explode("\n", trim($rawData));

		// Additional data with proper format
		$additionalData = [
			['empCode' => '130110', 'name' => 'BALU R', 'amount' => 1000.00],
			['empCode' => '126211', 'name' => 'GURAV SHAILAJA S.', 'amount' => 1000.00],
			['empCode' => '127079', 'name' => 'SALAK RAM', 'amount' => 1000.00],
			['empCode' => '126321', 'name' => 'AMIT H SATAM', 'amount' => 7400.00],
			['empCode' => '135009', 'name' => 'GOVIND PRASAD', 'amount' => 10000.00],
			['empCode' => '130277', 'name' => 'VIJAY BABU GAUR', 'amount' => 10000.00],
			['empCode' => '126325', 'name' => 'SANGITA A. KULKARNI', 'amount' => 8100.00],
		];

		DB::beginTransaction();
		try {
			$cutoffDate = Carbon::create(2025, 4, 30);
			$count = 0;
			$skipped = [];
			$negativeAmounts = [];

			foreach ($lines as $line) {
				if (trim($line) === '') continue;

				$parts = preg_split('/\s+/', trim($line));
				if (count($parts) < 3) {
					$this->warn("Invalid line format: {$line}");
					continue;
				}

				// Correct order: EMPCODE, NAME, AMOUNT
				$empCode = trim($parts[0]);
				$amount = floatval(end($parts));

				// Check for negative amounts
				// if ($amount < 0) {
				// 	$negativeAmounts[] = ['empCode' => $empCode, 'amount' => $amount];
				// 	$this->warn("Negative amount detected for {$empCode}: {$amount}. Skipping.");
				// 	continue;
				// }

				$employee = Employee::withoutGlobalScope('society_member')
					->where('empCode', $empCode)
					->first();

				if (!$employee) {
					$skipped[] = $empCode;
					$this->warn("Employee {$empCode} not found. Skipping.");
					continue;
				}

				if ($employee->is_society_member !== 'YES') {
					$employee->update(['is_society_member' => 'YES']);
				}

				$account = Account::firstOrCreate([
					'employee_id' => $employee->id,
					'account_type' => 'SHARE',
				], [
					'opened_date' => $cutoffDate,
					'status' => 'ACTIVE',
					'current_balance' => 0
				]);

				$account->update([
					'current_balance' => $amount
				]);

				// Check if transaction already exists to avoid duplicates
				$existingTx = Transaction::where('account_id', $account->account_id)
					->where('category', 'OPENING BALANCE')
					->where('description', 'Opening Share Balance till April 2025')
					->first();

				if (!$existingTx) {
					Transaction::create([
						'account_id' => $account->account_id,
						'tx_date' => $cutoffDate,
						'amount' => $amount,
						'tx_type' => 'CREDIT',
						'category' => 'OPENING BALANCE',
						'description' => 'Opening Share Balance till April 2025',
					]);
				}

				$this->info("Imported Share for {$empCode} | Amount: {$amount}");
				$count++;
			}

			// Process additional data
			foreach ($additionalData as $data) {
				$employee = Employee::withoutGlobalScope('society_member')
					->where('empCode', $data['empCode'])
					->first();

				if (!$employee) {
					$skipped[] = $data['empCode'];
					$this->warn("Employee {$data['empCode']} not found. Skipping.");
					continue;
				}

				if ($employee->is_society_member !== 'YES') {
					$employee->update(['is_society_member' => 'YES']);
				}

				$account = Account::firstOrCreate([
					'employee_id' => $employee->id,
					'account_type' => 'SHARE',
				], [
					'opened_date' => $cutoffDate,
					'status' => 'ACTIVE',
					'current_balance' => 0
				]);

				$account->update([
					'current_balance' => $data['amount']
				]);

				$existingTx = Transaction::where('account_id', $account->account_id)
					->where('category', 'OPENING BALANCE')
					->where('description', 'Opening Share Balance till April 2025')
					->first();

				if (!$existingTx) {
					Transaction::create([
						'account_id' => $account->account_id,
						'tx_date' => $cutoffDate,
						'amount' => $data['amount'],
						'tx_type' => 'CREDIT',
						'category' => 'OPENING BALANCE',
						'description' => 'Opening Share Balance till April 2025',
					]);
				}

				$this->info("Imported Share for {$data['empCode']} | Amount: {$data['amount']}");
				$count++;
			}

			DB::commit();
			$this->info("Successfully imported {$count} share accounts.");

			if (!empty($skipped)) {
				$this->warn("Skipped employees: " . implode(', ', array_unique($skipped)));
			}

			if (!empty($negativeAmounts)) {
				$this->warn("Negative amounts skipped: " . json_encode($negativeAmounts));
			}
		} catch (\Exception $e) {
			DB::rollBack();
			$this->error("Failed to import shares: " . $e->getMessage());
		}
	}
}
