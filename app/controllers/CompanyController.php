<?php

namespace App\Controllers;

use App\Services\CompanyService;
use App\Components\Database;

class CompanyController
{
	public function all()
	{
		echo (new CompanyService)->all();
	}

	public function create()
	{
		$form_type = filter_input(INPUT_POST, "form_type");
		$company_name = filter_input(INPUT_POST, "company_name");
		$internal_code = filter_input(INPUT_POST, "internal_code");

		if ($form_type == 'create') {
			if ((new CompanyService)->create($company_name, $internal_code) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		}

		if ($form_type == 'update') {
			if ((new CompanyService)->update($company_name, $internal_code) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		}
	}
}