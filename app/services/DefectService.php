<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class DefectService
{
	public function all()
	{
		$conn = Database::connect();

		if ($_SESSION["user_location"] === 2) {

			$sql = "SELECT * FROM Defect WHERE GT_Inspection = 1 ORDER BY Description ASC";
		} else if ($_SESSION["user_location"] === 4) {

			$sql = "SELECT * FROM Defect WHERE Xray_Inspection = 1 ORDER BY Description ASC";
		} else if ($_SESSION["user_location"] === 3) {

			$sql = "SELECT * FROM Defect WHERE Curing_Inspection = 1 ORDER BY Description ASC";
		} else {
			$sql = "SELECT * FROM Defect ORDER BY Description ASC";
			// $sql = "SELECT * FROM Defect WHERE 0 = 1";
		}

		$query = Sqlsrv::queryJson(
			$conn,
			$sql
		);

		return $query;
	}

	public function reverse()
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryJson(
			$conn,
			'SELECT * FROM Defect WHERE Curing_Inspection = 1'
		);

		return $query;
	}

	public function masterAll()
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM Defect "
		);

		return $query;
	}

	public function create($description, $description_th, $scrap, $company)
	{
		if (self::checkExist($description) === true) {
			return false;
		}

		$set_company = [0, 0, 0, 0, 0];
		$set_scrap = [0, 0];

		if (count($scrap) > 0) {
			foreach ($scrap as $value) {
				$set_scrap[$value - 1] = 1;
			}

			foreach ($company as $value) {
				$set_company[$value - 1] = 1;
			}
		}

		$date = date("Y-m-d H:i:s");

		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO Defect(
					Description,
					CategoryCode,
					GT_Inspection,
			        Xray_Inspection,
			        QTech,
			        DSL,
			        DRB,
			        DSI,
			        SVO,
			        STR,
			        CreateBy,
			        CreateDate,
			        Company,
			        UpdateBy,
			        UpdateDate
				) VALUES (
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?
				)",
			[
				$description,
				$description_th,
				$set_scrap[0],
				$set_scrap[1],
				0,
				$set_company[0],
				$set_company[1],
				$set_company[2],
				$set_company[3],
				$set_company[4],
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function updateV2($id, $gt, $fn, $df)
	{
		$date = date("Y-m-d H:i:s");

		if ($gt === 'true') {
			$gt = 1;
		} else {
			$gt = 0;
		}

		if ($fn === 'true') {
			$fn = 1;
		} else {
			$fn = 0;
		}

		if ($df === 'true') {
			$df = 1;
		} else {
			$df = 0;
		}


		$conn = Database::connect();

		$query = Sqlsrv::update(
			$conn,
			"UPDATE Defect
				SET GT_Inspection = ?,
        Xray_Inspection = ?,
        Curing_Inspection = ?,
        QTech = ?,
        UpdateBy = ?,
        UpdateDate =?
        WHERE ID = ?",
			[
				$gt,
				$fn,
				$df,
				0,
				$_SESSION["user_login"],
				$date,
				$id
			]
		);

		if ($query) {
			return 200;
		} else {
			return 404;
		}
	}

	public function update($id, $scrap)
	{
		$set_scrap = [0, 0];

		if (count($scrap) > 0) {
			foreach ($scrap as $value) {
				$set_scrap[$value - 1] = 1;
			}
		}

		$date = date("Y-m-d H:i:s");

		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE Defect
				SET
				GT_Inspection = ?,
		        Xray_Inspection = ?,
		        QTech = ?,
		        UpdateBy = ?,
		        UpdateDate =?
		        WHERE ID = ?",
			[
				$set_scrap[0],
				$set_scrap[1],
				0,
				$_SESSION["user_login"],
				$date,
				$id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function syncDefect()
	{
		$conn = Database::connect();
		$query = sqlsrv_query(
			$conn,
			"INSERT INTO Defect
			SELECT
			DSG_SCRAPID,
			DSG_SCRAPCATEGORYCODE,
			DSG_DESCRIPTION,
			0 [GT], -- gt
			0 [CURING], -- curing
			0 [XRAY], -- xray
			0 [QTECH], -- q tech
			0 [DSL], -- dsl
			0 [DRB], -- drb
			0 [DSI], -- dsi
			0 [SVO], -- svo
			1 [STR], -- str
			1 [CREATEBY], -- create by
			GETDATE() [CREATEDATE], -- craete date
			'STR' [COMPANY], -- company
			1 [UPDATEBY], -- update by
			GETDATE() [UPDATEDATE] -- update date
			from [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[DSG_ScrapTable]
			where DSG_STR= 1
			AND DSG_SCRAPID NOT IN (
				SELECT ID FROM Defect
			)"
		);

		if ($query) {

			$conn = Database::connect();
			$query = Sqlsrv::update(
				$conn,
				"UPDATE
				X
				SET
					X.Description = AX.DSG_DESCRIPTION,
					X.CategoryCode = AX.DSG_SCRAPCATEGORYCODE
				FROM Defect X
				LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[DSG_ScrapTable] AX
				ON X.ID = AX.DSG_SCRAPID AND AX.DSG_STR= 1
				WHERE X.Description <> AX.DSG_DESCRIPTION OR
					X.CategoryCode <> AX.DSG_SCRAPCATEGORYCODE"
			);

			return true;
		} else {
			return false;
		}
	}
}
