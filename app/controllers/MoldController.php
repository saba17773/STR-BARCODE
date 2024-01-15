<?php

namespace App\Controllers;

use App\Services\MoldService;

class MoldController
{
	public function all()
	{
		echo (new MoldService)->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "ID");
		$desc = filter_input(INPUT_POST, "Description");
		$form_type = filter_input(INPUT_POST, "form_type");

		if ($form_type == "create") {
			if((new MoldService)->create($desc, $id) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);

				exit;
			}
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if((new MoldService)->update($desc, $id) === false) {
				echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกได้"]);
				exit;
			}
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}
}