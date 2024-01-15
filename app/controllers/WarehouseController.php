<?php

namespace App\Controllers;

use App\Services\WarehouseService;
use App\Services\BarcodeService;
use App\Services\InventService;
use App\Components\Database;
use App\Components\Security;
use App\V2\Barcode\BarcodeAPI;

class WarehouseController
{

	public function __construct()
	{
		$this->warehouse = new WarehouseService;
	}

	public function all()
	{
		echo $this->warehouse->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "wh_id");
		$description = filter_input(INPUT_POST, "wh_name");
		$type = filter_input(INPUT_POST, "type");

		$result = (new WarehouseService)->create($id, $description, $type);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "Create Successful!"]);
		} else {
			echo json_encode(["status" => 404, "message" => "Create Failed!"]);
		}
	}

	public function incoming()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$barcode_decode = Security::_decode($barcode);

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new InventService)->isExist($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new InventService)->checkItemId($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ยางยังไม่ได้อบ"]));
		}

		if ((new InventService)->checkWarehouseTransReceiveData($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ยังไม่ได้รับเข้า Warehouse Trans"]));
		}

		if ((new InventService)->checkWarehouseReceiveData($barcode) === true) {
			exit(json_encode(["status" => 405, "message" => "มี Barcode อยู่ในระบบแล้ว"]));
		}

		$res = (new WarehouseService)->receiveToWarehouse($barcode_decode);


		if ($res == 200) {
			exit(json_encode([
				"status" => 200,
				"message" => "Successful",
				"curecode" => (new BarcodeAPI)->getCureCode($barcode),
				'batch' => (new BarcodeAPI)->getBatch($barcode)
			]));
		} else {
			exit(json_encode([
				"status" => 404,
				"message" => $res,
				"curecode" => '',
				'batch' => ''
			]));
		}

		echo json_encode([$barcode_decode]);
	}

	public function createType()
	{
		$description = filter_input(INPUT_POST, "description");
		$id = filter_input(INPUT_POST, "_id");

		$result = (new WarehouseService)->createWarehouseType($id, $description);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ทำรายการเสร็จสิ้น"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}

	public function getAllWarehouseType()
	{
		echo (new WarehouseService)->getAllWarehouseType();
	}

	public function deleteType()
	{
		$id = filter_input(INPUT_POST, "id");
		$result = (new WarehouseService)->deleteWarehouseType($id);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "Delete Successful!"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}

	public function getUserWarehouse()
	{
		return json_encode(["warehouse" => $_SESSION["user_warehouse"]]);
	}

	public function FGWithdraw()
	{
		renderView('report/fg_withdraw');
	}

	public function FGWithdrawPDF()
	{
		$date_internal = filter_input(INPUT_POST, "date_withdraw");
		$dateinter = date('Y-m-d', strtotime($date_internal));

		$arr = $this->warehouse->FGWithdrawPDF($dateinter);
		//$arr = (new InternalService)->allpdf($dateinter);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (13 - $number);
		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$json_decode[] = (object) [
					'Row' => '',
					'ItemID' => '',
					'time_create' => '',
					'TemplateSerialNo' => '',
					'NameTH' => '',
					'Note' => '',
					'Batch' => '',
					'qty' => '',
					'FirstName' => '',
					'Department' => '',
					'Name' => '',
				];
				$sorted = $json_decode;
			}
		}

		renderView("report/pdf_finish_good_withdraw", [
			"dateinter" => $date_internal,
			"datajson" => $json_decode
		]);
	}

	public function createwhsendtable()
	{

		$truck = trim(filter_input(INPUT_POST, "truck"));
		//$round = trim(filter_input(INPUT_POST, "round"));
		$check = trim(filter_input(INPUT_POST, "form_type"));
		$id = trim(filter_input(INPUT_POST, "_id"));




		$result = (new WarehouseService)->createsendwhtable($truck, $check, $id);
		//$result = 404;

		
		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}

	public function allwhtable()
	{

		echo (new WarehouseService)->allwhtable();
	}

	public function alllinewhtable($id)
	{

		echo (new WarehouseService)->alllinewhtable($id);
	}

	public function printLinewhsendround($journalId)
	{

		if (isset($journalId)) {

			//	echo  $journalId;
			// $mode = $_GET["mode"];
			// $create_date = $_GET["create_date"];
			// $title = '';
			// $issue = '';
			// if ($mode === 'MOV') {
			// 	$title = 'Final Withdrawal Report';
			// 	$issue = 'FM-MP-1.9.3,Issued#1';
			// 	$check = 1;
			// } else {
			// 	$title = 'Finish Good Withdrawal Report';
			// 	$issue = 'FM-MP-1.9.4,Issued#1';
			// 	$check = 2;
			// }

			$response = (new WarehouseService)->reportwhLine($journalId);
			$responsedata = (new WarehouseService)->reportwhLinedata($journalId);

			// print_r($responsedata);
			// exit();




			renderView("page/sendwhLineround", [
				"datajson" => $responsedata,
				"datahead" => $response
				// "create_date" => $create_date,
				// "title" => $title,
				// "issue" => $issue,
				// "check" => $check
			]);
		} else {
			exit("error journal id not found.");
		}
	}

	// Quality
	public function qualitycheck()
	{
		$barcode = trim(filter_input(INPUT_POST, "barcode"));

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}
		if ((new WarehouseService)->qualitycheckbarcode($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้มีอยู่ในระบบแล้ว"]));
		}
		if ((new WarehouseService)->qualitycheck($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่อยู่ในสถาะ Movement"]));
		}	
		else {
			exit(json_encode(["status" => 200, "message" => "Success"]));
		}
	}
	public function countquality($barcode)
	{
		echo json_encode([
			"count" => count(json_decode($this->warehouse->countquality($barcode)))
		]);
	}
	
}
