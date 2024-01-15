<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Services\ItemService;

class XrayService
{
	private $itemService = null;

	public function __construct()
	{
		$this->itemService = new ItemService();
	}

	public function isItemID($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		$q = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ? 
			AND ItemID IS NOT NULL",
			[$barcode_decode]
		);
		return $q;
	}

	public function issueToWH($barcode, $from = null)
	{
		try {

			$barcode_decode = $barcode;
			$conn = Database::connect();
			$date = date("Y-m-d H:i:s");
			// $w = new Utils;
			// $week = $w->getWeek($date);

			if ($from !== null) {
				$sysCompany = 'STR';
				$sysShift = 1;
				$sysLocation = 5;
				$sysUserId = 19;
			} else {
				$sysCompany = $_SESSION["user_company"];
				$sysShift = $_SESSION["Shift"];
				$sysLocation = $_SESSION["user_location"];
				$sysUserId = $_SESSION["user_login"];
			}

			if (sqlsrv_begin_transaction($conn) === false) {
				return "transaction failed!";
			}

			// $get_location = Sqlsrv::queryArray(
			// 	$conn,
			// 	"SELECT 
			// 	L.ID,
			// 	LL.WarehouseID,
			// 	L.ReceiveLocation,
			// 	L.Company,
			// 	L.DisposalID
			// 	FROM Location L
			// 	LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			// 	WHERE L.ID = ?
			// 	AND L.InUse = 1",
			// 	[$sysLocation]
			// );

			$get_barcode_info = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 * FROM InventTable WHERE Barcode = ?",
				[$barcode_decode]
			);

			if (USE_ITEMQ === true) {
				$itemFG = $this->itemService->getItemFG($barcode_decode);
				if ($itemFG === null) {
					throw new \Exception("Item FG Not found.");
				}
			} else {
				$itemFG = $get_barcode_info[0]["ItemID"];
			}

			// $itemFG = $this->itemService->getItemFG($barcode_decode);
			// if ($itemFG === null) {
			// 	throw new \Exception("Item FG Not found.");
			// }

			$trans_id = Utils::genTransId($barcode_decode);

			$move_out = sqlsrv_query(
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
					4, // final
					null,
					2, // final finishing
					4, // final inspection
					-1, // qty
					$get_barcode_info[0]["Unit"], // unit id
					2, // docs type
					$sysCompany,
					$sysUserId,
					$date,
					$sysShift
				]
			);

			if (!$move_out) {
				sqlsrv_rollback($conn);
				return "insert trans move out error.";
			}

			// Update Invent table
			$update_inventtable = sqlsrv_query(
				$conn,
				"UPDATE InventTable 
					SET WarehouseTransReceiveDate = ?,
					DisposalID = ?, -- X-ray
					WarehouseID = ?,
					LocationID = ?,
					[Status] = 1, -- Receive
					UpdateBy = ?,
					Batch = ?,
					UpdateDate = ?,
					ItemID = ?,
					WMSUpdate = 1
					WHERE Barcode = ?",
				[
					$date,
					5, // transit
					3, // WH X-ray
					6, // LC Trans
					$sysUserId,
					$get_barcode_info[0]["Batch"],
					$date,
					$itemFG,
					$barcode_decode
				]
			);

			if (!$update_inventtable) {
				sqlsrv_rollback($conn);
				return "Update InventTable Error";
			}

			$trans_id = Utils::genTransId($barcode_decode);

			$move_in = sqlsrv_query(
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
					$itemFG,
					$get_barcode_info[0]["Batch"],
					5, // transit
					null,
					3, // fg
					6, // transit
					1, // qty
					$get_barcode_info[0]["Unit"], // unit id
					1, // docs type
					$sysCompany,
					$sysUserId,
					$date,
					$sysShift
				]
			);

			if (!$move_in) {
				sqlsrv_rollback($conn);
				return "insert trans move in error.";
			}

			// Update Onhand

			// move out onhand -1
			// $move_out_onhand = sqlsrv_query(
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
			// 		2, // final finishing
			// 		4, // final inspect
			// 		$get_barcode_info[0]["Batch"],
			// 		$get_barcode_info[0]["Company"]
			// 	]
			// );

			// if (!$move_out_onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "move out onhand error.";
			// }

			// $move_in_onhand = sqlsrv_query(
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
			// 		3, // fg
			// 		6, // transit
			// 		$get_barcode_info[0]["Batch"],
			// 		$sysCompany,
			// 		$get_barcode_info[0]["ItemID"],
			// 		3, // fg
			// 		6, // transiit
			// 		$get_barcode_info[0]["Batch"],
			// 		1, // qty
			// 		$sysCompany
			// 	]
			// );

			// if (!$move_in_onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "move in onhand error.";
			// }

			// if (
			// 	$update_inventtable &&
			// 	$move_out &&
			// 	$move_in
			// ) {

			// 	sqlsrv_commit($conn);
			// 	return 200;
			// } else {
			// 	sqlsrv_rollback($conn);
			// 	return 404;
			// }

			\sqlsrv_commit($conn);
			return 200;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function insertlinewhsend($barcode, $_idtrucktable)
	{
		try {

			$barcode_decode = $barcode;
			$conn = Database::connect();
			$date = date("Y-m-d H:i:s");


			if (sqlsrv_begin_transaction($conn) === false) {
				return "transaction failed!";
			}

			$checkbarcoce = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 * FROM InventTable WHERE Barcode = ? AND ItemID LIKE '%Q%' ",
				[$barcode]
			);

			if ($checkbarcoce[0]["ItemID"] != "") {

				return 404;
			}

			$getJornal = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 * FROM SendToWHTable WHERE Id = ?",
				[$_idtrucktable]
			);

			$jornalId = $getJornal[0]["JournalID"];

			$insertLinsendwh = sqlsrv_query(
				$conn,
				"INSERT INTO SendToWHLine(
					JournalID,
					Barcode,
					ItemID,
					Batch,
					CreateDate
				) SELECT 
				'$jornalId',
				'$barcode',
				IT.ItemID ,
				IT.Batch,
				getdate()
				FROM InventTable IT  where Barcode = ?",
				[
					$barcode
				]
			);

			if (!$insertLinsendwh) {
				sqlsrv_rollback($conn);
				return "insert SendToWHLine error.";
			}



			\sqlsrv_commit($conn);
			return 200;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function insertbarcodesendtemp($barcode)
	{
		try {

			$barcode_decode = $barcode;
			$conn = Database::connect();
			$date = date("Y/m/d H:i:s");


			if (sqlsrv_begin_transaction($conn) === false) {
				return "transaction failed!";
			}

			$insertbarcodesendtemp = sqlsrv_query(
				$conn,
				"INSERT INTO WMS_BarcodeSendTemp(
					[STORERKEY]
					,[RECEIPTKEY]
					,[RECEIPTLINENUMBER]
					,[FORCEID]
					,[SERIALNUMBER]
					,[SKU]
					,[DESCRIPTION]
					,[LANENO]
					,[LPN]
					,[BATCHNO]
					,[MANDATE]
					,[EXPDATE]
					,[PROCTYPE]
					,[UOM]
					,[QTY]
					,[CREATEDATE]
					,[UPDATEDATE]
					,[CREATEBY]
					,[UPDATEBY]
					,[INTSTS]
					,[ERRMSG]
					)SELECT
					'STR',
					'',
					'',
					'0',
					IT.Barcode,
					IT.ItemID,
					IM.NameTH,
					'3LANE01',
					'',
					LEFT(IT.Batch,7),
					dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year,convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4 - datepart(dw, dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year, convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4) + 2,
					dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year,convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4 - datepart(dw, dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year, convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4) + 2 +180,
					0,
					'PCS',
					1,
					convert(varchar, IT.UpdateDate, 121),
					?,
					-- IT.updateby,
					UM.Username,
					'',
					'0',
					''
					FROM InventTable IT 
					INNER Join UserMaster UM ON UM.ID = IT.UpdateBy
					INNER Join Itemmaster IM ON IM.ID = IT.ItemID
					where IT.Barcode = ?",
				[
					$date,
					$barcode
				]
			);

			if (!$insertbarcodesendtemp) {
				sqlsrv_rollback($conn);
				return "insert BarcodeSendTemp error.";
			}



			sqlsrv_commit($conn);
			return 200;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}
