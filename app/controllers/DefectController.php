<?php

namespace App\Controllers;

use App\Services\DefectService;
use App\Components\Database;

class DefectController
{
	public function all()
	{
		header('Content-Type: application/json');
		echo (new DefectService)->all();
	}

	public function reverse()
	{
		header('Content-Type: application/json');
		echo (new DefectService)->reverse();
	}

	public function masterAll()
	{
		echo (new DefectService)->masterAll();
	}

	public function update()
	{
		$gt = filter_input(INPUT_POST, "gt");
		$fn = filter_input(INPUT_POST, "fn");
		$df = filter_input(INPUT_POST, "df");
		$id = filter_input(INPUT_POST, "id");

		$result = (new DefectService)->updateV2($id, $gt, $fn, $df);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ทำรายการสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ทำรายการไม่สำเร็จ"]);
		}
	}

	public function create()
	{
		$scrap = $_POST["scrap"];
		$company = $_POST["company"];

		$description = filter_input(INPUT_POST, "description");
		$description_th = filter_input(INPUT_POST, "description_th");
		$id = filter_input(INPUT_POST, "_id");
		$form_type = filter_input(INPUT_POST, "form_type");

		// echo DefectService->create($description, $scrap, $company);

		if ($form_type == "create") {
			if((new DefectService)->create($description, $description_th, $scrap, $company) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if((new DefectService)->update($id, $scrap) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}

	public function syncDefect()
	{
		$sync = (new DefectService)->syncDefect();

		if ($sync === true) {
			return json_encode([
				'result' => true,
				'message' => 'Sync Success!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => 'Sync Failed!'
			]);
		}
	}

}
