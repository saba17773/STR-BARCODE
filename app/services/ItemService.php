<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Security;
use App\V2\Database\Connector;
use Wattanar\SqlsrvHelper;

class ItemService
{

	private $database = null;
	private $sqlsrvHelper = null;

	public function __construct()
	{
		$this->database = new Connector();
		$this->sqlsrvHelper = new SqlsrvHelper();
	}

	public function all()
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM ItemMaster WHERE  ItemGroup = 'FG'");
	}

	public function isItem($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = $this->database->dbConnect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ?
			AND ItemID IS NOT NULL",
			[$barcode_decode]
		);
	}
	public function allBrand()
	{
		$conn = $this->database->dbConnect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM BrandMaster");
	}

	public function syncItem()
	{
		$conn = $this->database->dbConnect();
		$query = sqlsrv_query(
			$conn,
			"INSERT ItemMaster(ID, NameTH, Pattern, Brand, UnitID, ItemGroup, InternalNumber, ProductGroup, SubGroup, ManualBatch, QtyPerPallet)
			SELECT
			I.ITEMID, I.DSGThaiItemDescription ,  DSGPatternID, DSGBandID, I.BOMUNITID, I.ItemGroupId,
			I.DSG_InternalItemId, I.DSGProductGroupID, I.DSGSubGroupID, 0[manual_batch], I.DSG_QtyPerpallet
			FROM [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] I
			WHERE I.DSGProductGroupID IN ('TBR', 'RDT','SEM')
			AND I.ITEMGROUPID IN ('FG', 'SM', 'FG-B')
			AND I.DSG_Obsolete = 0
			AND I.ITEMID NOT IN (
			SELECT ID FROM ItemMaster
			)
			AND ITEMNAME <> 'ห้ามใช้'
			GROUP BY I.ITEMID, I.DSGThaiItemDescription ,  DSGPatternID, DSGBandID, I.BOMUNITID,
			I.ItemGroupId, I.DSG_InternalItemId, I.DSGProductGroupID, I.DSGSubGroupID
			,I.DSG_QTYPERPALLET"
		);

		$queryuodate = sqlsrv_query(
			$conn,
			"UPDATE IM
			SET
				IM.NameTH = II.DSGThaiItemDescription ,
			  IM.Pattern = II.DSGPatternID,
			  IM.Brand = II.DSGBandID ,
			  IM.UnitID = II.BOMUNITID ,
			  IM.SubGroup = II.DSGSubGroupID,
			  IM.ProductGroup = II.DSGProductGroupID,
			  IM.ItemGroup = II.ItemGroupId,
			  IM.InternalNumber = II.DSG_InternalItemId,
			  IM.QtyPerPallet = II.DSG_QtyPerpallet

			  FROM ItemMaster IM
			JOIN (
			 SELECT I.ITEMID,
			  I.DSGThaiItemDescription ,
			  I.DSGPatternID,
			  I.DSGBandID,
			  I.BOMUNITID,
			  I.ItemGroupId,
			  I.DSG_InternalItemId,
			  I.DSGProductGroupID,
			  I.DSGSubGroupID,
			  0[manual_batch],
			  I.DSG_QtyPerpallet
				 FROM[FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] I
					WHERE  I.DSGProductGroupID IN ('TBR', 'RDT','SEM')
				AND I.ITEMGROUPID IN ('FG', 'SM', 'FG-B')
				AND I.DSG_Obsolete = 0

				AND ITEMNAME <> 'ห้ามใช้'
				GROUP BY I.ITEMID,
				I.DSGThaiItemDescription ,
				DSGPatternID,
				DSGBandID,
				I.BOMUNITID,
				I.ItemGroupId,
				I.DSG_InternalItemId,
				I.DSGProductGroupID,
				I.DSGSubGroupID,
				I.DSG_QTYPERPALLET

			 ) II ON II.ITEMID = IM.ID

			 where IM.NameTH <>II.DSGThaiItemDescription
			 or IM.Pattern <> II.DSGPatternID
			 or IM.Brand <>II.DSGBandID
			 or IM.UnitID <> II.BOMUNITID
			 or IM.SubGroup <> II.DSGSubGroupID
			 or IM.ProductGroup <> II.DSGProductGroupID
			 or IM.ItemGroup <>II.ItemGroupId
			 or IM.InternalNumber <>II.DSG_InternalItemId
			 or IM.QtyPerPallet <> II.DSG_QtyPerpallet"
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function getItemFG($barcode)
	{
		try {
			$sql = "SELECT CCM.ItemID 
			FROM InventTable I 
			LEFT JOIN CureCodeMaster CCM ON CCM.ID = I.CuringCode
			where Barcode = ?";

			$itemFG = $this->sqlsrvHelper->getRows(sqlsrv_query(
				$this->database->dbConnect(),
				$sql,
				[
					$barcode
				]
			));

			if (count($itemFG) === 0) {
				return null;
			} else {
				return $itemFG[0]["ItemID"];
			}
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}
