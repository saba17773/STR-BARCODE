<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use Wattanar\SqlsrvHelper;
use App\Common\Response;

class CureTireService
{
	private $sqlHelper = null;
	private $response = null;

	public function __construct()
	{
		$this->sqlHelper = new SqlsrvHelper();
		$this->response = new Response();
	}

	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM CureCodeMaster");
	}

	public function create($id_name, $des_name, $item_name, $gt_name)
	{
		try {
			// code
			$conn = Database::connect();

			if (self::checkWhExist($des_name, $item_name, $gt_name, $id_name) === false) {

				// get item q
				$itmeq_list = $this->sqlHelper->getRows(sqlsrv_query(
					$conn,
					"SELECT I.ITEMID AS ITEMQ
						FROM [frey\live].[DSL_AX40_SP1_LIVE].dbo.[INVENTTABLE] I
						WHERE I.ITEMID = REPLACE(?, 'I', 'Q')
						AND I.DATAAREAID = 'dv'",
					[
						$item_name
					]
				));

				if (count($itmeq_list) > 0) {
					$itemq = $itmeq_list[0]["ITEMQ"];
				} else {
					$itemq = null;
				}

				$query = Sqlsrv::insert(
					$conn,
					"INSERT INTO CureCodeMaster(
						ID,[Name],ItemID,GreentireID,Company, ItemQ
					) VALUES (
						?,?,?,?,?, ?
					)",
					[$id_name, $des_name, $item_name, $gt_name, $_SESSION["user_company"], $itemq]
				);
				if (!$query) {
					return $this->response->array(false, "Create failed.");
				}
				return $this->response->array(true, "Create success.");
			} else {
				return $this->response->array(false, "Data incorrect.");
			}
		} catch (\Exception $e) {
			return $this->response->array(false, $e->getMessage());
		}
	}

	public function update($des_name, $item_name, $gt_name, $id_name)
	{
		try {
			// code
			$id_name = trim($id_name);

			$conn = Database::connect();

			$itmeq_list = $this->sqlHelper->getRows(sqlsrv_query(
				$conn,
				"SELECT I.ITEMID AS ITEMQ
					FROM [frey\live].[DSL_AX40_SP1_LIVE].dbo.[INVENTTABLE] I
					WHERE I.ITEMID = REPLACE(?, 'I', 'Q')
					AND I.DATAAREAID = 'dv'",
				[
					$item_name
				]
			));

			if (count($itmeq_list) > 0) {
				$itemq = $itmeq_list[0]["ITEMQ"];
			} else {
				$itemq = null;
			}

			$query = Sqlsrv::update(
				$conn,
				"UPDATE CureCodeMaster 
			     SET  Name=?,
				      ItemID=?,
				      GreentireID=?,
				      Company=?,
							ItemQ = ?
				WHERE ID =?",
				[$des_name, $item_name, $gt_name, $_SESSION["user_company"], $itemq, $id_name]
			);

			if (!$query) {
				return $this->response->array(false, "Update failed.");
			}

			return $this->response->array(true, "Update success.");
		} catch (\Exception $e) {
			return $this->response->array(false, $e->getMessage());
		}
	}

	public function checkWhExist($des_name, $item_name, $gt_name, $id_name)
	{
		$id_name = trim($id_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM CureCodeMaster 
				WHERE Name = ? AND ItemID =? AND GreentireID =? AND ID =?",
			[$des_name, $item_name, $gt_name, $id_name]
		);
	}

	public function isSetDontCheckSerial($curecode)
	{
		$conn = Database::connect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT CCM.ItemID FROM CureCodeMaster CCM
			LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
			WHERE CCM.ID = ? AND IM.CheckSerial = 1",
			[
				$curecode
			]
		));
	}

	public function changbath()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			[Type],
			CASE WHEN [Type] = 1 THEN 'Greentire' ELSE 'Cure' END [TypeName],
			CASE WHEN [Date] = 1 THEN 'Sunday' 
			 WHEN [Date] = 2 THEN 'Monday' 
			 WHEN [Date] = 3 THEN 'Tuesday' 
			 WHEN [Date] = 4 THEN 'Wednesday' 
			 WHEN [Date] = 5THEN 'Thursday' 
			 WHEN [Date] = 6 THEN 'Friday' 
			 ELSE 'Saturday' END [Date],
			 [Time],
			 [Date] AS DateId
		 FROM SetStratBatch"
		);
	}

	public function updatebatch($_id, $Date, $timeset, $caldate, $caltime)
	{
		try {
			// code
			// $id_name = trim($id_name);
			//datecal calculate



			$conn = Database::connect();

			$batchold = $this->sqlHelper->getRows(sqlsrv_query(
				$conn,
				"SELECT *
					FROM SetStratBatch
					WHERE [Type] = ?",
				[
					$_id
				]
			));

			$query = Sqlsrv::update(
				$conn,
				"UPDATE SetStratBatch 
			     SET  [Date] = ?,
					  [Time] = ?,
					  [CalDate] =?,
      				  [CalTime] =?
				WHERE [Type] =?",
				[$Date, $timeset, $caldate, $caltime, $_id]
			);

			if (!$query) {
				return $this->response->array(false, "Update failed.");
			}

			$insertlog = Sqlsrv::insert(
				$conn,
				"INSERT INTO SetStartBatchLog(
					[Type]
      				,[NewDate]
      				,[OldDate]
      				,[NewTime]
      				,[OldTime]
      				,[UpdateDate]
      				,[UpdateBy]
				) VALUES (
					?,?,?,?,?,GETDATE(),?
				)",
				[$_id, $Date, $batchold[0]["Date"], $timeset, $batchold[0]["Time"], $_SESSION["user_login"]]
			);
			if (!$insertlog) {
				return $this->response->array(false, "insertLog failed.");
			}

			return $this->response->array(true, "Update success.");
		} catch (\Exception $e) {
			return $this->response->array(false, $e->getMessage());
		}
	}
}
