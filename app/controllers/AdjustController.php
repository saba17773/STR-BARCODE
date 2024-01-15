<?php

namespace App\Controllers;

use App\Components\Database;
use App\Services\AdjustService;
use App\Services\BarcodeService;

class AdjustController
{
	public function __construct()
	{
		$this->adjust = new AdjustService;
		$this->barcode = new BarcodeService;
	}

	public function store()
	{
		$conn = Database::connect();

		$greentire_code = filter_input(INPUT_POST, 'greentire_code');
		$date = filter_input(INPUT_POST, 'date');
		$barcode = filter_input(INPUT_POST, 'barcode');
		$boi = filter_input(INPUT_POST, 'boi');
		$boi_cod = substr($boi, 3);


		if ($this->barcode->isRanged($barcode) === false) {
			exit(json_encode(['status' => 400, 'message' => 'ไม่พบ Barcode!']));
		}

		if ($this->barcode->isExistInventTable($barcode) === true) {
			exit(json_encode(['status' => 400, 'message' => 'Barcode Number มีอยู่ในระบบแล้ว!']));
		}

		$result = $this->adjust->store(
			$greentire_code,
			$date,
			$barcode,
			$boi_cod,
			$boi
		);

		if ($result === 200) {
			exit(json_encode(['status' => 200, 'message' => 'Adjust Successful!']));
		} else {
			exit(json_encode(['status' => 400, 'message' => $result]));
		}
	}
}
