<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Services\GreentireService;

class AdjustService
{
	public function __construct()
	{
		$this->db = new Database;
		$this->security = new Security;
		$this->utils = new Utils;
		$this->greentire = new GreentireService;
	}

	public function store($code, $date, $barcode, $boi_cod, $boi)
	{
		$current_date = Date("Y-m-d H:i:s");
		$conn = $this->db->connect();
		$batch = $this->utils->getWeek($date) . $boi_cod;

		if (sqlsrv_begin_transaction($conn) === false) {
			return "Can't connect database.";
		}

		if ($_SESSION['user_warehouse'] === 1) { // greentire

			$check_code = Sqlsrv::hasRows(
				$conn,
				"SELECT GreentireID FROM CureCodeMaster
				WHERE GreentireID = ?",
				[$code]
			);

			if ($check_code === false) {
				sqlsrv_rollback($conn);
				return "Greentire Code ไม่มีในระบบ";
			}

			// Insert Invent Table
			$inventtable = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTable(
					Barcode,
					DateBuild,
					Batch,
					GT_Code,
					QTY,
					Unit,
					DisposalID,
					WarehouseID,
					LocationID,
					[Status],
					Company,
					UpdateBy,
					UpdateDate,
					CreateBy,
					CreateDate
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?
				)",
				[
					$this->security->_decode($barcode),
					$date,
					$batch,
					$code,
					1, // qty
					1, // unit
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // status
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION['user_login'],
					$date
				]
			);

			if (!$inventtable) {
				sqlsrv_rollback($conn);
				return "insert invent table error";
			}

			$transId = $this->utils->genTransId($this->security->_decode($barcode));

			// Insert invent Trans
			$insert_inventtrans = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTrans(
					TransID ,Barcode
					,CodeID ,Batch
					,DisposalID
					,WarehouseID ,LocationID
					,QTY ,UnitID
					,DocumentTypeID ,Company
					,CreateBy ,CreateDate,
					Shift
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$transId . 1,
					$this->security->_decode($barcode),
					$code,
					$batch, // batch
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // qty
					1, // unit
					1, // document id
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$insert_inventtrans) {
				sqlsrv_rollback($conn);
				return "insert invent trans error.";
			}

			$insert_curingboi = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(
					 Barcode
					,BOI 
				)VALUES(
					?, ?
				)",
				[

					$this->security->_decode($barcode),
					$boi
				]
			);

			if (!$insert_curingboi) {
				sqlsrv_rollback($conn);
				return "insert curing boi error.";
			}

			// Update Onhand
			// $onhand = Sqlsrv::insert(
			// 	$conn,
			// 	"UPDATE Onhand SET QTY += 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND	LocationID = ?
			// 	AND	Company = ?
			// 	AND Batch = ?
			// 	IF @@ROWCOUNT = 0
			// 	INSERT INTO Onhand(
			// 		CodeID,
			// 		WarehouseID,
			// 		LocationID,
			// 		Company,
			// 		QTY,
			// 		Batch
			// 	) VALUES (?, ?, ?, ?, ?, ?)",
			// 	[
			// 		$code,
			// 		$_SESSION['user_warehouse'], // wh
			// 		$_SESSION['user_location'], // lc
			// 		$_SESSION['user_company'],
			// 		$this->utils->getWeek($date),

			// 		$code,
			// 		$_SESSION['user_warehouse'], // wh
			// 		$_SESSION['user_location'], // lc
			// 		$_SESSION['user_company'],
			// 		1,
			// 		$this->utils->getWeek($date)
			// 	]
			// );

			// if (!$onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "update onhand error.";
			// }
		} else if ($_SESSION['user_warehouse'] === 2) { // final

			$check_curing_code = Sqlsrv::hasRows(
				$conn,
				"SELECT ID FROM CureCodeMaster
				WHERE ID = ?",
				[$code]
			);

			if ($check_curing_code === false) {
				sqlsrv_rollback($conn);
				return "Curing Code ไม่มีในระบบ";
			}

			$getCureCodeInfo = Sqlsrv::queryArray(
				$conn,
				"SELECT ID, ItemQ AS ItemID, GreentireID FROM CureCodeMaster
				WHERE ID = ?",
				[$code]
			);

			if (!$getCureCodeInfo) {
				sqlsrv_rollback($conn);
				return "fetch curing code error.";
			}
			$check_curing_item = Sqlsrv::hasRows(
				$conn,
				"SELECT ID FROM CureCodeMaster
				WHERE ID = ? AND ItemQ IS NOT NULL",
				[$code]
			);

			if ($check_curing_item === false) {
				sqlsrv_rollback($conn);
				return "ItemQ ไม่มีในระบบ";
			}


			// Insert Invent Table
			$inventtable = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTable(
					Barcode,
					CuringDate,
					CuringCode,
					FinalReceiveDate,
					Batch,
					ItemID,
					GT_Code,
					QTY,
					Unit,
					DisposalID,
					WarehouseID,
					LocationID,
					Status,
					Company,
					UpdateBy,
					UpdateDate,
					CreateBy,
					CreateDate
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?
				)",
				[
					$this->security->_decode($barcode),
					$date,
					$getCureCodeInfo[0]['ID'],
					$current_date,
					$batch,
					$getCureCodeInfo[0]['ItemID'],
					$getCureCodeInfo[0]['GreentireID'],
					1, // qty
					1, // unit
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // status
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION['user_login'],
					$date
				]
			);

			if (!$inventtable) {
				sqlsrv_rollback($conn);
				return "insert invent table error.";
			}

			$transId = $this->utils->genTransId($this->security->_decode($barcode));

			// Insert invent Trans
			$insert_inventtrans = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTrans(
					TransID ,Barcode
					,CodeID ,Batch
					,DisposalID
					,WarehouseID ,LocationID
					,QTY ,UnitID
					,DocumentTypeID ,Company
					,CreateBy ,CreateDate,
					Shift
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$transId . 1,
					$this->security->_decode($barcode),
					$getCureCodeInfo[0]['ItemID'],
					$batch, // batch
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // qty
					1, // unit
					1, // document id
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$insert_inventtrans) {
				sqlsrv_rollback($conn);
				return "insert invent trans error.";
			}

			$insert_curingboi = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(
					 Barcode
					,BOI 
				)VALUES(
					?, ?
				)",
				[

					$this->security->_decode($barcode),
					$boi
				]
			);

			if (!$insert_curingboi) {
				sqlsrv_rollback($conn);
				return "insert curing boi error.";
			}

			// Update Onhand
			// $onhand = Sqlsrv::insert(
			// 	$conn,
			// 	"UPDATE Onhand SET QTY += 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND	LocationID = ?
			// 	AND	Company = ?
			// 	AND Batch = ?
			// 	IF @@ROWCOUNT = 0
			// 	INSERT INTO Onhand(
			// 		CodeID,
			// 		WarehouseID,
			// 		LocationID,
			// 		Company,
			// 		QTY,
			// 		Batch
			// 	) VALUES (?, ?, ?, ?, ?, ?)",
			// 	[
			// 		$getCureCodeInfo[0]["ItemID"],
			// 		$_SESSION['user_warehouse'], // wh
			// 		$_SESSION['user_location'], // lc
			// 		$_SESSION['user_company'],
			// 		$this->utils->getWeek($date),

			// 		$getCureCodeInfo[0]["ItemID"],
			// 		$_SESSION['user_warehouse'], // wh
			// 		$_SESSION['user_location'], // lc
			// 		$_SESSION['user_company'],
			// 		1,
			// 		$this->utils->getWeek($date)
			// 	]
			// );

			// if (!$onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "update onhand error.";
			// }
		} else if ($_SESSION['user_warehouse'] === 3) { // FG

			$check_item_code = Sqlsrv::hasRows(
				$conn,
				"SELECT ItemID FROM CureCodeMaster
				WHERE ItemID = ?",
				[$code]
			);

			if ($check_item_code === false) {
				sqlsrv_rollback($conn);
				return "Item Code ไม่มีในระบบ";
			}

			$getCureCodeInfo = Sqlsrv::queryArray(
				$conn,
				"SELECT ID, GreentireID, ItemID FROM CureCodeMaster
				WHERE ItemID = ?",
				[$code]
			);

			if (!$getCureCodeInfo) {
				sqlsrv_rollback($conn);
				return "fetch curing code error.";
			}

			// Insert Invent Table
			$inventtable = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTable(
					Barcode,
					CuringDate,
					CuringCode,
					WarehouseReceiveDate,
					-- WarehouseTransReceiveDate,
					Batch,
					ItemID,
					GT_Code,
					QTY,
					Unit,
					DisposalID,
					WarehouseID,
					LocationID,
					Status,
					Company,
					UpdateBy,
					UpdateDate,
					CreateBy,
					CreateDate
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?
				)",
				[
					$barcode,
					$date,
					$getCureCodeInfo[0]['ID'],
					$current_date,
					// $current_date,
					$batch,
					$getCureCodeInfo[0]['ItemID'],
					$getCureCodeInfo[0]['GreentireID'],
					1, // qty
					1, // unit
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // status
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION['user_login'],
					$date
				]
			);

			if (!$inventtable) {
				sqlsrv_rollback($conn);
				return "insert invent table error.";
			}

			$transId = $this->utils->genTransId($this->security->_decode($barcode));

			// Insert invent Trans
			$insert_inventtrans = Sqlsrv::insert(
				$conn,
				"INSERT INTO InventTrans(
					TransID ,Barcode
					,CodeID ,Batch
					,DisposalID
					,WarehouseID ,LocationID
					,QTY ,UnitID
					,DocumentTypeID ,Company
					,CreateBy ,CreateDate,
					Shift
				)VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$transId . 1,
					$this->security->_decode($barcode),
					$getCureCodeInfo[0]['ItemID'],
					$batch, // batch
					16, // disposal => adjust
					$_SESSION['user_warehouse'], // wh
					$_SESSION['user_location'], // lc
					1, // qty
					1, // unit
					1, // document id
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$insert_inventtrans) {
				sqlsrv_rollback($conn);
				return "insert invent trans error.";
			}

			$insert_curingboi = Sqlsrv::insert(
				$conn,
				"INSERT INTO CuringBOI(
					 Barcode
					,BOI 
				)VALUES(
					?, ?
				)",
				[

					$this->security->_decode($barcode),
					$boi
				]
			);

			if (!$insert_curingboi) {
				sqlsrv_rollback($conn);
				return "insert curing boi error.";
			}

			// Update Onhand
			// 	$onhand = Sqlsrv::insert(
			// 		$conn,
			// 		"UPDATE Onhand SET QTY += 1
			// 		WHERE CodeID = ?
			// 		AND WarehouseID = ?
			// 		AND	LocationID = ?
			// 		AND	Company = ?
			// 		AND Batch = ?
			// 		IF @@ROWCOUNT = 0
			// 		INSERT INTO Onhand(
			// 			CodeID,
			// 			WarehouseID,
			// 			LocationID,
			// 			Company,
			// 			QTY,
			// 			Batch
			// 		) VALUES (?, ?, ?, ?, ?, ?)",
			// 		[
			// 			$code,
			// 			$_SESSION['user_warehouse'], // wh
			// 			$_SESSION['user_location'], // lc
			// 			$_SESSION['user_company'],
			// 			$this->utils->getWeek($date),

			// 			$code,
			// 			$_SESSION['user_warehouse'], // wh
			// 			$_SESSION['user_location'], // lc
			// 			$_SESSION['user_company'],
			// 			1,
			// 			$this->utils->getWeek($date)
			// 		]
			// 	);

			// 	if (!$onhand) {
			// 		sqlsrv_rollback($conn);
			// 		return "update onhand error.";
			// 	}
			// } else {

			// sqlsrv_rollback($conn);
			// return "user warehouse incorrect!";
		}

		sqlsrv_commit($conn);
		return 200;
	}
}
