<?php

namespace App\Services;

use App\Components\Security;
use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;

class FinalService
{
	public function isFinalReceiveDateExist($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ?
			AND FinalReceiveDate IS NOT NULL",
			[$barcode_decode]
		);
	}

	public function save($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReturnReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		if (count($get_location) === 0) {
			sqlsrv_rollback($conn);
			return 'User location ไม่ถูกต้อง';
		}

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$trans_id = Utils::genTransId($barcode_decode);

		$move_out = Sqlsrv::insert(
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
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				$get_barcode_info[0]["DisposalID"],
				null,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_out) {
			sqlsrv_rollback($conn);
			return "insert trans move out error.";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET
			DisposalID = ?, -- X-ray
			WarehouseID = ?,
		  LocationID = ?,
			Status = 1, -- Receive
			FinalReceiveDate = ?,
			GateReceiveNo = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$get_location[0]["DisposalID"],
				$get_location[0]["WarehouseID"], // WH X-ray
				$get_location[0]["ReceiveLocation"], // LC Trans
				$date,
				null,
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
		}

		$trans_id = Utils::genTransId($barcode_decode);

		$move_in = Sqlsrv::insert(
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
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_info[0]["ItemID"],
				$get_barcode_info[0]["Batch"],
				$get_location[0]["DisposalID"],
				null,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReceiveLocation"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}

		// Update Onhand

		// move out onhand -1
		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?
		// 	AND QTY > 0",
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
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["ReceiveLocation"],
		// 		$get_barcode_info[0]["Batch"],
		// 		$_SESSION["user_company"],
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["ReceiveLocation"],
		// 		$get_barcode_info[0]["Batch"],
		// 		1, // qty
		// 		$_SESSION["user_company"]
		// 	]
		// );

		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		if (
			$update_inventtable &&
			$move_out &&
			$move_in
		) {

			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
		// END
	}

	public function saveReturn($barcode)
	{
		$barcode_decode = Security::_decode($barcode);

		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.ReturnReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReturnReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		if ($_SESSION["user_warehouse"] === 2) {
			$inventTableStatus = 5; // Hold
		} else if ($_SESSION["user_warehouse"] === 3) {
			$inventTableStatus = 1; // Receive
		} else {
			$inventTableStatus = 5; // hold
		}

		if ($_SESSION["user_warehouse"] !== 1) {
			$_code = $get_barcode_info[0]["ItemID"];
		} else {
			$_code = $get_barcode_info[0]["GT_Code"];
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET
			DisposalID = ?, -- X-ray
			WarehouseID = ?,
		  LocationID = ?,
			Status = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			PalletNo = null
			WHERE Barcode = ?",
			[
				9, // return
				$get_barcode_info[0]["WarehouseID"], // WH
				$get_location[0]["ReturnReceiveLocation"],
				//11, // Hold > edit get from ReturnReceiveLocation
				$inventTableStatus,
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return $date;
		}
		//ppp
		$trans_id = Utils::genTransId($barcode_decode);

		$move_in = Sqlsrv::insert(
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
				$barcode_decode,
				$_code,
				$get_barcode_info[0]["Batch"],
				9, // Return
				'CUR511', // Return
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				//11, // Final Hold
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}

		$move_in_onhand = Sqlsrv::update(
			$conn,
			"UPDATE Onhand SET QTY += 1
			WHERE CodeID = ?
			AND WarehouseID = ?
			AND LocationID = ?
			AND Batch = ?
			AND Company =?
			IF @@ROWCOUNT = 0
			INSERT INTO Onhand
			VALUES (?, ?, ?, ?, ?, ?)",
			[
				$_code,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				$_SESSION["user_company"],
				$_code,
				$get_location[0]["WarehouseID"],
				$get_location[0]["ReturnReceiveLocation"],
				$get_barcode_info[0]["Batch"],
				1, // qty
				$_SESSION["user_company"]
			]
		);

		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		sqlsrv_commit($conn);
		return 200;
	}

	public function getReportFinal($time, $product_group, $pressBOI)
	{
		$sqltime = '';
		foreach ($time as $v) {
			$sqltime .= ' ' . $v . ' OR ';
		}
		$sqltime = trim($sqltime, ' OR ');
		
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" WHERE BOI ='$pressBOI' ";
		}

		$sql = "SELECT Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,SUM(Z.QTY)[QTY]
				FROM
				(
					SELECT ITS.CodeID AS ItemID
					,CCM.ID AS CuringCode
					,I.NameTH
					,ITS.Batch
					,T.QTY
					FROM
					(
						SELECT *
						FROM
						(
							SELECT *
							FROM
							(
								SELECT T.*
									,CB.Id
									,CB.BOI
									,ROW_NUMBER() OVER(PARTITION BY T.BARCODE ORDER BY CB.ID DESC) R1
								FROM CuringBOI CB 
								JOIN (
									SELECT T.Barcode
										,T.QTY
										,T.FinalReceiveDate
										,T.GT_Code
										,T.CuringCode
									FROM InventTable T
									WHERE($sqltime)
								)T ON CB.Barcode = T.Barcode
							)T1
							WHERE R1 = 1
						)T2
						$whereBOI
					)T
					LEFT JOIN InventTrans ITS
						ON ITS.Barcode = T.Barcode
						AND ITS.DocumentTypeID = 1
						AND ITS.DisposalID = 4
						AND ITS.CreateDate = T.FinalReceiveDate
					LEFT JOIN CureCodeMaster CCM
						ON CCM.GreentireID = T.GT_Code
						AND CCM.ItemID = ITS.CodeID
						OR CCM.ItemQ = ITS.CodeID
						AND CCM.ID = T.CuringCode
					LEFT JOIN ItemMaster I ON I.ID = CCM.ItemID
					WHERE I.ProductGroup = '$product_group'
					AND ITS.DisposalID NOT IN (23, 24)
				)Z
				GROUP BY
					Z.ItemID,
					Z.CuringCode,
					Z.NameTH,
					Z.Batch,
					Z.QTY
				ORDER BY Z.CuringCode, Z.Batch ASC";
		$conn = Database::connect();
		$result = Sqlsrv::queryJson(
			$conn,
			$sql
		);
		// print_r(Sqlsrv_error());exit;
		// print_r($sql);exit;
		return $result;
	}


	public function getReportFinalToWh($date, $shift, $trucknumber)
	{
		$date_today = date('Y-m-d', strtotime($date));
		$date_tomorrow = date('Y-m-d', strtotime($date . '+1 days'));

		if ($shift == "day") {

			$date_to = $date_today . " 08:00:00";
			$date_from = $date_today . " 19:59:59";
		} else {
			$date_to = $date_today . " 20:00:00";
			$date_from = $date_tomorrow . " 07:59:59";
		}

		// return $date_from;
		$sql = "SELECT  
		-- ST.TruckID + ' ' + ST.JournalDescription AS RoundTruck
		ST.TruckID 
		,ST.JournalDescription
		,SL.ItemID
		,IM.NameTH
		,CM.CuringCode AS ID
		,SL.Batch
		,CONVERT(VARCHAR(5), ST.CreateDate, 108)CreateDate
		,COUNT(SL.Barcode) AS qty
		 FROM SendToWHLine SL
		LEFT JOIN SendToWHTable ST ON ST.JournalID = SL.JournalID	
		LEFT JOIN ItemMaster IM ON IM.ID = SL.ItemID
		--LEFT JOIN CureCodeMaster CM ON CM.ItemID = SL.ItemID
		LEFT JOIN InventTable CM ON CM.Barcode = SL.Barcode
		WHERE ST.CreateDate between '$date_to'  and '$date_from'
		AND ST.TruckID  IN ($trucknumber)
		GROUP BY ST.TruckID
		, ST.JournalDescription 
		,SL.ItemID
		,IM.NameTH
		,SL.Batch
		,CM.CuringCode
		,ST.CreateDate
		ORDER BY SL.ItemID ASC";

		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			$sql
		);
	}

	public function getroundFinalToWh($date, $shift, $trucknumber)
	{
		$date_today = date('Y-m-d', strtotime($date));
		$date_tomorrow = date('Y-m-d', strtotime($date . '+1 days'));

		if ($shift == "day") {

			$date_to = $date_today . " 08:00:00";
			$date_from = $date_today . " 19:59:59";
		} else {
			$date_to = $date_today . " 20:00:00";
			$date_from = $date_tomorrow . " 07:59:59";
		}

		// return $date_from;
		$sql = "SELECT * FROM (SELECT  
		ST.TruckID 
		,ST.JournalDescription AS desjornal
		,ST.CreateDate
		FROM SendToWHLine SL
		LEFT JOIN SendToWHTable ST ON ST.JournalID = SL.JournalID	
		LEFT JOIN ItemMaster IM ON IM.ID = SL.ItemID
		WHERE ST.CreateDate between '$date_to'  and '$date_from'
		AND ST.TruckID  IN ($trucknumber)
		--AND IM.ProductGroup = 'RDT'
		GROUP BY 	
		ST.TruckID 
		,ST.JournalDescription
		,ST.CreateDate)T
		ORDER BY T.CreateDate";

		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			$sql
		);
	}

	public function countround($date, $shift, $round, $jornaldes)
	{
		$date_today = date('Y-m-d', strtotime($date));
		$date_tomorrow = date('Y-m-d', strtotime($date . '+1 days'));

		if ($shift == "day") {

			$date_to = $date_today . " 08:00:00";
			$date_from = $date_today . " 19:59:59";
		} else {
			$date_to = $date_today . " 20:00:00";
			$date_from = $date_tomorrow . " 07:59:59";
		}

		// return $date_from;

		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT count(*) AS Rows
			FROM(
			SELECT 
					ST.TruckID 
					,ST.JournalDescription
					,SL.ItemID
					,IM.NameTH
					--,CM.ID
					,SL.Batch
					
					 FROM SendToWHLine SL
					LEFT JOIN SendToWHTable ST ON ST.JournalID = SL.JournalID	
					LEFT JOIN ItemMaster IM ON IM.ID = SL.ItemID
					-- LEFT JOIN CureCodeMaster CM ON CM.ItemID = SL.ItemID
					--LEFT JOIN InventTable CM ON CM.CuringCode = SL.ItemID
					WHERE ST.CreateDate between '$date_to'  and '$date_from'
					GROUP BY ST.TruckID
					, ST.JournalDescription 
					,ST.JournalDescription
					,SL.ItemID
					,IM.NameTH
					--,CM.ID
					,SL.Batch)T
					
				WHERE  T.TruckID = '$round'  AND T.JournalDescription = '$jornaldes'"
		);

		return $query[0]['Rows'];

		// $conn = Database::connect();
		// return Sqlsrv::queryArray(
		// 	$conn,
		// 	$sql
		// );
	}
}