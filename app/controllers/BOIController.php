<?php

namespace App\Controllers;

use App\Services\BoiService;

class BOIController
{
	private $boiService = null;

	public function __construct()
	{
		$this->boiService = new BoiService();
	}

	public function all()
	{
		echo $this->boiService->all();
	}


	public function create()
	{
		$id = trim(filter_input(INPUT_POST, "building_id"));
		$desc = trim(filter_input(INPUT_POST, "building_desc"));
		$boi = trim(filter_input(INPUT_POST, "BOI_id"));
		$form_type = trim(filter_input(INPUT_POST, "form_type"));
		$delete_id = trim(filter_input(INPUT_POST, "delete_id"));

		if ($form_type == "create") {
			$result = $this->boiService->create($boi, $desc);
			if ($result["status"] === false) {
				echo json_encode(["status" => 404, "message" => $result["message"]]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if ($this->boiService->update($boi, $desc) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
		if ($form_type == "delete") {
			if ($this->boiService->delete($delete_id) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถลบได้"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "ดำเนินการเรียบร้อย"]);
		}
	}

	// public function delete()
	// {
	// 	$id = "SABA";
	// 	//echo $id; exit();
	// 	if ($this->boiService->delete($id)) {
	// 		echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
	// 	} else {
	// 		echo json_encode(["status" => 404, "message" => $id]);
	// 	}
	// }

	public function allBOI()
	{
		echo $this->boiService->allBOI();
	}

	public function BOIName($_id)
	{
		if (!isset($_id) || $_id == 'ALL') {
			$_idBOI = 0;
		} else {
			$_idBOI = $_id;
		}
		echo $this->boiService->BOIName($_idBOI);
	}

	public function allGT()
	{
		echo $this->boiService->allGT();
	}

	public function alltruck()
	{
		echo $this->boiService->alltruck();
	}

	public function alltmobiletruck()
	{
		echo $this->boiService->alltmobiletruck();
	}

	public function allround()
	{
		echo $this->boiService->allround();
	}

	public function createround()
	{
		// $id = trim(filter_input(INPUT_POST, "building_id"));
		$desc = trim(filter_input(INPUT_POST, "round_desc"));
		$id = trim(filter_input(INPUT_POST, "round_id"));
		$form_type = trim(filter_input(INPUT_POST, "form_type"));
		$delete_id = trim(filter_input(INPUT_POST, "delete_id"));

		if ($form_type == "create") {
			$result = $this->boiService->createround($desc);
			if ($result["status"] === false) {
				echo json_encode(["status" => 404, "message" => $result["message"]]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if ($this->boiService->updateround($id, $desc) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
		if ($form_type == "delete") {
			if ($this->boiService->deleteround($delete_id) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถลบได้"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "ดำเนินการเรียบร้อย"]);
		}
	}

	public function truckcheck()
	{
		$id  = trim(filter_input(INPUT_POST, "truckshow"));
		$check =  $this->boiService->truckcheck($id);
		if ($check == null || $check == "") {
			echo json_encode(["status" => 404, "message" => $id . " ไม่มีในระบบ"]);
			exit();
		}
		$checkroundsendth =  $this->boiService->truckchecksendtable($id);

		if ($checkroundsendth["Id"] == null || $checkroundsendth["Id"] == "") {
			echo json_encode(["status" => 404, "message" => $id . " ยังไม่สร้างรายการ"]);
			exit();
		} else {
			echo json_encode(["status" => 200, "ID" => $checkroundsendth["Id"], "JournalID" => $checkroundsendth["JournalID"], "roundcar" => $checkroundsendth["roundcar"]]);
		}
	}
}
