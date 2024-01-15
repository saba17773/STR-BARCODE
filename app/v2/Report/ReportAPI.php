<?php

namespace App\V2\Report;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class ReportAPI
{

	public function __construct()
	{
		$this->db = new Connector;
	}

	public function dailyFinalHoldView($date, $shift, $type, $pressBOI, $holdtype)
	{

		if ($type === 'pcr') {
			$type = 'RDT';
		} else {
			$type = 'TBR';
		}

		$date_today = date('Y-m-d', strtotime($date));
		$date_tom = date('Y-m-d', strtotime($date . "+1 days"));
		$date1 = $date_today . ' 08:00:01';
		$date2 = $date_today . ' 20:00:00';
		$date3 = $date_today . ' 20:00:01';
		$date4 = $date_tom . ' 08:00:00';

		if ((int) $shift === 1) {
			$shift_time = ' BETWEEN \'' . $date1 . '\' AND \'' . $date2 . '\' ';
		} else {
			$shift_time = ' BETWEEN \'' . $date3 . '\' AND \'' . $date4 . '\' ';
		}
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}

		if($holdtype==2){
			return Sqlsrv::queryArray(
				$this->db->dbConnect(),
				"SELECT
				IT.Barcode,
				IT.CuringCode,
				IT.PressNo,
				IT.GT_Code,
				IT.PressSide,
				IT.TemplateSerialNo,
				IM.NameTH [ItemName],
				D.Description [Defect],
				U.Name [CreateBy],
				ITS.Batch,
				ITS.CreateDate,
				IT.BuildingNo [BuildNo],
				IT.CreateDate [BuildDate],
				IT.CuringDate,
				UU.Name [Operator],
				(
					SELECT SM.Description [Shift]
					FROM InventTrans ITS2
					LEFT JOIN ShiftMaster SM ON SM.ID = ITS2.Shift
					WHERE IT.Barcode = ITS2.Barcode
					AND ITS2.CreateDate = IT.CreateDate
					AND  ITS2.DisposalID = 1
					AND ITS2.DocumentTypeID = 1
				) AS Shift
				,CASE 
					WHEN TD.HoldType = 1 THEN 'Normal'
					WHEN TD.HoldType = 2 THEN 'Mode Light Buff'
				ELSE '' END HoldType
				from InventTable IT
				left join InventTrans ITS ON IT.Barcode = ITS.Barcode
				LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
				--left join ItemMaster IM ON IM.ID = ITS.CodeID
				LEFT JOIN ItemMaster IM ON (
	        CASE
	            WHEN SUBSTRING(ITS.CodeID, 1, 1) = 'Q' THEN REPLACE(ITS.CodeID, 'Q', 'I')
	            ELSE ITS.CodeID
	        END
	        ) = IM.ID
				left join Defect D ON D.ID = ITS.DefectID
				left join UserMaster U ON U.ID = ITS.CreateBy
				left join UserMaster UU ON UU.ID = IT.CreateBy
				left join PressMaster PM ON PM.ID = IT.PressNo
				left join TransDefect TD ON TD.Barcode = ITS.Barcode
				and TD.DisposalID = ITS.DisposalID and TD.LocationID = ITS.LocationID and TD.WarehouseID = ITS.WarehouseID
				where
				ITS.DisposalID = 10 and
				ITS.DocumentTypeID = 1 and
				ITS.WarehouseID = 2 and
				ITS.CreateDate $shift_time and
				IM.ProductGroup = ?
				$whereBOI
				and TD.HoldType = ?
				-- and
				-- CONVERT(date, ITS.CreateDate) = ?
				ORDER BY ITS.CreateDate ASC",
				[
					$type,
					$holdtype
					// date('Y-m-d', strtotime($date))
				]
			);
		}else{
			return Sqlsrv::queryArray(
				$this->db->dbConnect(),
				"SELECT
				IT.Barcode,
				IT.CuringCode,
				IT.PressNo,
				IT.GT_Code,
				IT.PressSide,
				IT.TemplateSerialNo,
				IM.NameTH [ItemName],
				D.Description [Defect],
				U.Name [CreateBy],
				ITS.Batch,
				ITS.CreateDate,
				IT.BuildingNo [BuildNo],
				IT.CreateDate [BuildDate],
				IT.CuringDate,
				UU.Name [Operator],
				(
					SELECT SM.Description [Shift]
					FROM InventTrans ITS2
					LEFT JOIN ShiftMaster SM ON SM.ID = ITS2.Shift
					WHERE IT.Barcode = ITS2.Barcode
					AND ITS2.CreateDate = IT.CreateDate
					AND  ITS2.DisposalID = 1
					AND ITS2.DocumentTypeID = 1
				) AS Shift
				,CASE 
					WHEN TD.HoldType = 1 THEN 'Normal'
					WHEN TD.HoldType = 2 THEN 'Mode Light Buff'
				ELSE '' END HoldType
				from InventTable IT
				left join InventTrans ITS ON IT.Barcode = ITS.Barcode
				LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
				--left join ItemMaster IM ON IM.ID = ITS.CodeID
				LEFT JOIN ItemMaster IM ON (
	        CASE
	            WHEN SUBSTRING(ITS.CodeID, 1, 1) = 'Q' THEN REPLACE(ITS.CodeID, 'Q', 'I')
	            ELSE ITS.CodeID
	        END
	        ) = IM.ID
				left join Defect D ON D.ID = ITS.DefectID
				left join UserMaster U ON U.ID = ITS.CreateBy
				left join UserMaster UU ON UU.ID = IT.CreateBy
				left join PressMaster PM ON PM.ID = IT.PressNo
				left join TransDefect TD ON TD.Barcode = ITS.Barcode
				and TD.DisposalID = ITS.DisposalID and TD.LocationID = ITS.LocationID and TD.WarehouseID = ITS.WarehouseID
				and TD.HoldType <> 2
				where
				ITS.DisposalID = 10 and
				ITS.DocumentTypeID = 1 and
				ITS.WarehouseID = 2 and
				ITS.CreateDate $shift_time and
				IM.ProductGroup = ?
				$whereBOI
				-- and
				-- CONVERT(date, ITS.CreateDate) = ?
				ORDER BY ITS.CreateDate ASC",
				[
					$type
					// date('Y-m-d', strtotime($date))
				]
			);
		}
		
	}
}
