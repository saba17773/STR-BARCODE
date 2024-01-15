<?php

namespace App\Controllers;

use App\Services\DeductService;
use App\Components\Database;

class DeductController
{
	public function machine_TBR()
	{
		// header('Content-Type: application/json');
		echo (new DeductService)->machine_TBR();
	}

	public function machine_PCR()
	{
		// header('Content-Type: application/json');
		echo (new DeductService)->machine_PCR();
	}

	public function bindGrid($date,$shift,$machine)
	{
		$dateinter = date('Y-m-d', strtotime($date));
        echo (new DeductService)->bindGrid($dateinter,$shift,$machine);
	}

	public function checkLog($date,$machine)
	{
		$dateinter = date('Y-m-d', strtotime($date));
		$userid = filter_input(INPUT_POST, "UserId");
		$shift2 = filter_input(INPUT_POST, "shift2");
		$checkLog = (new DeductService)->checkLog($dateinter,$machine,$userid,$shift2);
		if ($checkLog["status"] === 200) 
		{
			echo json_encode(["status" => 200]);
		} else 
		{
			echo json_encode(["status" => 404]);
		}
	}
	
	public function insertDeduct($deductDate,$machine)
	{
		$charge = filter_input(INPUT_POST, "charge");
		$remark = filter_input(INPUT_POST, "remark");
		$userid = filter_input(INPUT_POST, "UserId");
		$shift2 = filter_input(INPUT_POST, "shift2");
		$buildtypeid = filter_input(INPUT_POST, "buildtypeid");
		$createby = $_SESSION["user_login"];
		$dateinter = date('Y-m-d', strtotime($deductDate));
		

		
		$insertDeduct = (new DeductService)->insertDeduct($dateinter,$machine,$userid,$charge,$remark,$createby,$shift2,$buildtypeid);
		if ($insertDeduct["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "insert ok"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
		}
	}
	
	public function updateDeduct()
	{
		$remark = filter_input(INPUT_POST, "remark");
		$charge = filter_input(INPUT_POST, "charge");
		$id = filter_input(INPUT_POST, "Id");
		$updateby = $_SESSION["user_login"];
		
		$updateDeduct = (new DeductService)->updateDeduct($charge,$remark, $updateby,$id);
		if ($updateDeduct["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "update ok"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "อัพเดตข้อมูลไม่สำเร็จ"]);
		}
	}
	
	public function bindGridDeduct($userid)
	{
        echo (new DeductService)->bindGridDeduct($userid);
	}

	public function Type_TBR()
	{
		echo (new DeductService)->Type_TBR();
	}

	public function Type_PCR()
	{
		echo (new DeductService)->Type_PCR();
	}

	public function bindGridEmp()
	{
        echo (new DeductService)->bindGridEmp();
    }

}