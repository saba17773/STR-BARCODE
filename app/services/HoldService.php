<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;
use App\Components\Security;
use App\Services\BarcodeService;
use App\Services\FinalService;
use Respect\Validation\Validator as V;

class HoldService
{
	public function run($barcode, $defect, $holdtype)
	{
		$conn = Database::connect();
		$barcode_decode = Security::_decode($barcode);

		$date = date("Y-m-d H:i:s");

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

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

		if ($_SESSION["user_warehouse"] != $get_barcode_info[0]["WarehouseID"]) {
			return "คุณไม่มีสิทธิ์ Hold!";
		}

		$_warehouse = $get_barcode_info[0]["WarehouseID"]; // greentire

		$_disposal = 10; // hold

		// setup hold wh,lc,disp
		if ($get_barcode_info[0]["CuringDate"] === null) {
			$_item = $get_barcode_info[0]["GT_Code"];
			$_location = 9; // hold gt
			// $_batch = null;
			$_batch = $get_barcode_info[0]["Batch"];
		} else {
			$_item = $get_barcode_info[0]["ItemID"];
			$_location = 11; // hold x ray
			$_batch = $get_barcode_info[0]["Batch"];

			// if final receive data is exist
			if ((new FinalService)->isFinalReceiveDateExist($barcode) === false) {
				return "Barcode not Recived to Final.";
			}
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$_item,
				$_batch,
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

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?, -- hold
			WarehouseID = ?,
		    LocationID = ?,
			Status = 5, -- Hold
			UpdateBy = ?,
			UpdateDate = ?,
			CheckBuild = 1
			WHERE Barcode = ?",
			[
				$_disposal, // Hold
				$_warehouse, // WH X-ray
				$_location, // LC Trans
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
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
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$_item,
				$_batch,
				$_disposal,
				$defect,
				$_warehouse,
				$_location,
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return Database::errors();
		}

		$trans_move_log_Defect = Sqlsrv::insert(
			$conn,
			"INSERT INTO TransDefect(
				Barcode,
				DisposalID,
				LocationID,
				WarehouseID,
				CreateDate,
				DefectID,
				HoldType
				
				) VALUES (
				?, ?, ?, ?, ?,
				?, ?
			)",
			[

				$barcode_decode,
				$_disposal,
				$_location,
				$_warehouse,
				$date,
				$defect,
				$holdtype
			]
		);

		if (!$trans_move_log_Defect) {
			sqlsrv_rollback($conn);
			return "insert transDefect move in error.";
			// return Database::errors();
		}

		// Check Batch
		if ($_batch === null) {
			// Update Onhand
			// move out onhand -1
			// $move_out_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand
			// 	SET QTY -= 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch IS NULL
			// 	AND Company =?",
			// 	[
			// 		$_item,
			// 		$get_barcode_info[0]["WarehouseID"],
			// 		$get_barcode_info[0]["LocationID"],
			// 		$get_barcode_info[0]["Company"]
			// 	]
			// );
		} else {
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
			// 	AND Company =?",
			// 	[
			// 		$_item,
			// 		$get_barcode_info[0]["WarehouseID"],
			// 		$get_barcode_info[0]["LocationID"],
			// 		$get_barcode_info[0]["Batch"],
			// 		$get_barcode_info[0]["Company"]
			// 	]
			// );
		}


		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		if ($_batch === null) {

			// $move_in_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand SET QTY += 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch IS NULL
			// 	AND Company =?
			// 	IF @@ROWCOUNT = 0
			// 	INSERT INTO Onhand
			// 	VALUES (?, ?, ?, ?, ?, ?)",
			// 	[
			// 		$_item,
			// 		$_warehouse,
			// 		$_location,
			// 		$_SESSION["user_company"],
			// 		$_item,
			// 		$_warehouse,
			// 		$_location,
			// 		null,
			// 		1, // qty
			// 		$_SESSION["user_company"]
			// 	]
			// );
		} else {

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
			// 		$_item,
			// 		$_warehouse,
			// 		$_location,
			// 		$_batch,
			// 		$_SESSION["user_company"],
			// 		$_item,
			// 		$_warehouse,
			// 		$_location,
			// 		$_batch,
			// 		1, // qty
			// 		$_SESSION["user_company"]
			// 	]
			// );
		}

		// echo Database::errors();
		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		// if ($trans_move_out &&
		// 	$trans_move_in &&
		// 	$update_inventtable) {

		// 	sqlsrv_commit($conn);
		// 	return 200;
		// } else {
		// 	sqlsrv_rollback($conn);
		// 	return "Error";
		// }

		sqlsrv_commit($conn);
		return 200;
	}

	public function unhold($barcode, $auth)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();

		$date = date("Y-m-d H:i:s");

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

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

		// if ($_SESSION["user_warehouse"] != $get_barcode_info[0]["WarehouseID"]) {
		// 	return "คุณไม่มีสิทธิ์ Hold!";
		// }

		$_warehouse = $get_barcode_info[0]["WarehouseID"]; // greentire

		// setup hold wh,lc,disp
		if ($get_barcode_info[0]["CuringDate"] === null) {
			$_item = $get_barcode_info[0]["GT_Code"];
			$_location = 2; //  gt
			// $_batch = null;
			$_batch = $get_barcode_info[0]["Batch"];
			$_disposal = 26; // greentire
		} else {
			$_item = $get_barcode_info[0]["ItemID"];
			$_location = 4; // x ray
			$_batch = $get_barcode_info[0]["Batch"];
			//$_disposal = 3; // curing => duplicate @ 2/11/2559 => 15:32
			$_disposal = 4; // x-ray
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
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
				$_item,
				$_batch,
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
				$_SESSION["Shift"],
				$auth
			]
		);

		if (!$trans_move_out) {
			sqlsrv_rollback($conn);
			return "trans move out error";
		}

		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?, -- hold
			WarehouseID = ?,
		    LocationID = ?,
			Status = 1, -- Receive
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				$_disposal, // Hold
				$_warehouse, // WH X-ray
				$_location, // LC Trans
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "Update InventTable Error";
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
				$_item,
				$_batch,
				$_disposal,
				null,
				$_warehouse,
				$_location,
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
			// return Database::errors();
		}

		// Check Batch
		if ($_batch === null) {
			// Update Onhand
			// move out onhand -1
			$move_out_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand
				SET QTY -= 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch IS NULL
				AND Company =?",
				[
					$_item,
					$get_barcode_info[0]["WarehouseID"],
					$get_barcode_info[0]["LocationID"],
					$get_barcode_info[0]["Company"]
				]
			);
		} else {
			// Update Onhand
			// move out onhand -1
			$move_out_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand
				SET QTY -= 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch = ?
				AND Company =?",
				[
					$_item,
					$get_barcode_info[0]["WarehouseID"],
					$get_barcode_info[0]["LocationID"],
					$get_barcode_info[0]["Batch"],
					$get_barcode_info[0]["Company"]
				]
			);
		}


		if (!$move_out_onhand) {
			sqlsrv_rollback($conn);
			return "move out onhand error.";
		}

		if ($_batch === null) {

			$move_in_onhand = Sqlsrv::update(
				$conn,
				"UPDATE Onhand SET QTY += 1
				WHERE CodeID = ?
				AND WarehouseID = ?
				AND LocationID = ?
				AND Batch IS NULL
				AND Company =?
				IF @@ROWCOUNT = 0
				INSERT INTO Onhand
				VALUES (?, ?, ?, ?, ?, ?)",
				[
					$_item,
					$_warehouse,
					$_location,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					null,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		} else {

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
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					$_SESSION["user_company"],
					$_item,
					$_warehouse,
					$_location,
					$_batch,
					1, // qty
					$_SESSION["user_company"]
				]
			);
		}

		// echo Database::errors();
		if (!$move_in_onhand) {
			sqlsrv_rollback($conn);
			return "move in onhand error.";
		}

		if (
			$trans_move_out &&
			$trans_move_in &&
			$update_inventtable &&
			$move_out_onhand &&
			$move_in_onhand
		) {

			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "error";
		}
	}
	public function SaveLightBuff($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();

		$date = date("Y-m-d H:i:s");
		$trans_id = Utils::genTransId($barcode_decode);

		// get barcode info
		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

	
		// Update Invent table
		$update_inventtable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?, -- light buff
			UpdateBy = ?,
			UpdateDate = ?
			WHERE Barcode = ?",
			[
				28, // Hold
				$_SESSION["user_login"],
				$date,
				$barcode_decode
			]
		);

		if (!$update_inventtable) {
			sqlsrv_rollback($conn);
			return "update invent error";
		}

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
				Shift
				--AuthorizeBy
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
				3,
				null,
				4,
				3,
				-1, // qty
				1, // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
				
			]
		);

		if (!$trans_move_in) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
			// return Database::errors();
		}

		$trans_move_indlight = Sqlsrv::insert(
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
				--AuthorizeBy
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
				28,
				null,
				4,
				3,
				1, // qty
				1, // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
				
			]
		);

		if (!$trans_move_indlight) {
			sqlsrv_rollback($conn);
			return "insert trans Light Biff in error.";
			// return Database::errors();
		}


		if (
			$update_inventtable &&
			$trans_move_in &&
			$trans_move_indlight
			
		) {

			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "error";
		}
	}
}
