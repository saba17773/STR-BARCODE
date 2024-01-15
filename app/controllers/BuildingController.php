<?php

namespace App\Controllers;

use App\Services\BuildingService;
use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Models\InventTable;
use App\Models\InventTrans;
use App\Models\Barcode;
use App\Components\Utils;

class BuildingController
{
	public function all()
	{
		echo (new BuildingService)->all();
	}

	public function check()
	{
		$building_no = filter_input(INPUT_POST, "building_no");

		$conn = (new Database)->connect();

		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT BM.ID
			FROM BuildingMaster BM
			WHERE BM.ID = ?",
			[$building_no]
		);

		if ($query) {
			echo json_encode(["status" => 200]);
		} else {
			echo json_encode(["status" => 404]);
		}
	}

	public function create()
	{
		$id = trim(filter_input(INPUT_POST, "building_id"));
		$desc = trim(filter_input(INPUT_POST, "building_desc"));
		$form_type = trim(filter_input(INPUT_POST, "form_type"));
		$boi = trim(filter_input(INPUT_POST, "building_boi"));
		if ($form_type == "create") {
			if ((new BuildingService)->create($id, $desc, $boi) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if ((new BuildingService)->update($id, $desc, $boi) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => $boi]);
		}
	}

	public function delete()
	{
		$id = filter_input(INPUT_POST, "id");
		if ((new BuildingService)->delete($id)) {
			echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ลบไม่สำเร็จ"]);
		}
	}

	public function genBuildingCode($barcode)
	{
		renderView("page/building_generator", [
			"barcode" => $barcode
		]);
	}

	public function genBuildingCodeA5($barcode)
	{
		renderView("page/building_generator_a5", [
			"barcode" => $barcode
		]);
	}

	public function checkBuild()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');


		$b = new Barcode;
		if (!$b->inRange($barcode)) {
			return json_encode([
				"result" => false,
				"message" => "Barcode ไม่ถูกต้อง"
			]);
		}

		$invent_table = new InventTable;
		$invent_table->Barcode = $barcode;
		if ($invent_table->isBarcodeExist() === false) {
			return json_encode([
				"result" => false,
				"message" => "ยังไม่ได้ Build"
			]);
		} else {
			$update_build = (new BuildingService)->updateCheckBuild($barcode);
			if ($update_build === false) {
				return json_encode([
					"result" => false,
					"message" => "Update Check Build Failed!"
				]);
			} else {
				return json_encode([
					"result" => true,
					"message" => "Build แล้ว"
				]);
			}
		}
	}

	public function checkcure()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');


		$b = new Barcode;
		if (!$b->checkcure($barcode)) {
			return json_encode([
				"result" => false,
				"message" => "Barcode ไม่ถูกต้อง"
			]);
		} else {
			$update_build = (new BuildingService)->updateCheckcure($barcode);
			if ($update_build === false) {
				return json_encode([
					"result" => false,
					"message" => "Update Check Cure Failed!"
				]);
			} else {
				return json_encode([
					"result" => true,
					"message" => "Cure แล้ว"
				]);
			}
		}
	}

	public function changeCode()
	{
		renderView('page/change_code');
	}

	//mobile
	public function changeCode_mb()
	{
		renderView('page/change_code_mb');
	}

	public function saveChangeCode()
	{
		$copy_barcode = filter_input(INPUT_POST, 'copy_barcode');
		$barcode = filter_input(INPUT_POST, 'barcode');

			

		$buildingService = new BuildingService;
		$result = $buildingService->changeCodeV2($copy_barcode, $barcode);

	

		return json_encode([
			"result" => $result['result'],
			"message" => $result['message']
		]);

		
	}

	public function boi()
	{
		echo (new BuildingService)->boi();
	}

	public function genroundCodeA5($barcode)
	{
		renderView("page/round_barcode", [
			"barcode" => $barcode
		]);
	}

	public function forceChangeCode() {
		$item_id = $_POST['item_id'];
		$barcode = $_POST['barcode'];
		$cure_code = $_POST["cure_code"];

		// echo json_encode( [$item_id, $barcode, $cure_code] );
		// exit;

		$buildingService = new BuildingService;
		$result = $buildingService->forceChangeCode($item_id, $barcode, $cure_code);


		return json_encode([
			"result" => $result['result'],
			"message" => $result['message']
		]);
	}

	public function check_save_wms(){
  	
		$barcode = filter_input(INPUT_POST, 'barcode');
		$check_data = (new InventTable)->iswmscheck($barcode);
  		if ($check_data == false) {
   			return json_encode([
    			"result" => true,
    			"message" => "Data Not Found"
   		]);
  	} else {

  
		$insert_wms = (new InventTable)->insert_wms($barcode);
		if ($insert_wms === false) {
			return json_encode([
			"result" => false,
			"message" => "Insert Failed!"
			]);
		} else {
			return json_encode([
			"result" => true,
			"message" => "Complete"
			]);
		}
	}
 }

	
}
