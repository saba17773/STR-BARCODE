<?php

namespace App\Services;

use App\Components\Utils;
use App\Components\Security;
use App\Services\InventService;
use Wattanar\Sqlsrv;
use App\V2\Batch\BatchAPI;
use App\V2\Database\Connector;

class CuringService
{
	private $database = null;
	private $inventService = null;
	private $batchApi = null;

	public function __construct()
	{
		$this->database = new Connector();
		$this->inventService = new InventService();
		$this->batchApi = new BatchAPI();
	}

	public function curing($curing_code, $template_code, $barcode, $check)
	{
		$conn = $this->database->dbConnect();

		$date = date('Y-m-d H:i:s');
		$date2 =  date('Y-m-d');
		$datecheck = explode("-", $date2);
		$stratdate =  $datecheck[0] . "-01-01";

		$curcode = explode("@", trim($curing_code));

		if (count($curcode) !== 4) {
			return "Curing Code Format Incorrect!";
		}

		$press_no = $curcode[0];
		$press_side = $curcode[1];
		$mold_no = $curcode[2];
		$curing_code_master = $curcode[3];



		if ($this->inventService->checkGreenTireCodeAndCuringCode($barcode, $curing_code_master) == false) {
			//return "Curing code number not match.";
			return "อบยางข้าม Code";
		}

		$TBR_DELAY = 10;
		$PCR_DELAY = 3;

		if ($this->isSkippingDelay() === false) {

			if (
				substr($press_no, 0, 1) !== "I" &&
				substr($press_no, 0, 1) !== "J" &&
				substr($press_no, 0, 1) !== "L"
			) {
				$checkCuringDelay = $this->pressSideCuringDelay($press_no, $press_side, $TBR_DELAY);
				if ($checkCuringDelay !== true) {
					// return "You can curing tire again after " . ($TBR_DELAY - (int)$checkCuringDelay) . " minute.";
					return "กรุณารอ  เวลาที่เหลือยู่ " . ($TBR_DELAY - (int) $checkCuringDelay) . " นาที";
				}
			} else if (
				substr($press_no, 0, 1) === "I" ||
				substr($press_no, 0, 1) === "J" ||
				substr($press_no, 0, 1) === "L"
			) {
				$checkCuringDelay = $this->pressSideCuringDelay($press_no, $press_side, $PCR_DELAY);
				if ($checkCuringDelay !== true) {
					//	return "You can curing tire again after " . ($PCR_DELAY - (int)$checkCuringDelay) . " minute.";
					return "กรุณารอ  เวลาที่เหลืออยู่ " . ($PCR_DELAY - (int) $checkCuringDelay) . " นาที";
				}
			}
		}

		// if ($this->pressSideCuringUpdate($press_no, $press_side) === false) {
		// 	return "Update press side date failed!";
		// }

		// ######### EDIT ITEM Q #########

		if (USE_ITEMQ === true) {
			$item_id = $this->getItemID($curing_code_master)[0]["ItemQ"];
		} else {
			$item_id = $this->getItemID($curing_code_master)[0]["ItemID"];
		}

		$greentire_id = $this->getItemID($curing_code_master)[0]["GreentireID"];

		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];
		$user_company = $_SESSION["user_company"];
		$user_login = $_SESSION["user_login"];


		$week = $this->batchApi->getManualBatch($date, $item_id, $press_no, $check);

		// return $week;
		// exit();

		// if (substr($press_no, 0, 1) === "I" ||
		// 		substr($press_no, 0, 1) === "J" ||
		// 		substr($press_no, 0, 1) === "G" ||
		// 		substr($press_no, 0, 1) === "H") {

		// $conn_svo = $this->database->connectSVO();

		// $get_barcode_info = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT TOP 1 * FROM InventTable
		// 	WHERE Barcode = ?",
		// 	[
		// 		$barcode
		// 	]
		// );

		// sqlsrv_query(
		// 	$conn_svo,
		// 	"INSERT INTO STR_BARCODE_TEMP(
		// 		Barcode,
		// 		ItemID,
		// 		Batch,
		// 		CreateDate
		// 	) VALUES (?, ?, ?, ?) ",
		// 	[
		// 		$barcode,
		// 		$item_id,
		// 		(new Utils)->getWeekNormal($date),
		// 		date('Y-m-d H:i:s')
		// 	]
		// );
		// }

		// $ddate = new \DateTime();
		// $week = date("Y") . "-" . $ddate->format("W");

		// $week = (new Utils)->getWeek($date);

		if (sqlsrv_begin_transaction($conn) === false) {
			return "Cannot connect database.";
		}

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





		// return $get_BOICeck;

		// $update_onhand_gt = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$greentire_id,
		// 		$get_barcode_info[0]["WarehouseID"],
		// 		$get_barcode_info[0]["LocationID"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$get_barcode_info[0]["Company"]
		// 	]
		// );

		// if (!$update_onhand_gt) {
		// 	sqlsrv_rollback($conn);
		// 	return "update onhand move out error.";
		// }

		$trans_id = Utils::genTransId(Security::_decode($barcode));

		$insert_form = Sqlsrv::insert(
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				Security::_decode($barcode),
				$greentire_id,
				$get_barcode_info[0]["Batch"], // batch
				$get_barcode_info[0]["DisposalID"], // disposal id
				null, // defect
				$get_barcode_info[0]["WarehouseID"], // wh
				$get_barcode_info[0]["LocationID"], // location
				-1, // qty
				1, // unit
				2, // docs type
				$user_company,
				$user_login,
				$date,
				$_SESSION["Shift"]
			]
		);



		if (!$insert_form) {
			sqlsrv_rollback($conn);
			return "transaction move out error.";
		}



		$update = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET CuringDate = ?,
			CuringCode = ?,
			ItemID = ?,
  			Batch = ?,
  			PressNo = ?,
  			PressSide = ?,
  			MoldNo = ?,
  			TemplateSerialNo = ?,
  			DisposalID = ?,
		    WarehouseID = ?,
		    LocationID = ?,
		    UpdateBy = ?,
		    UpdateDate = ?,
		    CheckBuild = 1
		    WHERE Barcode = ?",
			[
				$date,
				$curing_code_master,
				$item_id,
				$week,
				$press_no,
				$press_side,
				$mold_no,
				$template_code,
				$get_location[0]["DisposalID"], // Disposal Curing
				$get_location[0]["WarehouseID"], // WH X-ray
				$get_location[0]["ReceiveLocation"], // LC X-ray
				$_SESSION["user_login"],
				$date,
				Security::_decode($barcode)
			]
		);

		if (!$update) {
			sqlsrv_rollback($conn);
			return "update invent table error ";
		}

		$insert_to = Sqlsrv::insert(
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				Utils::genTransId(Security::_decode($barcode)) . 2,
				Security::_decode($barcode),
				$item_id,
				$week,
				$get_location[0]["DisposalID"], // disposal id
				null, // defect
				$get_location[0]["WarehouseID"], // wh
				$get_location[0]["ReceiveLocation"], // location
				1, // qty
				1, // unit
				1, // docs type
				$user_company,
				$user_login,
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$insert_to) {
			sqlsrv_rollback($conn);
			return "transaction move in error.";
		}






		// Update

		// ======== UPDATE ONHAND ==============

		if ($this->pressSideCuringUpdate($press_no, $press_side) === false) {
			sqlsrv_rollback($conn);
			return "Update press side date failed!";
		}

		if (
			substr($press_no, 0, 1) !== "I" &&
			// substr($press_no, 0, 1) !== "J" &&
			substr($press_no, 0, 1) !== "L"
		) {
			$insert_curetran = Sqlsrv::insert(
				$conn,
				"INSERT INTO CureTrans(
					Barcode,
					PressNo,
					PressSide,
					MoldNo,
					CuringCode,
					ProductGroup,
					CreateBy,
					CreateDate
				) VALUES (
					?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$barcode,
					$press_no,
					$press_side,
					$mold_no,
					$curing_code_master,
					"TBR",
					$_SESSION["user_login"],
					$date
				]
			);

			if (!$insert_curetran) {
				sqlsrv_rollback($conn);
				return "insert cure trans failed !!.";
			}
		} else if (
			substr($press_no, 0, 1) === "I" ||
			// substr($press_no, 0, 1) === "J" ||
			substr($press_no, 0, 1) === "L"
		) {
			$insert_curetran = Sqlsrv::insert(
				$conn,
				"INSERT INTO CureTrans(
					Barcode,
					PressNo,
					PressSide,
					MoldNo,
					CuringCode,
					ProductGroup,
					CreateBy,
					CreateDate
				) VALUES (
					?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$barcode,
					$press_no,
					$press_side,
					$mold_no,
					$curing_code_master,
					"PCR",
					$_SESSION["user_login"],
					$date
				]
			);

			if (!$insert_curetran) {
				sqlsrv_rollback($conn);
				return "insert cure trans failed !!.";
			}
		}
		///  BOI 

		$get_BOI = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM PressMaster
			WHERE ID = ?",
			[$press_no]
		);


		$get_BOICeck = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode  FROM CuringBOI
			WHERE Barcode = ?",
			[$barcode]
		);

		if (count($get_BOICeck) > 0) {

			$updateBOI = Sqlsrv::update(
				$conn,
				"UPDATE CuringBOI
				SET BOI = ?
				WHERE Barcode = ?",
				[
					$get_BOI[0]["BOI"],
					$barcode
				]
			);
			if (!$updateBOI) {
				sqlsrv_rollback($conn);
				return "Update boi error.";
			}
		} else {

			// $insert_BOI = Sqlsrv::insert(
			// 	$conn,
			// 	"INSERT INTO CuringBOI(Barcode,BOI) VALUES (
			// 		?, ?
			// 	)",
			// 	[

			// 		$barcode,
			// 		$get_BOI[0]["BOI"]
			// 	]
			// );

			$insert_BOI1 = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(Barcode, BOI)
				(
				 SELECT 
				 I.Barcode
				,'BOI1' AS BOI
			--	,I.CuringDate
				 FROM InventTable I
				 LEFT JOIN CuringBOI B ON B.Barcode = I.Barcode
				  WHERE  convert(date,CuringDate) BETWEEN '$stratdate' AND GETDATE()
				  AND B.Barcode IS NULL
				 and RIGHT(I.Batch,3) = 'D51'
				 GROUP BY I.Barcode
				)"
			);

			// if (!$insert_BOI1) {
			// 	sqlsrv_rollback($conn);
			// 	return "insert boi error";
			// }

			$insert_BOI2 = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(Barcode, BOI)
				(
				 SELECT 
				 I.Barcode
				,'BOI2' AS BOI
				--,I.CuringDate
				 FROM InventTable I
				 LEFT JOIN CuringBOI B ON B.Barcode = I.Barcode
				  WHERE  convert(date,CuringDate) BETWEEN '$stratdate' AND GETDATE()
				  AND B.Barcode IS NULL
				 and RIGHT(I.Batch,3) = 'D52'
				 GROUP BY I.Barcode
				)"
			);

			$insert_BOI3 = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(Barcode, BOI)
				(
				 SELECT 
				 I.Barcode
				,'BOI3' AS BOI
				--,I.CuringDate
				 FROM InventTable I
				 LEFT JOIN CuringBOI B ON B.Barcode = I.Barcode
				  WHERE  convert(date,CuringDate) BETWEEN '$stratdate' AND GETDATE()
				  AND B.Barcode IS NULL
				 and RIGHT(I.Batch,3) = 'D53'
				 GROUP BY I.Barcode
				)"
			);

			$insert_BOI3D = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(Barcode, BOI)
				(
				 SELECT 
				 I.Barcode
				,'BOI1-3' AS BOI
				--,I.CuringDate
				 FROM InventTable I
				 LEFT JOIN CuringBOI B ON B.Barcode = I.Barcode
				  WHERE  convert(date,CuringDate) BETWEEN '$stratdate' AND GETDATE()
				  AND B.Barcode IS NULL
				  and RIGHT(I.Batch,5) = 'D51-3'
				 GROUP BY I.Barcode
				)"
			);

			if (!$insert_BOI1) {
				sqlsrv_rollback($conn);
				return "insert boi error";
			}
		}
		$insert_logBOI = Sqlsrv::insert(
			$conn,
			"INSERT INTO LogBOIcuring(Barcode,BOI,CreateDate) VALUES (
				?, ?, ?
			)",
			[

				$barcode,
				$get_BOI[0]["BOI"],
				$date
			]
		);
		if (!$insert_logBOI) {
			sqlsrv_rollback($conn);
			return "insert logboi error.";
		}

		sqlsrv_commit($conn);
		return 200;
	}

	public function isSkippingDelay()
	{
		$conn = $this->database->dbConnect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT ID FROM UserMaster
			WHERE SkipingDelay = 1
			AND ID = ?",
			[
				$_SESSION['user_login']
			]
		));
	}

	public function pressSideCuringUpdate($pressNo, $pressSide)
	{
		$conn = $this->database->dbConnect();

		$date = Date("Y-m-d H:i:s");

		$query = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster SET $pressSide = ?
			WHERE ID  = ?",
			[$date, $pressNo]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function pressSideCuringDelay($pressNo, $pressSide, $delay)
	{
		$conn = $this->database->dbConnect();

		$sql = "SELECT TOP 1 PM.$pressSide FROM PressMaster PM
		WHERE DATEDIFF(MINUTE, PM.$pressSide, GETDATE()) < ?
		AND PM.ID = ?";

		$query = Sqlsrv::hasRows(
			$conn,
			$sql,
			[
				$delay,
				$pressNo
			]
		);

		if ($query === false) {
			return true;
		} else {
			$remainTime = Sqlsrv::queryArray(
				$conn,
				"SELECT DATEDIFF(MINUTE, PM.$pressSide, GETDATE()) as remain_time
				FROM PressMaster PM
				WHERE PM.ID = ?",
				[$pressNo]
			);
			return $remainTime[0]["remain_time"];
		}
	}

	public function checkTemplateExist($template_code)
	{
		$affix_template_code = (int) substr($template_code, 3, 9);
		$prefix_template_code = substr($template_code, 0, 3);
		$conn = $this->database->dbConnect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 * FROM TemplateRegister
				WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 4, 9)) <= ?
				AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 4, 9)) >= ?
				AND SUBSTRING(StartBarcode, 1, 3) = ?
				AND SUBSTRING(FinishBarcode, 1, 3) = ?",
			[
				$affix_template_code,
				$affix_template_code,
				$prefix_template_code,
				$prefix_template_code
			]
		);

		if ($query === true) {
			return true;
		} else {
			return false;
		}
	}

	public function checkIsExistInventTable($template_code)
	{
		$conn = $this->database->dbConnect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT TemplateSerialNo FROM InventTable
				WHERE TemplateSerialNo = ?",
			[$template_code]
		);
		return $query;
	}

	public function checkPressNo($press_no)
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM PressMaster
				WHERE ID = ?",
			[$press_no]
		);
	}

	public function checkPressSide($press_side)
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM PressArmMaster
				WHERE ID = ?",
			[$press_side]
		);
	}

	public function checkMoldNo($mold_no)
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM MoldMaster
				WHERE ID = ?",
			[$mold_no]
		);
	}

	public function checkCureCode($curing_code_master)
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM CureCodeMaster
				WHERE ID = ?",
			[$curing_code_master]
		);
	}

	public function isExistInventTrans()
	{
		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];
		$conn = $this->database->dbConnect();
		$isExist = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
				WHERE WarehouseID = ?
				AND LocationID = ?
				AND Barcode = ?",
			[
				$user_warehouse,
				$user_location
			]
		);

		return $isExist;
	}

	public function getItemID($curing_code)
	{
		$conn = $this->database->dbConnect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 ItemID, ItemQ, GreentireID FROM CureCodeMaster
				WHERE ID = ?",
			[$curing_code]
		);
		return $query;
	}

	public function isCuring($barcode)
	{
		$conn = $this->database->dbConnect();
		$barcode_decode = Security::_decode($barcode);
		return Sqlsrv::hasRows(
			$conn,
			"SELECT CuringDate FROM InventTable
			WHERE Barcode = ?
			AND CuringDate IS NOT NULL",
			[$barcode_decode]
		);
	}
	public function getAging($curing_code)
	{
		$conn = $this->database->dbConnect();
		$getAging = Sqlsrv::queryArray(
			$conn,
			"SELECT AGING
				FROM CureCodeMaster C JOIN 
				ItemMaster I ON C.ItemID = I.ID JOIN
				FIFOBatch F ON I.ProductGroup = F.ProductGroup
				WHERE C.ID = ? ",
			[
				$curing_code
			]
		);
		return $getAging[0]['AGING'];
	}
	public function chkFIFOBatch($curing_code, $barcode)
	{
		$conn = $this->database->dbConnect();
		//get aging day
		$get_qtyMin = Sqlsrv::queryArray(
			$conn,
			"SELECT QtyMin
				FROM CureCodeMaster C JOIN 
				ItemMaster I ON C.ItemID = I.ID JOIN
				FIFOBatch F ON I.ProductGroup = F.ProductGroup
				WHERE C.ID = ? ",
			[
				$curing_code
			]
		);

		//count id inventtable
		$get_countid = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) AS ID_CHK
				FROM InventTable
				WHERE WarehouseID = '1' AND Status <> '4' 
				AND CheckBuild = '1' AND GT_Code = (SELECT GT_Code FROM InventTable where Barcode = ?)
				AND DateBuild <= (SELECT DATEADD(DAY,-(F.Aging),GETDATE()) 
					FROM CureCodeMaster C JOIN 
					ItemMaster I ON C.ItemID = I.ID JOIN
					FIFOBatch F ON I.ProductGroup = F.ProductGroup
					WHERE C.ID = ? )",
			[
				$barcode,
				$curing_code
			]
		);

		//chk fifo
		if ($get_countid[0]["ID_CHK"] > $get_qtyMin[0]["QtyMin"]) {
			return false;
		} else {
			return true;
		}
	}
}
