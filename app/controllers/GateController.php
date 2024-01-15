<?php

namespace App\Controllers;

use App\Services\GateService;

class GateController
{
	public function all()
	{
		echo (new GateService)->all();
	}

	public function save()
	{
		$description = filter_input(INPUT_POST, "description");

		$result = (new GateService)->save($description);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		}else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}
}