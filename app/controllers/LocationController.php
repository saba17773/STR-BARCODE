<?php

namespace App\Controllers;

use App\Services\LocationService;

class LocationController
{
	public function all()
	{
		header('Content-Type: application/json');
		echo (new LocationService)->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "loca_id");
		$loca_name = filter_input(INPUT_POST, "loca_name");
		$wh_name = filter_input(INPUT_POST, "wh_name");
		$auto_issue = filter_input(INPUT_POST, "auto_issue");
		$loca_use = filter_input(INPUT_POST, "loca_use");		
		$form_type = filter_input(INPUT_POST, "form_type");
		$receive_name = filter_input(INPUT_POST, "receive_name");
		$disposal = filter_input(INPUT_POST, "disposal");

		header('Content-Type: application/json');

		if ($form_type == "create") {

			$result = (new LocationService)->create($loca_name, $wh_name, $auto_issue, $loca_use, $receive_name, $disposal);

			if ($result === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);	
			} else {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);
			}
			
		}

		if ($form_type == "update") {
			if((new LocationService)->update($loca_name,$wh_name,$auto_issue,$loca_use,$id, $receive_name, $disposal) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);
			} else {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			}
			
		}
	}

	public function setLocation($locationId)
	{
		$reverse = filter_input(INPUT_POST, 'reverse');
		$return = filter_input(INPUT_POST, 'return');
		$unpick = filter_input(INPUT_POST, 'unpick');

		$result = (new LocationService)->setLocation($locationId, $reverse, $return, $unpick);

		header('Content-Type: application/json');

		if ($result === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);	
			} else {
				echo json_encode(["status" => 404, "message" => $result]);
			}
	}

	public function getLocationByWarehouse($warehouseId)
	{
		header('Content-Type: application/json');
		echo (new LocationService)->getLocationByWarehouse($warehouseId);
	}
}