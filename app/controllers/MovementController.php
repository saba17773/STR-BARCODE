<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\ItemService;
use App\Services\MovementService;
use App\Services\InventService;
use App\Services\FinalService;

class MovementController
{

	public function allMovementType()
	{
		echo (new MovementService)->allMovementType();
	}

	public function Requisitionlist($requ)
	{
		echo (new MovementService)->Requisitionlist($requ);
	}

	public function allMovementIssue()
	{
		echo (new MovementService)->allMovementIssue();
	}

	public function getLatestJournalTransByJournalId($journalId)
	{
		echo (new MovementService)->getLatestJournalTransByJournalId($journalId);
	}

	public function getInventJournalTable($journalId)
	{
		echo (new MovementService)->getInventJournalTable($journalId);
	}
	public function ItemMaster()
	{
		echo (new MovementService)->ItemMaster();
	}

	public function Batchmaster()
	{
		echo (new MovementService)->Batchmaster();
	}


	public function saveJournalTable()
	{
		$employee_code = filter_input(INPUT_POST, "employee_code");
		$division = filter_input(INPUT_POST, "division_value");
		$user = filter_input(INPUT_POST, "user");
		$pass = filter_input(INPUT_POST, "pass");


		$result = (new MovementService)->saveJournalTable($employee_code, $division, $user, $pass);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "journal" => $result["journal"], "test" => $result["test"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function saveJournalTableDestop()
	{
		$employee_code = filter_input(INPUT_POST, "employee_code");
		$division = filter_input(INPUT_POST, "division_value");
		$user = filter_input(INPUT_POST, "user");
		$pass = filter_input(INPUT_POST, "pass");
		$journal_type = filter_input(INPUT_POST, "Movement_type");

		if($journal_type == "MOVWHRTN"){
			$result = (new MovementService)->saveJournalRtTableDestop($employee_code, $division, $user, $pass, $journal_type);
		}else{
			$result = (new MovementService)->saveJournalTableDestop($employee_code, $division, $user, $pass, $journal_type);
		}
		

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "journal" => $result["journal"], "test" => $journal_type]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function saveInventJournalLine()
	{
		$journal_ID = filter_input(INPUT_POST, "journal_ID");
		$ItemID = filter_input(INPUT_POST, "ItemID");
		$press_Batch = filter_input(INPUT_POST, "press_Batch");
		$Requisition_Note = filter_input(INPUT_POST, "Requisition_Note");
		$qty = filter_input(INPUT_POST, "qty");
		$checkstatus = filter_input(INPUT_POST, "checkstatus");
		$ID_row = filter_input(INPUT_POST, "ID_row");
		$TemplateSN = filter_input(INPUT_POST, "TemplateSN");
		$status = filter_input(INPUT_POST, "status");


		$result = (new MovementService)->saveInventJournalLine($journal_ID, $ItemID, $press_Batch, $Requisition_Note, $qty, $checkstatus, $ID_row, $TemplateSN, $status);
		//
		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" =>  $result["message"]]);
		}
	}

	public function EditreacordLine($id, $check, $junalId)
	{

		$Idid = explode("%20", $id);
		$result = (new MovementService)->EditreacordLine($Idid[0], $check,$junalId);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => 'SUSCESS']);
		} else {
			echo json_encode(["status" => 404, "message" => 'Error']);
		}
	}

	public function checkSN($id, $check)
	{
		//$Idid = explode("%20",$id);

		$result = (new MovementService)->checkSN($id, $check);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "item" => $result["item"], "batch" => $result["batch"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function save()
	{
		$id = filter_input(INPUT_POST, "id");
		$description = filter_input(INPUT_POST, "description");

		$result = (new MovementService)->save($id, $description);

		if ($result === 200) {
			echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ดำเนินการไม่สำเร็จ"]);
		}
	}

	public function saveMovementIssue()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$requsition = filter_input(INPUT_POST, "requsition_value");
		$journalId = filter_input(INPUT_POST, "journalId");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่ในระบบแล้ว"]));
		}

		if ($_SESSION['user_warehouse'] === 2) { // final
			if ((new FinalService)->isFinalReceiveDateExist($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
			}

			if ((new InventService)->checkWarehouseTransReceiveDate($barcode) === true) {
				exit(json_encode(["status" => 404, "message" => "Warehouse Trans Receive Date ไม่เป็นค่าว่าง"]));
			}
		} else if ($_SESSION['user_warehouse'] === 3) { // FG
			if ((new InventService)->checkWarehouseReceiveDate($barcode) === false) {
				exit(json_encode(["status" => 404, "message" => "ไม่มี Warehouse Receive Date"]));
			}
		}

		$result = (new MovementService)->saveMovementIssue($barcode, $requsition, $journalId);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => "Movement Successful!"]));
		}
	}

	public function saveReverseOK()
	{
		$barcode = filter_input(INPUT_POST, "barcodeForOK");
		$auth = filter_input(INPUT_POST, "auth");

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้อบ."]));
		}

		if ((new InventService)->isReverse($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่แล้วในระบบ"]));
		}

		if ((new FinalService)->isFinalReceiveDateExist($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
		}

		$result = (new MovementService)->saveReverseOK($barcode, $auth);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Reverse Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}
	}

	public function saveReverseScrap()
	{
		$barcode = filter_input(INPUT_POST, "barcode");
		$defect = filter_input(INPUT_POST, "defect");
		$auth = filter_input(INPUT_POST, "auth");

		if ($defect === NULL || $defect === "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isReceived($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Received."]));
		}

		if ((new ItemService)->isItem($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้อบ."]));
		}

		if ((new InventService)->isReverse($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode มีอยู่แล้วในระบบ"]));
		}

		if ((new FinalService)->isFinalReceiveDateExist($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ไม่เท่ากับ Recived to Final."]));
		}

		$result = (new MovementService)->saveReverseScrap($barcode, $defect, $auth);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Movement Reverse Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}
	}

	public function completeIssue()
	{
		$journalId = filter_input(INPUT_POST, "journalId");

		$result = (new MovementService)->completeIssue($journalId);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Complete Successful!"]));
		} else {
			exit(json_encode(["status" => 404, "message" => $result]));
		}
	}

	public function printIssueByJournalID($journalId)
	{
		$movement = new MovementService;

		if (isset($journalId)) {
			$mode = $_GET["mode"];
			$create_date = $_GET["create_date"];
			$title = '';
			$issue = '';
			if ($mode === 'MOV') {
				$title = 'Final Withdrawal Report';
				$issue = 'FM-MP-1.15.3,Issued#1';
				$check = 1;
				$toppic = "รายงานการเบิกยาง(เบิกไม่นำกลับ)";
				$checkreport = 0 ;
			} else if ($mode === 'MOVWH') {
				$title = 'Finish Good Withdrawal Report';
				$issue = 'FM-MP-1.15.7, Issued#1';
				$check = 2;
				$toppic = "รายงานการเบิกยาง";
				$checkreport = 0 ;
			}else if ($mode === 'MOVRTN') {
				$title = 'Final Withdrawal Report';
				$issue = 'FM-MP-1.15.3,Issued#1';
				$check = 1;
				$toppic = "รายงานการเบิกยาง(เบิกนำกลับ)";
				$checkreport = 0 ;
			}else if ($mode === 'MOVWHRTN') {
				$title = 'Finish Good Withdrawal Report';
				$issue = 'FM-MP-1.15.6, Issued#1';
				$check = 2;
				$toppic = "รายงานการเบิกยาง(เบิกนำกลับ)";
				$checkreport = 1 ;
			}

			$response = $movement->printByJournalType($journalId, $mode);
			$totalqty = $movement->printByJournalTypeTotal($journalId);

			// echo "<pre>" . print_r($mode, true) . "</pre>"; 
			// exit();
			if($checkreport == 0){
				renderView("page/movement_issue_printing", [
					"datajson" => $response,
					"journalId" => $journalId,
					"create_date" => $create_date,
					"title" => $title,
					"issue" => $issue,
					"check" => $check,
					"totalqty" => $totalqty,
					"toppic" => $toppic
				]);
			}else {
				renderView("page/movement_issue_printing_return", [
					"datajson" => $response,
					"journalId" => $journalId,
					"create_date" => $create_date,
					"title" => $title,
					"issue" => $issue,
					"check" => $check,
					"totalqty" => $totalqty,
					"toppic" => $toppic
				]);
			}
			
		} else {
			exit("error journal id not found.");
		}
	}


	//printLine
	public function printIssueByJournalLine($journalId)
	{
		$movement = new MovementService;

		// echo $mode ; exit();

		if (isset($journalId)) {
			$create_date = $_GET["create_date"];

			$title = '';
			$issue = '';
			if ($mode === 'MOV') {
				$title = 'Final Withdrawal Report';
				$issue = 'FM-MP-1.15.4,Issued#1';
			} else {
				$title = 'Finish Good Withdrawal Report';
				$issue = 'FM-MP-1.15.4,Issued#1';
			}
			$checkType = $movement->chekType($journalId);
			$response = $movement->printByJournalLine($journalId);
			//  echo $checkType; exit();
			if($checkType == 'MOVWHRTN'){
				renderView("page/movement_Line_printingRNT", [
					"datajson" => $response,
					"journalId" => $journalId,
					"create_date" => $create_date,
					"title" => $title,
					"issue" => $issue
				]);
			}else if($checkType == 'MOVWH'){
				renderView("page/movement_Line_printingRBB", [
					"datajson" => $response,
					"journalId" => $journalId,
					"create_date" => $create_date,
					"title" => $title,
					"issue" => 'FM-MP-1.15.5,Issued#1'
				]);
			}else{
				renderView("page/movement_Line_printing", [
					"datajson" => $response,
					"journalId" => $journalId,
					"create_date" => $create_date,
					"title" => $title,
					"issue" => $issue
				]);
			}
			
			// exit(json_encode(["status" => 200, "message" => $response]));
		} else {
			exit("error journal id not found.");
		}
	}


	public function qaReverse()
	{
		renderView('page/movement_reverse');
	}

	public function checkWithdrawal()
	{
		$WithdrawalID = filter_input(INPUT_POST, "search");

		$result = (new MovementService)->checkWithdrawal($WithdrawalID);
		//
		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function SaveWithdrawal()
	{
		$barcode = filter_input(INPUT_POST, "serchbarcode");
		$item = filter_input(INPUT_POST, "item");
		$JournalType = filter_input(INPUT_POST, "JournalType");
		$JournalId = filter_input(INPUT_POST, "journal_ID_type");
		$RequsitionID_code = filter_input(INPUT_POST, "RequsitionID_code");
		$checktypeserch = filter_input(INPUT_POST, "CheckTypeserch");
		$TemplateSerialNo = filter_input(INPUT_POST, "TemplateSerialNo");
		$result = (new MovementService)->SaveWithdrawal($barcode, $item, $JournalType, $JournalId, $RequsitionID_code, $checktypeserch, $TemplateSerialNo);

		if ($result["status"] === 200) {
			echo json_encode([
				"status" => 200,
				"message" => $result["message"],
				"barcode" => $result["barcode"],
				"Batch" => $result["Batch"],
				"CuringCode" => $result["CuringCode"]
			]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	//PostBack
	public function checkStatusPostBack($InventJournalID)
	{
		$result = (new MovementService)->checkStatusPostBack($InventJournalID);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function checkAuthorizePostBack()
	{
		$username = filter_input(INPUT_POST, "userPostBack");
		$password = filter_input(INPUT_POST, "passPostBack");


		$result = (new MovementService)->checkAuthorizePostBack($username, $password);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function UpdatePostBack($InventJournalID)
	{
		$username = filter_input(INPUT_POST, "userPostBack");
		$result = (new MovementService)->UpdatePostBack($InventJournalID, $username);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function printIssueByJournalIDSummary($journalId)
	{
		$movement = new MovementService;

		if (isset($journalId)) {
			$mode = $_GET["mode"];
			$create_date = $_GET["create_date"];
			$title = '';
			$issue = '';
			if ($mode === 'MOV') {
				$title = 'Final Withdrawal Report';
				$issue = 'FM-MP-1.9.3,Issued#1';
				$check = 1;
			} else {
				$title = 'Finish Good Withdrawal Report';
				$issue = 'FM-MP-1.9.4,Issued#1';
				$check = 2;
			}

			$response = $movement->printByJournalTypeSummary($journalId, $mode);

			renderView("page/movement_issue_printingSummary", [
				"datajson" => $response,
				"journalId" => $journalId,
				"create_date" => $create_date,
				"title" => $title,
				"issue" => $issue,
				"check" => $check
			]);
		} else {
			exit("error journal id not found.");
		}
	}
}
