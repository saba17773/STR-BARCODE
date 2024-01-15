<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;
use App\Components\Security;
use App\V2\Database\Handler;
use App\V2\Batch\BatchAPI;

class MovementService
{
	public function printByJournalType($journalId, $mode)
	{
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			J.ItemID
			,CONVERT(time,J.CreateDate)[time_create]
			,I.TemplateSerialNo
			,I.ItemID
			,IT.NameTH
			,I.CuringCode
			,R.Description[Note]
			,IJ.EmpCode
			,E.FirstName
			,E.LastName
			,E.DivisionCode
			,D.Description[Department]
			,J.CreateBy
			,ITS.Batch
			,U.Name
			,1[qty]
			,ROW_NUMBER() OVER(ORDER BY name ASC) AS Row
			,PM.BOI
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode
			--AND J.ItemID=I.ItemID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN ItemMaster IT ON (
        CASE
            WHEN SUBSTRING(I.ItemID, 1, 1) = 'Q' THEN REPLACE(I.ItemID, 'Q', 'I')
            ELSE I.ItemID
        END
        ) = IT.ID
			LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
			WHERE IJ.JournalTypeID = '$mode'
			AND J.InventJournalID = ?
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$journalId
			]
		);
	}

	public function printByJournalTypeTotal($journalId){
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) AS TOTAL
				,InventJournalID 
			FROM InventJournalTrans 
			WHERE InventJournalID = ?
			GROUP BY InventJournalID",
			[
				$journalId
			]
		);
		return $query[0]['TOTAL'];
	}

	public function printByJournalLine($journalId)
	{
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			IJ.InventJournalID,
			IJ.CreateDate,
			IT.ItemID,
			IM.NameTH,
			IT.Batch,
			RN.Description [RN],
			IT.QTY,
			EM.FirstName + ' ' + EM.LastName AS name,
			DM.Description,
			ROW_NUMBER() OVER(ORDER BY IJ.InventJournalID ASC) AS Row
			FROM InventJournalTable IJ
  			LEFT JOIN InventJournalLine IT ON IT.InventJournalID = IJ.InventJournalID
    		LEFT JOIN ItemMaster IM ON IM.ID = IT.ItemID
    		LEFT JOIN RequsitionNote RN ON RN.ID = IT.RequsitionID
				LEFT JOIN Employee EM ON EM.Code = IJ.EmpCode
				LEFT JOIN DivisionMaster DM ON DM.Code = IJ.Department WHERE IJ.InventJournalID = ?
				ORDER BY CONVERT(time,IJ.CreateDate) ASC",
			[
				$journalId
			]
		);
	}

	public function allMovementType()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM JournalType WHERE ID in ('MOV','MOVWH','MOVRTN','MOVWHRTN')"
		);
	}

	public function Requisitionlist($requ)
	{
		if ($requ == 'MOV') {
			$datarequst = 'Final';
		}
		if ($requ == 'MOVWH' || $requ == 'MOVWHRTN') {
			$datarequst = 'FinishGood';
		}
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM RequsitionNote WHERE $datarequst = ?",
			[1]
		);
	}

	public function getLatestJournalTransByJournalId($journalId)
	{
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT
			IT.CuringCode,
			JT.BarcodeID,
			RN.Description as RN,
			U.Name as CreateBy,
			JT.CreateDate
			FROM InventJournalTrans JT
			LEFT JOIN InventTable IT ON JT.BarcodeID = IT.Barcode
			LEFT JOIN RequsitionNote RN ON RN.ID = JT.RequsitionID
			LEFT JOIN UserMaster U ON U.ID = JT.CreateBy
			WHERE JT.InventJournalID = ?
			ORDER BY JT.CreateDate DESC",
			[$journalId]
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [
				'BarcodeID' => Security::_encode($v->BarcodeID),
				'CuringCode' => $v->CuringCode,
				'RN' => $v->RN,
				'CreateBy' => $v->CreateBy,
				'CreateDate' => $v->CreateDate
			];
		}

		return json_encode($temp);
	}

	public function ItemMaster()
	{
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT ID,NameTH,ItemGroup,UnitID from Itemmaster where Itemgroup = ? and  UnitID = ?",
			[
				'FG',
				'PCS'
			]
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [

				'ID' => $v->ID,
				'NameTH' => $v->NameTH

			];
		}

		return json_encode($temp);
	}

	public function Batchmaster()
	{
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT Batch from Inventtable group by Batch order by Batch desc"
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [

				'Batch' => $v->Batch
			];
		}

		return json_encode($temp);
	}

	public function getInventJournalTable($journalId)
	{
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT
			IL.ID,
			IL.InventJournalID,
			IL.ItemID,
			IL.Batch,
			IL.QTY,
			IL.Remain,
			IL.Issue,
			IL.RequsitionID,
			IL.Status,
			ST.Description [STN],
			IL.CreateDate,
			IL.CreateBy,
			IM.NameTH,
			IT.CreateDate [dateCreate],
			RN.Description [RN],
			IT.JournalTypeID,
			IL.TemplateSerialNo

			FROM InventJournalLine  IL
			LEFT JOIN InventJournalTable IT ON IT.InventJournalID = IL.InventJournalID
			LEFT JOIN ItemMaster IM ON IM.ID = IL.ItemID
			LEFT JOIN RequsitionNote RN ON RN.ID = IL.RequsitionID
			LEFT JOIN Status  ST ON ST.ID = IL.Status
			 WHERE IL.InventJournalID = ?",
			[$journalId]
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [
				'InventJournalID' => Security::_encode($v->InventJournalID),
				'ItemID' => $v->ItemID,
				'RN' => $v->RN,
				'Batch' => $v->Batch,
				'QTY' => $v->QTY,
				'Remain' => $v->Remain,
				'Issue' => $v->Issue,
				'RequsitionID' => $v->RequsitionID,
				'CreateDate' => $v->CreateDate,
				'Status' => $v->Status,
				'ID' => $v->ID,
				'dateCreate' => $v->dateCreate,
				'NameTH' => $v->NameTH,
				'RN' => $v->RN,
				'JournalTypeID' => $v->JournalTypeID,
				'StatusName' => $v->STN,
				'TemplateSerialNo' => $v->TemplateSerialNo

			];
		}

		return json_encode($temp);
	}


	public function createNew(
		$barcode,
		$employee,
		$disposal,
		$division,
		$journalId
	) {
		$barcode_decode = Security::_decode($barcode);
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);
		// get user location
		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		// insert invent journal table
		$journalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$journalId, // Invent Journal ID
				"MOV", // journal type id
				"C001", // customer
				$division,
				$employee,
				1, // open
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		if ($journalTable) {
			sqlsrv_rollback($conn);
			return "journal taable move in error";
		}

		// generate trans id
		$trans_id = Utils::genTransId(Security::_decode($barcode_decode));

		// insert invent journal trans
		$journalTrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTrans(
				ID,
				InventJournalID,
				ItemID,
				QTY,
				BarcodeID,
				DisposalID,
				CreateDate,
				CreateBy,
				Company
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$trans_id,
				$journalId,
				"I-0044460",
				1,
				$barcode,
				$disposal,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		if ($journalTrans) {
			sqlsrv_rollback($conn);
			return "journal trans move in error";
		}

		// update invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ? ,
			WarehouseID = ? ,
			LocationID = ?,
			Status = 4, -- issue
			Company = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$disposal,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if ($update_inventtable) {
			sqlsrv_rollback($conn);
			return "invent table move in error";
		}

		// Generate Trans ID
		$trans_id = Utils::genTransId($barcode_decode);

		// Insert invent Trans
		$insert_inventtrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID ,
				Barcode,
				CodeID ,
				Batch,
				DisposalID ,
				DefectID,
				WarehouseID ,
				LocationID,
				QTY ,
				UnitID,
				DocumentTypeID ,
				Company,
				CreateBy ,
				CreateDate
			)VALUES(
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?
			)",
			[
				$trans_id,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"], // gt code
				$get_barcode_info[0]["Batch"], // batch
				$disposal, // disposal
				null, // defect
				$get_barcode_info[0]["WarehouseID"], // wh
				$get_barcode_info[0]["LocationID"], // lc
				1, // qty
				1, // unit
				2, // document id => issue
				$_SESSION["user_login"],
				$_SESSION["user_company"],
				$date
			]
		);

		if ($insert_inventtrans) {
			sqlsrv_rollback($conn);
			return "invent trans move out error";
		}

		// Update Onhand
		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_barcode_info[0]["WarehouseID"],
		// 		$get_barcode_info[0]["LocationID"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$get_barcode_info[0]["Company"]
		// 	]
		// );

		// if ($move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "onhand move out error";
		// }

		sqlsrv_commit($conn);
		return 200;

		// if (
		// 	$update_inventtable &&
		// 	$insert_inventtrans
		// ) {
		// 	sqlsrv_commit($conn);
		// 	return 200;
		// } else {
		// 	sqlsrv_rollback($conn);
		// 	return "Error";
		// }
	}

	public function save($id, $desc)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE JournalType SET Description = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO JournalType(ID, Description)
			VALUES (?, ?)",
			[
				$desc,
				$id,
				$id,
				$desc
			]
		);

		if ($query) {
			return 200;
		} else {
			return 404;
		}
	}

	public function saveJournalTable($emp, $division, $data1, $data2, $journal_type = "MOV")
	{
		$conn = Database::connect();

		if ($_SESSION['user_warehouse'] === 3) {
			$journal_type = 'MOVWH';
		} else if ($_SESSION['user_warehouse'] === 2) {
			$journal_type = 'MOV';
		}

		$year = date("Y");
		$date = date("m-d-Y H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$queryYear = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal,Years FROM Sequeue"
		);
		if (!$queryYear) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}
		
		
		

		$getEmployeeInfo = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM Employee
			WHERE Code = ? AND Username = ?",
			[$emp, $data1]
		);
		$checklogin = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM UserMaster
			WHERE Username =? AND Password=?",
			[$getEmployeeInfo[0]["Username"], $data2]

		);

		if (!$checklogin) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "ข้อมูลไม่ถูกต้อง"
			];
		}

		if ($queryYear[0]["Years"] == $year) {
			$conn = Database::connect();
			$updateJournalSequeue = Sqlsrv::update(
				$conn,
				"UPDATE Sequeue SET SeqJournal += 1"
			);
		}

		if ($queryYear[0]["Years"] <> $year) {
			$conn = Database::connect();

			$updateJournalSequeue = Sqlsrv::update(

				$conn,
				"UPDATE Sequeue SET SeqJournal = ?, Years = ?",
				[1, $year]

			);
		}

		if (!$updateJournalSequeue) {
			sqlsrv_rollback($conn);
			return "ไม่สามารถอัพเดทได้";
		}

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal FROM Sequeue"
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		// return [
		// 	"status" => 404,
		// 	"message" =>  $queryYear[0]["Years"]. "==". $year
		// ];


		$insertInventJournalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company,
				MovementBy
			) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?,?)",
			[
				"W" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				$journal_type,
				null,
				$getEmployeeInfo[0]["DivisionCode"],
				$emp,
				1,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"],
				$checklogin[0]["Username"]
			]
		);

		if ($updateJournalSequeue && $insertInventJournalTable) {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"journal" => "W" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				"test" => $checklogin[0]["Username"]
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}
	}

	public function saveJournalTableDestop($emp, $division, $data1, $data2, $journal_type)
	{
		$conn = Database::connect();

		// if ($_SESSION['user_warehouse'] === 3) {
		// 	$journal_type = 'MOVWH';
		// } else if ($_SESSION['user_warehouse'] === 2) {
		// 	$journal_type = 'MOV';
		// }

		$year = date("Y");
		$date = date("m-d-Y H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$queryYear = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal,Years FROM Sequeue WHERE SeqLpn = 115"
		);

		if (!$queryYear) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		$getEmployeeInfo = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM Employee
			WHERE Code = ? AND Username = ?",
			[$emp, $data1]
		);
		$checklogin = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM UserMaster
			WHERE Username =? AND Password=?",
			[$getEmployeeInfo[0]["Username"], $data2]

		);

		if (!$checklogin) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "ข้อมูลไม่ถูกต้อง"
			];
		}


		if ($queryYear[0]["Years"] == $year) {
			$conn = Database::connect();
			$updateJournalSequeue = Sqlsrv::update(
				$conn,
				"UPDATE Sequeue SET SeqJournal += 1 WHERE SeqLpn = 115"
			);
		}

		if ($queryYear[0]["Years"] <> $year) {
			$conn = Database::connect();

			$updateJournalSequeue = Sqlsrv::update(

				$conn,
				"UPDATE Sequeue SET SeqJournal = ?, Years = ? WHERE SeqLpn = 115",
				[1, $year]

			);
		}



		if (!$updateJournalSequeue) {
			sqlsrv_rollback($conn);
			return "ไม่สามารถอัพเดทได้";
		}
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal,Years FROM Sequeue"
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}



		// return [
		// 	"status" => 404,
		// 	"message" =>$query[0]["SeqJournal"]
		// 	//"W".substr($year,-2)."-".str_pad($query[0]["SeqJournal"], 6, "0",STR_PAD_LEFT)
		// ];
		// exit();
		$insertInventJournalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company,
				MovementBy
			) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?,?)",
			[
				"W" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				$journal_type,
				null,
				$getEmployeeInfo[0]["DivisionCode"],
				$emp,
				1,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"],
				$checklogin[0]["Username"]
			]
		);

		if ($updateJournalSequeue && $insertInventJournalTable) {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"journal" => "W" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				"test" => $checklogin[0]["Username"]
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}
	}

	public function saveInventJournalLine($journal_ID, $ItemID, $press_Batch, $Requisition_Note, $qty, $checkstatus, $ID_row, $TemplateSN, $status)
	{
		$conn = Database::connect();


		$year = date("Y");
		$date = date("m-d-Y H:i:s");
		$DaiId =  date("Y-m-d H:i:s");
		$creatrdate = date_create($DaiId);
		$IDdate =  date_format($creatrdate, "YmdHis");
		$remain = $qty;

		if ($checkstatus == 0) {
			$InventJournalLine = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventJournalLine(
					ID,
					InventJournalID,
					TemplateSerialNo,
					ItemID,
					Batch,
					QTY,
					Remain,
					Issue,
					RequsitionID,
					Status,
					CreateDate,
					CreateBy
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ? ,?,?,?)",
				[
					$IDdate,
					$journal_ID,
					$TemplateSN,
					$ItemID,
					$press_Batch,
					$qty,
					$remain,
					0,
					$Requisition_Note,
					1,
					$date,
					$_SESSION["user_login"]
				]
			);
			if ($InventJournalLine) {
				sqlsrv_commit($conn);
				return [
					"status" => 200,

					"message" => $IDdate
				];
			} else {
				sqlsrv_rollback($conn);
				return [
					"status" => 404,
					"message" => "error"
				];
			}
		}
		if ($checkstatus == 1) {

			$checkIssue = Sqlsrv::queryArray(
				$conn,
				"SELECT ItemID,
				Batch,
				Status, 
				Issue, 
				RequsitionID, 
				QTY 
				FROM InventJournalLine   WHERE ID = ?
				-- AND ItemID = ? 
				 ",
				[
					$ID_row, $ItemID
				]
	
			);
			
			// status 1 
			if ($checkIssue[0]["Status"] == 1){
				$remain = $remain;
				$statusL = 1;
				$Requisition_Note = $Requisition_Note;
				
			}
			// status 2
			else if ($checkIssue[0]["Status"] == 2){

				
				if($qty > $checkIssue[0]["Issue"]){

					$remain = $qty - $checkIssue[0]["Issue"];
					$statusL = 2;
					$Requisition_Note = $checkIssue[0]["RequsitionID"];

				}
				else if($qty ==  $checkIssue[0]["Issue"]){
					
					$remain = 0;
					$statusL = 3;
					$Requisition_Note = $checkIssue[0]["RequsitionID"];
					

				}else{
					return [
						"status" => 404,
						"message" => "จำนวนที่แก้ไขน้อยกว่าจำนวนที่ยิงเบิก"
					];
				}
				
			}else{
				// status 3
				if($qty > $checkIssue[0]["Issue"]){

					$remain = $qty - $checkIssue[0]["Issue"];
					$statusL = 2;
					$Requisition_Note = $checkIssue[0]["RequsitionID"];
				}else{
					return [
						"status" => 404,
						"message" => "จำนวนที่แก้ไขน้อยกว่าหรือเท่ากับจำนวนที่ยิงเบิก"
					];
				}
			}
				
			$InventJournalLine = Sqlsrv::update(
				$conn,
				"UPDATE InventJournalLine SET
					InventJournalID = ?,
					ItemID = ?,
					TemplateSerialNo = ?,
					Batch = ?,
					QTY = ?,
					Remain = ?,
					Issue = ?,
					RequsitionID = ?,
					Status = ?,
					CreateDate = ?,
					CreateBy = ?
					WHERE ID = ?",
				[
					$journal_ID,
					//$checkIssue[0]["ItemID"],
					$ItemID,
					$TemplateSN,
					$press_Batch,
					$qty,
					$remain,
					$checkIssue[0]["Issue"],
					// $checkIssue[0]["RequsitionID"],
					$Requisition_Note,
					// $statusL,
					1,
					$date,
					$_SESSION["user_login"],
					$ID_row
				]
			);

			$insert_log = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventjournalLineLog (
				TransactionID,
				JournalID,
				ItemID,
				TemplateSerialNo,
				Batch,
				RequsitionID,
				OldQTY,
				NewQTY,
				OldStatus,
				NewStatus,
				Type,
				UpdateBy,
				UpdateDate
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",	
				[
					$IDdate,
					$journal_ID,
					$ItemID,
					$TemplateSN,
					$press_Batch,
					$Requisition_Note,
					$checkIssue[0]["QTY"],
					$qty,
					$checkIssue[0]["Status"],
					$statusL,
					1,//UPDATE
					$_SESSION["user_login"],
					$date
				]
			);
			if ($InventJournalLine && $insert_log) {
				sqlsrv_commit($conn);

				$checkcomplete = Sqlsrv::queryArray(
					$conn,
					"SELECT * FROM InventJournalLine 
					WHERE InventJournalID = ?
					AND Status <> 3 ",
					[
						$journal_ID
					]
				);

				if(count($checkcomplete) == 0){
					$updatestatuscomplete = Sqlsrv::update(
						$conn,
						"UPDATE InventJournalTable SET Status = '3' WHERE InventJournalID = ?",
						[
							$journal_ID
						]
					);
					sqlsrv_commit($conn);

				}
				return [
					"status" => 200,

					"message" => "SUSCESS"
				];
			} else {
				sqlsrv_rollback($conn);
				return [
					"status" => 404,
					"message" => "error"
				];
			}
		}
	}

	public function EditreacordLine($id, $check,$junalId)
	{
		$conn = Database::connect();


		$year = date("Y");
		$date = date("m-d-Y H:i:s");

		$checkDelete = Sqlsrv::queryArray(
			$conn,
			"SELECT  
			InventJournalID,
			TemplateSerialNo,
			ItemID,
			Batch,
			QTY,
			RequsitionID,
			Status,
			CreateBy FROM InventJournalLine   WHERE ID = ? ",
			[
				$id
			]
		);
		$checkupdatejourtb = Sqlsrv::queryArray(
			$conn,
			"SELECT  
			InventJournalID,
			Status,
			CreateBy FROM InventJournalTable   WHERE InventJournalID = ? ",
			[
				$checkDelete[0]["InventJournalID"]
			]
		);

		if ($check == 0) {

			$insert_log = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventjournalLineLog (
				TransactionID,
				JournalID,
				ItemID,
				TemplateSerialNo,
				Batch,
				RequsitionID,
				OldQTY,
				NewQTY,
				OldStatus,
				NewStatus,
				Type,
				UpdateBy,
				UpdateDate
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",	
				[
					$id,
					$checkDelete[0]["InventJournalID"],
					$checkDelete[0]["ItemID"],
					$checkDelete[0]["TemplateSerialNo"],
					$checkDelete[0]["Batch"],
					$checkDelete[0]["RequsitionID"],
					$checkDelete[0]["QTY"],
					NULL,
					$checkDelete[0]["Status"],
					NULL,
					0,//DELETE
					$_SESSION["user_login"],
					$date
				]
			);
			if($insert_log == true)
			{
				$InventJournalLineEdit = sqlsrv_query(
					$conn,
					"DELETE InventJournalLine WHERE ID = ?",
					[
						$id
					]
				);
						

				if ($InventJournalLineEdit) {
					sqlsrv_commit($conn);

					$checkcomplete = Sqlsrv::queryArray(
						$conn,
						"SELECT * FROM InventJournalLine 
						WHERE InventJournalID = ?
						AND Status <> 3 ",
						[
							$junalId
						]
					);

					$check_row = Sqlsrv::queryArray(
						$conn,
						"SELECT * FROM InventJournalLine 
						WHERE InventJournalID = ? ",
						[
							$junalId
						]
					);
	
					if(count($checkcomplete) == 0 && $checkupdatejourtb[0]["Status"] != 3 && count($check_row) > 0){
						$updatestatuscomplete = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalTable SET Status = '3' WHERE InventJournalID = ?",
							[
								$junalId
							]
						);
						sqlsrv_commit($conn);
					}
					else {
						$updatestatuscomplete = Sqlsrv::update(
							$conn,
							" UPDATE InventJournalTable SET 
							  Status =  ?
							  WHERE InventJournalID = ?",
							[
								$checkupdatejourtb[0]["Status"],
								$junalId
							]
						);
						sqlsrv_commit($conn);
					}
					return [
						"status" => 200,
	
						"message" => "delete complete"
					];
				} else {
					sqlsrv_rollback($conn);
					return [
						"status" => 404,
						"message" => "error"
					];
				}
			}
		}

		if ($check == 1) {
			$InventJournalLineEdit = Sqlsrv::update(
				$conn,
				"UPDATE InventJournalTable SET
					Status = ? WHERE InventJournalID = ?",
				[
					6,
					$id
				]
			);
			if ($InventJournalLineEdit) {
				sqlsrv_commit($conn);
				return [
					"status" => 200,

					"message" => "update complete"
				];
			} else {
				sqlsrv_rollback($conn);
				return [
					"status" => 404,
					"message" => "error"
				];
			}
		}
	}

	public function checkSN($id, $check)
	{

		$conn = Database::connect();
		$checkSN = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM InventTable WHERE TemplateSerialNo = ?",
			[
				$id
			]

		);

		$checkwithdrow = Sqlsrv::queryArray(
			$conn,
			"SELECT InventJournalID,JournalTypeID FROM InventJournalTable WHERE InventJournalID = ?",
			[
				$check
			]

		);


		if (!$checkSN) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "Serial ไม่มีในระบบ"
			];
		} else {
			if ($checkSN[0]["Status"] !== 1) {
				sqlsrv_rollback($conn);
				return [
					"status" => 404,
					"message" => "Barcode Status not Receive"
				];
			} else {

				if ($checkwithdrow[0]["JournalTypeID"] == 'MOV' && $checkSN[0]["WarehouseID"] !== 2) {
					sqlsrv_rollback($conn);
					return [
						"status" => 404,
						"message" => "JournalType Not Match"
					];
				}

				if ($checkwithdrow[0]["JournalTypeID"] == 'MOV' && $checkSN[0]["WarehouseID"] == 2) {
					sqlsrv_commit($conn);
					return [
						"status" => 200,
						"item" => $checkSN[0]["ItemID"],
						"batch" => $checkSN[0]["Batch"]
					];
				}

				if ($checkwithdrow[0]["JournalTypeID"] == 'MOVWH' && $checkSN[0]["WarehouseID"] !== 3) {
					sqlsrv_rollback($conn);
					return [
						"status" => 404,
						"message" => "JournalType Not Match"
					];
				}

				if ($checkwithdrow[0]["JournalTypeID"] == 'MOVWH' && $checkSN[0]["WarehouseID"] == 3) {
					sqlsrv_commit($conn);
					return [
						"status" => 200,
						"item" => $checkSN[0]["ItemID"],
						"batch" => $checkSN[0]["Batch"]
					];
				}
			}
		}
	}


	public function saveMovementIssue($barcode, $requsition, $journalId)
	{
		$conn = Database::connect();

		$date = Date('Y-m-d H:i:s');
		$barcode_decode = Security::_decode($barcode);

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$removeLPNIDFrom = sqlsrv_query(
			$conn,
			"UPDATE InventTable
			SET LPNID = null
			WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);

		$updateLPNInUse = sqlsrv_query(
			$conn,
			"UPDATE LPNMaster
			SET QtyInUse -= 1,
			Remain += 1
			WHERE LPNID = (
				SELECT TOP 1 LPNID FROM LPNLine
				WHERE Barcode = ?
			)",
			[
				$barcode_decode
			]
		);

		$removeLPNLineByBarcode = sqlsrv_query(
			$conn,
			"DELETE FROM LPNLine
			WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);

		$getItem = Sqlsrv::queryArray(
			$conn,
			"SELECT ItemID FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$transId = Utils::genTransId($barcode_decode);

		// Insert Journal Trans
		$insertJournalTrans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTrans(
				ID,
				InventJournalID,
				ItemID,
				QTY,
				BarcodeID,
				RequsitionID,
				CreateDate,
				CreateBy,
				Company
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$transId,
				$journalId,
				$getItem[0]["ItemID"],
				1,
				$barcode_decode,
				$requsition,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"]
			]
		);

		// get user location
		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[Security::_decode($barcode)]
		);

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?,
			WarehouseID = ?,
		  LocationID = ?,
			Status = 4, -- issue
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				11, // Movement
				$get_barcode_info[0]["WarehouseID"], // WH
				$get_barcode_info[0]["LocationID"], // LC
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		// Generate trans id
		$transId = Utils::genTransId($barcode_decode);

		// insert trans move out
		$trans_move_out = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				InventJournalID,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$transId,
				$barcode_decode,
				$getItem[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				11, // Movement
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$journalId,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$getItem[0]["ItemID"],
		// 		$get_barcode_info[0]["WarehouseID"],
		// 		$get_barcode_info[0]["LocationID"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$get_barcode_info[0]["Company"]
		// 	]
		// );

		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		sqlsrv_commit($conn);
		return 200;

		// if (
		// 	$trans_move_out &&
		// 	$update_inventtable &&
		// 	$move_out_onhand
		// ) {

		// 	sqlsrv_commit($conn);
		// 	return 200;
		// } else {
		// 	sqlsrv_rollback($conn);
		// 	return 404;
		// }
	}

	public function allMovementIssue()
	{
		$conn = Database::connect();
		$user_warehouse = $_SESSION["user_warehouse"];
		if ($_SESSION['user_name'] !== 'admin') {
			$warehouse_condition = ' U.Warehouse = ' . $user_warehouse;
		} else {
			$warehouse_condition = '\'A\'=\'A\'';
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {

			// for Mobile
			$sql = "SELECT
				IJ.InventJournalID,
				IJ.JournalTypeID,
				IJ.Department,
				IJ.EmpCode,
				DV.Description as Division,
				(E.FirstName +' '+E.LastName) as Name,
				S.Description as Status,
				U.Name as CreateBy,
				IJ.CreateDate
				FROM InventJournalTable IJ
				LEFT JOIN DivisionMaster DV ON DV.Code = IJ.Department
				LEFT JOIN Employee E ON IJ.EmpCode = E.Code
				LEFT JOIN Status S ON S.ID = IJ.Status
				LEFT JOIN UserMaster U ON U.ID = IJ.CreateBy
				WHERE S.ID = 1 AND " . $warehouse_condition;
			// echo $sql; exit;
		} else {

			// For Desktop
			$sql = "SELECT IJ.InventJournalID,
			IJ.JournalTypeID,
			IJ.Department,
			IJ.EmpCode,
			AM.PostbackMovement,
			DV.Description as Division,
			(E.FirstName +' '+E.LastName) as Name,
			S.ID as IdStatus,
			S.Description as Status,
			U.Name as CreateBy,
			IJ.CreateDate,
			CASE
				WHEN IJ.Status = 3 THEN (SELECT UMS.Name FROM UserMaster UMS WHERE UMS.ID = U.ID)
				ELSE NULL
			END [CompleteBy],
			IJ.CompleteDate,
			JT.Description
			FROM InventJournalTable IJ
			LEFT JOIN DivisionMaster DV ON DV.Code = IJ.Department
			LEFT JOIN Employee E ON IJ.EmpCode = E.Code
			LEFT JOIN Status S ON S.ID = IJ.Status
			LEFT JOIN UserMaster U ON U.ID = IJ.CreateBy
			LEFT JOIN UserMaster UU ON UU.ID = IJ.CompleteBy
			LEFT JOIN AuthorizeMaster AM ON AM.ID = U.Authorize
			LEFT JOIN JournalType JT ON JT.ID = IJ.JournalTypeID
			order by CreateDate desc";
		}

		// echo $sql; exit;

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function saveReverseOK($barcode, $auth)
	{
		$barcode_decode = Security::_decode($barcode);

		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		if ($get_barcode_info[0]["PressSide"] === "L") {
			$preess_side_for_update = "L";
		} else {
			$preess_side_for_update = "R";
		}

		$updatePressDateTime = sqlsrv_query(
			$conn,
			"UPDATE PressMaster
			SET $preess_side_for_update = ?
			WHERE ID = ?
			AND $preess_side_for_update = ?",
			[
				date('Y-m-d H:i:s', strtotime('-10 minute')),
				$get_barcode_info[0]["PressNo"],
				$get_barcode_info[0]["PressSide"]
			]
		);

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReverseReceiveLocation,
			R.WarehouseID as WarehouseReverseReceive,
			R.DisposalID as DisposalReverse
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			LEFT JOIN Location R ON L.ReverseReceiveLocation = R.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$ddate = new \DateTime($get_barcode_info[0]["DateBuild"]);

		// $week = date("Y") . "-" . $ddate->format("W");
		$week = (new BatchAPI)->getGreentireBatch($barcode_decode);

		$moveToReverseTable = sqlsrv_query(
			$conn,
			"INSERT INTO ReverseTable(
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			CreateBy,
			CreateDate
			)
			SELECT
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			?,
			?
			FROM InventTable
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$moveToReverseTable) {
			sqlsrv_rollback($conn);
			return "move to reverse table error";
		}

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable
			SET CuringDate = null,
			CuringCode = null,
			ItemID = null,
			PressNo = null,
			PressSide = null,
			MoldNo = null,
			TemplateSerialNo = null,
			UpdateBy = ?,
			UpdateDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			Batch = ?
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				$get_location[0]["DisposalReverse"],
				$get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				$week,
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error";
		}

		$delectCureTrans = sqlsrv_query(
			$conn,
			"DELETE  FROM CureTrans WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);
		if (!$delectCureTrans) {
			sqlsrv_rollback($conn);
			return "delete cure trans error";
		}

		// Generate trans id
		$trans_id = Utils::genTransId($barcode_decode);

		// insert trans move out
		$trans_move_out = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				13, // reverse
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type = issue
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// Gen trans id for transaction move in
		$trans_id = Utils::genTransId($barcode_decode);

		// transaction move in
		$trans_move_in = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_info[0]["GT_Code"],
				$week,
				$get_location[0]["DisposalReverse"],
				null,
				$get_location[0]["WarehouseReverseReceive"],
				$get_location[0]["ReverseReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			// return "insert trans move in error.";
			return Database::errors();
		}

		// move out onhand -1
		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_barcode_info[0]["WarehouseID"],
		// 		$get_barcode_info[0]["LocationID"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$get_barcode_info[0]["Company"]
		// 	]
		// );

		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		// // Move in onhand
		// $move_in_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand SET QTY += 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?
		// 	IF @@ROWCOUNT = 0
		// 	INSERT INTO Onhand
		// 	VALUES (?, ?, ?, ?, ?, ?)",
		// 	[
		// 		$get_barcode_info[0]["GT_Code"],
		// 		$get_location[0]["WarehouseReverseReceive"],
		// 		$get_location[0]["ReverseReceiveLocation"],
		// 		$week,
		// 		$_SESSION["user_company"],
		// 		$get_barcode_info[0]["GT_Code"],
		// 		$get_location[0]["WarehouseReverseReceive"],
		// 		$get_location[0]["ReverseReceiveLocation"],
		// 		$week,
		// 		1, // qty
		// 		$_SESSION["user_company"]
		// 	]
		// );

		// // echo Database::errors();
		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		sqlsrv_commit($conn);
		return 200;
	}

	public function saveReverseScrap($barcode, $defect, $auth)
	{
		$barcode_decode = Security::_decode($barcode);

		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		if ($get_barcode_info[0]["PressSide"] === "L") {
			$preess_side_for_update = "L";
		} else {
			$preess_side_for_update = "R";
		}

		$updatePressDateTime = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster
			SET $preess_side_for_update = ?
			WHERE ID = ?
			AND $preess_side_for_update = ?",
			[
				date('Y-m-d H:i:s', strtotime('-20 minute')),
				$get_barcode_info[0]["PressNo"],
				$get_barcode_info[0]["PressSide"]
			]
		);

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReverseReceiveLocation,
			R.WarehouseID as WarehouseReverseReceive,
			R.DisposalID as DisposalReverse
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			LEFT JOIN Location R ON L.ReverseReceiveLocation = R.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$ddate = new \DateTime($get_barcode_info[0]["DateBuild"]);
		// $week = date("Y") . "-" . $ddate->format("W");
		$week = (new BatchAPI)->getGreentireBatch($barcode_decode);

		$getWarehouseAndLocationToHold = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM Location
			WHERE DisposalID = 10 -- Hold
			AND WarehouseID = 4" // -- Curing Hold
		);

		$moveToReverseTable = sqlsrv_query(
			$conn,
			"INSERT INTO ReverseTable(
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			CreateBy,
			CreateDate
			)
			SELECT
			Barcode,
			DateBuild,
			BuildingNo,
			GT_Code,
			CuringDate,
			CuringCode,
			ItemID,
			Batch,
			QTY,
			Unit,
			PressNo,
			PressSide,
			MoldNo,
			TemplateSerialNo,
			CuredTireReciveDate,
			CuredTireLineNo,
			FinalReceiveDate,
			GateReceiveNo,
			XrayDate,
			XrayNo,
			QTechReceiveDate,
			WarehouseReceiveDate,
			WarehouseTransReceiveDate,
			LoadingDate,
			DONo,
			PickingListID,
			OrderID,
			DisposalID,
			WarehouseID,
			LocationID,
			Status,
			Company,
			UpdateBy,
			UpdateDate,
			?,
			?
			FROM InventTable
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$moveToReverseTable) {
			sqlsrv_rollback($conn);
			return "move to reverse table error";
		}

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable
			SET CuringDate = null,
			CuringCode = null,
			ItemID = null,
			PressNo = null,
			PressSide = null,
			MoldNo = null,
			TemplateSerialNo = null,
			UpdateBy = ?,
			UpdateDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			Status = 5, -- Hold
			Batch = ?
			WHERE Barcode = ?",
			[
				$_SESSION["user_login"],
				$date,
				10, //$get_location[0]["DisposalReverse"],
				$get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				$week,
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error";
		}

		$delectCureTrans = sqlsrv_query(
			$conn,
			"DELETE  FROM CureTrans WHERE Barcode = ?",
			[
				$barcode_decode
			]
		);
		if (!$delectCureTrans) {
			sqlsrv_rollback($conn);
			return "delete cure trans error";
		}

		// Generate trans id
		$trans_id = Utils::genTransId($barcode_decode);

		// insert trans move out
		$trans_move_out = sqlsrv_query(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				13, // reverse
				null, // defect
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type = issue
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// Gen trans id for transaction move in
		$trans_id = Utils::genTransId($barcode_decode);

		// transaction move in
		$trans_move_in = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift,
				AuthorizeBy
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_info[0]["GT_Code"],
				$week,
				10, // Hold
				// $get_location[0]["DisposalReverse"],
				$defect,
				$get_location[0]["WarehouseReverseReceive"],
				$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return sqlsrv_errors();
		}

		// move out onhand -1
		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_barcode_info[0]["WarehouseID"],
		// 		$get_barcode_info[0]["LocationID"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$get_barcode_info[0]["Company"]
		// 	]
		// );

		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		// // Move in onhand
		// $move_in_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand SET QTY += 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?
		// 	IF @@ROWCOUNT = 0
		// 	INSERT INTO Onhand
		// 	VALUES (?, ?, ?, ?, ?, ?)",
		// 	[
		// 		$get_barcode_info[0]["GT_Code"],
		// 		$get_location[0]["WarehouseReverseReceive"],
		// 		$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
		// 		$week,
		// 		$_SESSION["user_company"],
		// 		$get_barcode_info[0]["GT_Code"],
		// 		$get_location[0]["WarehouseReverseReceive"],
		// 		$getWarehouseAndLocationToHold[0]["ReverseReceiveLocation"],
		// 		$week,
		// 		1, // qty
		// 		$_SESSION["user_company"]
		// 	]
		// );

		// // echo Database::errors();
		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		sqlsrv_commit($conn);
		return 200;
	}

	public function completeIssue($journalId)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$isEmpty = Sqlsrv::hasRows(
			$conn,
			"SELECT InventJournalID FROM InventJournalTrans
			WHERE InventJournalID = ?",
			[$journalId]
		);

		if ($isEmpty === false) {
			return "ไม่มีรายการ";
		}

		$update = Sqlsrv::update(
			$conn,
			"UPDATE InventJournalTable
			SET Status = 3, -- Complete
			CompleteBy = ?,
			CompleteDate = ?
			WHERE InventJournalID = ?",
			[$_SESSION["user_login"], $date, $journalId]
		);

		if ($update) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

	public function checkWithdrawal($WithdrawalID)
	{
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT *  FROM InventJournalTable WHERE InventJournalID= ?",
			[$WithdrawalID]

		);
		if ($query) {
			return [
				"status" => 200,
				"message" => $WithdrawalID
			];
		} else {
			return [
				"status" => 400,
				"message" => 'ไม่มีข้อมูลในระบบ'
			];
		}
	}

	public function SaveWithdrawal($barcode, $item, $JournalType, $JournalId, $RequsitionID_code, $checktypeserch, $TemplateSerialNo)
	{
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT *  FROM InventTable WHERE Barcode= ?",
			[$barcode]

		);

		$checkitem = Sqlsrv::queryArray(
			$conn,
			"SELECT L.ID,L.ItemID , L.Batch,L.Remain, L.Status,L.QTY,L.Issue from InventJournalLine L
		--	LEFT JOIN CureCodeMaster CM ON CM.ItemID = L.ItemID
			 WHERE L.ID = ?",
			[$item]

		);

		$checkJournalId_update = Sqlsrv::queryArray(
			$conn,
			"SELECT SUM(Remain) AS TotalRemain,COUNT(InventJournalID) as CountJournal , SUM(Status) as TotalStatus FROM InventJournalLine  WHERE InventJournalID = ?",
			[$JournalId]

		);

		$checkTrans = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM InventJournalTrans WHERE LineID = ? AND BarcodeID = ?",
			[
				$item,
				$barcode
			]

		);


		$checkuser = Sqlsrv::queryArray(
			$conn,
			"SELECT Warehouse from UserMaster WHERE ID =  ?",
			[$_SESSION["user_login"]]

		);

		$check_InventJournalTable = Sqlsrv::queryArray(
			$conn,
			"SELECT * from InventJournalTable WHERE InventJournalID = ?",
			[$JournalId]

		);



		$checkSNnum = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode,TemplateSerialNo FROM InventTable WHERE Barcode = ? and TemplateSerialNo = ?",
			[
				$barcode,
				$TemplateSerialNo
			]

		);






		$trans_id = Utils::genTransId($barcode);


		if ($checktypeserch == 0) {
			if ($JournalType == 'MOV'|| $JournalType == 'MOVRTN') {


				if (!$query[0]["Barcode"]) {
					return [
						"status" => 404,
						"message" => 'ไม่มีข้อมูลในระบบ'
					];
				} else {
					if (!$query[0]["FinalReceiveDate"]) {
						return [
							"status" => 404,
							"message" => 'Barcode ยังไม่ได้รับเข้า Final'
						];
					}

					if (
						$query[0]["FinalReceiveDate"] &&
						$query[0]["WarehouseTransReceiveDate"]
					) {
						return [
							"status" => 404,
							"message" => 'Barcode ไม่อยู่ที่ Final'
						];
					}
					if (
						$query[0]["FinalReceiveDate"]   &&
						!$query[0]["WarehouseTransReceiveDate"]  &&
						$query[0]["Status"] !== 1
					) {
						return [
							"status" => 404,
							"message" => 'Barcode Status Not Receive'
						];
					}
					if (
						$query[0]["FinalReceiveDate"]  &&
						!$query[0]["WarehouseTransReceiveDate"] &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] !== $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] !== $checkitem[0]["Batch"]
					) {
						return [
							"status" => 404,
							"message" => 'Item หรือ Batch ไม่ถูกต้อง'
						];
					}

					if (
						$query[0]["ItemID"] !== $checkitem[0]["ItemID"] ||
						$query[0]["Batch"] !== $checkitem[0]["Batch"]
					) {
						return [
							"status" => 404,
							"message" => 'Item หรือ Batch ไม่ถูกต้อง'
						];
					}


					if (
						$query[0]["FinalReceiveDate"]  &&
						!$query[0]["WarehouseTransReceiveDate"] &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] !== $checkuser[0]["Warehouse"]
					) {
						return [
							"status" => 404,
							"message" => 'Warehouse Not Match'
						];
					}



					if (
						$query[0]["FinalReceiveDate"]  &&
						!$query[0]["WarehouseTransReceiveDate"] &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] == $checkuser[0]["Warehouse"] &&
						$checkitem[0]["Remain"] <= 0
					) {
						return [
							"status" => 404,
							"message" => 'Remain = 0'
						];
					}

					if (
						$query[0]["FinalReceiveDate"]  &&
						!$query[0]["WarehouseTransReceiveDate"] &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] !== $checkuser[0]["Warehouse"] &&
						$checkitem[0]["Remain"] <= 0
					) {
						return [
							"status" => 404,
							"message" => 'Remain = 0'
						];
					}


					if (!$checkSNnum[0]["TemplateSerialNo"] && $TemplateSerialNo !== "") {

						return [
							"status" => 404,
							"message" => 'ข้อมูล Serial ไม่ตรงกัน'
						];
					} else {


						$update_inventtable = Sqlsrv::update(
							$conn,
							"UPDATE InventTable
					SET DisposalID = ? ,
							Status = ?,
							UpdateBy = ?,
							UpdateDate = ?
					WHERE Barcode = ?",
							[
								11,
								4,
								$_SESSION["user_login"],
								$date,
								$query[0]["Barcode"]
							]
						);


						//Insert invent Trans
						$insert_inventtrans = Sqlsrv::insert(
							$conn,
							"INSERT INTO InventTrans(
							TransID ,
							Barcode,
							CodeID ,
							Batch,
							DisposalID ,
							DefectID,
							WarehouseID ,
							LocationID,
							QTY ,
							UnitID,
							DocumentTypeID ,
							Company,
							CreateBy ,
							CreateDate,
							Shift,
							InventJournalID
						)VALUES(
							?, ?, ?, ?, ?,
							?, ?, ?, ?, ?,
							?, ?, ?, ?, ?,
							?
						)",
							[
								$trans_id,
								$barcode,
								$checkitem[0]["ItemID"], // gt code
								$checkitem[0]["Batch"], // batch
								11, // disposal
								null, // defect
								$query[0]["WarehouseID"], // wh
								$query[0]["LocationID"], // lc
								-1, // qty
								1, // unit
								2, // document id => issue
								$_SESSION["user_company"],
								$_SESSION["user_login"],
								$date,
								$_SESSION["Shift"],
								$JournalId
							]
						);

						// $move_out_onhand = Sqlsrv::update(
						// 	$conn,
						// 	"UPDATE Onhand
						// SET QTY -= 1
						// WHERE CodeID = ?
						// AND WarehouseID = ?
						// AND LocationID = ?
						// AND Batch = ?
						// AND Company =?",
						// 	[
						// 		$checkitem[0]["ItemID"],
						// 		$query[0]["WarehouseID"],
						// 		$query[0]["LocationID"],
						// 		$checkitem[0]["Batch"],
						// 		$_SESSION["user_company"]
						// 	]
						// );

						//insert invent journal trans
						$journalTrans = Sqlsrv::insert(
							$conn,
							"INSERT INTO InventJournalTrans(
							ID,
							LineID,
							InventJournalID,
							ItemID,
							QTY,
							BarcodeID,
							RequsitionID,
							CreateDate,
							CreateBy,
							Company
						) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
							[
								$trans_id,
								$checkitem[0]["ID"],
								$JournalId,
								$checkitem[0]["ItemID"],
								1,
								$barcode,
								$RequsitionID_code,
								$date,
								$_SESSION["user_login"],
								$_SESSION["user_company"]
							]
						);
						//updatestatus
						$InventJournalLine = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalLine SET


							Remain -=1,
							Issue += 1,
							Status = ?

							WHERE ID = ?",
							[
								2,
								$item
							]
						);

						//updatestatusInventJournalTable
						$InventJournalTable_update = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalTable SET
							Status = ?,
							CompleteBy = ?,
							CompleteDate = ?

							WHERE InventJournalID = ?",
							[
								2,
								$_SESSION["user_login"],
								$date,
								$JournalId
							]
						);

						if ($checkitem[0]["Remain"] == 1) {


							$InventJournalLine_complete = Sqlsrv::update(
								$conn,
								"UPDATE InventJournalLine SET



								Status = ?

								WHERE ID = ?",
								[
									3,
									$item
								]
							);

							if ($checkJournalId_update[0]["TotalRemain"] == 1) {
								$InventJournalTable_update_complete = Sqlsrv::update(
									$conn,
									"UPDATE InventJournalTable SET
									Status = ?,
									CompleteBy = ?,
									CompleteDate = ?

									WHERE InventJournalID = ?",
									[
										3,
										$_SESSION["user_login"],
										$date,
										$JournalId
									]
								);
							}
						}





						if ($update_inventtable && $insert_inventtrans  && $journalTrans && $InventJournalLine && $InventJournalTable_update) {
							return [
								"status" => 200,
								"message" => 'Successful',
								"barcode" => $query[0]["Barcode"],
								"Batch" => $query[0]["Batch"],
								"CuringCode" => $query[0]["CuringCode"]
							];
						} else {
							return [
								"status" => 404,
								"message" => 'No Complete1'
							];
						}
					}
				}
			}

			if ($JournalType == 'MOVWH' || $JournalType == 'MOVWHRTN') {


				if (!$query[0]["Barcode"]) {
					return [
						"status" => 404,
						"message" => 'ไม่มีข้อมูลในระบบ'
					];
				} else {
					if (!$query[0]["WarehouseReceiveDate"]) {
						return [
							"status" => 404,
							"message" => 'Barcode ยังไม่ได้รับเข้า Warehouse'
						];
					}

					if (
						$query[0]["WarehouseReceiveDate"] &&
						$query[0]["Status"] !== 1
					) {
						return [
							"status" => 404,
							"message" => 'Barcode Status Not Receive'
						];
					}

					if (
						$query[0]["WarehouseReceiveDate"]  &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] !== $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] !== $checkitem[0]["Batch"]
					) {
						return [
							"status" => 404,
							"message" => 'Item หรือ Batch ไม่ถูกต้อง'
						];
					}
					if (
						$query[0]["ItemID"] !== $checkitem[0]["ItemID"] ||
						$query[0]["Batch"] !== $checkitem[0]["Batch"]
					) {
						return [
							"status" => 404,
							"message" => 'Item หรือ Batch ไม่ถูกต้อง'
						];
					}

					if (
						$query[0]["FinalReceiveDate"]  &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] !== $checkuser[0]["Warehouse"]
					) {
						return [
							
							"status" => 404,
							"message" => 'Warehouse Not Match'
						];
					}

					if (
						$query[0]["FinalReceiveDate"]  &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] == $checkuser[0]["Warehouse"] &&
						$checkitem[0]["Remain"] <= 0
					) {
						return [
							"status" => 404,
							"message" => 'Remain = 0'
						];
					}

					if (
						$query[0]["FinalReceiveDate"]  &&
						$query[0]["Status"] == 1 &&
						$query[0]["ItemID"] == $checkitem[0]["ItemID"] &&
						$query[0]["Batch"] == $checkitem[0]["Batch"] &&
						$query[0]["WarehouseID"] !== $checkuser[0]["Warehouse"] &&
						$checkitem[0]["Remain"] <= 0
					) {
						return [
							"status" => 404,
							"message" => 'Remain = 0'
						];
					} else {


						$update_inventtable = Sqlsrv::update(
							$conn,
							"UPDATE InventTable
					SET DisposalID = ? ,
							Status = ?,
							UpdateBy = ?,
							UpdateDate = ?
					WHERE Barcode = ?",
							[
								11,
								4,
								$_SESSION["user_login"],
								$date,
								$query[0]["Barcode"]
							]
						);


						//Insert invent Trans
						$insert_inventtrans = Sqlsrv::insert(
							$conn,
							"INSERT INTO InventTrans(
							TransID ,
							Barcode,
							CodeID ,
							Batch,
							DisposalID ,
							DefectID,
							WarehouseID ,
							LocationID,
							QTY ,
							UnitID,
							DocumentTypeID ,
							Company,
							CreateBy ,
							CreateDate,
							Shift,
							InventJournalID
						)VALUES(
							?, ?, ?, ?, ?,
							?, ?, ?, ?, ?,
							?, ?, ?, ?, ?,
							?
						)",
							[
								$trans_id,
								$barcode,
								$checkitem[0]["ItemID"], // gt code
								$checkitem[0]["Batch"], // batch
								11, // disposal
								null, // defect
								$query[0]["WarehouseID"], // wh
								$query[0]["LocationID"], // lc
								-1, // qty
								1, // unit
								2, // document id => issue
								$_SESSION["user_company"],
								$_SESSION["user_login"],
								$date,
								$_SESSION["Shift"],
								$JournalId
							]
						);

						// $move_out_onhand = Sqlsrv::update(
						// 	$conn,
						// 	"UPDATE Onhand
						// SET QTY -= 1
						// WHERE CodeID = ?
						// AND WarehouseID = ?
						// AND LocationID = ?
						// AND Batch = ?
						// AND Company =?",
						// 	[
						// 		$checkitem[0]["ItemID"],
						// 		$query[0]["WarehouseID"],
						// 		$query[0]["LocationID"],
						// 		$checkitem[0]["Batch"],
						// 		$_SESSION["user_company"]
						// 	]
						// );

						//insert invent journal trans
						$journalTrans = Sqlsrv::insert(
							$conn,
							"INSERT INTO InventJournalTrans(
							ID,
							LineID,
							InventJournalID,
							ItemID,
							QTY,
							BarcodeID,
							RequsitionID,
							CreateDate,
							CreateBy,
							Company
						) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
							[
								$trans_id,
								$checkitem[0]["ID"],
								$JournalId,
								$checkitem[0]["ItemID"],
								1,
								$barcode,
								$RequsitionID_code,
								$date,
								$_SESSION["user_login"],
								$_SESSION["user_company"]
							]
						);
						//updatestatus
						$InventJournalLine = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalLine SET


							Remain -=1,
							Issue += 1,
							Status = ?

							WHERE ID = ?",
							[
								2,
								$item
							]
						);

						//updatestatusInventJournalTable
						$InventJournalTable_update = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalTable SET
							Status = ?,
							CompleteBy = ?,
							CompleteDate = ?

							WHERE InventJournalID = ?",
							[
								2,
								$_SESSION["user_login"],
								$date,
								$JournalId
							]
						);

						if ($checkitem[0]["Remain"] == 1) {


							$InventJournalLine_complete = Sqlsrv::update(
								$conn,
								"UPDATE InventJournalLine SET



								Status = ?

								WHERE ID = ?",
								[
									3,
									$item
								]
							);

							if ($checkJournalId_update[0]["TotalRemain"] == 1) {
								$InventJournalTable_update_complete = Sqlsrv::update(
									$conn,
									"UPDATE InventJournalTable SET
									Status = ?,
									CompleteBy = ?,
									CompleteDate = ?

									WHERE InventJournalID = ?",
									[
										3,
										$_SESSION["user_login"],
										$date,
										$JournalId
									]
								);
							}
						}





						if ($update_inventtable && $insert_inventtrans  && $journalTrans && $InventJournalLine && $InventJournalTable_update) {
							return [
								"status" => 200,
								"message" => 'Successful',
								"barcode" => $query[0]["Barcode"],
								"Batch" => $query[0]["Batch"],
								"CuringCode" => $query[0]["CuringCode"]
							];
						} else {
							return [
								"status" => 404,
								"message" => 'No Complete2'
							];
						}
					}
				}
			}
		} else {

			if (!$query[0]["Barcode"]) {
				return [
					"status" => 404,
					"message" => 'ไม่มีข้อมูลในระบบ'
				];
			}

			if (
				$query[0]["Barcode"]  &&
				$query[0]["ItemID"] !== $checkitem[0]["ItemID"]
			) {
				return [
					"status" => 404,
					"message" => 'Withdrawal No. Or Item Not Match'
				];
			}

			if ($checkitem[0]["Issue"] <= 0) {
				return [
					"status" => 404,
					"message" => 'Issue = 0'
				];
			}

			if ($checkTrans[0]["BarcodeID"] !== $barcode) {
				return [
					"status" => 404,
					"message" => 'Barcode Not Match'
				];
			} else {
				// update InventTable Y
				$update_inventtable = Sqlsrv::update(
					$conn,
					"UPDATE InventTable
					SET DisposalID = ? ,
							Status = ?,
							UpdateBy = ?,
							UpdateDate = ?
					WHERE Barcode = ?",
					[
						9,
						1,
						$_SESSION["user_login"],
						$date,
						$query[0]["Barcode"]
					]
				);

				//Insert invent Trans
				$insert_inventtrans = Sqlsrv::insert(
					$conn,
					"INSERT INTO InventTrans(
						TransID ,
						Barcode,
						CodeID ,
						Batch,
						DisposalID ,
						DefectID,
						WarehouseID ,
						LocationID,
						QTY ,
						UnitID,
						DocumentTypeID ,
						Company,
						CreateBy ,
						CreateDate,
						Shift,
						InventJournalID
					)VALUES(
						?, ?, ?, ?, ?,
						?, ?, ?, ?, ?,
						?, ?, ?, ?, ?,
						?
					)",
					[
						$trans_id,
						$barcode,
						$checkitem[0]["ItemID"], // gt code
						$checkitem[0]["Batch"], // batch
						9, // disposal
						null, // defect
						$query[0]["WarehouseID"], // wh
						$query[0]["LocationID"], // lc
						1, // qty
						1, // unit
						1, // document id => issue
						$_SESSION["user_company"],
						$_SESSION["user_login"],
						$date,
						$_SESSION["Shift"],
						null
					]
				);



				// Update Onhand
				// $Updatet_onhand = Sqlsrv::update(
				// 	$conn,
				// 	"UPDATE Onhand
				// 	SET QTY += 1
				// 	WHERE CodeID = ?
				// 	AND WarehouseID = ?
				// 	AND LocationID = ?
				// 	AND Batch = ?
				// 	AND Company =?",
				// 	[
				// 		$checkitem[0]["ItemID"],
				// 		$query[0]["WarehouseID"],
				// 		$query[0]["LocationID"],
				// 		$checkitem[0]["Batch"],
				// 		$_SESSION["user_company"]
				// 	]
				// );

				if ($checkitem[0]["Remain"] + 1 == $checkitem[0]["QTY"]) {
					//update InventJournalLine Y
					$UpdateJournalLine = Sqlsrv::update(
						$conn,
						"UPDATE InventJournalLine SET


							Remain +=1,
							Issue -= 1,
							Status = ?

							WHERE ID = ?",
						[
							1,
							$item
						]
					);
					if ($checkitem[0]["Status"] == 3  &&  $check_InventJournalTable[0]["Status"] == 2) {
						$check = $checkJournalId_update[0]["TotalStatus"] - 2;
						if ($check / $checkJournalId_update[0]["CountJournal"] == 1) {

							//updatestatusInventJournalTable
							$InventJournalTable_update = Sqlsrv::update(
								$conn,
								"UPDATE InventJournalTable SET
									Status = ?
									WHERE InventJournalID = ?",
								[
									6,
									$JournalId
								]
							);
						} else {
							if ($checkJournalId_update[0]["TotalStatus"] / $checkJournalId_update[0]["CountJournal"] == 1) {

								//updatestatusInventJournalTable
								$InventJournalTable_update = Sqlsrv::update(
									$conn,
									"UPDATE InventJournalTable SET
											Status = ?
											WHERE InventJournalID = ?",
									[
										6,
										$JournalId
									]
								);
							}
						}
					} else {
						//updatestatusInventJournalTable
						$InventJournalTable_update = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalTable SET
									Status = ?
									WHERE InventJournalID = ?",
							[
								2,
								$JournalId
							]
						);
					}
				} else {
					//update InventJournalLine Y
					$UpdateJournalLine = Sqlsrv::update(
						$conn,
						"UPDATE InventJournalLine SET


							Remain +=1,
							Issue -= 1,
							Status = ?

							WHERE ID = ?",
						[
							2,
							$item
						]
					);

					if ($check_InventJournalTable[0]["Status"] == 3  && $checkitem[0]["Status"] == 3) {
						//updatestatusInventJournalTable
						$InventJournalTable_update = Sqlsrv::update(
							$conn,
							"UPDATE InventJournalTable SET
								Status = ?
								WHERE InventJournalID = ?",
							[
								2,
								$JournalId
							]
						);
					} else {

						if ($checkJournalId_update[0]["TotalStatus"] / $checkJournalId_update[0]["CountJournal"] == 3) {

							//updatestatusInventJournalTable
							$InventJournalTable_update = Sqlsrv::update(
								$conn,
								"UPDATE InventJournalTable SET
									Status = ?
									WHERE InventJournalID = ?",
								[
									3,
									$JournalId
								]
							);
						} else {
							//updatestatusInventJournalTable
							$InventJournalTable_update = Sqlsrv::update(
								$conn,
								"UPDATE InventJournalTable SET
									Status = ?
									WHERE InventJournalID = ?",
								[
									2,
									$JournalId
								]
							);
						}
					}
				}


				//Insert InventJournalTrans
				$delectInventJournalTrans = sqlsrv_query(
					$conn,
					"DELETE  FROM InventJournalTrans WHERE BarcodeID = ?",
					[
						$query[0]["Barcode"]
					]
				);

				if ($update_inventtable && $delectInventJournalTrans   && $insert_inventtrans) {
					return [
						"status" => 200,
						"message" => 'Successful',
						"barcode" => $query[0]["Barcode"],
						"Batch" => $query[0]["Batch"],
						"CuringCode" => $query[0]["CuringCode"]
					];
				} else {
					return [
						"status" => 404,
						"message" => 'No Complete3'
					];
				}
			}
		}
	}
	//PostBack
	public function checkStatusPostBack($InventJournalID)
	{

		$conn = Database::connect();
		$allstatus = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) ALL_STATUS
			FROM InventJournalLine
			WHERE InventJournalID = ?",
			[
				$InventJournalID
			]

		);

		$openstatus = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) OPEN_STATUS
			FROM InventJournalLine
			WHERE InventJournalID = ?
			AND Status = 1",
			[
				$InventJournalID
			]

		);

		if ($allstatus[0]["ALL_STATUS"] === $openstatus[0]["OPEN_STATUS"]) {
			return [
				"status" => 200,
				"message" => "Status Line OK"
			];
		} else {
			return [
				"status" => 404,
				"message" => "ไม่สามารถ PostBack ได้เนื่องจากมีบางรายการสถานะไม่เท่ากับ OPEN"
			];
		}
	}

	public function checkAuthorizePostBack($username, $password)
	{

		$conn = Database::connect();

		$isEmpty = Sqlsrv::hasRows(
			$conn,
			"SELECT *
			FROM UserMaster 
			WHERE Username COLLATE Latin1_General_CS_AS = ?
			AND Password COLLATE Latin1_General_CS_AS = ?",
			[
				$username,
				$password
			]
		);

		if ($isEmpty === false) {

			return [
				"status" => 404,
				"message" => "Username หรือ Password ผิดพลาด"
			];
		}


		$auth = Sqlsrv::queryArray(
			$conn,
			"SELECT U.ID,U.Authorize,U.Username,A.PostbackMovement
			FROM UserMaster U JOIN
			AuthorizeMaster A ON U.Authorize = A.ID
			WHERE U.Username COLLATE Latin1_General_CS_AS = ?
			AND U.Password COLLATE Latin1_General_CS_AS = ?",
			[
				$username,
				$password
			]

		);

		if ($auth[0]["PostbackMovement"] === 1) {
			return [
				"status" => 200,
				"message" => "Status OK"
			];
		} else {
			return [
				"status" => 404,
				"message" => "ไม่มีสิทธิ์ในการ PostBack Journal"
			];
		}
	}

	public function UpdatePostBack($InventJournalID, $username)
	{
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$sql = Sqlsrv::queryArray(
			$conn,
			"SELECT ID
			FROM UserMaster 
			WHERE Username COLLATE Latin1_General_CS_AS = ?",
			[
				$username
			]

		);

		$userid = $sql[0]["ID"];

		// insert invent journal postback
		$journalPostBack = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalPostBackTrans(
				InventJournalID,
				PostBackBy,
				PostBackDate
			) VALUES (?, ?, ?)",
			[
				$InventJournalID,
				$userid,
				$date
			]
		);

		if (!$journalPostBack) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "insert journal post back error"
			];
		}

		// update invent journal table
		$updateInventJournalTB = Sqlsrv::update(
			$conn,
			"UPDATE InventJournalTable
			SET Status = 1
			WHERE InventJournalID = ?",
			[
				$InventJournalID
			]
		);

		if (!$updateInventJournalTB) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "update invent journal table error"
			];
		}

		sqlsrv_commit($conn);
		return [
			"status" => 200,
			"message" => "PostBack Complete"
		];
	}

	public function printByJournalTypeSummary($journalId, $mode)
	{
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			T.ItemID
		   ,T.CuringCode
		   ,T.NameTH
		   ,T.Note
		   ,T.FirstName
		   ,T.LastName
		   ,T.DivisionCode
		   ,T.Department
		   ,T.CreateBy
		   ,T.Batch
		   ,T.Name
		   ,SUM(T.qty) AS qty
		   ,T.BOI
FROM(
SELECT
		   J.ItemID
		   ,I.CuringCode
		   ,IT.NameTH
		   ,R.Description[Note]
		   ,E.FirstName
		   ,E.LastName
		   ,E.DivisionCode
		   ,D.Description[Department]
		   ,J.CreateBy
		   ,ITS.Batch
		   ,U.Name
		   ,1[qty]
		   ,PM.BOI
		   FROM InventJournalTrans J
		   LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode
		   --AND J.ItemID=I.ItemID
		   LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
		   LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
		   LEFT JOIN Employee E ON IJ.EmpCode=E.Code
		   LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
		   LEFT JOIN UserMaster U ON J.CreateBy=U.ID
		   LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
		   LEFT JOIN ItemMaster IT ON (
	   CASE
		   WHEN SUBSTRING(I.ItemID, 1, 1) = 'Q' THEN REPLACE(I.ItemID, 'Q', 'I')
		   ELSE I.ItemID
	   END
	   ) = IT.ID
		   LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
		   WHERE IJ.JournalTypeID = 'MOVWH'
		   AND J.InventJournalID = ?
		   --ORDER BY CONVERT(time,J.CreateDate) ASC
		   )T
		   GROUP BY 
			T.ItemID
		   ,T.CuringCode
		   ,T.NameTH
		   ,T.Note
		   ,T.FirstName
		   ,T.LastName
		   ,T.DivisionCode
		   ,T.Department
		   ,T.CreateBy
		   ,T.Batch
		   ,T.Name
		   ,T.BOI",
			[
				$journalId
			]
		);
	}

	public function saveJournalRtTableDestop($emp, $division, $data1, $data2, $journal_type)
	{
		$conn = Database::connect();

		// if ($_SESSION['user_warehouse'] === 3) {
		// 	$journal_type = 'MOVWH';
		// } else if ($_SESSION['user_warehouse'] === 2) {
		// 	$journal_type = 'MOV';
		// }

		$year = date("Y");
		$date = date("m-d-Y H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$queryYear = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal,Years FROM Sequeue WHERE SeqLpn = 116"
		);

		if (!$queryYear) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		$getEmployeeInfo = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM Employee
			WHERE Code = ? AND Username = ?",
			[$emp, $data1]
		);
		$checklogin = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM UserMaster
			WHERE Username =? AND Password=?",
			[$getEmployeeInfo[0]["Username"], $data2]

		);

		if (!$checklogin) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "ข้อมูลไม่ถูกต้อง"
			];
		}


		if ($queryYear[0]["Years"] == $year) {
			$conn = Database::connect();
			$updateJournalSequeue = Sqlsrv::update(
				$conn,
				"UPDATE Sequeue SET SeqJournal += 1 WHERE SeqLpn = 116"
			);
		}

		if ($queryYear[0]["Years"] <> $year) {
			$conn = Database::connect();

			$updateJournalSequeue = Sqlsrv::update(

				$conn,
				"UPDATE Sequeue SET SeqJournal = ?, Years = ? WHERE SeqLpn = 116",
				[1, $year]

			);
		}



		if (!$updateJournalSequeue) {
			sqlsrv_rollback($conn);
			return "ไม่สามารถอัพเดทได้";
		}
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqJournal,Years FROM Sequeue WHERE SeqLpn = 116"
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}



		// return [
		// 	"status" => 404,
		// 	"message" =>$query[0]["SeqJournal"]
		// 	//"W".substr($year,-2)."-".str_pad($query[0]["SeqJournal"], 6, "0",STR_PAD_LEFT)
		// ];
		// exit();
		$insertInventJournalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventJournalTable(
				InventJournalID,
				JournalTypeID,
				Customer,
				Department,
				EmpCode,
				Status,
				CreateDate,
				CreateBy,
				Company,
				MovementBy
			) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?,?)",
			[
				"WD" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				$journal_type,
				null,
				$getEmployeeInfo[0]["DivisionCode"],
				$emp,
				1,
				$date,
				$_SESSION["user_login"],
				$_SESSION["user_company"],
				$checklogin[0]["Username"]
			]
		);

		if ($updateJournalSequeue && $insertInventJournalTable) {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"journal" => "WD" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 6, "0", STR_PAD_LEFT),
				"test" => $checklogin[0]["Username"]
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}
	}

	function chekType($journalId){
		
		$conn = Database::connect();

		$getdata = Sqlsrv::queryArray(
			$conn,
			"SELECT JournalTypeID 
			FROM InventJournalTable
			WHERE  InventJournalID = ?",
			[$journalId]
		);

		return $getdata[0]["JournalTypeID"];
	}
}	// End