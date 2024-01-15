<?php

namespace App\Controllers;

use App\Services\ReurnCauseService;

class ReturnCauseController
{

	public function all()
	{
		echo (new ReurnCauseService)->all();
	}

	public function saveRequsitionNote(){
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

		$result = (new ReurnCauseService)->saveRequsitionNote($id, $description, $selectedWarehouse);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "Successful!"]);
		} else {
			echo json_encode(["status" => 404, "message" => $result]);
		}
	}

	public function saveJournalTable($type){

		$employee_code = filter_input(INPUT_POST, "employee_code");
		$division = filter_input(INPUT_POST, "division_value");
		$user = filter_input(INPUT_POST, "user");
		$pass = filter_input(INPUT_POST, "pass");


		$result = (new ReurnCauseService)->saveJournalTable($type);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "journal" => $result["journal"],"test"=>$result["test"],"journal"=>$result["journal"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function allreturncause()
	{
		echo (new ReurnCauseService)->allreturncause();
	}

	public function allcheck($reId)
	{
		echo (new ReurnCauseService)->allcheck($reId);
	}

	public function savereturntIssue(){

		$barcode = filter_input(INPUT_POST, "barcode");
		$journalId = filter_input(INPUT_POST, "journalId");
		 $requsition = filter_input(INPUT_POST, "requsition");
		// $pass = filter_input(INPUT_POST, "pass");


		$result = (new ReurnCauseService)->savereturntIssue($barcode,$journalId,$requsition);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "test" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function completeIssue()
	{
		$journalId = filter_input(INPUT_POST, "journalId");

		$result = (new ReurnCauseService)->completeIssue($journalId);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Complete Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}
	}

	public function getLatestJournalTransByJournalId($journalId)
	{
		echo (new ReurnCauseService)->getLatestJournalTransByJournalId($journalId);
	}

	public function printReturnByReturnID($journalId){
		$returnCa = new ReurnCauseService ;

		if (isset($journalId)) {
			$create_date = $_GET["create_date"];
			$mode = $_GET["mode"];

			if($mode == 'RTN')
			{
				$toppic ='Tire Return Final Finishing';
			}
			else {
					$toppic ='Tire Return  Warehouse';
			}


			$title = '';
			//$issue = '';

				$title = 'SIAMTRUCK RADIAL CO.LTD.';
				//$issue = 'FM-MP-1.9.4,Issued#1';


			$response = $returnCa->printByJournalLine($journalId);
			$datatoppic = $returnCa->datatoppic($journalId);

			renderView("page/report_returncause", [
				"datajson" => $response,
				"journalId" => $journalId,
				"create_date" => $create_date,
				"title" => $title,
				"toppic" => $toppic,
				"nameUser" => $datatoppic["nameUser"],
				"Ref" => $datatoppic["Ref"],


			]);
			 //exit(json_encode(["status" => 200, "message" => $datatoppic["nameUser"]]));
		} else {
			exit("error journal id not found.");
		}
	}

	public function allReturnType()
	{
		echo (new ReurnCauseService)->allReturnType();
	}




}
