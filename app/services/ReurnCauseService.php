<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;
use App\Components\Security;
use App\V2\Database\Handler;

class ReurnCauseService
{

	public function all()
	{
		$conn = Database::connect();

		$getUserWarehouseType = Sqlsrv::queryArray(
			$conn,
			"SELECT Type
			FROM WarehouseMaster
			WHERE ID = ?",
			[$_SESSION["user_warehouse"]]
		);

		$userWarehouseType = $getUserWarehouseType[0]["Type"];

		if ($_SESSION["user_name"] === "admin") {

			$sql = "SELECT * FROM ReturnCause";

		} else if ($userWarehouseType === 2) { // Final

			$sql = "SELECT * FROM ReturnCause WHERE Final = 1";

		} else if ($userWarehouseType === 3) { // FG

			$sql = "SELECT * FROM ReturnCause WHERE FinishGood = 1";

		}

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function saveRequsitionNote($id, $description, $warehouse)
	{
		$conn = Database::connect();

		$date = Date('Y-m-d H:i:s');

		$insert = Sqlsrv::insert(
			$conn,
			"UPDATE ReturnCause
			SET Description = ?,
			Final = ?,
			FinishGood = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO ReturnCause(
				Description,
				Final,
				FinishGood,
				CreateBy,
				CreateDate,
				Company,
				UpdateBy,
				UpdateDate
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$description,
				$warehouse[0],
				$warehouse[1],
				$_SESSION["user_login"],
				$date,
				$id,

				$description,
				$warehouse[0],
				$warehouse[1],
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date
			]
		);

		if ($insert) {
			return 200;
		} else {
			return 400;
		}
	}

	public function saveJournalTable($journal_type)
	{
		$conn = Database::connect();

		// if ($_SESSION['user_warehouse'] === 3) {
		// 	$journal_type = 'RTNWH';
		// } else if ($_SESSION['user_warehouse'] === 2) {
		// 	$journal_type = 'RTN';
		// }

		$year = date("Y");
		$date = date("m-d-Y H:i:s");
		$data = explode("-",$date);
		$checkyear = explode(" ",$data[2]);

		$checkcountyear = Sqlsrv::queryArray(
			$conn,
			"SELECT Years FROM SeRecause"
		);

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

	 if($checkcountyear[0]["Years"] == $year){
		 $updateJournalSequeue = Sqlsrv::update(
 			$conn,
 			"UPDATE SeRecause SET SeqRecasue += 1"
 		);

	 }

	 else {

		 $updateJournalSequeue = Sqlsrv::update(
 			$conn,
 			"UPDATE SeRecause SET SeqRecasue = ?, Years = ?",
			[1,$year]

 		);

	 }



		if (!$updateJournalSequeue) {
			sqlsrv_rollback($conn);
			return "ไม่สามารถอัพเดทได้";
		}

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SeqRecasue FROM SeRecause"
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "error"
			];
		}

		// $getEmployeeInfo = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT * FROM Employee
		// 	WHERE Code = ? AND Username = ?",
		// 	[$emp,$data1]
		// );
		// $checklogin = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT * FROM UserMaster
		// 	WHERE Username =? AND Password=?",
		// 	[$getEmployeeInfo[0]["Username"],$data2]
		//
		// );

		// if (!$checklogin) {
		// 	sqlsrv_rollback($conn);
		// 	return [
		// 		"status" => 404,
		// 		"message" => "ข้อมูลไม่ถูกต้อง"
		// 	];
		// }


		$insertInventJournalTable = Sqlsrv::insert(
			$conn,
			"INSERT INTO ReturnJournalTable(
				ReturnJournalID,
				JournalType,
				Status,
				CreatedBy,
				CreatedDate,
				Company



			) VALUES (?, ?, ?, ?, ?, ?)",
			[
				"R".substr($year,-2)."-".str_pad($query[0]["SeqRecasue"], 6, "0",STR_PAD_LEFT),
				$journal_type,
				1,
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_company"]
			]
		);

		if ($updateJournalSequeue && $insertInventJournalTable) {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"journal" => "R".substr($year,-2)."-".str_pad($query[0]["SeqRecasue"], 6, "0",STR_PAD_LEFT),
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

	public function allreturncause()
	{
		$conn = Database::connect();
		$user_warehouse = $_SESSION["user_warehouse"];
		if ($_SESSION['user_name'] !== 'admin') {
			if($user_warehouse == 2){
					//$warehouse_condition = ' RJ.JournalType =\'RTN\'' ;
						$warehouse_condition = '\'A\'=\'A\'';
			}
			else {
				//	$warehouse_condition = ' RJ.JournalType = \'RTNWH\'' ;
					$warehouse_condition = '\'A\'=\'A\'';
			}

		} else {
			$warehouse_condition = '\'A\'=\'A\'';
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {

			// for Mobile
				$sql = "SELECT
				RJ.ReturnJournalID,
				JT.Description,
				RJ.RefInventJournalID,
				RJ.CreatedBy,
				RJ.CreatedDate,
				RJ.Company,
				RJ.CompletBy,
				RJ.CompleteDate,
				U.Name,
				UU.Name as namecomplete

				FROM ReturnJournalTable RJ

				LEFT JOIN UserMaster U ON RJ.CreatedBy = U.ID
				LEFT JOIN JournalType JT ON RJ.JournalType = JT.ID
				LEFT JOIN UserMaster UU ON RJ.CompletBy = UU.ID
				WHERE RJ.Status <> 3 AND " . $warehouse_condition . "order by RJ.ReturnJournalID DESC";
				// echo $sql; exit;
		} else {

			// For Desktop
			$sql = "SELECT
			RJ.ReturnJournalID,
			JT.Description,
			RJ.RefInventJournalID,
			RJ.CreatedBy,
			RJ.CreatedDate,
			RJ.Company,
			RJ.CompletBy,
			RJ.CompleteDate,
			U.Name,
			UU.Name as namecomplete,
			RJ.JournalType,
			RJ.Status


			FROM ReturnJournalTable RJ

			LEFT JOIN UserMaster U ON RJ.CreatedBy = U.ID
			LEFT JOIN JournalType JT ON RJ.JournalType = JT.ID
			LEFT JOIN UserMaster UU ON RJ.CompletBy = UU.ID
			WHERE" . $warehouse_condition . "order by RJ.ReturnJournalID DESC";
			// echo $sql; exit;
		}

		// echo $sql; exit;

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);

	}

	public function allcheck($reId){
		$conn = Database::connect();

		$getUserWarehouseType = Sqlsrv::queryArray(
			$conn,
			"SELECT JournalType FROM ReturnJournalTable WHERE ReturnJournalID =?",
			[$reId]
		);

		$userWarehouseType = $getUserWarehouseType[0]["JournalType"];

		if ($userWarehouseType === "RTN") {

			$sql = "SELECT * FROM ReturnCause  WHERE Final = 1";

		} else  { // Final

			$sql = "SELECT * FROM ReturnCause  WHERE FinishGood = 1";

		}

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function savereturntIssue($barcode,$journalId,$requsition){
		$conn = Database::connect();
		$year = date("Y");
		$date = date("m-d-Y H:i:s");
		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}


		$checkbarcode = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode,Status,WarehouseID,CuringCode,Batch,ItemID,DisposalID,LocationID FROM InventTable WHERE Barcode = ?",
			[
				$barcode
			]
		);

		$checkInventTrans = Sqlsrv::queryArray(
			$conn,
			"SELECT top 1 InventJournalID FROM InventTrans WHERE Barcode = ? ORDER BY id DESC",
			[
				$barcode
			]
		);

		$checkjournalId = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM ReturnJournalTable WHERE ReturnJournalID = ?",
			[
				$journalId
			]
		);

		if(!$checkbarcode){
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "Barcode ไม่ถูกต้อง"
			];
		}
		else {
			if($checkjournalId[0]["JournalType"] =='RTN'){

				if ($checkbarcode[0]["Status"] == 4) {

					if($checkbarcode[0]["WarehouseID"] !== 2){
						sqlsrv_rollback($conn);
						 return [
							"status" => 404,
							"message" => "Barcode Number not Issued by Final"
						];
					}

					else {
						if($checkInventTrans[0]["InventJournalID"] == $checkjournalId[0]["RefInventJournalID"] || $checkjournalId[0]["RefInventJournalID"] == NULL){
								$conn = Database::connect();

								$updateReturnJournalTable = Sqlsrv::update(
									$conn,
									"UPDATE ReturnJournalTable
									SET RefInventJournalID =?
									WHERE ReturnJournalID = ?",
									[
										$checkInventTrans[0]["InventJournalID"],
										$journalId
									]
								);

								if (!$updateReturnJournalTable) {
									sqlsrv_rollback($conn);
									return [
										"status" => 404,
										"message" => "No"
									];
								}

								$insertReturnJournalTrans = Sqlsrv::insert(
									$conn,
									"INSERT INTO ReturnJournalTrans(
										JournalID,
										Barcode,
										CuringCode,
										Batch,
										RefInventJournalID,
										ReturnCause,
										qty,
										CreatedBy,
										CreatedDate
									) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
									[
										$journalId,
										$barcode,
										$checkbarcode[0]["CuringCode"],
										$checkbarcode[0]["Batch"],
										$checkInventTrans[0]["InventJournalID"],
										$requsition,
										1,
										$_SESSION["user_login"],
										$date
									]
								);

								if (!$insertReturnJournalTrans) {
									sqlsrv_rollback($conn);
									return "ไม่สามารถ inserte insertReturnJournalTrans ได้ ";
								}

								$updateInventTable = Sqlsrv::update(
									$conn,
									"UPDATE InventTable
										SET DisposalID = ?,
										WarehouseID = ?,
										LocationID = ? ,
										Status = ?,
										UpdateBy =?,
										UpdateDate =?
										WHERE Barcode = ?",
										[
											9,
											2,
											11,
											5,
											$_SESSION["user_login"],
											$date,
											$barcode
										]

									);

								if (!$updateInventTable) {
										sqlsrv_rollback($conn);
										return "ไม่สามารถ update InventTable ได้ ";
									}

								$barcode_decode = Security::_decode($barcode);
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
										CreateDate,
										Shift
									)VALUES(
										?, ?, ?, ?, ?,
										?, ?, ?, ?, ?,
										?, ?, ?, ?, ?
									)",
									[
										$trans_id,
										$barcode_decode,
										$checkbarcode[0]["ItemID"], // gt code
										$checkbarcode[0]["Batch"], // batch
										9, // disposal
										'CUR511', // defect
										2, // wh
										11, // lc
										1, // qty
										1, // unit
										1, // document id => issue
										$_SESSION["user_company"],
										$_SESSION["user_login"],
										$date,
										1
									]
								);

								if (!$insert_inventtrans) {
									sqlsrv_rollback($conn);
									return "ไม่สามารถ insert InventTran ได้ ";
								}

								$checkbarcode2 = Sqlsrv::queryArray(
									$conn,
									"SELECT Barcode,Status,WarehouseID,CuringCode,Batch,ItemID,DisposalID,LocationID FROM InventTable WHERE Barcode = ?",
									[
										$barcode
									]
								);

								if (!$checkbarcode2) {
									sqlsrv_rollback($conn);
									return "ไม่สามารถ query checkBarcode ได้ ";
								}

								// chk hasRows
								$checkOnhand = self::chekOnhand(
									$checkbarcode2[0]["ItemID"],
									$checkbarcode2[0]["WarehouseID"],
									$checkbarcode2[0]["LocationID"],
									$checkbarcode2[0]["Batch"]
								);

								if ($checkOnhand === true) {

									// $Updatet_onhand = Sqlsrv::update(
									// 	$conn,
									// 	"UPDATE Onhand SET QTY  += 1
									// 	WHERE CodeID = ? AND WarehouseID =? AND LocationID = ? AND Batch =?",
									// 	[
									// 		$checkbarcode2[0]["ItemID"],
									// 		$checkbarcode2[0]["WarehouseID"],
									// 		$checkbarcode2[0]["LocationID"],
									// 		$checkbarcode2[0]["Batch"]
									// 	]
									// );

									// if (!$Updatet_onhand) {
									// 	sqlsrv_rollback($conn);
									// 	return [
									// 		"status" => 404,
									// 		"message" => "No update Onhand"
									// 	];
									// }

								}
								else{
									// $insertOnhand = Sqlsrv::insert(
									// 	$conn,
									// 	"INSERT INTO Onhand(
									// 		CodeID ,
									// 		WarehouseID,
									// 		LocationID ,
									// 		Batch,
									// 		QTY ,
									// 		Company
									// 		)VALUES(
									// 		?, ?, ?,
									// 		?, ?, ?)",
									// 	[
									// 		$checkbarcode2[0]["ItemID"],
									// 		$checkbarcode2[0]["WarehouseID"],
									// 		$checkbarcode2[0]["LocationID"], // gt code
									// 		$checkbarcode[0]["Batch"], // batch
									// 		1, // disposal
									// 		'STR'
									// 	]
									// );

									// if (!$insertOnhand) {
									// 	sqlsrv_rollback($conn);
									// 	return [
									// 		"status" => 404,
									// 		"message" => "No insert Onhand"
									// 	];
									// }
								}

						}

						else {
							sqlsrv_rollback($conn);
							return [
								"status" => 404,
								"message" => "Ref.Withdrawal not match"
							];
						}
					}

				}

				else {
					sqlsrv_rollback($conn);
					return [
						"status" => 404,
						"message" => "Barcode Number Status not Issued"
					];

				}
			}
			// JournalType =RTNWH
			else{
				if ($checkbarcode[0]["Status"] == 4 || $checkbarcode[0]["Status"] == 3 ) {

					if($checkbarcode[0]["WarehouseID"] !== 3){
						sqlsrv_rollback($conn);
						 return [
							"status" => 404,
							"message" => "Barcode Number not Issued by Warehouse"
						];
					}
					else {

						if($checkInventTrans[0]["InventJournalID"] == $checkjournalId[0]["RefInventJournalID"] || $checkjournalId[0]["RefInventJournalID"] == NULL){
								$conn = Database::connect();

								$updateReturnJournalTable = Sqlsrv::update(
									$conn,
									"UPDATE ReturnJournalTable
									SET RefInventJournalID =?
									WHERE ReturnJournalID = ?",
									[
										$checkInventTrans[0]["InventJournalID"],
										$journalId
									]
								);

								if (!$updateReturnJournalTable) {
									sqlsrv_rollback($conn);
									return [
										"status" => 404,
										"message" => "no updateReturnJournalTable"
									];
								}

								$insertReturnJournalTrans = Sqlsrv::insert(
									$conn,
									"INSERT INTO ReturnJournalTrans(
										JournalID,
										Barcode,
										CuringCode,
										Batch,
										RefInventJournalID,
										ReturnCause,
										qty,
										CreatedBy,
										CreatedDate
									) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
									[
										$journalId,
										$barcode,
										$checkbarcode[0]["CuringCode"],
										$checkbarcode[0]["Batch"],
										$checkInventTrans[0]["InventJournalID"],
										$requsition,
										1,
										$_SESSION["user_login"],
										$date
									]
								);

								if (!$insertReturnJournalTrans) {
									sqlsrv_rollback($conn);
									return "ไม่สามารถ inserte insertReturnJournalTrans ได้ ";
								}

								$updateInventTable = Sqlsrv::update(
									$conn,
									"UPDATE InventTable
										SET DisposalID = ?,
										WarehouseID = ?,
										LocationID = ? ,
										Status = ?,
										UpdateBy =?,
										UpdateDate =?
										WHERE Barcode = ?",
										[
											9,
											3,
											15,
											1,
											$_SESSION["user_login"],
											$date,
											$barcode
										]

									);

								if (!$updateInventTable) {
										sqlsrv_rollback($conn);
										return "ไม่สามารถ update InventTable ได้ ";
								}

								$barcode_decode = Security::_decode($barcode);
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
										CreateDate,
										Shift
									)VALUES(
										?, ?, ?, ?, ?,
										?, ?, ?, ?, ?,
										?, ?, ?, ?, ?
									)",
									[
										$trans_id,
										$barcode_decode,
										$checkbarcode[0]["ItemID"], // gt code
										$checkbarcode[0]["Batch"], // batch
										9, // disposal
										'CUR511', // defect
										3, // wh
										15, // lc
										1, // qty
										1, // unit
										1, // document id => issue
										$_SESSION["user_company"],
										$_SESSION["user_login"],
										$date,
										1
									]
								);

								if (!$insert_inventtrans) {
									sqlsrv_rollback($conn);
									return [
										"status" => 404,
										"message" => "No"
									];
								}

								$checkbarcode2 = Sqlsrv::queryArray(
									$conn,
									"SELECT Barcode,Status,WarehouseID,CuringCode,Batch,ItemID,DisposalID,LocationID FROM InventTable WHERE Barcode = ?",
									[
										$barcode
									]
								);

								if (!$checkbarcode2) {
									sqlsrv_rollback($conn);
									return "ไม่สามารถ query checkBarcode ได้ ";
								}

								// chk hasRows
								$checkOnhand = self::chekOnhand(
									$checkbarcode2[0]["ItemID"],
									$checkbarcode2[0]["WarehouseID"],
									$checkbarcode2[0]["LocationID"],
									$checkbarcode2[0]["Batch"]
								);

								if ($checkOnhand === true) {
									// $Updatet_onhand = Sqlsrv::update(
									// 	$conn,
									// 	"UPDATE Onhand SET QTY  += 1
									// 	WHERE CodeID = ? AND WarehouseID =? AND LocationID = ? AND Batch =?",
									// 	[
									// 		$checkbarcode2[0]["ItemID"],
									// 		$checkbarcode2[0]["WarehouseID"],
									// 		$checkbarcode2[0]["LocationID"],
									// 		$checkbarcode2[0]["Batch"]
									// 	]
									// );
									// if (!$Updatet_onhand) {
									// 	sqlsrv_rollback($conn);
									// 	return [
									// 		"status" => 404,
									// 		"message" => "No update Onhand"
									// 	];
									// }

								}
								else{
									// $insertOnhand = Sqlsrv::insert(
									// 	$conn,
									// 	"INSERT INTO Onhand(
									// 		CodeID ,
									// 		WarehouseID,
									// 		LocationID ,
									// 		Batch,
									// 		QTY ,
									// 		Company
									// 		)VALUES(
									// 		?, ?, ?,
									// 		?, ?, ?)",
									// 	[
									// 		$checkbarcode2[0]["ItemID"],
									// 		$checkbarcode2[0]["WarehouseID"],
									// 		$checkbarcode2[0]["LocationID"], // gt code
									// 		$checkbarcode[0]["Batch"], // batch
									// 		1, // disposal
									// 		'STR'
									// 	]
									// );

									// if (!$insertOnhand) {
									// 	sqlsrv_rollback($conn);
									// 	return [
									// 		"status" => 404,
									// 		"message" => "No insert Onhand"
									// 	];
									// }
								}

						}
						else {
							sqlsrv_rollback($conn);
							return [
								"status" => 404,
								"message" => "Ref.Withdrawal not match"
							];
						}
					}
				}
				else {
					sqlsrv_rollback($conn);
					return [
						"status" => 404,
						"message" => "Barcode Number Status not Confirmed or Issue"
					];
				}
			}

		}
		sqlsrv_commit($conn);
		return [
			"status" => 200,
			"message" => "ok"
		];
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
			"SELECT JournalID FROM ReturnJournalTrans
			WHERE JournalID = ?",
			[$journalId]
		);

		if ($isEmpty === false) {
			return "ไม่มีรายการ";
		}

		$update = Sqlsrv::update(
			$conn,
			"UPDATE ReturnJournalTable
			SET Status = 3
					,CompletBy =?
					,CompleteDate =?
			WHERE ReturnJournalID = ?",
			[
				$_SESSION["user_login"],
				$date,
				$journalId
			]
		);

		if ($update) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

	public function getLatestJournalTransByJournalId($journalId){
		$conn = Database::connect();
		$query =  Sqlsrv::queryArrayObject(
			$conn,
			"SELECT
				RT.Barcode,
				IM.ID,
				IM.NameTH,
				RT.Batch,
				RC.Description,
				UM.Name,
				RT.CreatedDate
				FROM ReturnJournalTrans RT
				LEFT JOIN  ReturnCause RC ON RC.Id = RT.ReturnCause
				LEFT JOIN InventTable IVT ON IVT.Barcode = RT.Barcode
				LEFT JOIN ItemMaster  IM ON  IM.ID = IVT.ItemID
				LEFT JOIN UserMaster UM ON UM.ID = RT.CreatedBy
				WHERE RT.JournalID = ?
			ORDER BY RT.CreatedDate DESC",
			[$journalId]
		);

		$temp = [];

		foreach ($query as $v) {
			$temp[] = [
				'Barcode' => Security::_encode($v->Barcode),
				'IDItem' => $v->ID,
				'NameTH' => $v->NameTH,
				'Batch' => $v->Batch,
				'Description' => $v->Description,
				'CreatedBy' => $v->Name,
				'CreatedDate' => $v->CreatedDate
			];
		}

		return json_encode($temp);
	}

	public function printByJournalLine($journalId)
	{
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			 RT.JournalType,
			 RT.CreatedDate,
			 UM.Name,
			 RT.ReturnJournalID,
			 RT.RefInventJournalID,
			 Rtt.CuringCode,
			 CM.ItemID,
			 IM.NameTH,
			 Rtt.Batch,
 		 	SUM(Rtt.qty) as TotalQty,
			RC.Description
			FROM ReturnJournalTable RT
			LEFT JOIN UserMaster UM ON UM.ID = RT.CreatedBy
			LEFT JOIN ReturnJournalTrans Rtt ON Rtt.JournalID = RT.ReturnJournalID
			LEFT JOIN CureCodeMaster CM ON CM.ID = Rtt.CuringCode
			LEFT JOIN ItemMaster IM ON IM.ID = CM.ItemID
			LEFT JOIN ReturnCause RC ON RC.Id = Rtt.ReturnCause
			WHERE RT.ReturnJournalID = ?
			GROUP BY
			RT.JournalType,
 			RT.CreatedDate,
 			UM.Name,
 			RT.ReturnJournalID,
 			RT.RefInventJournalID,
 			Rtt.CuringCode,
 			CM.ItemID,
 			IM.NameTH,
 			Rtt.Batch,
			RC.Description",
			 [
			 	$journalId
			 ]
		);
	}

	public function datatoppic($journalId){
		$conn = Database::connect();

		$toppicuser = Sqlsrv::queryArray(
			$conn,
			"SELECT
			UM.Name,
			RT.RefInventJournalID,
			RT.CreatedDate
			FROM ReturnJournalTable RT
			LEFT JOIN UserMaster UM ON UM.ID = RT.CreatedBy
			WHERE RT.ReturnJournalID = ?",
			[
				$journalId
			]
		);

		return [
			"nameUser" => $toppicuser[0]["Name"],
			"Ref" => $toppicuser[0]["RefInventJournalID"],
			"CreateDate" =>$toppicuser[0]["CreatedDate"]
		];
	}

	public function allReturnType(){
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM JournalType WHERE ID in ('RTN','RTNWH')"
		);
	}

	public function chekOnhand($item,$warehouse,$location,$batch){
		try {
			$conn = Database::connect();
			return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM Onhand
			WHERE CodeID = ? AND WarehouseID =? AND LocationID = ? AND Batch =?",
			[$item, $warehouse, $location,$batch]
			);

		} catch (\Exception $e) {
			return false ;
		}
	}




}
