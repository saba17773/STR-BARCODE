<?php

namespace App\Controllers;

use App\Services\CuringService;
use App\Services\InventService;
use App\Services\CureTireService;
use App\Services\BarcodeService;
use App\V2\Curing\CuringAPI;
use App\V2\Barcode\BarcodeAPI;

class CuringController
{
	private $curingService = null;
	private $inventService = null;
	private $curetireService = null;
	private $barcodeService = null;
	private $curingApi = null;
	private $barcodeApi = null;

	public function __construct()
	{
		$this->curingService = new CuringService();
		$this->inventService = new InventService();
		$this->curetireService = new CureTireService();
		$this->barcodeService = new BarcodeService();
		$this->curingApi = new CuringAPI();
		$this->barcodeApi = new BarcodeAPI();
	}

	public function curing()
	{
		$curing_code = filter_input(INPUT_POST, "curing_code");
		$template_code = filter_input(INPUT_POST, "template_code");
		$barcode = filter_input(INPUT_POST, "barcode");
		$cure_type = filter_input(INPUT_POST, "cure_type");

		$curcode = explode("@", trim($curing_code));
		$check = 1;


		if ($curing_code === NULL || $curing_code === "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน "]));
		}

		if (count($curcode) != 4) {
			//exit(json_encode(["status" => 404, "message" => "Curing Code Format Incorrect!"]));
			exit(json_encode(["status" => 404, "message" => "รูปแบบ Curing Code ไม่ถูกต้อง"]));
		}

		$press_no = $curcode[0];
		$press_side = $curcode[1];
		$mold_no = $curcode[2];
		$curing_code_master = $curcode[3];
		
		 if ($this->inventService->CheckGreentireActive() === true){
			if ($this->inventService->CheckGreentireQc($barcode) === true) {
				// exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
				exit(json_encode(["status" => 404, "message" => "ยังไม่ผ่าน Inspection"]));
			}
	
		 }
		
		if ($this->curingService->chkFIFOBatch($curing_code_master, $barcode) === false) {

			exit(json_encode([
				"status" => 404, "message" => "มี GT ที่อายุมากกว่า " . $this->curingService->getAging($curing_code_master) . " วัน"
			]));
		}

		if (
			$this->curingService->checkPressNo($press_no) === false ||
			$this->curingService->checkPressSide($press_side) === false ||
			$this->curingService->checkMoldNo($mold_no) === false ||
			$this->curingService->checkCureCode($curing_code_master) === false
		) {

			exit(json_encode(["status" => 404, "message" => "รูปแบบ Curing Code ไม่ถูกต้อง"]));
		}

		if ($cure_type !== 'without_serial') {

			if ($this->curingService->checkTemplateExist($template_code) === false) {
				//	exit(json_encode(["status" => 404, "message" => "Serial No. not found."]));
				exit(json_encode(["status" => 404, "message" => "Serial Code ไม่มีอยู่ในระบบ"]));
			}

			if ($this->curingService->checkIsExistInventTable($template_code) === true) {
				//exit(json_encode(["status" => 404, "message" => "Template serial number not found."]));
				exit(json_encode(["status" => 404, "message" => "Serial Code มีการใช้งานแล้ว"]));
			}
		} else {

			$template_code = null;

			if ($this->curetireService->isSetDontCheckSerial($curing_code_master) === false) {
				// exit(json_encode(["status" => 404, "message" => "Don\'t check serial not set"]));
				exit(json_encode(["status" => 404, "message" => "ตรวจสอบข้อมูล CheckSerial"]));
			}
		}
		if ($this->barcodeService->isRanged($barcode) === false) {
			//exit(json_encode(["status" => 404, "message" => "Barcode not found."]));
			exit(json_encode(["status" => 404, "message" => "Barcode ไม่ถูกต้อง"]));
		}

		if ($this->inventService->isExist($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "Barcode not build."]));
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้ Build"]));
		}

		if (
			$this->barcodeService->isRanged($barcode) === true &&
			$this->inventService->isScrap($barcode) === true
		) {
			// exit(json_encode(["status" => 404, "message" => "Barcode number, status = scrap"]));
			exit(json_encode(["status" => 404, "message" => "Barcode มีสถานะ Hold ,Repair "]));
		}

		if (
			$this->barcodeService->isRanged($barcode) === true &&
			$this->inventService->isCuringCodeNull($barcode) === false
		) {
			// exit(json_encode(["status" => 404, "message" => "Barcode number already exist."]));
			exit(json_encode(["status" => 404, "message" => "Barcode ยิงอบไปแล้ว"]));
		}

		if ($this->barcodeService->isReceived($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
			exit(json_encode(["status" => 404, "message" => "Barcode  อบยางข้าม Code"]));
		}

		if ($this->inventService->ischeckboi($press_no) === false) {
			// exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
			exit(json_encode(["status" => 404, "message" => "ตรวจสอบ Phsae ที่ Pressmaster"]));
		}


		$cure = $this->curingService->curing($curing_code, $template_code, $barcode, $check);

		if ($cure == 200) {
			//exit(json_encode(["status" => 200, "message" => "Curing Successful"]));
			//	exit(json_encode(["status" => 200, "message" => $barcode]));
			exit(json_encode([
				"status" => 200,
				"message" => $barcode,
				"curecode" => $this->barcodeApi->getCureCode($barcode),
				'batch' => $this->barcodeApi->getBatch($barcode),
				"pressno" => $this->barcodeApi->getPressNo($barcode),
				'pressside' => $this->barcodeApi->getPressSide($barcode)
			]));
		} else {
			//	exit(json_encode(["status" => 404, "message" => $cure]));
			exit(json_encode([
				"status" => 404,
				"message" => $cure,
				"curecode" => '',
				'batch' => ''
			]));
		}
		echo json_encode([$barcode]);
	}

	public function genCuringCode($barcode)
	{
		$eee =  $this->curingApi->test1($barcode);
		$itembrand = [];
		foreach ($eee as $v) {

			//echo "<pre>".print_r($v,true)."</pre>";
			$itembrand = $v[0];
		}
		$itembrand;
		renderView("page/curing_generator", [
			"barcode" => $barcode,
			"ItemBrand" => $itembrand
		]);
	}
}
