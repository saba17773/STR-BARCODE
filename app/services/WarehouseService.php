<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;
use App\V2\Database\Connector;

class WarehouseService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			WM.ID,
			WM.Description,
			WM.Type,
			WM.Company,
			WT.Description as TypeName
			FROM WarehouseMaster WM
			LEFT JOIN WarehouseTypeMaster WT
			ON WM.Type = WT.ID"
		);
	}

	public function create($id, $description, $type)
	{
		$description = trim($description);
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::insert(
			$conn,
			"UPDATE WarehouseMastergit
			SET Description = ?,
			Type = ?,
			Company = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO WarehouseMaster(
				Description, Company, Type
			) VALUES (?, ?, ?)",
			[
				$description,
				$type,
				$_SESSION["user_company"],
				$id,
				$description,
				$_SESSION["user_company"],
				$type
			]
		);

		if (!$query) {
			sqlsrv_commit($conn);
			return 404;
		} else {
			sqlsrv_commit($conn);
			return 200;
		}
	}

	public function update($wh_name, $id)
	{
		$wh_name = trim($wh_name);

		$conn = Database::connect();

		if (self::checkWhExist($wh_name) === false) {

			$query = Sqlsrv::update(
				$conn,
				"UPDATE WarehouseMaster
				SET Description = ?,
				Company = ?
				WHERE ID =?",
				[$wh_name, $_SESSION["user_company"], $id]
			);

			if (!$query) {
				return false;
			}

			return true;
		} else {
			return false;
		}
	}

	public function checkWhExist($wh_name)
	{
		$wh_name = trim($wh_name);

		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM WarehouseMaster WH
			WHERE WH.Description = ?",
			[$wh_name]
		);
	}

	public function receiveToWarehouse($barcode_decode)
	{
		$conn = Database::connect();
		$conn_wms = (new Connector)->connectWMS();

		$date = date("Y-m-d H:i:s");
		$barcode_decode = trim($barcode_decode);
		if (sqlsrv_begin_transaction($conn) === false) {
			return "begin transaction failed";
		}

		if (!isset($_SESSION["user_location"])) {
			return 'session failed';
		}

		$w = new Utils;
		$week = $w->getWeek($date);

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

		if (!$get_location) {
			sqlsrv_rollback($conn);
			return "Cannot use location";
		}

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$trans_id = Utils::genTransId($barcode_decode);

		$move_out_trans = Sqlsrv::insert(
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

		if (!$move_out_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move out error.";
		}

		// Update invent table
		$update_inventtble = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET WarehouseReceiveDate = ?,
			DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			Batch = ?
			WHERE Barcode = ?",
			[
				$date,
				// $get_location[0]["DisposalID"],
				// $get_location[0]["WarehouseID"],
				// $get_location[0]["ReceiveLocation"],
				6,
				3,
				7,
				$_SESSION["user_login"],
				$date,
				$get_barcode_info[0]["Batch"],
				$barcode_decode
			]
		);

		if (!$update_inventtble) {
			sqlsrv_rollback($conn);
			return "update inventtble error.";
		}

		$trans_id = Utils::genTransId($barcode_decode);
		$get_barcode_trans = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$move_in_trans = Sqlsrv::insert(
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
				$get_barcode_trans[0]["DisposalID"],
				null,
				$get_barcode_trans[0]["WarehouseID"],
				$get_barcode_trans[0]["LocationID"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
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

		// Move in onhand +1
		// $move_in_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand SET QTY += 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Company = ?
		// 	AND Batch = ?
		// 	IF @@ROWCOUNT = 0
		// 	INSERT INTO Onhand(
		// 		CodeID,
		// 		WarehouseID,
		// 		LocationID,
		// 		Batch,
		// 		QTY,
		// 		Company
		// 	) VALUES (?, ?, ?, ?, ?, ?)",
		// 	[
		// 		$get_barcode_info[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["ReceiveLocation"],
		// 		$_SESSION["user_company"],
		// 		$get_barcode_info[0]["Batch"],

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

		$_itemName = Sqlsrv::queryArray(
			$conn,
			"SELECT IM.NameTH FROM InventTable I
			LEFT JOIN ItemMaster IM ON IM.ID = I.ItemID
			WHERE I.Barcode = ?",
			[
				$barcode_decode
			]
		);

		if (count($_itemName) === 0) {
			$_itemName[0]['NameTH'] = null;
		}

		// #### WMS

		// if (substr($get_barcode_info[0]['PressNo'], 0, 1) === "I" ||
		// 		substr($get_barcode_info[0]['PressNo'], 0, 1) === "J" ||
		// 		substr($get_barcode_info[0]['PressNo'], 0, 1) === "G" ||
		// 		substr($get_barcode_info[0]['PressNo'], 0, 1) === "H") {

		// 		$insertWMSTempInterface = sqlsrv_query(
		// 			$conn_wms,
		// 			"INSERT INTO WMS_BarcodeTemp(
		// 				ForceID,
		// 				Barcode,
		// 				ItemNo,
		// 				BatchNo,
		// 				ManufacturingDate,
		// 				ExpiryDate,
		// 				Flage,
		// 				[Type],
		// 				CreateDate,
		// 				Gate,
		// 				ITEM_NAME
		// 			) VALUES (
		// 				?, ?, ?, ?, ?,
		// 				?, ?, ?, ?, ?,
		// 				?
		// 			)",
		// 			[
		// 				'51',
		// 				$barcode_decode,
		// 				$get_barcode_info[0]['ItemID'],
		// 				$get_barcode_info[0]['Batch'],
		// 				date('Y-m-d', strtotime($get_barcode_info[0]['CuringDate'])),
		// 				date('Y-m-d', strtotime("+180 day", strtotime($get_barcode_info[0]['CuringDate']))),
		// 				'N',
		// 				0,
		// 				$date,
		// 				51,
		// 				substr($_itemName[0]['NameTH'], 0, 50)
		// 			]
		// 		);

		// 		if (!$insertWMSTempInterface) {
		// 			sqlsrv_rollback($conn);
		// 			return "Insert wms barcode temp error";
		// 		}
		// }

		// #######

		sqlsrv_commit($conn);
		return 200;

		// if ($update_inventtble &&
		// 	$move_out_trans &&
		// 	$move_in_trans &&
		// 	$move_out_onhand &&
		// 	$move_in_onhand) {

		// 	sqlsrv_commit($conn);
		// 	return 200;
		// } else {
		// 	sqlsrv_rollback($conn);
		// 	return "transaction failed";
		// }
	}

	public function createWarehouseType($id, $desc)
	{
		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"UPDATE WarehouseTypeMaster
			SET Description = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO WarehouseTypeMaster(Description)
			VALUES(?)",
			[
				$desc,
				$id,
				$desc
			]
		);

		if ($query) {
			return 200;
		} else {
			return "ทำรายการไม่สำเร็จ";
		}
	}

	public function getAllWarehouseType()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM WarehouseTypeMaster"
		);
	}

	public function deleteWarehouseType($id)
	{
		$conn = Database::connect();
		$query =  Sqlsrv::delete(
			$conn,
			"DELETE FROM WarehouseTypeMaster
			WHERE ID = ?
			AND ID NOT IN (
				SELECT Type FROM WarehouseMaster
				WHERE Type = ?
			)",
			[$id, $id]
		);

		if ($query) {
			return 200;
		} else {
			return 404;
		}
	}

	public function getReportSendToSVO($time, $location_type)
	{
		$sqltime = '';
		foreach ($time as $v) {
			$sqltime .= ' T.SendSVODate BETWEEN ' . $v . ' OR ';
		}
		$sqltime = trim($sqltime, ' OR ');
		$sql = "SELECT
				Z.ItemID,
				Z.CuringCode,
				Z.NameTH,
				Z.Batch,
				SUM(Z.QTY)[QTY]
			FROM
			(
				SELECT
					CCM.ItemID,
					T.CuringCode,
					I.NameTH,
					T.Batch,
					T.QTY
				FROM InventTable T
				LEFT JOIN CureCodeMaster CCM ON CCM.ID = T.CuringCode
				LEFT JOIN ItemMaster I ON CCM.ItemID = I.ID AND CCM.ID = T.CuringCode
				WHERE T.DisposalID IN (23, 24)
				AND ($sqltime)
				AND T.WarehouseID = $location_type
			) Z
			GROUP BY
				Z.ItemID,
				Z.CuringCode,
				Z.NameTH,
				Z.Batch,
				Z.QTY";
		// return $sql;

		header('Content-Type: application/json');
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function getReportSentToWarehouse($time, $product_group, $pressBOI, $batch)
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

		$sql = "SELECT Z.ItemID,
		Z.CuringCode,
		Z.NameTH,
		Z.Batch,
		SUM(Z.QTY)[QTY]
		 FROM 
		 (
		 SELECT ITS.CodeID AS ItemID,
		 CCM.ID AS CuringCode,
		 I.NameTH,
		 ITS.Batch,
		 TT.QTY,
		 TT.CuringDate,
		 TT.BOI 
		 FROM
		 (
		  SELECT Barcode,WarehouseTransReceiveDate,GT_Code,QTY,CuringDate,BOI
		  FROM
		  (
		   SELECT *
		   FROM
		   (
			SELECT T.*,CB.Id,CB.BOI
			,ROW_NUMBER() OVER(PARTITION BY T.BARCODE ORDER BY CB.ID DESC) R1
			FROM CuringBOI CB 
			JOIN (
				SELECT T.Barcode
				,T.WarehouseTransReceiveDate
				,T.GT_Code
				,T.QTY
				,T.CuringDate
				FROM InventTable T
				WHERE ($sqltime)
			AND $batch
			)T ON CB.Barcode = T.Barcode
		   )T1
		   WHERE T1.R1 = 1
		  )T2
		  $whereBOI
		 )TT
		 LEFT JOIN InventTrans ITS ON ITS.Barcode = TT.Barcode
		 AND ITS.DocumentTypeID = 1
		 AND ITS.DisposalID = 5
		 AND ITS.CreateDate = TT.WarehouseTransReceiveDate
		 LEFT JOIN CureCodeMaster CCM
		 ON CCM.GreentireID = TT.GT_Code
		 AND CCM.ItemID = ITS.CodeID
		 LEFT JOIN ItemMaster I ON I.ID = ITS.CodeID
		 WHERE I.ProductGroup = '$product_group'
		 AND ITS.DisposalID NOT IN (23, 24)
		 )Z
		 GROUP BY Z.ItemID,
		  Z.CuringCode,
		  Z.NameTH,
		  Z.Batch
		 ORDER BY Z.CuringCode, Z.Batch ASC";




		// return $sql;
		// return trim($sqltime, ' OR ');
		// header('Content-Type: application/json');
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}


	public function getReportReceiveToWarehouse($shift, $time, $datewarehouse, $brand, $product_group, $pressBOI, $batch)
	{
		$sqltime = '';
		foreach ($time as $v) {
			$sqltime .= ' ' . $v . ' OR ';
		}
		$sqltime = trim($sqltime, ' OR ');

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		//tan edit 15/12/18
		// $sql = "SELECT
		// 	ITS.CodeID AS ItemID,
		// 	CCM.ID AS CuringCode,
		// 	I.NameTH,
		// 	T.Batch,
		// 	SUM(T.QTY)[QTY],
		// 	I.Brand,
		// 	I.Pattern
		// 	FROM InventTable T
		// 		LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
		// 		LEFT JOIN InventTrans ITS
		// 			ON ITS.Barcode = T.Barcode
		// 			AND ITS.DocumentTypeID = 1
		// 			AND ITS.DisposalID = 6
		// 			AND ITS.CreateDate = T.WarehouseReceiveDate
		// 		LEFT JOIN CureCodeMaster CCM
		// 			ON CCM.GreentireID = T.GT_Code
		// 			AND CCM.ItemID = ITS.CodeID
		// 		LEFT JOIN ItemMaster I ON I.ID = ITS.CodeID
		// 	WHERE DisposalID <> 16
		// 		AND T.WarehouseTransReceiveDate IS NOT NULL
		// 		AND T.WarehouseReceiveDate IS NOT NULL
		// 		and ($sqltime)
		// 		and B.BrandID IN ($brand)
		// 		AND I.ProductGroup = '$product_group'
		// 	group by
		// 		ITS.CodeID
		// 		,CCM.ID
		// 		,I.NameTH
		// 		,T.Batch
		// 		,I.Brand
		// 		,I.Pattern
		// 	";
		$sql = "SELECT 
		ITS.CodeID AS ItemID,
		   CCM.ID AS CuringCode,
		   I.NameTH,
		   ITS.Batch,
		   SUM(T.QTY)[QTY],
		   I.Brand,
		   I.Pattern
		 FROM  InventTable T
		
		LEFT JOIN (
			 SELECT Barcode,BOI
			 FROM CuringBOI
			 GROUP BY Barcode,BOI
			) CB ON CB.Barcode = T.Barcode
		LEFT JOIN InventTrans ITS
			 ON ITS.Barcode = T.Barcode
			 AND ITS.DocumentTypeID = 1
			 AND ITS.DisposalID = 6
			 AND ITS.CreateDate = T.WarehouseReceiveDate
		LEFT JOIN ItemMaster I ON I.ID = T.ItemID
		LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
		LEFT JOIN CureCodeMaster CCM
			 ON CCM.GreentireID = T.GT_Code
			 AND CCM.ItemID = ITS.CodeID
		  WHERE 
		 T.DisposalID <> 16
			AND T.WarehouseTransReceiveDate IS NOT NULL
			AND T.WarehouseReceiveDate IS NOT NULL
			AND ($sqltime)
			--AND T.CuringDate IS NOT NULL
			--AND T.ItemID = 'I-0054779'
			and B.BrandID IN ($brand)
			AND I.ProductGroup = '$product_group'
			AND $batch
			$whereBOI
		GROUP BY
		   ITS.CodeID
		   ,CCM.ID
		   ,I.NameTH
		   ,ITS.Batch
		   ,I.Brand
		   ,I.Pattern 
		ORDER BY ITS.CodeID,ITS.Batch ASC ";
		header('Content-Type: application/json');

		// return $sql;

		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}
	public function FGWithdrawPDF($dateinter)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
				J.InventJournalID
				,J.ItemID
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
				,S.Description
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode
			LEFT JOIN ItemMaster IT ON I.ItemID=IT.ID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN Status S ON IJ.Status=S.ID
			WHERE CONVERT(date,J.CreateDate) = ?
			AND IJ.JournalTypeID = 'MOVWH'
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$dateinter
			]
		);
	}
	public function getReportCurngaxSentToWarehouse($date_curing, $time, $product_group, $pressBOI, $shift, $batch)
	{
		$date_today = date('Y-m-d', strtotime($date_curing));
		$date_tomorrow = date('Y-m-d', strtotime($date_curing . '+1 days'));
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		if ($shift == "day") {
			$sql = "SELECT
					Z.ItemID,
					Z.CuringCode,
					Z.Batch,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_today 08:00:01' AND  '$date_today 10:00:00' THEN 1
							ELSE 0
						END
				) as Q1,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_today 10:00:01' AND  '$date_today 12:00:00' THEN 1
							ELSE 0
						END
				) as Q2,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_today 12:00:01' AND  '$date_today 14:00:00' THEN 1
							ELSE 0
						END
				) as Q3,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_today 14:00:01' AND '$date_today 16:00:00' THEN 1
							ELSE 0
						END
				) as Q4,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_today 16:00:01' AND '$date_today 18:00:00' THEN 1
							ELSE 0
						END
				) as Q5,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_today 18:00:01' AND '$date_today 20:00:00' THEN 1
							ELSE 0
						END
				) as Q6
				FROM
				(
					SELECT
						ITS.CodeID AS ItemID,
						CCM.ID AS CuringCode,
						I.NameTH,
						ITS.Batch,
						T.QTY,
						T.WarehouseTransReceiveDate
					FROM InventTable T

					LEFT JOIN InventTrans ITS
						ON ITS.Barcode = T.Barcode
						AND ITS.DocumentTypeID = 1
						AND ITS.DisposalID = 5
						AND ITS.CreateDate = T.WarehouseTransReceiveDate
					LEFT JOIN CureCodeMaster CCM
						ON CCM.GreentireID = T.GT_Code
						AND CCM.ItemID = ITS.CodeID
					LEFT JOIN ItemMaster I ON I.ID = ITS.CodeID
					LEFT JOIN PressMaster PM On PM.ID = T.PressNo
					LEFT JOIN CuringBOI CB ON CB.Barcode = T.Barcode
					WHERE I.ProductGroup = '$product_group'
					AND ITS.DisposalID NOT IN (23, 24)
					$whereBOI
					--AND CB.BOI = '$pressBOI'
					AND ($time)
					AND $batch
				) Z

				ORDER BY Z.CuringCode, Z.Batch ASC";
			$conn = Database::connect();
			return Sqlsrv::queryJson(
				$conn,
				$sql
			);
		} else {
			$sql = "SELECT
					Z.ItemID,
					Z.CuringCode,
					Z.Batch,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_today 20:00:01' AND  '$date_today 22:00:00' THEN 1
							ELSE 0
						END
				) as Q1,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_today 22:00:01' AND  '$date_tomorrow 00:00:00' THEN 1
							ELSE 0
						END
				) as Q2,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN  '$date_tomorrow 00:00:01' AND  '$date_tomorrow 02:00:00' THEN 1
							ELSE 0
						END
				) as Q3,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_tomorrow 02:00:01' AND '$date_tomorrow 04:00:00' THEN 1
							ELSE 0
						END
				) as Q4,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_tomorrow 04:00:01' AND '$date_tomorrow 06:00:00' THEN 1
							ELSE 0
						END
				) as Q5,
				(
					SELECT
						CASE
							WHEN z.WarehouseTransReceiveDate BETWEEN '$date_tomorrow 06:00:01' AND '$date_tomorrow 08:00:00' THEN 1
							ELSE 0
						END
				) as Q6
				FROM
				(
					SELECT
						ITS.CodeID AS ItemID,
						CCM.ID AS CuringCode,
						I.NameTH,
						ITS.Batch,
						T.QTY,
						T.WarehouseTransReceiveDate
					FROM InventTable T

					LEFT JOIN InventTrans ITS
						ON ITS.Barcode = T.Barcode
						AND ITS.DocumentTypeID = 1
						AND ITS.DisposalID = 5
						AND ITS.CreateDate = T.WarehouseTransReceiveDate
					LEFT JOIN CureCodeMaster CCM
						ON CCM.GreentireID = T.GT_Code
						AND CCM.ItemID = ITS.CodeID
					LEFT JOIN ItemMaster I ON I.ID = ITS.CodeID
					LEFT JOIN PressMaster PM On PM.ID = T.PressNo
					LEFT JOIN CuringBOI CB ON CB.Barcode = T.Barcode
					WHERE I.ProductGroup = '$product_group'
					AND ITS.DisposalID NOT IN (23, 24)
					$whereBOI
					AND ($time)
					AND $batch
				) Z

				ORDER BY Z.CuringCode, Z.Batch ASC";
			$conn = Database::connect();
			return Sqlsrv::queryJson(
				$conn,
				$sql
			);
		}
	}

	public function createsendwhtable($truck, $check, $id)
	{
		$year = date("Y");
		$date = date("Y-m-d H:i:s");
		//$date = '2021-04-03 08:00:00';
		$timecheck = date('H', strtotime($date));
		//$datenow = date("m-d-Y");
		$datenow =  date('Y-m-d', strtotime($date));
		//$datenow = $date;

		if ($timecheck  > "07" && $timecheck < "20") {
			$date1 = $datenow . ' 08:00:00';
			$date2 = $datenow . ' 19:59:59';
		} else {
			if ($timecheck == "20" || $timecheck == "21" || $timecheck == "22" || $timecheck == "23") {
				$datecuringnight = date('Y-m-d', strtotime($date . "+1 days"));
				$datenow2 = date('Y-m-d', strtotime($date));
			} else {
				$datenow2 = date('Y-m-d', strtotime($date . "-1 days"));
				$datecuringnight = date('Y-m-d', strtotime($date));
			}
			$date1 = $datenow2 . ' 20:00:00';
			$date2 = $datecuringnight . ' 07:59:59';
		}
		// return "START :" . $date1 . " END" . $date2;
		// exit();

		$conn = Database::connect();
		if ($check == "update") {
			$updatestatus = Sqlsrv::update(
				$conn,
				"UPDATE SendToWHTable SET Complete = ?,CompleteDate = ? WHERE Id = ?",
				[
					1,
					$date,
					$id
				]


			);

			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {

			$querytruckcheck = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 PlateNumber FROM TruckMaster
					where PlateNumber = '$truck' and SendToWh = 1"
			);

			if (count($querytruckcheck) <= 0) {
				return "หมายเลขรถ :" . $truck . " ไม่มีในระบบ";
				exit();
			}
			$queryround = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 JournalDescription FROM SendToWHTable
					where CreateDate between '$date1' and '$date2' and TruckID = '$truck' order by CreateDate desc"
			);
			if ($queryround[0]["JournalDescription"] == null || $queryround[0]["JournalDescription"] == '') {
				$round = "รอบที่ 1";
			} else {
				$rowsround = explode(" ", $queryround[0]["JournalDescription"]);
				$rowcal = $rowsround[1] + 1;
				$round = "รอบที่ " . $rowcal;
			}
			// return $round;
			// exit();

			$querycheck = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 JournalDescription FROM SendToWHTable
					where CreateDate between '$date1' and '$date2' and TruckID = '$truck' and Complete = 0 order by CreateDate desc"
			);
			if ($querycheck[0]["JournalDescription"] !== null) {

				return "หมายเลขรถ :" . $truck . $queryround[0]["JournalDescription"] . "ยังไม่ได้ complete";
				exit();
			}

			if (sqlsrv_begin_transaction($conn) === false) {
				return "transaction failed!";
			}
			$queryYear = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 SeqJournal,[Year] FROM SequeueSendWh"
			);

			if ($queryYear[0]["Year"] == $year) {
				//$conn = Database::connect();
				$updateJournalSequeue = Sqlsrv::update(
					$conn,
					"UPDATE SequeueSendWh SET SeqJournal += 1"
				);
				//	sqlsrv_commit($conn);
			}

			if ($queryYear[0]["Year"] <> $year) {
				//$conn = Database::connect();

				$updateJournalSequeue = Sqlsrv::update(

					$conn,
					"UPDATE SequeueSendWh SET SeqJournal = ?, [Year] = ?",
					[1, $year]
					//	sqlsrv_commit($conn);

				);
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 SeqJournal,[Year] FROM SequeueSendWh"
			);
			//return $date;
			$insertInventJournalTable = Sqlsrv::insert(
				$conn,
				"INSERT INTO SendToWHTable(
					JournalID,
					JournalDescription,
					TruckID,
					CreateDate,
					Complete
					--CompleteDate
				
				) VALUES (?, ?, ?, ?, ?)",
				[
					"FS" . substr($year, -2) . "-" . str_pad($query[0]["SeqJournal"], 5, "0", STR_PAD_LEFT),
					$round,
					$truck,
					$date,
					0

				]
			);


			if ($insertInventJournalTable) {
				sqlsrv_commit($conn);
				return
					[
						"status" => 200,
						"message" => "สร้าง " . $truck . $round . " สำเร็จ"
					];
			} else {
				return 404;
			}
		}
	}

	public function allwhtable()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			T.* 
			,( select count(barcode)as qty from SendToWHLine where JournalID = T.JournalID) as [Count]
			FROM SendToWHTable T ORDER BY Id DESC"

		);
	}

	public function alllinewhtable($id)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			SL.JournalID
			,SL.Barcode
			,SL.ItemID
			,IM.NameTH
			,CM.ID
			,SL.Batch
			,SL.CreateDate
			
			 FROM SendToWHLine SL
			 LEFT JOIN ItemMaster IM ON IM.ID = SL.ItemID
			 LeFT JOIN InventTable I ON I.Barcode = SL.Barcode
			 LEFT JOIN Curecodemaster CM ON CM.ItemID = SL.ItemID AND CM.ID = I.CuringCode
			 
			 WHERE SL.JournalID = ?",
			[
				$id
			]
		);
	}

	public function reportwhLine($journalId)
	{
		$conn = Database::connect();

		return Sqlsrv::queryArray(
			$conn,
			"SELECT 

			ST.TruckID,
			ST.JournalDescription,
			ST.CreateDate,
			(SELECT TOP 1 CreateDate FROM SendToWHLine  where JournalID = ST.JournalID order by CreateDate asc) AS FirstScan,
			(SELECT TOP 1 CreateDate FROM SendToWHLine  where JournalID = ST.JournalID order by CreateDate desc) AS Lastscan
			 FROM  SendToWHTable  ST 
			
			WHERE ST.JournalID = ?",
			[
				$journalId
			]
		);
	}

	public function reportwhLinedata($journalId)
	{
		$conn = Database::connect();

		return Sqlsrv::queryArray(

			$conn,
			"SELECT 
			CM.CuringCode AS ID
			,SL.ItemID
			,IM.NameTH
			,SL.Batch
			,COUNT(SL.Barcode)  AS QTY
			FROM SendToWHLine SL
			 LEFT JOIN InventTable CM ON CM.Barcode = SL.Barcode
			 LEFT JOIN ItemMaster IM ON IM.ID = SL.ItemID
			WHERE SL.JournalID = ?
			GROUP BY CM.CuringCode
			,SL.ItemID
			,IM.NameTH
			,SL.Batch
			ORDER BY SL.ItemID ASC",
			[
				$journalId
			]
		);
	}

	// Quality
	public function qualitycheck($barcode)
	{
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		$Check_barcode = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM InventTable
			WHERE DisposalID = '11'
			AND WarehouseID = '3' 
			AND Barcode = ? ",
			[
				$barcode 
			]
		);
		
		$select_barcode = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode,ItemID,CuringCode,Batch FROM InventTable
			 WHERE Barcode =  ? ",
			[
				$barcode
			]
		);

		if(count($Check_barcode) > 0 ){
		// Insert Log
		$insert_log = Sqlsrv::insert(
			$conn,
			"INSERT INTO QualityCheckingTable (
			Barcode,
			ItemID,
			CureCode,
			Batch,
			CreateBy,
			CreateDate
			) VALUES (?, ?, ?, ?, ?, ?)",	
			[
				$barcode,
				$Check_barcode[0]["ItemID"],
				$Check_barcode[0]["CuringCode"],
				$Check_barcode[0]["Batch"],
				$_SESSION["user_login"],
				$date
			]
		);
		sqlsrv_commit($conn);
	}else{
		return false;
	}

		if ($select_barcode && $insert_log) {
			sqlsrv_commit($conn);
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
	
    public function countquality($barcode)
    {
        $conn = Database::connect();
		$date = date("Y-m-d");
		$datechk = date("Y-m-d H:i:s");

		if($datechk >= $date." 08:00:01" && $datechk <= $date." 20:00:00")
		{
			$a = $date." 08:00:01";
			$b = $date." 20:00:00";
		}else{
			$a = $date." 20:00:01";
			$b = date('Y-m-d', strtotime($date . '+1 day')) . ' 08:00:00';
		}
        
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM QualityCheckingTable WHERE  CreateDate BETWEEN  '$a' AND  '$b' "
		);
	
	}

	public function qualitycheckbarcode($barcode)
	{
		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM QualityCheckingTable 
			 	WHERE Barcode = ? ",
			[
				$barcode 
			]
		);
	}
}