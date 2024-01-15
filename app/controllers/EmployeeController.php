<?php

namespace App\Controllers;

use App\Services\EmployeeService;

class EmployeeController
{
	public function all()
	{
		echo (new EmployeeService)->all();
	}

	public function allByDepartmentName($department_name)
	{
		echo (new EmployeeService)->allByDepartmentName($department_name);
	}

	public function allByStatus()
	{
		echo (new EmployeeService)->allByStatus();
	}

	public function getDivisionByEmpCode($empCode)
	{
		echo (new EmployeeService)->getDivisionByEmpCode($empCode);
	}

	public function setStatus()
	{
		
		$id = filter_input(INPUT_POST, "id"); 
		$status = filter_input(INPUT_POST, "status");

		$result = (new EmployeeService)->setStatus($id, $status);
		if ($result !== 200) {
			echo json_encode(["status" => 404, "message" => $result]);
		} else {
			echo json_encode(["status" => 200, "message" => "ทำรายการเสร็จสิ้น"]);
		}
	}

	public function sync()
	{
		$result = (new EmployeeService)->sync();
		if ($result === true) {
			echo json_encode(["result" => true, "message" => 'Sync Employee Successful!']);
		} else {
			echo json_encode(["result" => false, "message" => $result]);
		}
	}
}