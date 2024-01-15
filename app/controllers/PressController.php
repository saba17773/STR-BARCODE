<?php

namespace App\Controllers;

use App\Services\PressService;

class PressController
{
	public function all()
	{
		echo (new PressService)->all();
	}

	public function create()
	{
		$id = filter_input(INPUT_POST, "id");
		$desc = filter_input(INPUT_POST, "desc");
		$form_type = filter_input(INPUT_POST, "form_type");
		$boi = trim(filter_input(INPUT_POST, "building_boi"));


		if ($form_type == "create") {
			if ((new PressService)->create($id, $desc, $boi) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}

		if ($form_type == "update") {
			if ((new PressService)->update($id, $desc, $boi) === false) {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
				exit;
			}

			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		}
	}

	public function delete()
	{
		$id = filter_input(INPUT_POST, "id");
		if ((new PressService)->delete($id)) {
			echo json_encode(["status" => 200, "message" => "ลบสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ลบไม่สำเร็จ"]);
		}
	}
	//nueng
	public function loadid()
	{
		echo (new PressService)->loadid();
	}
	public function externorderkey()
	{
		echo (new PressService)->externorderkey();
	}

	//j modify
	public function allBDF()
	{
		echo (new PressService)->allBDF();
	}
	public function allBDFA()
	{
		echo (new PressService)->allBDFA();
	}
	public function allABCDEF()
	{
		echo (new PressService)->allABCDEF();
	}
	public function allday()
	{
		echo (new PressService)->allday();
	}
	public function allnight()
	{
		echo (new PressService)->allnight();
	}
	//saba
	public function alldaynew()
	{
		echo (new PressService)->alldaynew();
	}
	public function allnightnew()
	{
		echo (new PressService)->allnightnew();
	}

	//Nan
	public function Line_TBR()
	{
		echo (new PressService)->Line_TBR();
	}
	public function Line_PCR()
	{
		echo (new PressService)->Line_PCR();
	}
	public function Building_TBR()
	{
		echo (new PressService)->Building_TBR();
	}
	public function Building_PCR()
	{
		echo (new PressService)->Building_PCR();
	}

	public function allCurecode()
	{
		echo (new PressService)->allCurecode();
	}

	public function createcuring()
	{
		$id = filter_input(INPUT_POST, "IdPress");
		$GT 	 = 'filter_input(INPUT_POST, "selectMenuCuringL")';
		$selectMenuCuringL  = self::convertforinselect(implode(',', $_POST["selectMenuCuringL"]));
		$selectMenuCuringR  = self::convertforinselect(implode(',', $_POST["selectMenuCuringR"]));
		if ((new PressService)->updatecuringpress($id, $selectMenuCuringL, $selectMenuCuringR) === false) {
			echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			exit;
		}

		echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
	}
	public function convertforinselect($str)
	{
		$strploblem = "";
		$a = explode(',', $str);
		foreach ($a as $value) {
			if ($strploblem === "") {
				$strploblem .= $value;
			} else {
				$strploblem .= "," . $value;
			}
		}
		return $strploblem;
	}
}
