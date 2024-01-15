<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class DisposalService
{
	
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM DisposalToUseIn"
		);
	}

	public function create($desc, $disposal_action, $company)
	{
		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO DisposalToUseIn (
				DisposalDesc,
				CreateBy,
				CreateDate,
				Company,
				UpdateBy,
				UpdateDate
			) VALUES (?, ?, ?, ?, ?, ?)",
			[
				$desc,
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date
			]
		);

		if ($query) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

	public function update($id, $desc, $disposal_action, $company)
	{
		$set_disposal = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
		$set_company = [0, 0, 0, 0, 0];

		foreach ($disposal_action as $value) {
			$set_disposal[$value-1] = 1;		
		}

		foreach ($company as $value) {
			$set_company[$value-1] = 1;
		}

		$date = date("Y-m-d H:i:s");
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		
		$update = Sqlsrv::update(
			$conn,
			"UPDATE DisposalToUseIn 
			SET DisposalDesc = ?,
			GT_Build = ?,
			GT_Inspection = ?,
			GT_Scrap = ?,
			Curing = ?,
			Xray_Inspection = ?,
			Xray = ?,
			Xray_Scrap = ?,
			Xray_Qtech = ?,
			FG = ?,
			Loading = ?,
			DSL = ?,
			DRB = ?,
			DSI = ?,
			SVO = ?,
			STR = ?,
			Company = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE ID = ?",
			[
				$desc,
				$set_disposal[0],
				$set_disposal[1],
				$set_disposal[2],
				$set_disposal[3],
				$set_disposal[4],
				$set_disposal[5],
				$set_disposal[6],
				$set_disposal[7],
				$set_disposal[8],
				$set_disposal[9],
				$set_company[0],
				$set_company[1],
				$set_company[2],
				$set_company[3],
				$set_company[4],
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$id
			]
		);

		if ($update) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
	}

	public function checkExist($desc)
	{
		$desc = trim($desc);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM DisposalToUseIn
			WHERE DisposalDesc = ?",
			[$desc]
		);
	}
}