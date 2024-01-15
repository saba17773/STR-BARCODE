<?php

namespace App\Controllers;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;
use Wattanar\Requesty;
use App\Models\Onhand;
use App\V2\Batch\BatchAPI;

class TestController
{
	private $batchApi = null;

	public function __construct()
	{
		$this->batchApi = new BatchAPI();
	}

	public function testAPI()
	{
		$week = $this->batchApi->getManualBatch('2021-04-09 16:58:31', 'Q-0044461', 'A06');
		// echo $this->batchApi->getBatch("2021-01-03 19:00", true);
		echo $week;
		die();
	}

	public function testDateDiff()
	{
		exit;
		try {
			if (is_infinite(1 / 0) === true) {
				echo 0;
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}

		exit;
		$datetime1 = date_create('2017-05-22 08:00:00');
		$datetime2 = date_create('2017-05-22 08:02:59');
		$interval = date_diff($datetime1, $datetime2);

		echo (float) $interval->format('%i.%s');
	}

	public function index()
	{
		exit;
		$conn = DB::connect();
		if (sqlsrv_begin_transaction($conn) === false) {
			exit('transaction failed!');
		}
		// $all = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT * FROM InventTable 
		// 	WHERE Status = 1
		// 	and CuringDate <= '2017-05-09 23:59:59'
		// 	and CuringDate >= '2017-05-07 00:00:00'
		// 	and WarehouseID = 4"
		// );
		// 
		$__code = 'BT72';
		$__batch = '2017-20';

		$all = Sqlsrv::queryArray(
			$conn,
			"SELECT * from InventTable 
			where GT_Code  IN (
			'BT87',
			'BT88',
			'CN14',
			'Z089'
			) 
			and Batch = '2017-21'
			and WarehouseID = 1
			and LocationID = 2"
		);
		// 
		// $all = Sqlsrv::queryArray(
		// 	$conn,
		// 		"SELECT * FROM InventTable WHERE GT_Code = 'CN14' 
		// 			AND Barcode IN (
		// 			'I170151945',
		// 			'I170151944',
		// 			'I170151943',
		// 			'I170151942',
		// 			'I170151941',
		// 			'I170151940',
		// 			'I170151939',
		// 			'I170151938'
		// 		)"
		// 	);

		if ($all) {
			foreach ($all as $v) {
				// echo $v["Barcode"] . "<br>";
				$delete_trans = Sqlsrv::delete(
					$conn,
					"DELETE FROM InventTrans 
					WHERE Barcode = ?",
					[
						$v["Barcode"]
					]
				);

				if (!$delete_trans) {
					sqlsrv_rollback($conn);
					exit('error');
				}
			}
		} else {
			sqlsrv_rollback($conn);
			exit('error');
		}

		$n = new Onhand;
		$n->QTY = -1;
		foreach ($all as $a) {
			$n->CodeID = $a["GT_Code"];
			$n->WarehouseID = $a["WarehouseID"];
			$n->LocationID = $a["LocationID"];
			$n->Batch = $a["Batch"];
			$n->Company = $a["Company"];
			if (!$n->update()) {
				sqlsrv_rollback($conn);
				exit('update onhand error');
			}
		}

		sqlsrv_commit($conn);
		exit("successful!");
		// echo "<pre>" . print_r($all, true) . '</pre>';
	}

	public function whrec()
	{
		exit;
		// echo $_SESSION['user_location']; exit;
		$conn = DB::connect();
		$rows = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode, WarehouseTransReceiveDate
			FROM InventTable
			WHERE Barcode IN (
				'I170165565'			
			)"
		);

		foreach ($rows as  $v) {
			// echo $v['Barcode'] . ' / ' .$v['WarehouseTransReceiveDate'] . '<br>';
			$update = Sqlsrv::update(
				$conn,
				"UPDATE InventTable SET WarehouseReceiveDate = ?
				WHERE Barcode = ? AND WarehouseTransReceiveDate = ?",
				[
					$v['WarehouseTransReceiveDate'],
					$v['Barcode'],
					$v['WarehouseTransReceiveDate']
				]
			);

			if ($update) {
				echo "success <br>";
			} else {
				echo "failed <br>";
			}
		}
		// $response = Requesty::post('http://example.com/api/v1/user/save', ['foo' => 'bar']);
	}

	public function changeGreentireCode()
	{
		# code...
	}
}
