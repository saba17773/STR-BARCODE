<?php

namespace App\Controllers;

use App\Services\DepartmentService;

class DepartmentController
{
	public function all()
	{
		echo (new DepartmentService)->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "_id");
		$form_type = filter_input(INPUT_POST, "form_type");
		$dep_name = filter_input(INPUT_POST, "dep_name");

		if ($form_type == "create") {
			if((new DepartmentService)->create($dep_name) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if((new DepartmentService)->update($id, $dep_name) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}
}