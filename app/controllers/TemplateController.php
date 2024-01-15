<?php

namespace App\Controllers;

use App\Services\TemplateService;
use App\Components\Database;

class TemplateController
{
	public function all()
	{
		echo (new TemplateService)->all();
	}

	public function getLastRec()
	{
		$code = filter_input(INPUT_GET, "seq_format");
		echo json_encode(["number" => (new TemplateService)->getLastRec($code)]);
	}

	public function generate($from, $to)
	{
		$to_value = (int)substr($to, 3);
		$from_value = (int)substr($from, 3);
		$qty = ($to_value - $from_value) + 1;

		$create_new = (new TemplateService)->create($from, $to, $qty);
		if ($create_new === true) {
			renderView("page/template_barcode", [
				"from" => $from,
				"to" => $to
			]);
		}
		
	}

	public function printSerial($from, $to)
	{
		renderView("page/template_barcode_v2", [
			"from" => $from,
			"to" => $to
		]);
	}

	public function generator($from, $to)
	{
		$txt = substr($from, 0, 3);
		$from = (int)substr($from, 3);
		$to = (int)substr($to, 3);
		$qty = $to - $from;
		
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

		for ($i=$from; $i <= $to; $i++) { 
			$txt_show = $txt . str_pad($i, 6, "0", STR_PAD_LEFT);
			echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($txt_show, $generator::TYPE_CODE_128)) . '"> <br />';
			echo $txt_show. "<br /><br />";
		}
	}

	public function updateSerialNo()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		$new_serial = filter_input(INPUT_POST, 'new_serial');

		$splited_serial = substr($new_serial, 0, 5);
		$no_serial = substr($new_serial, 5, 4);

		$inrange = (new TemplateService)->inRanged($splited_serial, $no_serial);

		if ($inrange === false) {
			return json_encode([
					'result' => false,
					'message' => 'Serial ไม่อยู่ในระบบ'
			]);
		}

		if (strlen($new_serial) !== 9) {
			return json_encode([
					'result' => false,
					'message' => 'Format ไม่ถูกต้อง!'
			]);
		}

		if ((new TemplateService)->isExistsInInventTable($new_serial) === true) {
			return json_encode([
					'result' => false,
					'message' => 'Serial No ถูกใช้งานอยู่ในระบบแล้ว!'
			]);
		}

		if ((new TemplateService)->isCuringCodeNull($barcode) === true) {
			return json_encode([
					'result' => false,
					'message' => 'ไม่มี Curing Code'
			]);
		}

		$result = (new TemplateService)->updateSerialNo($barcode, $new_serial);

		if ($result !== true) {
			return json_encode([
					'result' => false,
					'message' => $result
			]);
		} else {
			return json_encode([
					'result' => true,
					'message' => 'Update Successful!'
			]);
		}
	}
}