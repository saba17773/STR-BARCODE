<?php

namespace App\Controllers;

use App\Services\RepairService;
use App\Services\BarcodeService;
use App\Services\UserService;
use App\Components\Security;

class RepairController
{
	public function repair()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$defect_code = filter_input(INPUT_POST, "defect_code");

		if ($defect_code === NULL || $defect_code === "" ) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isRepair($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Repair ไปแล้ว"]));
		}

		$repair = (new RepairService)->repair($barcode, $defect_code);

		if ($repair === 200) {
			echo json_encode(["status" => 200, "message" => "Repair Successful!"]);
		} else {
			echo json_encode(["status" => 404, "message" => $repair]);
		}
	}

	public function unrepair()
	{
		$barcode = filter_input(INPUT_POST, "barcode");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isHold($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ ยังไม่ได้ Hold"]));
		}

		$unrepair = (new RepairService)->unrepair($barcode);

		if ($unrepair == 200) {
			exit(json_encode(["status" => 200, "message" => "Unrepair Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $hold]));
		}
	}

	public function authorize()
	{
		// $barcode = filter_input(INPUT_POST, "barcode");
		// $barcode_decode = Security::_decode($barcode);
		$code = filter_input(INPUT_POST, "code");
		$pass = filter_input(INPUT_POST, "pass");

		if ((new UserService)->isUserBarcodeExist($code) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode ของ User"]));
		}

		if ((new UserService)->isAuthorize($code) === false) {
			exit(json_encode(["status" => 404, "message" => "User not authorized."]));
		}

		if ((new UserService)->isDepartmentTrue($code) === false) {
			exit(json_encode(["status" => 404, "message" => "User not authorized."]));
		}

		exit(json_encode(["status" => 200, "message" => "Authorize passed."]));
	}
}
