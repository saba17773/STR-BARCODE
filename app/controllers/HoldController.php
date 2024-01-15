<?php

namespace App\Controllers;

use App\Services\HoldService;
use App\Services\BarcodeService;
use App\Services\UserService;
use App\Components\Security;
use App\Services\InventService;

class HoldController
{
	public function hold()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		$defect = filter_input(INPUT_POST, "defect");
		$holdtype = filter_input(INPUT_POST, "holdtype");
		$barcode_decode = (new Security)->_decode($barcode);


		// exit(json_encode(["status" => 404, "message" => $defect]));
		if ($defect === NULL || $defect === "" ) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}
		if ($holdtype === NULL || $holdtype === "" || $holdtype === "undefined") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		}

		if ((new BarcodeService)->isHold($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Hold ไปแล้ว"]));
		}

		if ((new InventService)->isIssued($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode status = issue"]));
		}
		if ((new InventService)->CheckGreentireActive() === true){
			if ((new InventService)->CheckGreentireQc($barcode) === true) {
				exit(json_encode(["status" => 404, "message" => "ยังไม่ผ่าน Inspection"]));
			}
		}
		$hold = (new HoldService)->run($barcode, $defect, $holdtype);

		if ($hold == 200) {
			exit(json_encode(["status" => 200, "message" => "Hold Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $hold]));
		}
	}

	public function unhold()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$auth = filter_input(INPUT_POST, "auth");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isHold($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้ Hold"]));
		}

		

		$unhold = (new HoldService)->unhold($barcode, $auth);

		if ($unhold == 200) {
			exit(json_encode(["status" => 200, "message" => "Unhold Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $unhold]));
		}
	}

	public function authorize()
	{
		// $barcode = filter_input(INPUT_POST, "barcode");
		// $barcode_decode = (new Security)->_decode($barcode);
		$code = filter_input(INPUT_POST, "code");
		$pass = filter_input(INPUT_POST, "pass");
		$type = filter_input(INPUT_POST, "type");

		if ((new UserService)->isUserBarcodeExist($code) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีในระบบ"]));
		}

		if ((new UserService)->isAuthorize($code, $pass, $type) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		// if ((new UserService)->isDepartmentTrue($code, $type) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "Location incorrect."]));
		// }

		exit(json_encode(["status" => 200, "message" => "Authorize successful!"]));
	}

	public function lightbuff()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		

	

		// if ((new BarcodeService)->isRanged($barcode) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		// }

		if ((new InventService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode."]));
		}

	
		if ((new InventService)->isRecivefinal($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode ถูกยิงรับเข้า Final แล้ว"]));
		}
		// if ((new InventService)->isNotCuringFinal($barcode) === true) {
		// 	exit(json_encode(["status" => 404, "message" => "ฺBarcode ไม่ได้อยู่ในสถานะ Curing"]));
		// }
		if ((new InventService)->checktrans($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่ได้อยู่ในสถานะ Curing"]));
		}

		if ((new InventService)->isSave($barcode) === true) {
			$Lightsave = (new HoldService)->SaveLightBuff($barcode);
			$txtSuccess = (new InventService)->txtSuccess($barcode);

			if ($Lightsave == 200) {
				exit(json_encode(["status" => 200, "message" => $txtSuccess]));
			} else {
				exit(json_encode(["status" => 404, "message" => $Lightsave]));
			}
		}else{
			exit(json_encode(["status" => 404, "message" => "Barcode อยู่ในสถานะ Light buff"]));
		}



		// $hold = (new HoldService)->run($barcode, $defect, $holdtype);

		// if ($hold == 200) {
		// 	exit(json_encode(["status" => 200, "message" => "Hold Successful!"]));
		// } else {
		// 	exit(json_encode(["status" => 404, "message" => $hold]));
		// }
	}
}
