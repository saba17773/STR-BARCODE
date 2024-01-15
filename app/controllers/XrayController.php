<?php

namespace App\Controllers;

use App\Services\XrayService;
use App\Services\BarcodeService;
use App\Services\InventService;
use App\Services\FinalService;
use App\V2\Barcode\BarcodeAPI;

class XrayController
{
	private $barcodeService = null;
	private $xrayService = null;
	private $inventService = null;
	private $finalService = null;
	private $barcodeApi = null;

	public function __construct()
	{
		$this->barcodeService = new BarcodeService();
		$this->xrayService = new XrayService();
		$this->inventService = new InventService();
		$this->finalService = new FinalService();
		$this->barcodeApi = new BarcodeAPI();
	}

	public function issueToWH()
	{
		if (count($_POST) === 0) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}

		$barcode = $_POST['barcode']; //filter_input(INPUT_POST, "barcode");
		$_idtrucktable = $_POST['truck'];
		$typechek = $_POST['typecheck'];

		// if ($typechek == 1) {

		// 	$insertlinewh = $this->xrayService->insertlinewhsend($barcode, $_idtrucktable);
		// 	exit(json_encode([
		// 		"status" => 200,
		// 		"message" => "Success!"
		// 		// "curecode" => $this->barcodeApi->getCureCode($barcode),
		// 		// 'batch' => $this->barcodeApi->getBatch($barcode)
		// 	]));
		// }


		if (!isset($_POST['from'])) {
			$from = null;
		} else {
			$from = $_POST['from']; //filter_input(INPUT_POST, "from"); // from fix mouse
		}



		if ($this->barcodeService->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not found."]));
		}

		if ($this->barcodeService->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not found in invent table."]));
		}

		if ($this->xrayService->isItemID($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not curing."]));
		}

		if ($this->inventService->checkWarehouseTransReceiveData($barcode) === true) {
			exit(json_encode(["status" => 405, "message" => "Barcode number already exist."]));
		}

		if ($this->inventService->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
		}

		if ($this->finalService->isFinalReceiveDateExist($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode not Recived to Final."]));
		}




		if ($typechek == 1) {
			$result = $this->xrayService->issueToWH($barcode, $from);
			if ($result === 200) {
				$insertlinewh = $this->xrayService->insertlinewhsend($barcode, $_idtrucktable);
				if ($insertlinewh === 200) {
					$insertbarcodesendtemp = $this->xrayService->insertbarcodesendtemp($barcode);
					exit(json_encode([
						"status" => 200,
						"message" => "Success!"

					]));
				} else {
					exit(json_encode([
						"status" => 404,
						"message" => "Please check Item."
					]));
				}
			} else {

				exit(json_encode([
					"status" => 404,
					"message" => $result,
					"curecode" => '',
					'batch' => ''
				]));
			}
		} else {
			$result = $this->xrayService->issueToWH($barcode, $from);
		}

		if ($result === 200) {
			$insertbarcodesendtemp = $this->xrayService->insertbarcodesendtemp($barcode);
			exit(json_encode([
				"status" => 200,
				"message" => "Xray Issue Success!",
				"curecode" => $this->barcodeApi->getCureCode($barcode),
				'batch' => $this->barcodeApi->getBatch($barcode)
			]));
		} else {

			exit(json_encode([
				"status" => 404,
				"message" => $result,
				"curecode" => '',
				'batch' => ''
			]));
		}
	}
}
