<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\ScrapService;

class ScrapController 
{
	public function scrap()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$defectCode = filter_input(INPUT_POST, "defectCode");
		$ScrapSide = filter_input(INPUT_POST, "position_scrap");

		if ($defectCode === NULL || $defectCode === "" ) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}
		
		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isScrap($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Scrap ไปแล้ว"]));
		}

		$scrap = (new ScrapService)->scrap($barcode, $defectCode, $ScrapSide);

		if ($scrap === 200) {
			exit(json_encode(["status" => 200, "message" => "Scrap Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $scrap]));
		}
	}

	public function scrap_check()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		
		// if ((new BarcodeService)->isRanged($barcode) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		// }
		
		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isScrapCheck($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ Check Scrap ไปแล้ว"]));
		}

		if ((new BarcodeService)->isExitsWithoutScrapStatus($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ยังไม่ได้ยิง Scrap"]));
		}

		

		$scrap = (new ScrapService)->scrap_check($barcode);

		if ($scrap === 200) 
		{

			exit(json_encode(["status" => 200, "message" => "Barcode ".$barcode." Successful"]));
		} 
		else 
		{
			exit(json_encode(["status" => 404, "message" => $scrap]));
		}
	}
}