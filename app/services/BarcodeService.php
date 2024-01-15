<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use Wattanar\Sqlsrv;

class BarcodeService
{
	public function getBarcodeInfo($barcode)
	{
		$barcode_decode  = Security::_decode($barcode);
		$conn = Database::connect();

		return Sqlsrv::queryJson(
			$conn,
			"SELECT TOP 1 IT.ItemID, IM.NameTH, IM.Brand
			FROM InventTable IT
			LEFT JOIN ItemMaster IM ON IT.ItemID = IM.ID
			WHERE IT.Barcode = ?",
			[$barcode_decode]
		);
	}
	/**
	 * @return [json]
	 */
	public function getLastNumber()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 BP.FinishBarcode
				FROM BarcodePrinting BP
				ORDER BY BP.ID DESC"
		);

		if (isset($query[0]["FinishBarcode"])) {

			$currentYear = substr($query[0]["FinishBarcode"], 1, 2);

			if ((int) $currentYear !== (int) date('y')) {
				$newNumber = BARCODE_PREFIX . date("y") . "00000001";
			} else {
				$newNumber = (int) substr($query[0]["FinishBarcode"], 3);
				$newNumber += 1;
				$newNumber = BARCODE_PREFIX . date("y") . str_pad($newNumber, 8, "0", STR_PAD_LEFT);
			}

			echo json_encode(["code" => $newNumber]);
		} else {
			echo json_encode(["code" => BARCODE_PREFIX . date("y") . "0000001"]);
		}
	}

	public function create($start, $end, $qty)
	{
		$create_by = $_SESSION["user_login"];
		$company = $_SESSION["user_company"];
		$datetime = date("Y-m-d H:i:s");

		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			exit(Database::errors());
		}

		$query = sqlsrv_query(
			$conn,
			"INSERT INTO BarcodePrinting(
					QTY,
					StartBarcode,
					FinishBarcode,
					Status,
					CreateBy,
					CreateDate,
					Company,
					UpdateBy,
					UpdateDate) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				(int) $qty,
				$start,
				$end,
				1,
				(int) $create_by,
				$datetime,
				$company,
				(int) $create_by,
				$datetime
			]
		);

		if ($query) {
			sqlsrv_commit($conn);
		} else {
			sqlsrv_rollback($conn);
		}
	}

	public function isPrinted($start, $end)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT StartBarcode, FinishBarcode
				FROM BarcodePrinting
				WHERE StartBarcode = ?
				AND FinishBarcode = ?",
			[$start, $end]
		);
		if ($query) {
			return false;
		} else {
			return true;
		}
	}

	public function isRanged($barcode)
	{
		$conn = Database::connect();

		// $barcode_decode = Security::_decode($barcode);
		$barcode_decode = (int) substr(Security::_decode($barcode), 1);

		return Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 * FROM BarcodePrinting
				WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 2, 11)) <= ?
				AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
			[$barcode_decode, $barcode_decode]
		);
	}

	public function isExistInventTable($barcode)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Barcode
				FROM InventTable
				WHERE Barcode = ?",
			[Security::_decode($barcode)]
		);
	}

	public function isHold($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM InventTable
			WHERE Barcode = ?
			AND Status = 5",
			[$barcode_decode]
		);
	}

	public function isRepair($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM InventTable
			WHERE Barcode = ?
			AND DisposalID = 12",
			[$barcode_decode]
		);
	}

	public function isRepairCheck($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ?
			AND DisposalID = 12
			AND WarehouseID = 2",
			[$barcode_decode]
		);
	}

	public function isScrap($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM InventTable
			WHERE Barcode = ?
			AND DisposalID = 2",
			[$barcode_decode]
		);
	}

	public function isCuring($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT CuringDate FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);
	}

	public function isReceived($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM InventTable
			WHERE Barcode = ?
			AND Status = 1", // Received
			[$barcode_decode]
		);
	}

	public function getBarcodeStatus($barcode)
	{
		$conn = Database::connect();
		$status = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 ISS.Description
        FROM InventTable IT
        LEFT JOIN InventStatus ISS ON ISS.ID = IT.Status
        WHERE IT.BarcodeFoil = ?",
			[
				$barcode
			]
		);

		if ($status) {
			return $status[0]["Description"];
		} else {
			return "";
		}
	}

	public function getBarcodeInfoV2($barcode)
	{
		// $barcode_decode  = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM InventTable
	      WHERE Barcode = ?",
			[
				$barcode
			]
		);
	}

	public function getBarcodeFoilInfo($barcode)
	{
		// $barcode_decode  = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM InventTable
	      WHERE BarcodeFoil = ?",
			[
				$barcode
			]
		);
	}

	public function getBarcodeFromBarcodeFoil($barcode_foil)
	{
		$conn = Database::connect();
		$stmt = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 Barcode FROM InventTable
	      WHERE BarcodeFoil = ?",
			[
				$barcode_foil
			]
		);

		return $stmt[0]['Barcode'];
	}

	public function isBarcodeFoilNull($barcode_foil)
	{
		$conn = Database::connect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT TOP 1 BarcodeFoil FROM InventTable
      WHERE BarcodeFoil = ?",
			[
				$barcode_foil
			]
		));
	}

	public function isRangedchekQC($barcode)
	{
		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 Barcode FROM InventTable
				WHERE Barcode = ?",
			[$barcode]
		);
	}

	public function isScrapCheck($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM InventTable
			WHERE Barcode = ?
			AND DisposalID = 27",
			[$barcode_decode]
		);
	}

	public function isExitsWithoutScrapStatus($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status
			FROM InventTable
			WHERE DisposalID = 2
			AND Barcode = ?",
			[$barcode_decode]
		);
	}
}
