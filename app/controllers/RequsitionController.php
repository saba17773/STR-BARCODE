<?php

namespace App\Controllers;

use App\Services\RequsitionService;

class RequsitionController
{

	public function all()
	{
		echo (new RequsitionService)->all();
	}

	public function saveRequsitionNote()
	{
		$id = filter_input(INPUT_POST, "_id");
		$description = filter_input(INPUT_POST, "description");
		$selectWarehouse = $_POST["selectWarehouse"];

		$selectedWarehouse = [];

		if(count($selectWarehouse) === 1) {

			if (in_array(1, $selectWarehouse)) {
				$selectedWarehouse = [1, 0]; // [final, fg]
			} else if(in_array(2, $selectWarehouse)) {
				$selectedWarehouse = [0, 1]; // [final, fg]
			} else {
				$selectedWarehouse = [0, 0];
			}
		} else if(in_array(1, $selectWarehouse) && in_array(2, $selectWarehouse)) {
			$selectedWarehouse = [1, 1];
		} else {
			$selectedWarehouse = [0, 0];
		}

		$result = (new RequsitionService)->saveRequsitionNote($id, $description, $selectedWarehouse);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "Successful!"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}
}
