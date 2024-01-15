<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class PressService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT PM.ID,PM.Description,PM.BOI,BT.ID AS BOIName , PM.CuringCodeL, PM.CuringCodeR
			FROM PressMaster PM
			LEFT JOIN BOITable BT ON BT.ID = PM.BOI"
		);
		return $query;
	}

	public function create($id, $desc, $boi)
	{
		if (self::checkExist($id) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO PressMaster(ID, Description, Company, BOI) VALUES (?, ?, ?, ?)",
			[$id, $desc, $_SESSION["user_company"], $boi]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $desc, $boi)
	{

		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster
			SET	Description = ?,
					BOI =?
	        WHERE ID = ?",
			[
				$desc,
				$boi,
				$id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	public function checkExist($id)
	{
		$id = trim($id);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM PressMaster
			WHERE ID = ?",
			[$id]
		);
	}

	public function delete($id)
	{
		$conn = Database::connect();
		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM PressMaster PM
			LEFT JOIN InventTable IT ON PM.ID = IT.PressNo
			WHERE PM.ID = ?
			AND IT.PressNo IS NULL",
			[$id]
		);

		return $q;
	}

	//nueng
	public function loadid()
	{
		$conn = Database::connectDeviceWMSSTR();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * 
			From Openquery(
			  WMS_STR_LIVE,
			'SELECT DISTINCT LOADID
			From ORDERS 
			WHERE LOADID != '' ''
			ORDER BY LOADID
			')"
		);
		return $query;
	}
	public function externorderkey()
	{
		$dataload = $_GET["dataload"];
		// $loadid = implode(',',$dataload);

		$conn = Database::connectDeviceWMSSTR();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * 
			From Openquery(
			  WMS_STR_LIVE,
			'SELECT EXTERNORDERKEY
			From ORDERS 
			WHERE LOADID = ''$dataload''
			ORDER BY EXTERNORDERKEY
			')"
		);
		return $query;
	}

	//j modify
	public function allBDF()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT
					BDF
					--,'01-12'[No]
					,CASE WHEN BDF = 'N' THEN '01-16' ELSE 
					CASE WHEN BDF = 'P' THEN '01-20' ELSE 
					CASE WHEN BDF = 'J' THEN '01-13' ELSE 
					CASE WHEN BDF = 'L' THEN '01-21' ELSE
					'01-12' END END END END AS [No] 
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P
			WHERE LEFT(P.ID, 1) IN ('B','D','F','H','J','L','N','P')
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allBDFA()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT
					BDF
					--,'01-12'[No]
					--,CONVERT (NVARCHAR(100),'01')+'-'+max(RIGHT(ID, 2)) AS [No]
					,CASE WHEN BDF = 'M' THEN '01-16' ELSE 
					CASE WHEN BDF = 'O' THEN '01-20' ELSE 
					CASE WHEN BDF = 'I' THEN '01-13' ELSE 
					'01-12' END END END AS [No] 
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P
			WHERE LEFT(P.ID, 1) IN ('A','C','E','G','I','K','M','O')
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allABCDEF()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT
					BDF
					,'01-12'[No]
			FROM(
			SELECT	P.ID
					,P.Description
					,LEFT(P.ID, 1)[BDF]
			FROM PressMaster P
			/*WHERE LEFT(P.ID, 1) IN ('A','C','E')*/
			)Z
			GROUP BY
			Z.BDF"
		);
		return $query;
	}
	public function allday()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster
			WHERE TimeType=1"
		);
		return $query;
	}
	public function allnight()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster
			WHERE TimeType=2"
		);
		return $query;
	}
	//time new
	public function alldaynew()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster
			WHERE TimeType=3"
		);
		return $query;
	}
	public function allnightnew()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT *
			FROM TimeMaster
			WHERE TimeType=4"
		);
		return $query;
	}
	//Nan
	public function Line_TBR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT CASE Line WHEN 'V' THEN 'VMI' 
				ELSE Line END AS Line,
			CASE WHEN C > 1 THEN  '01-'+ RIGHT('00'+ convert(varchar, C), 2) 
				ELSE RIGHT('00'+ convert(varchar, C), 2) 
				END AS No
			FROM (
				SELECT	ID
				,LEFT(ID, 1)[Line]
				,COUNT(LEFT(ID, 1)) OVER (PARTITION BY LEFT(ID, 1)) C
				FROM BuildingMaster
				WHERE Type = 'TBR'
			)T1
			WHERE Line NOT IN ('S','Z') 
			GROUP BY LINE,C		
			"
		);
		return $query;
	}

	public function Line_PCR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT CASE Line WHEN 'V' THEN 'VMI' 
				ELSE Line END AS Line,
			CASE WHEN C > 1 THEN  '01-'+ RIGHT('00'+ convert(varchar, C), 2) 
				ELSE RIGHT('00'+ convert(varchar, C), 2) 
				END AS No
			FROM (
				SELECT	ID
				,LEFT(ID, 1)[Line]
				,COUNT(LEFT(ID, 1)) OVER (PARTITION BY LEFT(ID, 1)) C
				FROM BuildingMaster
				WHERE Type = 'PCR'
			)T1
			WHERE Line NOT IN ('S','Z') 
			GROUP BY LINE,C		
			"
		);
		return $query;
	}

	public function Building_TBR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID AS Machine
			FROM BuildingMaster
			WHERE Type = 'TBR'	
			"
		);
		return $query;
	}

	public function Building_PCR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID AS Machine
			FROM BuildingMaster
			WHERE Type = 'PCR'		
			"
		);
		return $query;
	}

	public function allCurecode()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID,Name FROM CureCodeMaster  group by ID,Name"
		);
		return $query;
	}

	public function updatecuringpress($id, $selectMenuCuringL, $selectMenuCuringR)
	{

		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE PressMaster
			SET	CuringCodeL = ?,
				CuringCodeR =?
	        WHERE ID = ?",
			[
				$selectMenuCuringL,
				$selectMenuCuringR,
				$id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}
}
