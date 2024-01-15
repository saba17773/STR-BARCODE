<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class TemplateService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT * FROM TemplateRegister ORDER BY ID DESC"
			);
		return $query;
	}

	public function getLastRec($seq_format)
	{
		$conn = Database::connect();

		$isExist = Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM TemplateRegister
				WHERE SUBSTRING(StartBarcode, 1, 5) = ?",
				[$seq_format]
			);

		if ($isExist === false) {
			return "0001";
		} else {
			
			$get_lastest = Sqlsrv::queryArray(
					$conn,
					"SELECT TOP 1 FinishBarcode
					FROM TemplateRegister
					WHERE SUBSTRING(StartBarcode, 1, 5) = ?
					ORDER BY ID DESC",
					[$seq_format]
				);

			return str_pad((int)substr($get_lastest[0]["FinishBarcode"], -4)+1, 4, "0", STR_PAD_LEFT);
		}
	}

	public function create($from, $to, $qty) 
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$isSerialExist = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM TemplateRegister
			WHERE StartBarcode = ?",
			[$from]
		);

		if ($isSerialExist === true) {
			sqlsrv_rollback($conn);
			return false;
		}
		
		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO TemplateRegister(
					QTY, StartBarcode,
					FinishBarcode, Status,
					CreateBy, CreateDate,
					Company, UpdateBy,
					UpdateDate	
				) VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					$qty,
					$from,
					$to,
					1,
					$_SESSION["user_login"],
					$date,
					$_SESSION["user_company"],
					$_SESSION["user_login"],
					$date
				]
			);

		if ($query) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
		
	}

	public function isExistsInInventTable($serialNo)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT TemplateSerialNo FROM InventTable 
			WHERE TemplateSerialNo = ?",
			[
				$serialNo
			]
		);
	}

	public function isCuringCodeNull($barcode)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT TemplateSerialNo FROM InventTable 
			WHERE Barcode = ? 
			AND CuringCode IS NULL ",
			[
				$barcode
			]
		);
	}

	public function inRanged($splited_serial, $no_serial)
	{
		$conn = Database::connect();

		$isExists = sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT A.srno FROM (
				SELECT SUBSTRING(StartBarcode, 1, 5) [srno] 
				FROM TemplateRegister
				) A WHERE A.srno = ?",
				[$splited_serial]
		));

		if ($isExists === true) {
			$serial = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 FinishBarcode FROM TemplateRegister 
				WHERE SUBSTRING(FinishBarcode, 1, 5) = ?
				ORDER BY ID DESC",
				[$splited_serial]
			);

			$no_serial = (int)$no_serial;
			if ($no_serial <= (int)substr($serial[0]['FinishBarcode'], 5, 4)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function updateSerialNo($barcode, $new_serial)
	{
		
		$conn = Database::connect();
		$update = sqlsrv_query(
			$conn,
			"UPDATE InventTable SET TemplateSerialNo = ?
			WHERE Barcode = ?",
			[
				strtoupper($new_serial),
				$barcode
			]
		);

		if ($update) {
			return true;
		} else {
			return false;
		}
	}
}