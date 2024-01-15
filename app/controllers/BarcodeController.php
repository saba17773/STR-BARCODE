<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Components\Security;
use Dompdf\Dompdf;
use App\Models\Barcode;
use App\Models\InventTable;

class BarcodeController
{
	public function getLastNumber()
	{
		echo (new BarcodeService)->getLastNumber();
	}

	public function printing()
	{
		$start = filter_input(INPUT_POST, "start");
		$end = filter_input(INPUT_POST, "end");
		$qty = filter_input(INPUT_POST, "qty");

		$reprint = filter_input(INPUT_POST, "reprint");

		// \var_dump($_POST); exit;

		if (!isset($reprint)) {

			if ((new BarcodeService)->isPrinted($start, $end) === false) {
				exit("Barcode already printed. <a href='" . APP_ROOT . "/barcode/printing'>Go Back</a>");
			}

			$create_barcode = (new BarcodeService)->create($start, $end, $qty);
		} else if (isset($reprint) && (int) $reprint === 1) {
			renderView("page/barcode_printing_generator", [
				"barcode" => [$start]
			]);
			exit;
		}

		$year = Date("y");

		$start =  (int) substr($start, 3);
		$end =  (int) substr($end, 3);
		$qty = (int) $qty;

		$barcode = [];



		for ($i = $start; $i <= $end; $i++) {
			$barcode[] = (new Security)->_encode(BARCODE_PREFIX . $year . str_pad($i, 8, "0", STR_PAD_LEFT));
		}

		renderView("page/barcode_printing_generator", [
			"barcode" => $barcode
		]);

		// $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

		// foreach ($barcode as $v) {
		// 	echo '<img style="margin: auto; display: block;" width="300" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($v, $generator::TYPE_CODE_128)) . '">';
		// 	echo "<div style='text-align: center;'>".$v."</div><br />";
		// }
	}

	public function generator($string)
	{
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		echo '<img width="300" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($string, $generator::TYPE_CODE_128)) . '"><br />';
		echo $string . "<br /><br />";
	}

	public function genGreentireCode($barcode)
	{
		renderView("page/greentire_generator", [
			"barcode" => $barcode
		]);
	}

	public function genGreentireCodeA5($barcode)
	{
		renderView("page/greentire_generator_a5", [
			"barcode" => $barcode
			//"barcode1" => $barcode1
		]);
	}

	public function genCuretireA5($barcode, $warehouse_user)
	{
		$font = 5;
		if ((int) $warehouse_user === 2) { // Final
			$warehouse = "CURE TIRE CODE";
		} else if ((int) $warehouse_user === 3) { // FG
			$warehouse = "ITEM CODE";
			$font = 5;
		} else {
			$warehouse = "CURE TIRE CODE";
		}
		$barcode  = str_replace("%20", " ", $barcode);
		renderView("page/curetire_generator_a5", [
			"barcode" => $barcode,
			"warehouse" => $warehouse,
			"font" => $font
		]);
	}

	public function getBarcodeInfo($barcode)
	{
		echo (new BarcodeService)->getBarcodeInfo($barcode);
	}

	public function changeBarcode()
	{
		renderView('page/change_barcode');
	}

	//mobile
	public function changeBarcode_mb()
	{
		renderView('page/change_barcode_mb');
	}


	public function saveChangeBarcode()
	{
		$old_barcode = filter_input(INPUT_POST, 'old_barcode');
		$new_barcode = filter_input(INPUT_POST, 'new_barcode');

		$barcode = new Barcode;

		if (!$barcode->inRange($old_barcode)) {
			return json_encode([
				"result" => false,
				"message" => "Barcode เก่า ไม่มีอยู่ในระบบ"
			]);
		}

		if (!$barcode->inRange($new_barcode)) {
			return json_encode([
				"result" => false,
				"message" => "Barcode ใหม่ ไม่มีอยู่ในระบบ"
			]);
		}

		$it = new InventTable;

		$it->Barcode = $old_barcode;
		if (!$it->isBarcodeExist()) {
			return json_encode([
				"result" => false,
				"message" => "Barcode เก่า ไม่มีอยู่ใน Invent Table"
			]);
		}

		$it->Barcode = $new_barcode;
		if ($it->isBarcodeExist()) {
			return json_encode([
				"result" => false,
				"message" => "Barcode ใหม่ ต้องไม่มีอยู่ใน Invent Table"
			]);
		}

		if ($barcode->changeBarcode($old_barcode, $new_barcode)) {
			return json_encode([
				"result" => true,
				"message" => "Change Barcode Successful!"
			]);
		} else {
			return json_encode([
				"result" => false,
				"message" => "Change Barcode Failed!"
			]);
		}
	}

	public function check_barcodewh()
	{
		 $barcode = new Barcode;
		 $barcode_ = filter_input(INPUT_POST, 'barcode');
		if($barcode->checkwh($barcode_) == true){
			return json_encode([
				"result" => true
			]);
		}else{
			return json_encode([
				"result" => false
			]);
		}
	}
}
