<?php

namespace App\Controllers;

use App\Services\DisposalService;

class DisposalController
{
	public function all()
	{
		echo (new DisposalService)->all();
	}

	public function actionAll()
	{
		echo (new DisposalService)->actionAll();
	}

	public function companyAll()
	{
		echo (new DisposalService)->companyAll();
	}

	public function create()
	{
		$desc = filter_input(INPUT_POST, "desc");
		$disposal_action = $_POST["disposal_action"];
		$company = $_POST["company"];
		$id = filter_input(INPUT_POST, "_id");
		$form_type = filter_input(INPUT_POST, "form_type");

		if ($form_type == "create") {

			$result = (new DisposalService)->create($desc, $disposal_action, $company);

			if ($result === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}

		}

		if ($form_type == "update") {
			if((new DisposalService)->update($id, $desc, $disposal_action, $company) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}
}