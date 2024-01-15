<?php

namespace App\Controllers;

use App\Components\Database;
use App\Services\GreentireService;
use App\Services\BuildingService;
use App\Services\BarcodeService;
use App\V2\Barcode\BarcodeAPI;

class GreentireController
{
	private $BarcodeService = null;
	private $GreentireService = null;
	private $BuildingService = null;

	public function __construct()
	{
		$this->BarcodeService = new BarcodeService();
		$this->GreentireService = new GreentireService();
		$this->BuildingService = new BuildingService();
	}
	public function all()
	{
		echo (new GreentireService)->all();
	}

	public function create()
	{
		$id = trim(filter_input(INPUT_POST, "id"));
		$description = trim(filter_input(INPUT_POST, "description"));
		$form_type = trim(filter_input(INPUT_POST, "form_type"));
		$_id = trim(filter_input(INPUT_POST, "_id"));

		if ($form_type == "create") {
			if ((new GreentireService)->create($id, $description) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if ((new GreentireService)->update($id, $description, $_id) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "map_item") {
			$item = $_POST["item"];
			if ((new GreentireService)->mapItem($_id, $item) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			} else {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			}
		}
	}

	public function receive()
	{
		$building_code = trim(filter_input(INPUT_POST, "building_code"));
		$greentire_code = trim(filter_input(INPUT_POST, "greentire_code"));
		$barcode = trim(filter_input(INPUT_POST, "barcode"));
		// $weight = trim(filter_input(INPUT_POST, "weight"));
		$weight = 0;

		// Check Building Code
		if ((new BuildingService)->isExist($building_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Building MC. ไม่มีอยู่ในระบบ"]));
		}

		// Check Green Tire Code
		if ((new GreentireService)->isExist($greentire_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Greentire code ไม่มีอยู่ในระบบ"]));
		}

		// Check Barcode is in ranged
		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่ถูกต้อง"]));
		}

		// Check barcode in invent table
		if ((new BarcodeService)->isExistInventTable($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode number มีอยู่แล้วในระบบ."]));
		}

		// Insert invent table
		$invent_table = (new GreentireService)->receive($barcode, $building_code, $greentire_code, $weight);

		if ($invent_table == 200) {
			exit(json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $invent_table]));
		}
	}

	public function receiveOld()
	{
		$building_code = trim(filter_input(INPUT_POST, "building_code"));
		$greentire_code = trim(filter_input(INPUT_POST, "greentire_code"));
		$barcode = trim(filter_input(INPUT_POST, "barcode"));
		// $weight = trim(filter_input(INPUT_POST, "weight"));
		$weight = 0;

		// Check Building Code
		if ((new BuildingService)->isExist($building_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Building MC. ไม่มีอยู่ในระบบ"]));
		}

		// Check Green Tire Code
		if ((new GreentireService)->isExist($greentire_code) === false) {
			exit(json_encode(["status" => 404, "message" => "Greentire code ไม่มีอยู่ในระบบ"]));
		}

		// Check Barcode is in ranged
		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่ถูกต้อง"]));
		}

		// Check barcode in invent table
		if ((new BarcodeService)->isExistInventTable($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode number มีอยู่แล้วในระบบ."]));
		}

		// Insert invent table
		$invent_table = (new GreentireService)->receive($barcode, $building_code, $greentire_code, $weight);

		if ($invent_table == 200) {
			exit(json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $invent_table]));
		}
	}

	public function delete()
	{
		$id = trim(filter_input(INPUT_POST, "id"));
		if ((new GreentireService)->delete($id)) {
			echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ลบไม่สำเร็จ"]);
		}
	}
	public function insert_buildtrans()
	{
		$barcode = trim(filter_input(INPUT_POST, "barcode"));
		$machine = trim(filter_input(INPUT_POST, "building_code"));

		$build_trans = (new GreentireService)->insert_buildtrans($barcode, $machine);

		if ($build_trans["status"] == 200) {
			echo json_encode([
				"status" => 200,
				"message" => 'บันทึกสำเร็จ'
			]);
		} else {
			echo json_encode([
				"status" => 404,
				"message" => "ไม่สามารถบันทึกข้อมูลได้"
			]);
		}
	}
	public function total_ratetrans()
	{
		$machine = trim(filter_input(INPUT_POST, "building_code"));

		$update_total = (new GreentireService)->update_totalratetrans($machine);

		if ($update_total["status"] == 200) {
			echo json_encode([
				"status" => 200,
				"message" => 'บันทึกสำเร็จ'
			]);
		} else {
			echo json_encode([
				"status" => 404,
				"message" => "ไม่สามารถบันทึกข้อมูลได้"
			]);
		}
	}
	public function save()
	{
		// $gate = filter_input(INPUT_POST, "gate");
		$gate = null;
		$barcode = filter_input(INPUT_POST, "barcode");

		if ($this->BarcodeService->isRangedchekQC($barcode) === false) {
			exit(json_encode([
				"status" => 404,
				"message" => "ไม่พบ Barcode",
				"color" => "red",
				"font_color" => "white"
			]));
		}

		if ($this->GreentireService->isstatushekQC($barcode) === true) {
			exit(json_encode([
				"status" => 404,
				"message" => "Barcode Status Not Receive",
				"color" => "red",
				"font_color" => "white"
			]));
		}


		if ($this->GreentireService->GreentireQcInventTable($barcode) === true) {
			exit(json_encode([
				"status" => 404,
				"message" => "Barcode ผ่านการยิง Inspection แล้ว",
				"color" => "red",
				"font_color" => "white"
			]));
		} else {
			if ($this->GreentireService->GreentireQcCheckWarehouse($barcode) === true) {
				exit(json_encode([
					"status" => 404,
					"message" => "Barcode ไม่ได้อยู่ในสถานะ GT",
					"color" => "red",
					"font_color" => "white"
				]));
			} else {
				// if ($this->GreentireService->GreentireQcSaveInventable($barcode) === false) {
				// 	echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				// } else {
				// 	echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
				// }

				$res = $this->GreentireService->GreentireQcSaveInventable($barcode);
				if ($res == 200) {
					exit(json_encode([
						"status" => 200,
						"message" => "Successful",
						"curecode" => (new BarcodeAPI)->getGtCode($barcode),
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
		}


		// if ($result === 200) {
		// 	echo json_encode([
		// 		"status" => 200,
		// 		"message" => "ดำเนินการเสร็จสิ้น",
		// 		"curecode" => (new BarcodeAPI)->getCureCode($barcode),
		// 		'batch' => (new BarcodeAPI)->getBatch($barcode),
		// 		'color' => 'green',
		// 		"font_color" => "white"
		// 		]);
		// } else {
		// 	echo json_encode([
		// 		"status" => 404,
		// 		"message" => $result,
		// 		"curecode" => '',
		// 		'batch' => '',
		// 		'color' => 'red',
		// 		"font_color" => "white"
		// 		]);
		// }
	}
	public function checkgreentireall()
	{
		echo $this->GreentireService->checkgreentireall();
	}

	public function updatecheckgreentire()
	{
		try {
			// code
			$_id = filter_input(INPUT_POST, "_id");
			$_active = filter_input(INPUT_POST, "_active");
			
			
			// return json_encode(["result" => false, "message" => $_active]);

			// exit();



			$update = $this->GreentireService->updatecheckgreentire($_id, $_active);
			if($update == true){
				return json_encode(["result" => true, "message" => "บันทึกรายการสำเร็จ"]);
			}else{
				return json_encode(["result" => false, "message" => "ไม่สามารถบันทึกได้"]);
			}
		} catch (\Exception $e) {
			return json_encode(["result" => false, "message" => $e->getMessage()]);
		}
	}
}
