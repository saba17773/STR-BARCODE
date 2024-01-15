<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;

class ReportService
{

	private $db;

	public function __construct()
	{
		$this->db = new Database;
	}

	public function greentireScrap($date, $product_group, $pressBOI)
	{
		$select_date = date('Y-m-d', strtotime($date)) . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}
		//echo $select_date."---".$next_date;exit();
		// return $select_date . ' = ' . $date_1;
		// if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
		// 	$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
		// 	$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		// }

		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT
			IT.Barcode,
			IT.CuringCode,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			GCM.ItemNumber [IDItem],
			IT.GT_Code [GT_Code],
			S.Description [Shift],
			ITS.CreateDate,
			IT.BuildingNo [MC],
			(
				SELECT TOP 1  S_S.Description [Shift] FROM InventTrans S_IT
				LEFT JOIN ShiftMaster S_S ON S_S.ID = S_IT.Shift
				WHERE S_IT.Barcode = IT.Barcode
				AND S_IT.CreateDate = IT.CreateDate
			) [Shift_Build],
			IT.DateBuild,
			ITS2.CreateDate [CreateDateHold],
			D2.Description [DefectDescHold]
			
			FROM InventTable IT
			LEFT JOIN


			InventTrans ITS ON IT.Barcode = ITS.Barcode AND ITS.DisposalID = 2
			AND ITS.DocumentTypeID = 2
			AND ITS.TransID = ( select MAX(TransID) from InventTrans where Barcode = ITS.Barcode and DisposalID = '2' AND DocumentTypeID = 2 )

			LEFT JOIN GreentireCodeMaster GCM ON GCM.ID = IT.GT_Code
			LEFT JOIN
			--( SELECT TOP 1 * FROM CureCodeMaster

			--)CCM ON CCM.GreentireID = GCM.ID
			CureCodeMaster CCM ON CCM.GreentireID = GCM.ID AND  CCM.ID = ( select MAX(ID) from CureCodeMaster where GreentireID = GCM.ID  )
			LEFT JOIN ItemMaster IM ON IM.ID = CCM.ItemID
			LEFT JOIN InventTrans ITS2 ON IT.Barcode = ITS2.Barcode AND ITS2.DisposalID = '10' 
			AND	 ITS2.DocumentTypeID = '1'
			AND ITS2.TransID = ( select MAX(TransID) from InventTrans where Barcode = ITS.Barcode and DisposalID = '10' AND DocumentTypeID = 1 )
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN Defect D2 ON D2.ID = ITS2.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			WHERE
			ITS.CreateDate BETWEEN ? AND ?
			AND ITS.WarehouseID = 1
			--AND IT.DisposalID = 2
			AND ITS.DisposalID = 2
			AND IM.ProductGroup = ?
			AND IT.DisposalID IN ('2','27')
			--AND ITS.Barcode = '52000529033'
			$whereBOI
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $next_date, $product_group]
		);

		// return Sqlsrv::queryArray(
		// 	$this->db->connect(),
		// 	"SELECT
		// 	IT.Barcode,
		// 	IT.GT_Code,
		// 	IT.ItemID,
		// 	(
		// 		SELECT TOP 1 ITSD.DefectID
		// 		FROM  InventTrans ITSD
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as DefectID,
		// 	(
		// 		SELECT TOP 1 ITSD.Batch
		// 		FROM  InventTrans ITSD
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as Batch,
		// 	(
		// 		SELECT TOP 1 DF.Description
		// 		FROM  InventTrans ITSD
		// 		LEFT JOIN Defect DF ON ITSD.DefectID = DF.ID
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as DefectDesc,
		// 	(
		// 		SELECT TOP 1 SM.Description
		// 		FROM  InventTrans ITSD
		// 		LEFT JOIN ShiftMaster SM ON SM.ID = ITSD.Shift
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		AND ITSD.DisposalID = 1 -- Greentire
		// 		AND ITSD.DocumentTypeID = 1 -- Receive
		// 		ORDER BY ITSD.TransID DESC
		// 	) as Shift,
		// 	(
		// 		SELECT TOP 1 ITS.CreateDate FROM InventTrans ITS
		// 		WHERE  ITS.Barcode = IT.Barcode
		// 		ORDER BY ITS.CreateDate DESC
		// 	) as CreateDate
		// 	FROM InventTable IT
		// 	LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
		// 	WHERE IT.DisposalID = 2 -- scrap
		// 	AND IT.WarehouseID = 1
		// 	AND ITS.CreateDate BETWEEN ? AND ?
		// 	GROUP BY IT.Barcode, IT.GT_Code, IT.ItemID
		// 	ORDER BY IT.Barcode ASC",
		// 	[$select_date, $next_date]
		// );
	}

	public function curetireScrap($date, $product_group, $pressBOI)
	{
		// $date = date('Y-m-d', strtotime($date));
		// $select_date = $date . ' 08:00:00';
		// $next_date = $date . ' 08:00:00';
		// $date_1 = date('Y-m-d H:i:s' ,strtotime($next_date . '+1 day'));
		$date = date('Y-m-d', strtotime($date));
		$select_date = $date . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';

		// if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
		// 	$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
		// 	$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		// }
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		

		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT
			IT.Barcode,
			IT.CuringCode,
			IT.GT_Code,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			IT.ItemID [ItemID],
			S.Description [Shift],
			IT.PressNo,
			IT.DateBuild,
			IT.BuildingNo,
			IT.CuringDate,
			ITS2.CreateDate [CreateDateHold],
			D2.Description [DefectDescHold],
			IM.NetWeight / 1000 AS NetWeight
			
			FROM InventTable IT
			LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
				AND IT.UpdateDate = ITS.CreateDate
			LEFT JOIN InventTrans ITS2 ON IT.Barcode = ITS2.Barcode AND ITS2.DisposalID = '10' 
			AND	 ITS2.DocumentTypeID = '1'
			AND ITS2.TransID = ( select MAX(TransID) from InventTrans where Barcode = ITS.Barcode and DisposalID = '10' AND DocumentTypeID = 1 )
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN Defect D2 ON D2.ID = ITS2.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			LEFT JOIN ItemMaster IM ON IM.ID = IT.ItemID
			WHERE
			IT.UpdateDate BETWEEN ? AND ?
			AND IT.WarehouseID = 2
			AND IT.DisposalID = 2
			AND ITS.DisposalID = 2
			$whereBOI
			--AND CB.BOI = 'BOI1'
			AND IT.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $next_date, $product_group]
		);
	}

	public function curingReportPdf($date)
	{
		$date = date('Y-m-d', strtotime($date));
		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT
				PressNo
				,CreateBy
				,Name
				,PressSide
				,CuringCode
				,SUM(Q1)[Q1]
				,SUM(Q2)[Q2]
				,SUM(Q3)[Q3]
				,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
				,U.Name
				,I.PressNo
				,I.PressSide
				,I.CuringCode
				,CONVERT(date,I.CuringDate)[date_b]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)= ?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%A%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			Z.CreateBy
			,Z.Name
			,Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			ORDER BY PressNo ASC",
			[$date]
		);
	}

	// J Report
	public function BuildingServiceallpdf($datebuilding, $shift, $group, $product_group, $pressBOI)
	{
		$datenight_original = str_replace('-', '/', $datebuilding);
		$datenight = date('Y-m-d 20:00:00', strtotime($datenight_original));
		$datebuildingnight = date('Y-m-d 08:00:00', strtotime($datenight . "+1 days"));
		$datebuildingnight_date_only = date('Y-m-d', strtotime($datenight_original . "+1 days"));
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}


		// $datebuildingnight = date('Y-m-d 08:00:00', strtotime($datebuildingnight));
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'day'[Shift]
					,BOI
					,Total

				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description
						,BOT.ID AS BOI
						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '08:00:01' AND CONVERT(time,I.DateBuild) <= '10:00:00')[QTY_1]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '10:00:01' AND CONVERT(time,I.DateBuild) <= '12:00:00')[QTY_2]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '12:00:01' AND CONVERT(time,I.DateBuild) <= '14:00:00')[QTY_3]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '14:00:01' AND CONVERT(time,I.DateBuild) <= '16:00:00')[QTY_4]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '16:00:01' AND CONVERT(time,I.DateBuild) <= '18:00:00')[QTY_5]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '18:00:01' AND CONVERT(time,I.DateBuild) <= '20:00:00')[QTY_6]
						,KP.Total
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				LEFT JOIN KeepBuilding KP ON I.BuildingNo = KP.BuildingMc AND I.GT_Code = KP.GTCode AND KP.DateBuild = ? AND KP.Shift = '1'
				WHERE CONVERT(date,I.DateBuild)=?
				AND I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				)Z
				WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.BOI
				,Z.Total ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$datebuilding,
					$product_group
				]
			);
		} else {

			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'night'[Shift]
					,Total


				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description

						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 20:00:01' AND I.DateBuild <= '$datebuilding 22:00:00')[QTY_1]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 22:00:01' AND I.DateBuild <= '$datebuilding 23:59:59')[QTY_2]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 00:00:00' AND I.DateBuild <= '$datebuildingnight_date_only 02:00:00')[QTY_3]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 02:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 04:00:00')[QTY_4]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 04:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 06:00:00')[QTY_5]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 06:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 08:00:00')[QTY_6]
						,KP.Total
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.QTY>0 AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				LEFT JOIN KeepBuilding KP ON I.BuildingNo = KP.BuildingMc AND I.GT_Code = KP.GTCode AND KP.DateBuild = ? AND KP.Shift = '2'
				WHERE
				I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				AND I.DateBuild between ? AND ?
				)Z WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.Total
				 ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$product_group,
					$datebuilding,
					$datebuildingnight
				]
			);
		}
	}

	public function InternalServiceallpdf($dateinter, $pressBOI)
	{
		$conn = Database::connect();
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
				J.InventJournalID
				,J.ItemID
				,CONVERT(time,J.CreateDate)[time_create]
				,I.TemplateSerialNo
				,I.ItemID
				,IT.NameTH
				,I.CuringCode
				,R.Description[Note]
				,IJ.EmpCode
				,E.FirstName
				,E.LastName
				,E.DivisionCode
				,D.Description[Department]
				,J.CreateBy
				,ITS.Batch
				,U.Name
				,1[qty]
				,ROW_NUMBER() OVER(ORDER BY name ASC) AS Row
				,S.Description
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode AND J.ItemID=I.ItemID
			LEFT JOIN CuringBOI CB ON CB.Barcode = I.Barcode
			LEFT JOIN ItemMaster IT ON (
        CASE
            WHEN SUBSTRING(I.ItemID, 1, 1) = 'Q' THEN REPLACE(I.ItemID, 'Q', 'I')
            ELSE I.ItemID
        END
        ) = IT.ID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN Status S ON Ij.Status=S.ID
			LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
			WHERE CONVERT(date,J.CreateDate) = ?
			AND IJ.JournalTypeID = 'MOV'
			$whereBOI
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$dateinter
			]
		);
	}

	public function CuringServiceallpdf1($datecuring, $shift, $press1)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdf2($datecuring, $shift, $press2)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdf3($datecuring, $shift, $press3)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfDummy($datecuring, $shift, $press1)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson($conn, "SELECT
					ID
					,PressSide
					,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,P.ID
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM PressMaster P
			LEFT JOIN InventTable I ON P.ID=I.PressNo AND CONVERT(date,I.CuringDate)=?
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE (P.ID LIKE '%13%' OR P.ID LIKE '%14%')
			)Z
			GROUP BY
			Z.ID
			,Z.PressSide
			,Z.CuringCode
			ORDER BY Z.ID ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT
					ID
					,PressSide
					,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]
			FROM(
			SELECT	T.CreateBy
					,U.Name
					,P.ID
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM PressMaster P
			LEFT JOIN InventTable I ON P.ID=I.PressNo AND CONVERT(date,I.CuringDate)=?
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE (P.ID LIKE '%13%' OR P.ID LIKE '%14%')
			)Z
			GROUP BY
			Z.ID
			,Z.PressSide
			,Z.CuringCode
			ORDER BY Z.ID ASC", [$datecuring]);
		}
	}

	public function CuringServiceallpdfQ1DummyTest($datecuring, $shift, $press1)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT
					ID
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,P.ID
					,I.PressSide
					,I.CuringCode
					,CCM.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM PressMaster P
			LEFT JOIN InventTable I ON P.ID=I.PressNo AND CONVERT(date,I.CuringDate)=?
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster CCM ON (
				CASE
					WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
					ELSE T.CodeID
				END
				)  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE (P.ID LIKE '%13%' OR P.ID LIKE '%14%')
			)Z
			GROUP BY
			Z.ID
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY Z.ID ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT
					ID
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]
			FROM(
			SELECT	T.CreateBy
					,U.Name
					,P.ID
					,I.PressSide
					,I.CuringCode
					,CCM.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM PressMaster P
			LEFT JOIN InventTable I ON P.ID=I.PressNo AND CONVERT(date,I.CuringDate)=?
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster CCM ON (
				CASE
					WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
					ELSE T.CodeID
				END
				)  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE (P.ID LIKE '%13%' OR P.ID LIKE '%14%')
			)Z
			GROUP BY
			Z.ID
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY Z.ID ASC", [$datecuring]);
		}
	}

	public function CuringServiceallpdfQ1Dummy($datecuring, $shift, $press1)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					-- ,I.CuringCode
					,T.CodeID
					,CCM.rate12
					,CCM.ID AS CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
            ELSE T.CodeID
        END
        )  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND (I.PressNo LIKE '%13%' OR I.PressNo LIKE '%14%')
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					-- ,I.CuringCode
					,T.CodeID
					,CCM.rate12
					,CCM.ID AS CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
            ELSE T.CodeID
        END
        )  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE I.PressNo IS NOT NULL
			AND (I.PressNo LIKE '%13%' OR I.PressNo LIKE '%14%')
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ1($datecuring, $shift, $press1)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					-- ,I.CuringCode
					-- ,C.rate12
					,T.CodeID
					,CCM.rate12
					,CCM.ID AS CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			-- LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			-- LEFT JOIN CureCodeMaster CCM
			-- 		ON CCM.GreentireID = I.GT_Code
			-- 		AND CCM.ItemQ = T.CodeID
			-- 		OR CCM.ItemID = T.CodeID
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
            ELSE T.CodeID
        END
        )  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					-- ,I.CuringCode
					-- ,C.rate12
					,T.CodeID
					,CCM.rate12
					,CCM.ID AS CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			-- LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(T.CodeID, 1, 1) = 'Q' THEN REPLACE(T.CodeID, 'Q', 'I')
            ELSE T.CodeID
        END
        )  = CCM.ItemID AND CCM.GreentireID = I.GT_Code
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ2($datecuring, $shift, $press2)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ3($datecuring, $shift, $press3)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%'
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function Curingname1_4($datecuring, $shift, $press01, $press04)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press01' AND '$press04'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press01' AND '$press04'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$date1, $date8]);
		}
	}

	public function Curingname5_8($datecuring, $shift, $press05, $press08)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press05' AND '$press08'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press05' AND '$press08'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$date1, $date8]);
		}
	}

	public function Curingname9_12($datecuring, $shift, $press09, $press12)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 20:00:01';
		$date2 = $datecuring . ' 23:00:00';
		$date3 = $datecuring . ' 23:00:01';
		$date4 = $datecuringnight . ' 02:00:00';
		$date5 = $datecuringnight . ' 02:00:01';
		$date6 = $datecuringnight . ' 05:00:00';
		$date7 = $datecuringnight . ' 05:00:01';
		$date8 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press09' AND '$press12'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$datecuring]);
		} else {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press09' AND '$press12'
			)Z
			GROUP BY
			Z.CreateBy
			,Z.Name", [$date1, $date8]);
		}
	}

	public function CuringServiceallgrouppdf($datecuring, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$conn = Database::connect();

		if ($shift == 'day') {
			return Sqlsrv::queryJson($conn, "SELECT
						Shift,
						Description
					FROM(
					SELECT   T.Shift
							,S.Description
					FROM InventTrans T
					LEFT JOIN ShiftMaster S ON T.Shift=S.ID
					WHERE CONVERT(date,T.CreateDate)= ?
					) Z
					GROUP BY
					Z.Shift,
					Z.Description", [$datecuring]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT
						Shift,
						Description
					FROM(
					SELECT   T.Shift
							,S.Description
					FROM InventTrans T
					LEFT JOIN ShiftMaster S ON T.Shift=S.ID
					WHERE CONVERT(date,T.CreateDate) BETWEEN ? AND ?
					) Z
					GROUP BY
					Z.Shift,
					Z.Description", [$datecuring, $datecuringnight]);
		}
	}

	public function RateBuildServicepdf($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T6.*,
			SUM(T6.Total_Diff) OVER (PARTITION BY T6.Machine) Sum_Total
			FROM (
				SELECT T5.*,
				T5.P1+T5.P2+T5.P3+T5.P4 AS Total,
				(T5.P1+T5.P2+T5.P3+T5.P4)-T5.Charge AS Total_Diff
				FROM (
					SELECT T4.CreateBy,T4.EmployeeID,T4.Name,T4.Machine,T4.Act,
					T4.BuildTypeId,T4.BuildType,T4.LoginDate,T4.LogoutDate,
					T4.Qty1,T4.Qty2,T4.Qty3,T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,
					T4.RateType,T4.P1,T4.P2,T4.P3,T4.COUNT_MAC,T4.Shift,SUM(T4.SCH) SCH,T4.Charge,
					CASE WHEN T4.RateType = 'PCR' AND (T4.COUNT_MAC < 3 )
					THEN (T4.P1+T4.P2+T4.P3)/2 ELSE 0
					END AS P4
					FROM (
						SELECT T3.CreateBy,T3.EmployeeID,T3.Name,
						T3.Machine,T3.Act,T3.BuildTypeId,T3.BuildType,T3.LoginDate,T3.LogoutDate,
						CASE T3.BuildTypeId WHEN 1 THEN 'เส้นที่ 1-'+CONVERT(VARCHAR,R.Qty1) END AS Qty1 ,
						CASE R.RateType
						WHEN 'TBR' THEN
							CASE T3.BuildTypeId WHEN 1 THEN 'เส้นที่ '+CONVERT(VARCHAR,R.Qty1+1)+'-'+CONVERT(VARCHAR,R.Qty2) END
						ELSE
							CASE T3.BuildTypeId WHEN 1 THEN '' END
						END AS Qty2,
						CASE R.RateType
						WHEN 'TBR' THEN
							CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty3-1)END
						ELSE
							CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty1) END
						END AS Qty3,
						R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
						CASE WHEN T3.Act >= R.Qty1 THEN R.RatePrice1 ELSE 0 END AS 'P1',
						-- CASE WHEN T3.Act >= R.Qty2 THEN (R.Qty2-R.Qty1)*R.RatePrice2 ELSE 0 END AS 'P2',
						CASE WHEN (T3.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
						WHEN  T3.Act > R.Qty1 AND ((T3.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T3.Act-R.Qty1)*R.RatePrice2
						ELSE 0
						END AS 'P2',
						CASE R.RateType
						WHEN 'TBR' THEN
							CASE WHEN T3.Act >= R.Qty3 THEN (T3.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
						WHEN 'PCR' THEN
							CASE WHEN  T3.Act >= R.Qty2 THEN ((T3.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
						ELSE 0
						END AS 'P3',
						COUNT(T3.Machine) OVER (PARTITION BY T3.Machine) COUNT_MAC,T3.Shift,
						CASE WHEN S.Total IS NULL THEN 0
						ELSE S.Total
						END AS 'SCH',T3.Charge
						FROM(
							SELECT T2.CreateBy,T1.EmployeeID,T1.Name,
							T1.Machine,T2.Act,T1.BuildTypeId,T1.BuildType,
							T1.LoginDate,T1.LogoutDate,T1.Row,T1.Shift,T1.Charge
							FROM (
								SELECT B.CreateBy,B.Machine,R.BuildTypeId,REPLACE(U.Name,'null','') Name ,U.EmployeeID,T.Description BuildType,
								ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
								R.LoginDate,R.LogoutDate,R.Shift,
								CASE WHEN D.Charge IS NULL THEN 0
								ELSE D.Charge END AS Charge
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN ? AND ?) JOIN
								UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId LEFT JOIN
								DeductRateBuild D ON B.Machine = D.Machine AND
								CONVERT(DATE,B.CreateDate) = CONVERT(DATE,D.DeductDate)
								AND R.Shift = D.Shift AND B.CreateBy = D.UserId
								WHERE B.CreateDate BETWEEN ? AND ? AND
								R.LoginDate BETWEEN ? AND ?  AND
								R.LogoutDate BETWEEN ? AND ? AND
								LEFT(B.Machine,1) = LEFT(?,1) AND R.RateGroupID = 1
								GROUP BY B.Machine,B.CreateBy,R.LoginDate,R.BuildTypeId,U.Name,
								U.EmployeeID,T.Description,R.LogoutDate,R.Shift,D.Charge
							)T1	JOIN
							(
								SELECT B.CreateBy,
								COUNT(B.Barcode) 'Act'
								FROM BuildTrans B JOIN
								InventTable I ON B.Barcode = I.Barcode
								WHERE B.CreateDate BETWEEN ? AND ?
								AND I.CheckBuild = 1 AND LEFT(B.Machine,1) = LEFT(?,1)
								AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
								GROUP BY B.CreateBy
							)T2 ON T1.CreateBy =  T2.CreateBy
							WHERE T1.Row = 1
						)T3 JOIN
						RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId LEFT JOIN
						RateBuildSchedule S ON T3.Machine = S.Machine AND T3.Shift = S.Shift AND
						CONVERT(DATE,T3.LoginDate) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
						WHERE T3.Act >= R.Qty1
					)T4
					GROUP BY T4.CreateBy,T4.EmployeeID,T4.Name,T4.Machine,T4.Act,
					T4.BuildTypeId,T4.BuildType,T4.LoginDate,T4.LogoutDate,
					T4.Qty1,T4.Qty2,T4.Qty3,T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,
					T4.RateType,T4.P1,T4.P2,T4.P3,T4.COUNT_MAC,T4.Shift,T4.Charge
					-- WHERE (DATEDIFF(HOUR,T4.LoginDate,T4.LogoutDate) >= 10 OR T4.Act >= T4.SCH )
				)T5
			)T6
			GROUP BY T6.CreateBy,T6.EmployeeID,T6.Name,T6.LoginDate,
			T6.Machine,T6.Act,T6.BuildTypeId,T6.BuildType,T6.Shift,
			T6.Qty1,T6.Qty2,T6.QTY3,T6.RatePrice1,T6.RatePrice2,
			T6.RatePrice3,T6.RateType,T6.P1,T6.P2,T6.P3,T6.P4,
			T6.Total,T6.COUNT_MAC,T6.SCH,T6.LogoutDate,T6.Charge,T6.Total_Diff
			ORDER BY T6.Machine,T6.BuildTypeId,T6.LoginDate
			",
			[
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$machine,
				$tstart,
				$tend,
				$machine
			]
		);

		return $query;
	}

	public function RateBuildServicepdf_Ply($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T5.*,SUM(T5.Total) Sum_Total
			FROM
			(
				SELECT T4.*,
				(T4.P1+T4.P2+T4.P3) Total
				FROM
				(
					SELECT T2.CreateBy,T2.EmployeeID,T2.Name,T2.DateBuild,
					T2.Machine,T3.Act,T2.ItemNumber,
					'เส้นที่ 1-'+CONVERT(VARCHAR,T2.Qty1) AS Qty1 ,
					'' Qty2,
					'มากกว่า >'+CONVERT(VARCHAR,T2.Qty1) AS Qty3,
					T2.RatePrice1,T2.RatePrice2,T2.RatePrice3,
					T2.RateType,T2.SCH,
					CASE WHEN T3.Act >= T2.Qty1
					THEN T2.RatePrice1
					ELSE 0 END AS 'P1',
					0 AS 'P2',
					CASE WHEN  T3.Act > T2.Qty2
					THEN ((T3.Act-T2.Qty2) /T2.Qty3)* T2.RatePrice3
					ELSE 0 END AS 'P3',
					T2.PLY
					FROM
					(
						SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DateBuild,
						T1.Machine,T1.Act,T1.ItemNumber,T1.Qty1,T1.Qty2,T1.Qty3,
						T1.RatePrice1,T1.RatePrice2,T1.RatePrice3,T1.RateType,
						CASE WHEN T1.SCH IS NULL THEN 0 ELSE T1.SCH END AS SCH,T1.PLY,
						ROW_NUMBER() OVER(partition by T1.Machine ORDER BY T1.Act DESC) AS Row
						FROM
						(
							SELECT I.CreateBy,U.EmployeeID,U.Name,
							CONVERT(DATE,I.DateBuild) DateBuild,
							I.BuildingNo Machine,COUNT(I.Barcode) Act,G.ItemNumber,
							R.Qty1,R.Qty2,R.Qty3,
							R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
							S.Total SCH,R.PLY
							FROM InventTable I
							JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
							LEFT JOIN RateBuildSchedule S ON I.BuildingNo = S.Machine AND S.Active = 1
							AND CONVERT(DATE,I.DateBuild) = CONVERT(DATE,S.DateRateBuild)
							JOIN UserMaster U ON I.CreateBy = U.ID
							JOIN RateMaster R ON I.BuildingNo = R.Machine
							WHERE I.DateBuild BETWEEN ? AND ?
							AND I.CheckBuild = 1 AND LEFT(I.BuildingNo,1) = LEFT(?,1)
							GROUP BY I.BuildingNo,G.ItemNumber,S.Total,I.CreateBy,
							CONVERT(DATE,I.DateBuild),U.EmployeeID,U.Name,R.Qty1,
							R.Qty2,R.Qty3,R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,R.PLY
						)T1 JOIN
						(
							SELECT T1.ItemGT,T1.PLY
							FROM(
								SELECT ItemGT,PLY,
								ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
								FROM ItemPLY
							)T1
							WHERE T1.Row=1
						)P ON T1.ItemNumber = P.ItemGT AND P.PLY = T1.PLY
					)T2 JOIN
					(
						SELECT I.BuildingNo,COUNT(I.Barcode) ACT
						FROM InventTable I
						WHERE I.DateBuild BETWEEN ? AND ?
						AND I.CheckBuild = 1 AND LEFT(I.BuildingNo,1) = LEFT(?,1)
						GROUP BY I.BuildingNo
					)T3 ON T2.Machine = T3.BuildingNo
					WHERE T2.Row = 1
				)T4
			)T5
			GROUP BY T5.CreateBy,T5.EmployeeID,T5.Name,
			T5.DateBuild,T5.Machine,T5.Act,T5.ItemNumber,
			T5.Qty1,T5.Qty2, T5.Qty3,T5.RatePrice1,
			T5.RatePrice2,T5.RatePrice3,T5.RateType,
			T5.SCH,T5.P1,T5.P2,T5.P3,T5.PLY,T5.Total
			ORDER BY T5.Machine
			",
			[
				$tstart,
				$tend,
				$machine,
				$tstart,
				$tend,
				$machine
			]
		);

		return $query;
	}
	public function chkMachinePLY($lineno)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(LEFT(ID,1)) AS Count_Mac
			FROM BuildingMaster
			WHERE TYPE = 'PCR' AND ID NOT IN ('VMI01','VMI02')
			AND LEFT(ID,1) = LEFT(?,1)
			GROUP BY LEFT(ID,1)
			",
			[
				$lineno
			]
		);
		if ($query[0]['Count_Mac'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getMachineByLine($line)
	{

		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT ID AS Machine,Type
			FROM BuildingMaster
			WHERE LEFT(ID,1) = LEFT(?,1) ",
			[
				$line
			]
		);
		return $query;
	}


	public function countUser($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT ID AS Machine,
			CASE WHEN T3.COUNT_USER IS NULL THEN 0 ELSE
			T3.COUNT_USER END AS COUNT_USER
			FROM  BuildingMaster BM LEFT JOIN
			(
				SELECT T2.Machine,T2.COUNT_USER
						FROM (
							SELECT T1.Machine,
								COUNT(T1.CreateBy) OVER (PARTITION BY T1.Machine) COUNT_USER
								FROM (
									SELECT t.CreateBy,t.Machine,t.BuildTypeId,t.Name,t.EmployeeID,
									t.BuildType,t.Act,t.LoginDate,t.LogoutDate,SUM(t.SCH)SCH,
									ROW_NUMBER() OVER(partition by t.CreateBy ORDER BY t.LoginDate DESC) AS Row
									FROM
									(
										SELECT B.CreateBy,B.Machine,R.BuildTypeId,REPLACE(U.Name, 'null', '') Name,
										U.EmployeeID,T.Description BuildType,COUNT(B.Barcode) Act,R.LoginDate,
										R.LogoutDate,
										CASE WHEN S.Total IS NULL THEN 0
										ELSE S.Total
										END AS 'SCH'
										FROM BuildTrans B JOIN
										RateTrans R ON B.CreateBy = R.UserId JOIN
										UserMaster U ON B.CreateBy = U.ID JOIN
										BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId JOIN
										InventTable I ON B.Barcode = I.Barcode LEFT JOIN
										RateBuildSchedule S ON B.Machine = S.Machine AND R.Shift = S.Shift AND
										CONVERT(DATE,R.LoginDate) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
										WHERE B.CreateDate BETWEEN ? AND ?
										AND R.LoginDate BETWEEN ? AND ?
										AND R.LogoutDate BETWEEN ? AND ?
										AND I.CheckBuild = 1 AND B.Machine = ? AND R.RateGroupID = 1
										GROUP BY B.Machine,B.CreateBy,R.LoginDate,R.BuildTypeId,
										U.Name,U.EmployeeID,T.Description,R.LogoutDate,S.Total
									)t
									GROUP BY t.CreateBy,t.Machine,t.BuildTypeId,t.Name,t.EmployeeID,
									t.BuildType,t.Act,t.LoginDate,t.LogoutDate
							)T1 JOIN
							RateMaster R ON T1.Machine = R.Machine AND T1.BuildTypeId = R.BuildTypeId
							WHERE T1.Row = 1  AND T1.Act >= R.Qty1
							GROUP BY T1.Machine,T1.CreateBy
						)T2 GROUP BY T2.Machine,T2.COUNT_USER
			)T3 ON BM.ID = T3.Machine
			WHERE BM.ID = ?
			",
			[
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$machine,
				$machine
			]
		);

		return $query[0]['COUNT_USER'];
	}

	public function countUserAct($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(T1.CreateBy) COUNT_USER,T1.Machine
			FROM
			(
				SELECT R.CreateBy,R.Machine,R.BuildTypeId,
				ROW_NUMBER() OVER(partition by R.CreateBy ORDER BY R.LoginDate DESC) AS Row,
				R.LoginDate,R.LogoutDate,R.Shift
				FROM RateTrans R
				WHERE R.LoginDate BETWEEN ? AND ?
			)T1
			WHERE T1.Row = 1 AND T1.Machine = ?
			GROUP BY T1.Machine
			ORDER BY Machine
			",
			[
				$tstart,
				$tend,
				// $tstart,
				// $tend,
				// $tstart,
				// $tend,
				$machine
			]
		);

		return $query[0]['COUNT_USER'];
	}


	public function countUser_PLY($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T4.Machine, COUNT(T4.Machine) AS COUNT_USER
			FROM
			(
				SELECT T2.CreateBy,T2.EmployeeID,T2.Name,T2.DateBuild,
				T2.Machine,T3.Act,T2.ItemNumber,
				'เส้นที่ 1-'+CONVERT(VARCHAR,T2.Qty1) AS Qty1 ,
				'' Qty2,
				'มากกว่า >'+CONVERT(VARCHAR,T2.Qty1) AS Qty3,
				T2.RatePrice1,T2.RatePrice2,T2.RatePrice3,
				T2.RateType,T2.SCH,
				CASE WHEN T3.Act >= T2.Qty1
				THEN T2.RatePrice1
				ELSE 0 END AS 'P1',
				0 AS 'P2',
				CASE WHEN  T3.Act > T2.Qty2
				THEN ((T3.Act-T2.Qty2) /T2.Qty3)* T2.RatePrice3
				ELSE 0 END AS 'P3',
				T2.PLY
				FROM
				(
					SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DateBuild,
					T1.Machine,T1.Act,T1.ItemNumber,T1.Qty1,T1.Qty2,T1.Qty3,
					T1.RatePrice1,T1.RatePrice2,T1.RatePrice3,T1.RateType,
					CASE WHEN T1.SCH IS NULL THEN 0 ELSE T1.SCH END AS SCH,T1.PLY,
					ROW_NUMBER() OVER(partition by T1.Machine ORDER BY T1.Act DESC) AS Row
					FROM
					(
						SELECT I.CreateBy,U.EmployeeID,U.Name,
						CONVERT(DATE,I.DateBuild) DateBuild,
						I.BuildingNo Machine,COUNT(I.Barcode) Act,G.ItemNumber,
						R.Qty1,R.Qty2,R.Qty3,
						R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
						S.Total SCH,R.PLY
						FROM InventTable I
						JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
						LEFT JOIN RateBuildSchedule S ON I.BuildingNo = S.Machine
						AND S.Active = 1
						AND CONVERT(DATE,I.DateBuild) = CONVERT(DATE,S.DateRateBuild)
						JOIN UserMaster U ON I.CreateBy = U.ID
						JOIN RateMaster R ON I.BuildingNo = R.Machine
						WHERE I.DateBuild BETWEEN ? AND ?
						AND I.CheckBuild = 1 AND I.BuildingNo = ?
						GROUP BY I.BuildingNo,G.ItemNumber,S.Total,I.CreateBy,
						CONVERT(DATE,I.DateBuild),U.EmployeeID,U.Name,R.Qty1,
						R.Qty2,R.Qty3,R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,R.PLY
					)T1 JOIN
					(
						SELECT T1.ItemGT,T1.PLY
						FROM(
							SELECT ItemGT,PLY,
							ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY) AS Row
							FROM ItemPLY
						)T1
						WHERE T1.Row=1
					)P ON T1.ItemNumber = P.ItemGT AND P.PLY = T1.PLY
				)T2 JOIN
				(
					SELECT I.BuildingNo,COUNT(I.Barcode) ACT
					FROM InventTable I
					WHERE I.DateBuild BETWEEN ? AND ?
					AND I.CheckBuild = 1 AND I.BuildingNo = ?
					GROUP BY I.BuildingNo
				)T3 ON T2.Machine = T3.BuildingNo
				WHERE T2.Row = 1
			)T4
			GROUP BY T4.Machine
			",
			[
				$tstart,
				$tend,
				$machine,
				$tstart,
				$tend,
				$machine
			]
		);
		if ($query) {
			return $query[0]['COUNT_USER'];
		} else {
			return 0;
		}
	}

	public function countUser_ALLLine($tstart, $tend, $lineno)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT SUM(COUNT_USER) usertotal_line
			FROM (
				SELECT ID AS Machine,
				CASE WHEN T3.COUNT_USER IS NULL THEN 0 ELSE
				T3.COUNT_USER END AS COUNT_USER
				FROM  BuildingMaster BM LEFT JOIN
				(
					SELECT T2.Machine,T2.COUNT_USER
							FROM (
								SELECT T1.Machine,
									COUNT(T1.CreateBy) OVER (PARTITION BY T1.Machine) COUNT_USER
									FROM (
										SELECT t.CreateBy,t.Machine,t.BuildTypeId,t.Name,t.EmployeeID,
										t.BuildType,t.Act,t.LoginDate,t.LogoutDate,SUM(t.SCH) SCH,
										ROW_NUMBER() OVER(partition by t.CreateBy ORDER BY t.LoginDate DESC) AS Row
										FROM
										(
											SELECT B.CreateBy,B.Machine,R.BuildTypeId,REPLACE(U.Name, 'null', '') Name,
											U.EmployeeID,T.Description BuildType,COUNT(B.Barcode) Act,R.LoginDate,
											R.LogoutDate,
											CASE WHEN S.Total IS NULL THEN 0
											ELSE S.Total
											END AS 'SCH'
											FROM BuildTrans B JOIN
											RateTrans R ON B.CreateBy = R.UserId JOIN
											UserMaster U ON B.CreateBy = U.ID JOIN
											BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId JOIN
											InventTable I ON B.Barcode = I.Barcode LEFT JOIN
											RateBuildSchedule S ON B.Machine = S.Machine AND R.Shift = S.Shift AND
											CONVERT(DATE,R.LoginDate) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
											WHERE B.CreateDate BETWEEN ? AND ?
											AND R.LoginDate BETWEEN ? AND ?
											AND I.CheckBuild = 1 AND LEFT(B.Machine,1) = LEFT(?,1)
											AND R.RateGroupID = 1
											GROUP BY B.Machine,B.CreateBy,R.LoginDate,R.BuildTypeId,
											U.Name,U.EmployeeID,T.Description,R.LogoutDate,S.Total
										)t
										GROUP BY t.CreateBy,t.Machine,t.BuildTypeId,t.Name,t.EmployeeID,
										t.BuildType,t.Act,t.LoginDate,t.LogoutDate
								)T1 JOIN
								RateMaster R ON T1.Machine = R.Machine AND T1.BuildTypeId = R.BuildTypeId
								WHERE T1.Row = 1 AND (DATEDIFF(HOUR,T1.LoginDate,T1.LogoutDate) >= 10 OR T1.Act >= T1.SCH)
								GROUP BY T1.Machine,T1.CreateBy
							)T2 GROUP BY T2.Machine,T2.COUNT_USER
				)T3 ON BM.ID = T3.Machine
				WHERE LEFT(BM.ID,1) = LEFT(?,1)
			)T4
			",
			[
				$tstart,
				$tend,
				$tstart,
				$tend,
				$lineno,
				$lineno
			]
		);

		return $query[0]['usertotal_line'];
	}

	public function countUser_ALLLine_PLY($tstart, $tend, $lineno)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(T4.Machine) AS usertotal_line
			FROM
			(
				SELECT T2.CreateBy,T2.EmployeeID,T2.Name,T2.DateBuild,
				T2.Machine,T3.Act,T2.ItemNumber,
				'เส้นที่ 1-'+CONVERT(VARCHAR,T2.Qty1) AS Qty1 ,
				'' Qty2,
				'มากกว่า >'+CONVERT(VARCHAR,T2.Qty1) AS Qty3,
				T2.RatePrice1,T2.RatePrice2,T2.RatePrice3,
				T2.RateType,T2.SCH,
				CASE WHEN T3.Act >= T2.Qty1
				THEN T2.RatePrice1
				ELSE 0 END AS 'P1',
				0 AS 'P2',
				CASE WHEN  T3.Act > T2.Qty2
				THEN ((T3.Act-T2.Qty2) /T2.Qty3)* T2.RatePrice3
				ELSE 0 END AS 'P3',
				T2.PLY
				FROM
				(
					SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DateBuild,
					T1.Machine,T1.Act,T1.ItemNumber,T1.Qty1,T1.Qty2,T1.Qty3,
					T1.RatePrice1,T1.RatePrice2,T1.RatePrice3,T1.RateType,
					CASE WHEN T1.SCH IS NULL THEN 0 ELSE T1.SCH END AS SCH,T1.PLY,
					ROW_NUMBER() OVER(partition by T1.Machine ORDER BY T1.Act DESC) AS Row
					FROM
					(
						SELECT I.CreateBy,U.EmployeeID,U.Name,
						CONVERT(DATE,I.DateBuild) DateBuild,
						I.BuildingNo Machine,COUNT(I.Barcode) Act,G.ItemNumber,
						R.Qty1,R.Qty2,R.Qty3,
						R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
						S.Total SCH,R.PLY
						FROM InventTable I
						JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
						LEFT JOIN RateBuildSchedule S ON I.BuildingNo = S.Machine AND S.Active = 1
						AND CONVERT(DATE,I.DateBuild) = CONVERT(DATE,S.DateRateBuild)
						JOIN UserMaster U ON I.CreateBy = U.ID
						JOIN RateMaster R ON I.BuildingNo = R.Machine
						WHERE I.DateBuild BETWEEN ? AND ?
						AND I.CheckBuild = 1 AND LEFT(I.BuildingNo,1) = LEFT(?,1)
						GROUP BY I.BuildingNo,G.ItemNumber,S.Total,I.CreateBy,
						CONVERT(DATE,I.DateBuild),U.EmployeeID,U.Name,R.Qty1,
						R.Qty2,R.Qty3,R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,R.PLY
					)T1 JOIN
					(
						SELECT T1.ItemGT,T1.PLY
						FROM(
							SELECT ItemGT,PLY,
							ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY) AS Row
							FROM ItemPLY
						)T1
						WHERE T1.Row=1
					)P ON T1.ItemNumber = P.ItemGT AND P.PLY = T1.PLY
				)T2 JOIN
				(
					SELECT I.BuildingNo,COUNT(I.Barcode) ACT
					FROM InventTable I
					WHERE I.DateBuild BETWEEN ? AND ?
					AND I.CheckBuild = 1 AND LEFT(I.BuildingNo,1) = LEFT(?,1)
					GROUP BY I.BuildingNo
				)T3 ON T2.Machine = T3.BuildingNo
				WHERE T2.Row = 1
			)T4
			",
			[
				$tstart,
				$tend,
				$lineno,
				$tstart,
				$tend,
				$lineno
			]
		);

		return $query[0]['usertotal_line'];
	}

	public function ActBuildServicepdf($tstart, $tend)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T5.*,T5.Act-T5.SCARP_MAC AS TOTAL
			FROM
			(
				SELECT T3.*,COUNT(T4.Barcode) SCARP_MAC
				FROM
				(
					SELECT T1.CreateBy,T1.EmployeeID,T1.Name,
					T1.Machine,
					CASE
						WHEN T2.Act IS NULL THEN 0
						ELSE T2.Act
					END AS Act,
					T1.BuildTypeId,T1.BuildType,
					CONVERT(VARCHAR(10), T1.LoginDate, 103) + ' '  + convert(VARCHAR(8), T1.LoginDate, 14) LoginDate,
					CONVERT(VARCHAR(10), T1.LogoutDate, 103) + ' '  + convert(VARCHAR(8), T1.LogoutDate, 14) LogoutDate,
					T1.Row,T1.Shift,SUM(S.Total) AS 'SCH'
					FROM (
						SELECT R.UserId AS CreateBy,R.Machine,R.BuildTypeId,REPLACE(U.Name,'null','') Name ,U.EmployeeID,T.Description BuildType,
						ROW_NUMBER() OVER(partition by R.UserId ORDER BY R.LoginDate DESC) AS Row,
						R.LoginDate,R.LogoutDate,R.Shift,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 OR
								 CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) >= 20
								THEN '2'
							ELSE '1'
						END AS SS,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
								THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
							ELSE CONVERT(DATE,R.LoginDate)
						END AS DAAY
						FROM RateTrans R JOIN
						UserMaster U ON R.UserId = U.ID JOIN
						BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
						WHERE R.LoginDate BETWEEN ? AND ?
						AND R.RateGroupID = 1
					)T1 LEFT JOIN
					(
						SELECT B.CreateBy,
						COUNT(B.Barcode) 'Act'
						FROM BuildTrans B
						JOIN InventTable I ON B.Barcode = I.Barcode AND I.CheckBuild = 1
						WHERE B.CreateDate BETWEEN ? AND ?
						GROUP BY B.CreateBy
					)T2 ON T1.CreateBy =  T2.CreateBy LEFT JOIN
					RateBuildSchedule S ON T1.Machine = S.Machine AND T1.SS = S.Shift
					AND CONVERT(DATE,T1.DAAY) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
					WHERE T1.Row = 1
					GROUP BY T1.CreateBy,T1.EmployeeID,T1.Name,
					T1.Machine,T2.Act,T1.BuildTypeId,T1.BuildType,
					CONVERT(VARCHAR(10), T1.LoginDate, 103),
					CONVERT(VARCHAR(10), T1.LogoutDate, 103),
					convert(VARCHAR(8), T1.LoginDate, 14),
					convert(VARCHAR(8), T1.LogoutDate, 14),
					T1.Row,T1.Shift,T1.LoginDate
				) T3
				 LEFT JOIN
				(
					SELECT E.Id,E.Barcode,E.CreateDate,E.CreateBy,I.DateBuild,I.BuildingNo
					FROM ExceptBuildRate E JOIN
					InventTable I ON E.Barcode = I.Barcode
					WHERE E.CreateDate BETWEEN ? AND ?
				) T4 ON T3.Machine = T4.BuildingNo
				GROUP BY T3.CreateBy,T3.EmployeeID,T3.Name,T3.Machine,T3.Act,T3.BuildTypeId,
				T3.BuildType,T3.LoginDate,T3.LogoutDate,T3.Row,T3.Shift,T3.SCH
			)T5
			ORDER BY Machine,BuildTypeId,LoginDate
			",
			[
				// $tstart,
				// $tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend
			]
		);

		return $query;
	}

	public function getMachineByDate($tstart, $tend)
	{

		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT R.Machine,M.Type
			FROM RateTrans R JOIN
			BuildingMaster M ON R.Machine = M.ID
			WHERE R.LoginDate BETWEEN ? AND ?
			AND R.Machine NOT IN ('ZS4','S4')
			GROUP BY R.Machine,M.Type
			ORDER BY Machine",
			[
				$tstart,
				$tend
			]
		);
		return $query;
	}

	public function GreentireInventoryServiceallpdf()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY CodeID ASC) AS Row
				,CodeID
				,SUM(onhand)[onhand]
				,SUM(hold)[hold]
				,SUM(repair)[repair]
				,Batch
		FROM(
		SELECT	O.CodeID,
				(SELECT O.QTY WHERE O.LocationID=2)[onhand],
				(SELECT O.QTY WHERE O.LocationID=9)[hold],
				(SELECT O.QTY WHERE O.LocationID=10)[repair],
				O.Batch
		FROM Onhand O
		WHERE O.WarehouseID =1 AND O.QTY IS NOT NULL AND O.QTY!=0
		)Z
		GROUP BY
		Z.CodeID, Z.Batch", [$dateinter]);
	}

	public function GreentireInventoryServiceallpdfwarehousesent($shift, $time, $counttime, $datewarehouse)
	{
		$conn = Database::connect();
		if ($shift == 'day') {

			if ($counttime == 1) {
				if ($time == 1) {
					$timeto = $datewarehouse . ' 08:00';
					$timefrom = $datewarehouse . ' 11:00';
				} elseif ($time == 2) {
					$timeto = $datewarehouse . ' 11:00';
					$timefrom = $datewarehouse . ' 14:00';
				} elseif ($time == 3) {
					$timeto = $datewarehouse . ' 14:00';
					$timefrom = $datewarehouse . ' 17:00';
				} elseif ($time == 4) {
					$timeto = $datewarehouse . ' 17:00';
					$timefrom = $datewarehouse . ' 20:00';
				}
			} elseif ($counttime == 2) {
				if ($time == '1,2') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 11:00';
					$timefrom2 = $datewarehouse . ' 14:00';
				} elseif ($time == '1,3') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 14:00';
					$timefrom2 = $datewarehouse . ' 17:00';
				} elseif ($time == '1,4') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 17:00';
					$timefrom2 = $datewarehouse . ' 20:00';
				} elseif ($time == '2,3') {
					$timeto1 = $datewarehouse . ' 11:00';
					$timefrom1 = $datewarehouse . ' 14:00';
					$timeto2 = $datewarehouse . ' 14:00';
					$timefrom2 = $datewarehouse . ' 17:00';
				} elseif ($time == '2,4') {
					$timeto1 = $datewarehouse . ' 11:00';
					$timefrom1 = $datewarehouse . ' 14:00';
					$timeto2 = $datewarehouse . ' 17:00';
					$timefrom2 = $datewarehouse . ' 20:00';
				} elseif ($time == '3,4') {
					$timeto1 = $datewarehouse . ' 14:00';
					$timefrom1 = $datewarehouse . ' 17:00';
					$timeto2 = $datewarehouse . ' 17:00';
					$timefrom2 = $datewarehouse . ' 20:00';
				}
			} elseif ($counttime == 3) {
				if ($time == '1,2,3') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 11:00';
					$timefrom2 = $datewarehouse . ' 14:00';
					$timeto3 = $datewarehouse . ' 14:00';
					$timefrom3 = $datewarehouse . ' 17:00';
				} elseif ($time == '1,2,4') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 11:00';
					$timefrom2 = $datewarehouse . ' 14:00';
					$timeto3 = $datewarehouse . ' 17:00';
					$timefrom3 = $datewarehouse . ' 20:00';
				} elseif ($time == '1,3,4') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 14:00';
					$timefrom2 = $datewarehouse . ' 17:00';
					$timeto3 = $datewarehouse . ' 17:00';
					$timefrom3 = $datewarehouse . ' 20:00';
				} elseif ($time == '2,3,4') {
					$timeto1 = $datewarehouse . ' 11:00';
					$timefrom1 = $datewarehouse . ' 14:00';
					$timeto2 = $datewarehouse . ' 14:00';
					$timefrom2 = $datewarehouse . ' 17:00';
					$timeto3 = $datewarehouse . ' 17:00';
					$timefrom3 = $datewarehouse . ' 20:00';
				}
			} elseif ($counttime == 4) {
				if ($time == '1,2,3,4') {
					$timeto1 = $datewarehouse . ' 08:00';
					$timefrom1 = $datewarehouse . ' 11:00';
					$timeto2 = $datewarehouse . ' 11:00';
					$timefrom2 = $datewarehouse . ' 14:00';
					$timeto3 = $datewarehouse . ' 14:00';
					$timefrom3 = $datewarehouse . ' 17:00';
					$timeto4 = $datewarehouse . ' 17:00';
					$timefrom4 = $datewarehouse . ' 20:00';
				}
			}

			if ($counttime == 1) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY", [$timeto, $timefrom]);
			} elseif ($counttime == 2) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY", [$timeto1, $timefrom1, $timeto2, $timefrom2]);
			} elseif ($counttime == 3) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY", [$timeto1, $timefrom1, $timeto2, $timefrom2, $timeto3, $timefrom3]);
			} elseif ($counttime == 4) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE
					 T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY", [$timeto1, $timefrom1, $timeto2, $timefrom2, $timeto3, $timefrom3, $timeto4, $timefrom4]);
			}
		} else if ($shift == 'night') {
			$datenight = str_replace('-', '/', $datewarehouse);
			$datewarehousenight = date('Y-m-d', strtotime($datenight . "+1 days"));
			$timeto_n = "20:00";
			$timefrom_n = "08:00";
			return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE CONVERT(date,T.WarehouseTransReceiveDate) BETWEEN ? AND ?
					AND CONVERT(time,T.WarehouseTransReceiveDate) >= ? AND CONVERT(time,T.WarehouseTransReceiveDate) <= ?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY", [$datewarehouse, $datewarehousenight, $timeto_n, $timefrom_n]);
		}
	}

	public function GreentireInventoryServiceallpdfwarehouserecive($shift, $time, $datewarehouse, $brand)
	{
		return json_encode([]);
		exit;
		$conn = Database::connect();
		if ($shift == 'day') {

			if ($counttime == 1) {
				if ($time == 1) {
					$timeto = $datewarehouse . " 08:00:00.000";
					$timefrom = $datewarehouse . " 11:00:00.000";
				} elseif ($time == 2) {
					$timeto = $datewarehouse . " 11:00:00.000";
					$timefrom = $datewarehouse . " 14:00:00.000";
				} elseif ($time == 3) {
					$timeto = $datewarehouse . " 14:00:00.000";
					$timefrom = $datewarehouse . " 17:00:00.000";
				} elseif ($time == 4) {
					$timeto = $datewarehouse . " 17:00:00.000";
					$timefrom = $datewarehouse . " 20:00:00.000";
				}
			} elseif ($counttime == 2) {
				if ($time == '1,2') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 11:00:00.000";
					$timefrom2 = $datewarehouse . " 14:00:00.000";
				} elseif ($time == '1,3') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 14:00:00.000";
					$timefrom2 = $datewarehouse . " 17:00:00.000";
				} elseif ($time == '1,4') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 17:00:00.000";
					$timefrom2 = $datewarehouse . " 20:00:00.000";
				} elseif ($time == '2,3') {
					$timeto1 = $datewarehouse . " 11:00:00.000";
					$timefrom1 = $datewarehouse . " 14:00:00.000";
					$timeto2 = $datewarehouse . " 14:00:00.000";
					$timefrom2 = $datewarehouse . " 17:00:00.000";
				} elseif ($time == '2,4') {
					$timeto1 = $datewarehouse . " 11:00:00.000";
					$timefrom1 = $datewarehouse . " 14:00:00.000";
					$timeto2 = $datewarehouse . " 17:00:00.000";
					$timefrom2 = $datewarehouse . " 20:00:00.000";
				} elseif ($time == '3,4') {
					$timeto1 = $datewarehouse . " 14:00:00.000";
					$timefrom1 = $datewarehouse . " 17:00:00.000";
					$timeto2 = $datewarehouse . " 17:00:00.000";
					$timefrom2 = $datewarehouse . " 20:00:00.000";
				}
			} elseif ($counttime == 3) {
				if ($time == '1,2,3') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 11:00:00.000";
					$timefrom2 = $datewarehouse . " 14:00:00.000";
					$timeto3 = $datewarehouse . " 14:00:00.000";
					$timefrom3 = $datewarehouse . " 17:00:00.000";
				} elseif ($time == '1,2,4') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 11:00:00.000";
					$timefrom2 = $datewarehouse . " 14:00:00.000";
					$timeto3 = $datewarehouse . " 17:00:00.000";
					$timefrom3 = $datewarehouse . " 20:00:00.000";
				} elseif ($time == '1,3,4') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 14:00:00.000";
					$timefrom2 = $datewarehouse . " 17:00:00.000";
					$timeto3 = $datewarehouse . " 17:00:00.000";
					$timefrom3 = $datewarehouse . " 20:00:00.000";
				} elseif ($time == '2,3,4') {
					$timeto1 = $datewarehouse . " 11:00:00.000";
					$timefrom1 = $datewarehouse . " 14:00:00.000";
					$timeto2 = $datewarehouse . " 14:00:00.000";
					$timefrom2 = $datewarehouse . " 17:00:00.000";
					$timeto3 = $datewarehouse . " 17:00:00.000";
					$timefrom3 = $datewarehouse . " 20:00:00.000";
				}
			} elseif ($counttime == 4) {
				if ($time == '1,2,3,4') {
					$timeto1 = $datewarehouse . " 08:00:00.000";
					$timefrom1 = $datewarehouse . " 11:00:00.000";
					$timeto2 = $datewarehouse . " 11:00:00.000";
					$timefrom2 = $datewarehouse . " 14:00:00.000";
					$timeto3 = $datewarehouse . " 14:00:00.000";
					$timefrom3 = $datewarehouse . " 17:00:00.000";
					$timeto4 = $datewarehouse . " 17:00:00.000";
					$timefrom4 = $datewarehouse . " 20:00:00.000";
				}
			}

			if ($counttime == 1) {
				return Sqlsrv::queryJson(
					$conn,
					"SELECT
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
						,Pattern
					FROM(
						SELECT T.ItemID
						,T.CuringCode
						,I.NameTH
						,T.Batch
						,T.QTY
						,B.BrandID
						,I.Brand
						,I.Pattern
						FROM InventTable T
						LEFT JOIN ItemMaster I ON T.ItemID=I.ID
						LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
						WHERE T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
						AND B.BrandID IN ($brand)
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto, $timefrom]
				);
			} elseif ($counttime == 2) {
				return Sqlsrv::queryJson(
					$conn,
					"SELECT
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
						,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?

					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1, $timefrom1, $timeto2, $timefrom2]
				);
			} elseif ($counttime == 3) {
				return Sqlsrv::queryJson(
					$conn,
					"SELECT
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
					,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE
					T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?

					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1, $timefrom1, $timeto2, $timefrom2, $timeto3, $timefrom3]
				);
			} elseif ($counttime == 4) {
				return Sqlsrv::queryJson(
					$conn,
					"SELECT
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
					,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE
					T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?

					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1, $timefrom1, $timeto2, $timefrom2, $timeto3, $timefrom3, $timeto4, $timefrom4]
				);
			}
		} else if ($shift == 'night') {
			$datenight = str_replace('-', '/', $datewarehouse);
			$datewarehousenight = date('Y-m-d', strtotime($datenight . "+1 days"));
			$timeto_n = "20:00:00.000";
			$timefrom_n = "08:00:00.000";
			$date_1 = $datewarehouse . " " . $timeto_n;
			$date_2 = $datewarehousenight . " " . $timefrom_n;
			return Sqlsrv::queryJson(
				$conn,
				"SELECT
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
					FROM(
					SELECT T.ItemID
					,T.CuringCode
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE T.WarehouseReceiveDate BETWEEN ? AND ?
					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,I.Brand
					,I.Pattern",
				[$date_1, $date_2]
			);
		}
	}

	public function CuringServiceallpresspdf($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();

		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT *
			FROM(
				SELECT
				S1.CuringTime [S1_CuringTime],
				S1.Row [S1_Row],
				S1.TemplateSerialNo [S1_TemplateSerialNo],
				S1.Barcode [S1_Barcode],
				S1.Description [S1_Description],
				S1.CuringCode [S1_CuringCode],
				S1.PressSide [S1_PressSide],
				S2.CuringTime [S2_CuringTime],
				S2.Row [S2_Row],
				S2.TemplateSerialNo [S2_TemplateSerialNo],
				S2.Barcode [S2_Barcode],
				S2.Description [S2_Description],
				S2.CuringCode [S2_CuringCode],
				S2.PressSide [S2_PressSide]
			FROM
			(

			SELECT CONVERT(time,I.CuringDate) CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='L' AND I.PressNo=?
				GROUP BY
					I.CuringDate
					-- CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S1

			FULL JOIN

			(
			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='R' AND I.PressNo=?
				GROUP BY
				I.CuringDate
					-- CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S2

			ON S1.Row = S2.Row
			)TABLE1", [$date1, $date2, $press, $date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT *
			FROM(
			SELECT
			S1.CuringTime [S1_CuringTime],
			S1.Row [S1_Row],
			S1.TemplateSerialNo [S1_TemplateSerialNo],
			S1.Barcode [S1_Barcode],
			S1.Description [S1_Description],
			S1.CuringCode [S1_CuringCode],
			S1.PressSide [S1_PressSide],
			S2.CuringTime [S2_CuringTime],
			S2.Row [S2_Row],
			S2.TemplateSerialNo [S2_TemplateSerialNo],
			S2.Barcode [S2_Barcode],
			S2.Description [S2_Description],
			S2.CuringCode [S2_CuringCode],
			S2.PressSide [S2_PressSide]
			FROM
			(

			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='L' AND I.PressNo=?
				GROUP BY
					CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S1

			FULL JOIN

			(
			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='R' AND I.PressNo=?
				GROUP BY
					CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S2

			ON S1.Row = S2.Row
			)TABLE1", [$date3, $date4, $press, $date3, $date4, $press]);
		}
	}

	public function CuringServiceallpresspdfGTL($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';

		$conn = Database::connect();
		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='L' AND PressNo=?
			GROUP BY GT_Code", [$date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			-- WHERE CuringDate BETWEEN '2017-05-04 20:00:00' AND '2017-05-05 08:00:00'
			AND PressSide='L' AND PressNo=?
			GROUP BY GT_Code", [$date3, $date4, $press]);
		}
	}

	public function CuringServiceallpresspdfGTR($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY GT_Code", [$date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY GT_Code", [$date3, $date4, $press]);
		}
	}

	public function CuringServiceallpresspdfweekly($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT Batch
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ? AND PressNo=?
			GROUP BY Batch", [$date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT Batch
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ? AND PressNo=?
			GROUP BY Batch", [$date3, $date4, $press]);
		}
	}

	public function CuringServiceallpresspdfCurcodeL($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='L' AND PressNo=?
			GROUP BY CuringCode", [$date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='L' AND PressNo=?
			GROUP BY CuringCode", [$date3, $date4, $press]);
		}
	}

	public function CuringServiceallpresspdfCurcodeR($datecuring, $press, $shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d', strtotime($datenight . "+1 days"));
		$date1 = $datecuring . ' 08:00:01';
		$date2 = $datecuring . ' 20:00:00';
		$date3 = $datecuring . ' 20:00:01';
		$date4 = $datecuringnight . ' 08:00:00';
		$conn = Database::connect();
		if ($shift == "day") {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY CuringCode", [$date1, $date2, $press]);
		} else {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode
			FROM InventTable
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY CuringCode", [$date3, $date4, $press]);
		}
	}

	public function curingAx($date, $shift, $product_group, $pressBOI)
	{
		$date_today = date('Y-m-d', strtotime($date));
		$date_tomorrow = date('Y-m-d', strtotime($date . '+1 days'));
		// return [$date_today, $date_tomorrow]; exit;
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}

		$conn = Database::connect();

		$sql_day = "SELECT *,
		T1.Q1 *T1.NetWeight AS WeightQ1,
		T1.Q2 *T1.NetWeight AS WeightQ2,
		T1.Q3 *T1.NetWeight AS WeightQ3,
		T1.Q4 *T1.NetWeight AS WeightQ4
		
		
		 FROM
		(
		
			SELECT
			IT.Barcode,
			IT.CuringCode,
			--CC.ID AS CuringCode,
			ITS.CodeID as ItemNo,
			IT.CuringDate,
			KC.Total,
			(
				SELECT
					CASE
						WHEN ITS.CreateDate BETWEEN  '$date_today 08:00:01' AND  '$date_today 11:00:00' THEN 1
						ELSE 0
					END
			) as Q1,
			(
				SELECT
					CASE
						WHEN ITS.CreateDate BETWEEN  '$date_today 11:00:01' AND  '$date_today 14:00:00' THEN 1
						ELSE 0
					END
			) as Q2,
			(
				SELECT
					CASE
						WHEN ITS.CreateDate BETWEEN  '$date_today 14:00:01' AND  '$date_today 17:00:00' THEN 1
						ELSE 0
					END
			) as Q3,
			(
				SELECT
					CASE
						WHEN ITS.CreateDate BETWEEN '$date_today 17:00:01' AND '$date_today 20:00:00' THEN 1
						ELSE 0
					END
			) as Q4,
			(
				SELECT
					CASE
						WHEN IT.FinalReceiveDate IS NOT NULL THEN 1
						ELSE 0
					END
			) as RECEIVED_ALL,
			
			cast(IM.NetWeight as int) as NetWeight
			FROM InventTable IT
			LEFT JOIN InventTrans ITS
				ON IT.Barcode = ITS.Barcode
				AND IT.CuringDate = ITS.CreateDate
				AND ITS.DocumentTypeID = 1
			LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN KeepCuring KC ON IT.CuringCode = KC.CuringCode AND ITS.CodeID = KC.ItemNo AND KC.CuringDate = '$date_today' AND KC.Shift = '1'
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			LEFT JOIN ItemMaster IM ON IM.ID = ITS.CodeID
			--LEFT JOIN CureCodeMaster CC ON CC.ItemQ = ITS.CodeID
			WHERE IT.CuringDate IS NOT NULL			
			AND IT.CuringDate <> ''
			AND IT.CuringDate between '$date_today  08:00:01' and '$date_today 20:00:00'
			AND IT.CheckBuild = 1
			$whereBOI
			--AND CB.BOI = '$pressBOI'
			AND IT.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			AND CONVERT(date, IT.CuringDate) = ?
			)T1
			ORDER BY T1.ItemNo ASC";

		// return $sql_day;

		if ($shift === 'day') {
			return Sqlsrv::queryJson(
				$conn,
				$sql_day,
				[
					$product_group,
					$date
				]
			);
		} else {
			// return $date_tomorrow;exit();
			// night shift
			$sql_night = "SELECT *,
			T1.Q1 *T1.NetWeight AS WeightQ1,
			T1.Q2 *T1.NetWeight AS WeightQ2,
			T1.Q3 *T1.NetWeight AS WeightQ3,
			T1.Q4 *T1.NetWeight AS WeightQ4
			
			
			 FROM
			(
				SELECT
				IT.Barcode,
				IT.CuringCode,
				--CC.ID AS CuringCode,
				ITS.CodeID as ItemNo,
				IT.CuringDate,
				KC.Total,
				(
					SELECT
						CASE
							WHEN ITS.CreateDate BETWEEN '$date_today 20:00:01' AND '$date_today 23:00:00' THEN 1
							ELSE 0
						END
				) as Q1,
				(
					SELECT
						CASE
							WHEN ITS.CreateDate BETWEEN '$date_today 23:00:01' AND '$date_tomorrow 02:00:00' THEN 1
							ELSE 0
						END
				) as Q2,
				(
					SELECT
						CASE
							WHEN ITS.CreateDate BETWEEN '$date_tomorrow 02:00:01' AND '$date_tomorrow 05:00:00' THEN 1
							ELSE 0
						END
				) as Q3,
				(
					SELECT
						CASE
							WHEN ITS.CreateDate BETWEEN '$date_tomorrow 05:00:01' AND '$date_tomorrow 08:00:00' THEN 1
							ELSE 0
						END
				) as Q4,
				(
					SELECT
						CASE
							WHEN IT.FinalReceiveDate IS NOT NULL THEN 1
							ELSE 0
						END
				) as RECEIVED_ALL,
				cast(IM.NetWeight as int) as NetWeight
				FROM InventTable IT
				LEFT JOIN InventTrans ITS
					ON IT.Barcode = ITS.Barcode
					AND IT.CuringDate = ITS.CreateDate
					AND ITS.DocumentTypeID = 1
				LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
				LEFT JOIN KeepCuring KC ON IT.CuringCode = KC.CuringCode AND ITS.CodeID = KC.ItemNo AND KC.CuringDate = '$date_today' AND KC.Shift = '2'
				LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
				LEFT JOIN ItemMaster IM ON IM.ID = ITS.CodeID
				--LEFT JOIN CureCodeMaster CC ON CC.ItemQ = ITS.CodeID
				WHERE IT.CuringDate IS NOT NULL
				AND IT.CheckBuild = 1
				AND IT.CuringDate <> ''
				$whereBOI
				AND IT.CuringDate between '$date_today 20:00:01' and '$date_tomorrow 08:00:00'
				AND IT.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = IT.GT_Code
					AND IM.ProductGroup = ?
				)
				-- AND IT.CuringDate between '$date_today 20:00:01' and '$date_tomorrow 08:00:00'
				)T1
				--WHERE T1.Total is not null
				ORDER BY T1.ItemNo ASC";

			return Sqlsrv::queryJson(
				$conn,
				$sql_night,
				[
					$product_group
				]
			);
		}
	}

	public function CureInventoryServiceallpdf($product_group, $pressBOI)
	{
		$conn = Database::connect();
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		return Sqlsrv::queryJson(
			$conn,
			"SELECT ROW_NUMBER() OVER(ORDER BY CuringDate ASC) AS Row
					,CuringDate
					,CuringCode
					,PressNo
					,PressSide
					,TemplateSerialNo
					,Barcode
				 	,checkcur
					,Batch
			FROM(
			SELECT	I.CuringDate
					,I.CuringCode
					,I.PressNo
					,I.PressSide
					,I.TemplateSerialNo
					,I.Barcode
					,I.CuredTireReciveDate
					,I.Batch
					,case when I.CuredTireReciveDate is null then ''
		            when I.CuredTireReciveDate IS not null then '/'
		             end [checkcur]
			FROM InventTable I
			LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = I.Barcode
			WHERE I.FinalReceiveDate IS NULL
			AND I.Status=1 AND I.WarehouseID=4
			AND I.CuringDate IS NOT NULL
			$whereBOI
			--AND CB.BOI = '$pressBOI'
			AND I.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = I.GT_Code
				AND IM.ProductGroup = ?
			)
			)Z
			GROUP BY
			Z.CuringDate,
			Z.CuringCode,
			Z.PressNo,
			Z.PressSide,
			Z.TemplateSerialNo,
			Z.Barcode,
			Z.checkcur,
			Z.Batch ORDER BY Z.CuringDate ASC",
			[
				$product_group
			]
		);
	}

	public function greentireInventoryV2($product_group, $pressBOI)
	{

		$conn = Database::connect();
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			A.Batch, A.GT_Code,  SUM(A.hold) [hold], SUM(A.repair) [repair], SUM(A.onhand) [onhand],A.DateBuild
			from (
			--Onhand
			select IT.Batch, IT.GT_Code,  0 [hold], 0 [repair], IT.QTY [onhand],CONVERT(VARCHAR(10),IT.DateBuild, 105) As DateBuild
			from InventTable IT
			LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			where IT.WarehouseID = 1
			and IT.LocationID = 2
			and DisposalID <> 11
			and Status <> 4
			and IT.CheckBuild = 1
			$whereBOI
			AND IT.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Hold
			select IT.Batch, IT.GT_Code, IT.QTY [hold], 0 [repair], 0 [onhand],CONVERT(VARCHAR(10),IT.DateBuild, 105) As DateBuild
			from InventTable IT
			LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			where IT.WarehouseID = 1
			--and IT.LocationID = 4
			and DisposalID  IN (9,10)
			and Status = 5
			and IT.CheckBuild = 1
			$whereBOI
			AND IT.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Repair
			select IT.Batch, IT.GT_Code, 0 [hold],  IT.QTY [repair],0 [onhand],CONVERT(VARCHAR(10),IT.DateBuild, 105) As DateBuild
			from InventTable IT
			LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			where IT.WarehouseID = 1
			and DisposalID  = 12
			and Status = 5
			and IT.CheckBuild = 1
			$whereBOI
			AND IT.GT_Code IN
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			) A
			group by A.Batch, A.GT_Code,A.DateBuild
			order by A.GT_Code, A.Batch,A.DateBuild asc",
			[
				$product_group,
				$product_group,
				$product_group
			]
		);
	}

	public function WIPServiceallpdf($product_group, $pressBOI)
	{
		// edit by harit 1/2/18
		$conn = Database::connect();


		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			A.Batch,
			A.CureCode,
			A.NameTH,
			SUM(A.hold) [hold],
			SUM(A.repair) [repair],
			SUM(A.onhand) [onhand],
			SUM(A.retur) [return]
			FROM (
			--Onhand
			SELECT
				IT.Batch,
				CCM.ID [CureCode],
				IM.NameTH,
				0 [hold],
				0 [repair],
				IT.QTY [onhand],
				0 [retur]
			from InventTable IT
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			left join PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			where IT.WarehouseID = 2
			and IT.LocationID = 4
			and DisposalID <> 11
			and Status <> 4
			AND IT.SendSVODate is null
			AND IM.ProductGroup = ?
			$whereBOI
			--AND CB.BOI = '$pressBOI'

			union all

			-- Hold
			SELECT
				IT.Batch,
				CCM.ID [CureCode],
				IM.NameTH,
				IT.QTY [hold],
				0 [repair],
				0 [onhand],
				0 [retur]
			from InventTable IT
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			left join PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			where IT.WarehouseID = 2
			--and IT.LocationID = 4
			and DisposalID  = 10
			and Status = 5
			AND IT.SendSVODate is null
			AND IM.ProductGroup = ?
			$whereBOI

			union all

			-- Repair
			SELECT
				IT.Batch,
				CCM.ID [CureCode],
				IM.NameTH,
				0 [hold],
				IT.QTY [repair],
				0 [onhand],
				0 [retur]
			from InventTable IT
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			left join PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			where IT.WarehouseID = 2
			and DisposalID  = 12
			and Status = 5
			AND IT.SendSVODate is null
			AND IM.ProductGroup = ?
			$whereBOI

			union all
			-- Return
			SELECT
				IT.Batch,
				CCM.ID [CureCode],
				IM.NameTH,
				0 [hold],
				0 [repair],
				0 [onhand],
				IT.QTY [retur]
			from InventTable IT
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			left join PressMaster PM ON PM.ID = IT.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
			where IT.WarehouseID = 2
			and DisposalID  = 9
			and Status = 5
			AND IT.SendSVODate is null
			AND IM.ProductGroup = ?
			$whereBOI

			) A
			group by A.Batch, A.CureCode, A.NameTH
			order by A.Batch,A.CureCode ASC",
			[
				$product_group,
				$product_group,
				$product_group,
				$product_group
			]
		);

		// return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY CodeID ASC) AS Row
		// 		,CodeID
		// 		,ID
		// 		,batch
		// 		,item_name
		// 		,SUM(onhand)[onhand]
		// 		,SUM(hold)[hold]
		// 		,SUM(repair)[repair]
		// FROM(
		// SELECT	O.CodeID,
		// 	C.ID,
		// 	O.Batch[batch],
		// 	I.NameTH[item_name],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=4)[onhand],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=11)[hold],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=12)[repair]
		// FROM Onhand O
		// LEFT JOIN CureCodeMaster C ON O.CodeID=C.ItemID
		// LEFT JOIN ItemMaster I ON I.ID = O.CodeID
		// WHERE O.WarehouseID =2 AND O.QTY IS NOT NULL AND O.QTY!=0
		// )Z
		// GROUP BY
		// Z.CodeID,
		// Z.ID,
		// Z.batch,
		// Z.item_name");
	}

	public function cureCodeMasterReport()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			IM.ID AS ITEMID,
			IM.NameTH AS ITEMNAME,
			IM.Pattern AS PATTERN,
			IM.Brand AS BRAND,
			CM.GreentireID AS GTCODE,
			CM.ID AS CURECODE
			FROM ItemMaster IM
			LEFT JOIN CureCodeMaster CM ON IM.ID = CM.ItemID
			WHERE CM.ID IS NOT NULL"
		);
	}

	public function curingPress($date_curing, $press_no, $shift)
	{
		$conn = Database::connect();
		$shift_name = '';
		if ($shift === 'day') {
			// day
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 08:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';

			$shift_name = 'กลางวัน';
		} else {
			// night
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing . '+1 days')) . ' 08:00:00';
			$shift_name = 'กลางคืน';
		}

		$L = Sqlsrv::queryJson(
			$conn,
			"SELECT
			IT.Barcode,
			-- IT.CuringCode,
			IT.CuringDate,
			SM.[Description] AS Shift,
			IT.QTY,
			IT.TemplateSerialNo,
			IT.GT_Code,
			CCM.ID AS CuringCode,
			CASE WHEN IT.FinalReceiveDate IS NULL  THEN '' ELSE '/' END [CHECK_Mustache]
			FROM InventTable IT
			LEFT JOIN InventTrans ITS
				ON IT.Barcode = ITS.Barcode
				AND IT.CuringDate = ITS.CreateDate
				AND ITS.DocumentTypeID = 1
				AND ITS.DisposalID = 3
			LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(ITS.CodeID, 1, 1) = 'Q' THEN REPLACE(ITS.CodeID, 'Q', 'I')
            ELSE ITS.CodeID
        END
        ) = CCM.ItemID  AND CCM.GreentireID = IT.GT_Code
			WHERE IT.PressSide = 'L'
			AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
			AND IT.PressNo = '$press_no'
			AND IT.CuringDate IS NOT NULL
			ORDER BY IT.CuringDate ASC"
		);

		$R = Sqlsrv::queryJson(
			$conn,
			"SELECT
			IT.Barcode,
			-- IT.CuringCode,
			IT.CuringDate,
			SM.[Description] AS Shift,
			IT.QTY,
			IT.TemplateSerialNo,
			IT.GT_Code,
			CCM.ID AS CuringCode,
			CASE WHEN IT.FinalReceiveDate IS NULL  THEN '' ELSE '/' END [CHECK_Mustache]
			FROM InventTable IT
			LEFT JOIN InventTrans ITS
				ON IT.Barcode = ITS.Barcode
				AND IT.CuringDate = ITS.CreateDate
				AND ITS.DocumentTypeID = 1
				AND ITS.DisposalID = 3
			LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
			LEFT JOIN CureCodeMaster CCM ON (
        CASE
            WHEN SUBSTRING(ITS.CodeID, 1, 1) = 'Q' THEN REPLACE(ITS.CodeID, 'Q', 'I')
            ELSE ITS.CodeID
        END
        ) = CCM.ItemID  AND CCM.GreentireID = IT.GT_Code
			WHERE IT.PressSide = 'R'
			AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
			AND IT.PressNo = '$press_no'
			AND IT.CuringDate IS NOT NULL
			ORDER BY IT.CuringDate ASC"
		);

		// $L = Sqlsrv::queryJson(
		// 	$conn,
		// 	"SELECT * , (
		// 		SELECT TOP 1 SM.Description FROM InventTrans ITS
		// 		LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
		// 		WHERE ITS.Barcode = IT.Barcode
		// 		AND ITS.DisposalID = 1
		// 		AND ITS.WarehouseID = 1
		// 		AND ITS.DocumentTypeID = 1
		// 		ORDER BY ITS.CreateDate ASC
		// 	) [Shift]
		// 	FROM InventTable IT
		// 	WHERE IT.PressSide = 'L'
		// 	AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
		// 	AND IT.PressNo = '$press_no'
		// 	AND IT.CuringDate IS NOT NULL
		// 	ORDER BY IT.CuringDate ASC"
		// );

		// $R = Sqlsrv::queryJson(
		// 	$conn,
		// 	"SELECT * , (
		// 		SELECT TOP 1 SM.Description FROM InventTrans ITS
		// 		LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
		// 		WHERE ITS.Barcode = IT.Barcode
		// 		AND ITS.DisposalID = 1
		// 		AND ITS.WarehouseID = 1
		// 		AND ITS.DocumentTypeID = 1
		// 		ORDER BY ITS.CreateDate ASC
		// 	) [Shift]
		// 	FROM InventTable IT
		// 	WHERE IT.PressSide = 'R'
		// 	AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
		// 	AND IT.PressNo = '$press_no'
		// 	AND IT.CuringDate IS NOT NULL
		// 	ORDER BY IT.CuringDate ASC"
		// );

		return [
			'L' => $L,
			'R' => $R,
			'shift' => $shift_name,
			'date_curing' => $date_curing,
			'weekly' => (new Utils)->getWeek($date_curing)
		];
	}

	public function buildingMachine($date, $shift, $machine)
	{
		if ($shift === 'day') {
			$start_date = date('Y-m-d', strtotime($date)) . ' 08:00:00';
			$end_date 	= date('Y-m-d', strtotime($date)) . ' 20:00:00';
			$shift 		= 'A';
		} else {
			$start_date = date('Y-m-d', strtotime($date)) . ' 20:00:00';
			$end_date 	= date('Y-m-d', strtotime($date . '+1 days')) . ' 08:00:00';
			$shift 		= 'B';
		}
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
				IT.BuildingNo
				,IT.GT_Code
				,IT.Barcode
				,IT.CreateDate
				,SH.Description
				,U.Name
				,IT.DisposalID
				--,D.DisposalDesc
				,CASE WHEN IT.DisposalID = 10 AND IT.WarehouseID = 1 THEN 'GT Hold'
				WHEN IT.DisposalID = 10 AND IT.WarehouseID = 2  THEN 'CT Hold'
				ELSE D.DisposalDesc END AS DisposalDesc
				FROM InventTable IT JOIN
				     InventTrans T
				     ON T.Barcode = IT.Barcode
				     AND T.CreateDate = IT.CreateDate
				     JOIN UserMaster U
				     ON U.ID = IT.CreateBy
				     JOIN ShiftMaster SH
				     ON SH.ID = T.Shift
				     JOIN DisposalToUseIn D
				     ON IT.DisposalID = D.ID
				WHERE IT.CreateDate >= '$start_date' AND IT.CreateDate <= '$end_date'
				AND IT.BuildingNo IN ($machine)
				AND IT.CheckBuild=1
				ORDER BY IT.BuildingNo,IT.CreateDate ASC"
		);
	}

	public function Loading($pickingListId, $orderId, $createDate)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT A.warehouse_desc,A.location_desc,A.ItemId,A.NameTH,A.BatchNo,SUM(A.AX_QTY)AX_QTY,SUM(A.STR_QTY)STR_QTY
			FROM
			(
						SELECT ItemId,NameTH,BatchNo,0[AX_QTY],SUM(QTY)[STR_QTY],warehouse_desc,location_desc
						FROM(
									SELECT LT.* ,
									WM.Description as warehouse_desc,
									L.Description as location_desc ,
									LS.Description as StatusDesc,
									UM.Name as Fullname,
									IT.TemplateSerialNo as SerialName,
									IM.NameTH
									FROM LoadingTrans LT
									LEFT JOIN InventTable IT ON LT.Barcode = IT.Barcode
									LEFT JOIN WarehouseMaster WM ON WM.ID = IT.WarehouseID
									LEFT JOIN Location L ON L.ID = IT.LocationID
									LEFT JOIN LoadingStatus LS ON LS.ID = LT.Status
									LEFT JOIN UserMaster UM ON LT.CreatedBy = UM.ID
									LEFT JOIN ItemMaster IM ON LT.ItemId = IM.ID
									WHERE LT.OrderId = '$orderId'
									AND LT.PickingListId = '$pickingListId'
									AND LT.Status<>6
						) Z
						GROUP BY Z.ItemId,Z.NameTH,Z.warehouse_desc,Z.location_desc,Z.BatchNo

						UNION

						SELECT CPT.ITEMID[ItemId],IT.DSGTHAIITEMDESCRIPTION[NameTH],ID.INVENTBATCHID[BatchNo],ABS(SUM(FLOOR(IVT.QTY)))[AX_QTY],0[STR_QTY],'Warehouse FG'warehouse_desc,'Loading'location_desc
						FROM [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[SALESTABLE] SO JOIN
							 (
							  SELECT
							  MAX(CJ.PACKINGSLIPID)PACKINGSLIPID
							  ,CJ.DATAAREAID
							  ,CJ.SALESID
							  FROM [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[CUSTPACKINGSLIPJOUR] CJ
							  GROUP BY CJ.DATAAREAID,CJ.SALESID
							 )CJ ON CJ.DATAAREAID = SO.DATAAREAID
							 AND CJ.SALESID = SO.SALESID
							 JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[CUSTPACKINGSLIPTRANS] CPT
							 ON CPT.DATAAREAID = CJ.DATAAREAID
							 AND CPT.PACKINGSLIPID = CJ.PACKINGSLIPID
							 JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTRANS] IVT
							 ON IVT.DATAAREAID = CPT.DATAAREAID
							 AND IVT.INVENTTRANSID = CPT.INVENTTRANSID
							 JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTDIM] ID
							 ON ID.INVENTDIMID = IVT.INVENTDIMID
							 AND ID.DATAAREAID = IVT.DATAAREAID
							 JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] IT
							 ON CPT.ITEMID=IT.ITEMID
							 AND IT.DATAAREAID<>'dv'
						WHERE SO.SALESID = '$orderId'
						--AND SO.DATAAREAID = 'STR'
						GROUP BY
						CPT.ITEMID,IT.DSGTHAIITEMDESCRIPTION,ID.INVENTBATCHID
						-- SO.SALESID,CJ.PACKINGSLIPID,CPT.ITEMID,IT.DSGTHAIITEMDESCRIPTION,CPT.INVENTTRANSID,IVT.INVENTDIMID
						-- ,ID.INVENTLOCATIONID,ID.INVENTBATCHID
						-- ,ID.WMSLOCATIONID
			)A
			GROUP BY A.warehouse_desc,A.location_desc,A.ItemId,A.NameTH,A.BatchNo"
		);
	}

	public function checkProductGroup($date_curing, $press_no, $shift)
	{
		$conn = Database::connect();
		$shift_name = '';
		if ($shift === 'day') {
			// day
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 08:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';

			$shift_name = 'กลางวัน';
		} else {
			// night
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing . '+1 days')) . ' 08:00:00';
			$shift_name = 'กลางคืน';
		}

		$L = Sqlsrv::queryJson(
			$conn,
			"SELECT TOP 1
			IM.ProductGroup
			FROM InventTable IT
			LEFT JOIN InventTrans ITS
				ON IT.Barcode = ITS.Barcode
				AND IT.CuringDate = ITS.CreateDate
				AND ITS.DocumentTypeID = 1
			LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
			LEFT JOIN CureCodeMaster CCM
					ON CCM.GreentireID = IT.GT_Code
					AND CCM.ItemID = ITS.CodeID
			LEFT JOIN ItemMaster IM ON IM.ID  = CCM.ItemID
			WHERE IT.CuringDate BETWEEN '$start_date' AND '$end_date'
			AND IT.PressNo = '$press_no'
			AND IT.CuringDate IS NOT NULL
			ORDER BY IT.CuringDate ASC"
		);



		return [

			'shift' => $L

		];
	}

	public function DeductServicepdf($month, $type, $machine, $userid, $year)
	{
		$conn = Database::connect();

		$sql1 = "";
		if ($type !== "") {
			$sql1 .= " B.Type = '$type' AND";
		}
		if ($machine !== "") {
			$sql1 .= " D.Machine = '$machine' AND";
		}
		if ($userid !== "") {
			$sql1 .= " D.UserId = $userid AND";
		}
		$sql1 .= " D.Id <> '' ";

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT U.EmployeeID,
			REPLACE(U.Name,'null','') Name,
			CONVERT(VARCHAR(10), D.DeductDate, 103) DeductDate,
			D.Machine,D.Charge,D.Remark,
			CASE D.Shift
				WHEN 1 THEN 'กลางวัน'
				ELSE 'กลางคืน'
			END AS Shift,
			D.UserId,D.Id
			FROM DeductRateBuild D JOIN
			UserMaster U ON D.UserId = U.ID JOIN
			BuildingMaster B ON D.Machine = B.ID
			WHERE U.Warehouse = 1 AND U.Location = 1
			AND MONTH(DeductDate) = $month AND YEAR(DeductDate) = $year AND  $sql1
			ORDER BY DeductDate
			"
		);

		return $query;
	}

	public function RateMonthlyServicepdf_PCR($date_start, $date_end, $machine_type)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T7.CreateBy
			,U.EmployeeID
			,REPLACE(U.Name,'null','') Name
			,SUM(T7.Charge) Charge
			,SUM(T7.D21) D21
			,SUM(T7.D22) D22
			,SUM(T7.D23) D23
			,SUM(T7.D24) D24
			,SUM(T7.D25) D25
			,SUM(T7.D26) D26
			,SUM(T7.D27) D27
			,SUM(T7.D28) D28
			,SUM(T7.D29) D29
			,SUM(T7.D30) D30
			,SUM(T7.D31) D31
			,SUM(T7.D01) D01
			,SUM(T7.D02) D02
			,SUM(T7.D03) D03
			,SUM(T7.D04) D04
			,SUM(T7.D05) D05
			,SUM(T7.D06) D06
			,SUM(T7.D07) D07
			,SUM(T7.D08) D08
			,SUM(T7.D09) D09
			,SUM(T7.D10) D10
			,SUM(T7.D11) D11
			,SUM(T7.D12) D12
			,SUM(T7.D13) D13
			,SUM(T7.D14) D14
			,SUM(T7.D15) D15
			,SUM(T7.D16) D16
			,SUM(T7.D17) D17
			,SUM(T7.D18) D18
			,SUM(T7.D19) D19
			,SUM(T7.D20) D20
			,T7.Total
		FROM (
			SELECT T6.CreateBy
				,T6.Charge
				,SUM(T6.Total) OVER (PARTITION BY T6.CreateBy) AS Total
				,CASE 
					WHEN T6.DD = 21
						THEN T6.Total
					ELSE 0
					END AS 'D21'
				,CASE 
					WHEN T6.DD = 22
						THEN T6.Total
					ELSE 0
					END AS 'D22'
				,CASE 
					WHEN T6.DD = 23
						THEN T6.Total
					ELSE 0
					END AS 'D23'
				,CASE 
					WHEN T6.DD = 24
						THEN T6.Total
					ELSE 0
					END AS 'D24'
				,CASE 
					WHEN T6.DD = 25
						THEN T6.Total
					ELSE 0
					END AS 'D25'
				,CASE 
					WHEN T6.DD = 26
						THEN T6.Total
					ELSE 0
					END AS 'D26'
				,CASE 
					WHEN T6.DD = 27
						THEN T6.Total
					ELSE 0
					END AS 'D27'
				,CASE 
					WHEN T6.DD = 28
						THEN T6.Total
					ELSE 0
					END AS 'D28'
				,CASE 
					WHEN T6.DD = 29
						THEN T6.Total
					ELSE 0
					END AS 'D29'
				,CASE 
					WHEN T6.DD = 30
						THEN T6.Total
					ELSE 0
					END AS 'D30'
				,CASE 
					WHEN T6.DD = 31
						THEN T6.Total
					ELSE 0
					END AS 'D31'
				,CASE 
					WHEN T6.DD = 01
						THEN T6.Total
					ELSE 0
					END AS 'D01'
				,CASE 
					WHEN T6.DD = 02
						THEN T6.Total
					ELSE 0
					END AS 'D02'
				,CASE 
					WHEN T6.DD = 03
						THEN T6.Total
					ELSE 0
					END AS 'D03'
				,CASE 
					WHEN T6.DD = 04
						THEN T6.Total
					ELSE 0
					END AS 'D04'
				,CASE 
					WHEN T6.DD = 05
						THEN T6.Total
					ELSE 0
					END AS 'D05'
				,CASE 
					WHEN T6.DD = 06
						THEN T6.Total
					ELSE 0
					END AS 'D06'
				,CASE 
					WHEN T6.DD = 07
						THEN T6.Total
					ELSE 0
					END AS 'D07'
				,CASE 
					WHEN T6.DD = 08
						THEN T6.Total
					ELSE 0
					END AS 'D08'
				,CASE 
					WHEN T6.DD = 09
						THEN T6.Total
					ELSE 0
					END AS 'D09'
				,CASE 
					WHEN T6.DD = 10
						THEN T6.Total
					ELSE 0
					END AS 'D10'
				,CASE 
					WHEN T6.DD = 11
						THEN T6.Total
					ELSE 0
					END AS 'D11'
				,CASE 
					WHEN T6.DD = 12
						THEN T6.Total
					ELSE 0
					END AS 'D12'
				,CASE 
					WHEN T6.DD = 13
						THEN T6.Total
					ELSE 0
					END AS 'D13'
				,CASE 
					WHEN T6.DD = 14
						THEN T6.Total
					ELSE 0
					END AS 'D14'
				,CASE 
					WHEN T6.DD = 15
						THEN T6.Total
					ELSE 0
					END AS 'D15'
				,CASE 
					WHEN T6.DD = 16
						THEN T6.Total
					ELSE 0
					END AS 'D16'
				,CASE 
					WHEN T6.DD = 17
						THEN T6.Total
					ELSE 0
					END AS 'D17'
				,CASE 
					WHEN T6.DD = 18
						THEN T6.Total
					ELSE 0
					END AS 'D18'
				,CASE 
					WHEN T6.DD = 19
						THEN T6.Total
					ELSE 0
					END AS 'D19'
				,CASE 
					WHEN T6.DD = 20
						THEN T6.Total
					ELSE 0
					END AS 'D20'
			FROM (
				SELECT DISTINCT T4.CreateBy
					,T4.Machine
					,T4.DAAY
					,RIGHT(T4.DAAY, 2) AS DD
					,T4.Charge
					,T4.P1 + T4.P2 + T4.P3 + CASE 
						WHEN T4.RateType = 'PCR'
							AND (T4.COUNT_MAC < 3)
							THEN (T4.P1 + T4.P2 + T4.P3) / 2
						ELSE 0
						END AS Total
				FROM (
					SELECT T3.CreateBy
						,T3.Machine
						,T3.LoginDate
						,T3.DAAY
						,T3.Shift
						,T3.BuildTypeId
						,T3.Type
						,T3.Act
						,ISNULL(D.Charge, 0) AS Charge
						,R.RatePrice1
						,R.RatePrice2
						,R.RatePrice3
						,R.RateType
						,CASE 
							WHEN T3.Act >= R.Qty1
								THEN R.RatePrice1
							ELSE 0
							END AS 'P1'
						,CASE 
							WHEN (T3.Act - R.Qty1) >= (R.Qty2 - R.Qty1)
								THEN (R.Qty2 - R.Qty1) * R.RatePrice2
							WHEN T3.Act > R.Qty1
								AND ((T3.Act - R.Qty1) <= (R.Qty2 - R.Qty1))
								THEN (T3.Act - R.Qty1) * R.RatePrice2
							ELSE 0
							END AS 'P2'
						,CASE R.RateType
							WHEN 'TBR'
								THEN CASE 
										WHEN T3.Act >= R.Qty3
											THEN (T3.Act - R.Qty2) * R.RatePrice3
										ELSE 0
										END
							WHEN 'PCR'
								THEN CASE 
										WHEN T3.Act >= R.Qty2
											THEN ((T3.Act - R.Qty2) / R.Qty3) * R.RatePrice3
										ELSE 0
										END
							ELSE 0
							END AS 'P3'
						,COUNT(T3.Machine) OVER (
							PARTITION BY T3.Machine
							,T3.DAAY
							,T3.Shift
							) COUNT_MAC
						,ISNULL(S.Total, 0) AS SCH
						,R.PLY
					FROM (
						SELECT T2.CreateBy
							,T2.Machine
							,T2.LoginDate
							,T2.Shift
							,T2.BuildTypeId
							,T.Type
							,T.Act
							,T2.DAAY
							,T2.ItemNumber
						FROM (
							SELECT T1.CreateBy
								,T1.Machine
								,T1.LoginDate
								,T1.Shift
								,T1.BuildTypeId
								,T1.GT_Code
								,T1.ItemNumber
								,T1.DAAY
							FROM (
								SELECT B.CreateBy
									,B.Machine
									,R.UserId
									,R.LoginDate
									,R.Shift
									,R.BuildTypeId
									,ROW_NUMBER() OVER (
										PARTITION BY B.CreateBy
										,CASE 
											WHEN CONVERT(INT, LEFT(CONVERT(VARCHAR, R.LoginDate, 8), 2)) <= 7
												THEN DATEADD(DAY, - 1, CONVERT(DATE, R.LoginDate))
											ELSE CONVERT(DATE, R.LoginDate)
											END
										,R.Shift ORDER BY R.LoginDate DESC
										) AS Row
									,I.GT_Code
									,G.ItemNumber
									,CASE 
										WHEN CONVERT(INT, LEFT(CONVERT(VARCHAR, R.LoginDate, 8), 2)) <= 7
											THEN DATEADD(DAY, - 1, CONVERT(DATE, R.LoginDate))
										ELSE CONVERT(DATE, R.LoginDate)
										END AS DAAY
								FROM BuildTrans B
								JOIN RateTrans R ON B.CreateBy = R.UserId
									AND (
										B.CreateDate BETWEEN R.LoginDate
											AND R.LogoutDate
										)
								JOIN BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
								JOIN InventTable I ON B.Barcode = I.Barcode
								JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
								WHERE B.CreateDate BETWEEN ?
										AND ?
									AND R.LoginDate BETWEEN ?
										AND ?
									AND R.LogoutDate BETWEEN ?
										AND ?
									AND R.RateGroupID = 1
								GROUP BY B.CreateBy
									,B.Machine
									,R.UserId
									,R.LoginDate
									,R.Shift
									,R.BuildTypeId
									,I.GT_Code
									,G.ItemNumber
								) T1
							WHERE T1.Row = 1
							) T2
						JOIN (
							SELECT B.CreateBy
								,COUNT(B.Barcode) 'Act'
								,BM.Type
								,CASE 
									WHEN CONVERT(INT, LEFT(CONVERT(VARCHAR, R.LoginDate, 8), 2)) <= 7
										THEN DATEADD(DAY, - 1, CONVERT(DATE, R.LoginDate))
									ELSE CONVERT(DATE, R.LoginDate)
									END AS DAAY
							FROM BuildTrans B
							JOIN RateTrans R ON B.CreateBy = R.UserId
								AND (
									B.CreateDate BETWEEN R.LoginDate
										AND R.LogoutDate
									)
							JOIN InventTable I ON B.Barcode = I.Barcode
							JOIN BuildingMaster BM ON B.Machine = BM.ID
							WHERE B.CreateDate BETWEEN ?
									AND ?
								AND I.CheckBuild = 1
								AND B.Barcode NOT IN (
									SELECT Barcode
									FROM ExceptBuildRate
									)
								AND BM.Type = ?
								AND B.Machine NOT IN (
									SELECT Machine
									FROM RateMaster
									WHERE RateType = ?
										AND PLY = 0
										AND BuildTypeId = 1
									GROUP BY Machine
									)
							GROUP BY B.CreateBy
								,BM.Type
								,CASE 
									WHEN CONVERT(INT, LEFT(CONVERT(VARCHAR, R.LoginDate, 8), 2)) <= 7
										THEN DATEADD(DAY, - 1, CONVERT(DATE, R.LoginDate))
									ELSE CONVERT(DATE, R.LoginDate)
									END
							) T ON T2.CreateBy = T.CreateBy
							AND T.DAAY = T2.DAAY
						) T3
					JOIN RateMaster R ON T3.Machine = R.Machine
						AND T3.BuildTypeId = R.BuildTypeId
					LEFT JOIN RateBuildSchedule S ON T3.Machine = S.Machine
						AND T3.Shift = S.Shift
						AND CONVERT(DATE, T3.DAAY) = CONVERT(DATE, S.DateRateBuild)
						AND S.Active = 1
					LEFT JOIN DeductRateBuild D ON T3.Machine = D.Machine
						AND CONVERT(DATE, T3.DAAY) = CONVERT(DATE, D.DeductDate)
						AND T3.CreateBy = D.UserId
						AND T3.Shift = D.Shift
					JOIN (
						SELECT T1.ItemGT
							,T1.PLY
						FROM (
							SELECT ItemGT
								,PLY
								,ROW_NUMBER() OVER (
									PARTITION BY ItemGT ORDER BY PLY DESC
									) AS Row
							FROM ItemPLY
							) T1
						WHERE T1.Row = 1
						) P ON T3.ItemNumber = P.ItemGT
						AND R.PLY = P.PLY
					WHERE T3.Act >= R.Qty1
						AND R.RateGroupID = 1
					) T4
				) T6
			) T7
		JOIN UserMaster U ON T7.CreateBy = U.ID
		GROUP BY T7.CreateBy
			,U.EmployeeID
			,U.Name
			,T7.Total		
			ORDER BY T7.CreateBy",
			[
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$machine_type,
				$machine_type
			]
		);

		return $query;
	}


	public function RateMonthlyServicepdf($date_start, $date_end, $machine_type)
	{
		$conn = Database::connect();

		// $query = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT T7.CreateBy,T7.EmployeeID,T7.Name,
		// 	SUM(T7.Charge) Charge,
		// 	SUM(T7.D21) D21 ,SUM(T7.D22) D22 ,SUM(T7.D23) D23 ,
		// 	SUM(T7.D24) D24 ,SUM(T7.D25) D25 ,SUM(T7.D26) D26 ,
		// 	SUM(T7.D27) D27 ,SUM(T7.D28) D28 ,SUM(T7.D29) D29 ,
		// 	SUM(T7.D30) D30 ,SUM(T7.D31) D31 ,SUM(T7.D01) D01 ,
		// 	SUM(T7.D02) D02 ,SUM(T7.D03) D03 ,SUM(T7.D04) D04 ,
		// 	SUM(T7.D05) D05 ,SUM(T7.D06) D06 ,SUM(T7.D07) D07 ,
		// 	SUM(T7.D08) D08 ,SUM(T7.D09) D09 ,SUM(T7.D10) D10 ,
		// 	SUM(T7.D11) D11 ,SUM(T7.D12) D12 ,SUM(T7.D13) D13 ,
		// 	SUM(T7.D14) D14 ,SUM(T7.D15) D15 ,SUM(T7.D16) D16 ,
		// 	SUM(T7.D17) D17 ,SUM(T7.D18) D18 ,SUM(T7.D19) D19 ,
		// 	SUM(T7.D20) D20 ,T7.Total
		// 	FROM
		// 	(
		// 		SELECT T6.CreateBy,T6.EmployeeID,T6.Name,T6.Charge,
		// 		SUM(T6.Total) OVER(partition by T6.CreateBy) AS Total
		// 		,CASE WHEN T6.DD = 21 THEN T6.Total ELSE 0 END AS 'D21'
		// 		,CASE WHEN T6.DD = 22 THEN T6.Total ELSE 0 END AS 'D22'
		// 		,CASE WHEN T6.DD = 23 THEN T6.Total ELSE 0 END AS 'D23'
		// 		,CASE WHEN T6.DD = 24 THEN T6.Total ELSE 0 END AS 'D24'
		// 		,CASE WHEN T6.DD = 25 THEN T6.Total ELSE 0 END AS 'D25'
		// 		,CASE WHEN T6.DD = 26 THEN T6.Total ELSE 0 END AS 'D26'
		// 		,CASE WHEN T6.DD = 27 THEN T6.Total ELSE 0 END AS 'D27'
		// 		,CASE WHEN T6.DD = 28 THEN T6.Total ELSE 0 END AS 'D28'
		// 		,CASE WHEN T6.DD = 29 THEN T6.Total ELSE 0 END AS 'D29'
		// 		,CASE WHEN T6.DD = 30 THEN T6.Total ELSE 0 END AS 'D30'
		// 		,CASE WHEN T6.DD = 31 THEN T6.Total ELSE 0 END AS 'D31'
		// 		,CASE WHEN T6.DD = 01 THEN T6.Total ELSE 0 END AS 'D01'
		// 		,CASE WHEN T6.DD = 02 THEN T6.Total ELSE 0 END AS 'D02'
		// 		,CASE WHEN T6.DD = 03 THEN T6.Total ELSE 0 END AS 'D03'
		// 		,CASE WHEN T6.DD = 04 THEN T6.Total ELSE 0 END AS 'D04'
		// 		,CASE WHEN T6.DD = 05 THEN T6.Total ELSE 0 END AS 'D05'
		// 		,CASE WHEN T6.DD = 06 THEN T6.Total ELSE 0 END AS 'D06'
		// 		,CASE WHEN T6.DD = 07 THEN T6.Total ELSE 0 END AS 'D07'
		// 		,CASE WHEN T6.DD = 08 THEN T6.Total ELSE 0 END AS 'D08'
		// 		,CASE WHEN T6.DD = 09 THEN T6.Total ELSE 0 END AS 'D09'
		// 		,CASE WHEN T6.DD = 10 THEN T6.Total ELSE 0 END AS 'D10'
		// 		,CASE WHEN T6.DD = 11 THEN T6.Total ELSE 0 END AS 'D11'
		// 		,CASE WHEN T6.DD = 12 THEN T6.Total ELSE 0 END AS 'D12'
		// 		,CASE WHEN T6.DD = 13 THEN T6.Total ELSE 0 END AS 'D13'
		// 		,CASE WHEN T6.DD = 14 THEN T6.Total ELSE 0 END AS 'D14'
		// 		,CASE WHEN T6.DD = 15 THEN T6.Total ELSE 0 END AS 'D15'
		// 		,CASE WHEN T6.DD = 16 THEN T6.Total ELSE 0 END AS 'D16'
		// 		,CASE WHEN T6.DD = 17 THEN T6.Total ELSE 0 END AS 'D17'
		// 		,CASE WHEN T6.DD = 18 THEN T6.Total ELSE 0 END AS 'D18'
		// 		,CASE WHEN T6.DD = 19 THEN T6.Total ELSE 0 END AS 'D19'
		// 		,CASE WHEN T6.DD = 20 THEN T6.Total ELSE 0 END AS 'D20'
		// 		FROM
		// 		(
		// 			SELECT T9.*,
		// 			(T9.P1+T9.P2+T9.P3) TOTAL
		// 			FROM
		// 			(
		// 				SELECT T8.CreateBy,T8.EmployeeID,T8.Name,T8.Charge,T8.DD,
		// 				T8.P1,T8.P2,T8.P3
		// 				FROM
		// 				(
		// 					SELECT T7.CreateBy,T7.EmployeeID,T7.Name,
		// 					T7.Machine,T7.BuildTypeId,
		// 					T7.Act,T7.DD,T7.Charge,T7.DAAY,R.RateType,
		// 					R.Qty1,R.Qty2,R.Qty3,T7.SS,
		// 					CASE
		// 						WHEN T7.Act >= R.Qty1 THEN R.RatePrice1
		// 					ELSE 0 END AS 'P1',
		// 					CASE
		// 						WHEN (T7.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
		// 						WHEN  T7.Act > R.Qty1 AND ((T7.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T7.Act-R.Qty1)*R.RatePrice2
		// 					ELSE 0 END AS 'P2',
		// 					CASE R.RateType
		// 						WHEN 'TBR' THEN
		// 							CASE WHEN T7.Act >= R.Qty3 THEN (T7.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
		// 						WHEN 'PCR' THEN
		// 							CASE WHEN  T7.Act >= R.Qty2 THEN ((T7.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
		// 					ELSE 0
		// 					END AS 'P3'
		// 					FROM
		// 					(
		// 						SELECT T6.*
		// 						,CASE WHEN D.Charge IS NULL THEN 0
		// 						ELSE D.Charge END AS Charge
		// 						FROM
		// 						(
		// 							SELECT T5.CreateBy,U.EmployeeID,
		// 							REPLACE(U.Name,'null','') Name,T5.BuildTypeId,
		// 							T5.DAAY,T5.SS,T5.DD,T5.Machine,T5.TT Act
		// 							FROM
		// 							(
		// 								SELECT T4.CreateBy,T4.DAAY,T4.SS,T4.Machine,
		// 								T4.Act,T4.DD ,T4.MacSize,T4.BuildTypeId,
		// 								CASE 
		// 									WHEN T4.Row2 = 2 THEN ROW_NUMBER() OVER(PARTITION BY T4.CreateBy,T4.DAAY ORDER BY T4.MacSize,T4.Machine ASC)  
		// 									ELSE ROW_NUMBER() OVER(PARTITION BY T4.CreateBy,T4.DAAY ORDER BY MAX(T4.Act) DESC) 
		// 								END AS Row
		// 								,SUM(T4.Act) OVER(PARTITION BY T4.CreateBy,T4.DAAY,T4.SS) AS TT
		// 								FROM
		// 								(
		// 									SELECT T3.CreateBy,T3.DAAY,T3.SS,T3.Machine,
		// 									T3.Act,T3.DD ,T3.MacSize,T3.BuildTypeId,
		// 									COUNT(T3.Row) OVER(PARTITION BY T3.CreateBy,T3.DAAY,T3.Row) AS Row2
		// 									FROM
		// 									(
		// 										SELECT T2.CreateBy,T2.DAAY,T2.SS,T2.Machine,
		// 										T2.Act,T2.DD ,R.MacSize,T2.BuildTypeId
		// 										,DENSE_RANK() OVER(PARTITION BY T2.CreateBy,T2.DAAY ORDER BY T2.ACT) AS Row  
		// 										FROM
		// 										(
		// 											SELECT T1.CreateBy,T1.DAAY,
		// 											RIGHT(T1.DAAY,2) DD,T1.SS,
		// 											T1.Machine,SUM(T1.Act) Act,
		// 											T1.BuildTypeId
		// 											FROM
		// 											(
		// 												SELECT T.CreateBy,
		// 												CASE
		// 													WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) <= 7
		// 														THEN DATEADD(DAY,-1,CONVERT(DATE,T.LoginDate))
		// 													ELSE CONVERT(DATE,T.LoginDate)
		// 												END AS DAAY ,
		// 												CASE
		// 													WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) <= 7 OR
		// 														 CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) >= 20
		// 														THEN 2
		// 													ELSE 1
		// 												END AS SS,
		// 												T.Machine,T.Act,T.BuildTypeId
		// 												FROM
		// 												(
		// 													SELECT D2.CreateBy,D2.Machine,D2.LoginDate,D2.BuildTypeId
		// 													,COUNT(D2.BARCODE) Act
		// 													FROM
		// 													(
		// 														SELECT D1.CreateBy,
		// 														D1.Barcode,
		// 														D1.Machine,
		// 														D1.LoginDate,
		// 														D1.BuildTypeId,
		// 														C = (SELECT Barcode
		// 														FROM ExceptBuildRate
		// 														WHERE Barcode = D1.Barcode)
		// 														FROM
		// 														(
		// 															SELECT B.CreateBy,
		// 															B.Barcode,
		// 															B.Machine,
		// 															R.LoginDate,
		// 															R.BuildTypeId
		// 															FROM BuildTrans B JOIN
		// 															InventTable I ON B.Barcode = I.Barcode JOIN
		// 															RateTrans R ON B.CreateBy = R.UserId AND B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate
		// 															WHERE B.CreateDate BETWEEN ? AND ?
		// 															AND I.CheckBuild = 1 
		// 															--AND B.Barcode NOT IN (SELECT BARCODE FROM ExceptBuildRate)
		// 															--GROUP BY B.CreateBy,B.Machine,R.LoginDate,R.BuildTypeId
		// 														)D1
		// 													)D2
		// 													WHERE D2.C IS NULL
		// 													GROUP BY D2.CreateBy,D2.Machine,D2.LoginDate,D2.BuildTypeId
		// 												)T
		// 											)T1
		// 											GROUP BY T1.CreateBy,T1.DAAY,
		// 											RIGHT(T1.DAAY,2),T1.SS,
		// 											T1.Machine,T1.BuildTypeId
		// 										)T2
		// 										JOIN RateMaster R ON T2.Machine = R.Machine
		// 										GROUP BY T2.CreateBy,T2.DAAY,T2.SS,T2.Machine,
		// 										T2.Act,T2.DD,R.MacSize,T2.BuildTypeId
		// 									)T3
		// 								)T4
		// 								GROUP BY T4.CreateBy,T4.DAAY,T4.SS,T4.Machine,
		// 								T4.Act,T4.DD ,T4.MacSize,T4.Row2,T4.BuildTypeId
		// 							)T5 JOIN
		// 							UserMaster U ON T5.CreateBy = U.ID
		// 							WHERE T5.Row = 1
		// 						)T6 LEFT JOIN DeductRateBuild D ON T6.CreateBy = D.UserId AND
		// 						T6.DAAY = CONVERT(DATE,D.DeductDate) AND T6.Machine = D.Machine
		// 						AND T6.SS = D.Shift
		// 					)T7
		// 					JOIN RateMaster R ON T7.Machine = R.Machine AND T7.BuildTypeId = R.BuildTypeId
		// 					WHERE T7.Act >= R.Qty1 AND R.RateGroupID = 1
		// 				)T8
		// 				WHERE  T8.RateType = ?
		// 			)T9
		// 		)T6
		// 	)T7
		// 	GROUP BY T7.CreateBy,T7.EmployeeID,T7.Name,T7.Total
		// 	ORDER BY CreateBy
		// 	",
		// 	[
		// 		$date_start,
		// 		$date_end,
		// 		$machine_type
		// 	]
		// );

		//return $query;

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T7.CreateBy,T7.EmployeeID,T7.Name,
			SUM(T7.Charge) Charge,
			SUM(T7.D21) D21 ,SUM(T7.D22) D22 ,SUM(T7.D23) D23 ,
			SUM(T7.D24) D24 ,SUM(T7.D25) D25 ,SUM(T7.D26) D26 ,
			SUM(T7.D27) D27 ,SUM(T7.D28) D28 ,SUM(T7.D29) D29 ,
			SUM(T7.D30) D30 ,SUM(T7.D31) D31 ,SUM(T7.D01) D01 ,
			SUM(T7.D02) D02 ,SUM(T7.D03) D03 ,SUM(T7.D04) D04 ,
			SUM(T7.D05) D05 ,SUM(T7.D06) D06 ,SUM(T7.D07) D07 ,
			SUM(T7.D08) D08 ,SUM(T7.D09) D09 ,SUM(T7.D10) D10 ,
			SUM(T7.D11) D11 ,SUM(T7.D12) D12 ,SUM(T7.D13) D13 ,
			SUM(T7.D14) D14 ,SUM(T7.D15) D15 ,SUM(T7.D16) D16 ,
			SUM(T7.D17) D17 ,SUM(T7.D18) D18 ,SUM(T7.D19) D19 ,
			SUM(T7.D20) D20 ,T7.Total
			FROM
			(
				SELECT T6.CreateBy,T6.EmployeeID,T6.Name,T6.Charge,
				SUM(T6.Total) OVER(partition by T6.CreateBy) AS Total
				,CASE WHEN T6.DD = 21 THEN T6.Total ELSE 0 END AS 'D21'
				,CASE WHEN T6.DD = 22 THEN T6.Total ELSE 0 END AS 'D22'
				,CASE WHEN T6.DD = 23 THEN T6.Total ELSE 0 END AS 'D23'
				,CASE WHEN T6.DD = 24 THEN T6.Total ELSE 0 END AS 'D24'
				,CASE WHEN T6.DD = 25 THEN T6.Total ELSE 0 END AS 'D25'
				,CASE WHEN T6.DD = 26 THEN T6.Total ELSE 0 END AS 'D26'
				,CASE WHEN T6.DD = 27 THEN T6.Total ELSE 0 END AS 'D27'
				,CASE WHEN T6.DD = 28 THEN T6.Total ELSE 0 END AS 'D28'
				,CASE WHEN T6.DD = 29 THEN T6.Total ELSE 0 END AS 'D29'
				,CASE WHEN T6.DD = 30 THEN T6.Total ELSE 0 END AS 'D30'
				,CASE WHEN T6.DD = 31 THEN T6.Total ELSE 0 END AS 'D31'
				,CASE WHEN T6.DD = 01 THEN T6.Total ELSE 0 END AS 'D01'
				,CASE WHEN T6.DD = 02 THEN T6.Total ELSE 0 END AS 'D02'
				,CASE WHEN T6.DD = 03 THEN T6.Total ELSE 0 END AS 'D03'
				,CASE WHEN T6.DD = 04 THEN T6.Total ELSE 0 END AS 'D04'
				,CASE WHEN T6.DD = 05 THEN T6.Total ELSE 0 END AS 'D05'
				,CASE WHEN T6.DD = 06 THEN T6.Total ELSE 0 END AS 'D06'
				,CASE WHEN T6.DD = 07 THEN T6.Total ELSE 0 END AS 'D07'
				,CASE WHEN T6.DD = 08 THEN T6.Total ELSE 0 END AS 'D08'
				,CASE WHEN T6.DD = 09 THEN T6.Total ELSE 0 END AS 'D09'
				,CASE WHEN T6.DD = 10 THEN T6.Total ELSE 0 END AS 'D10'
				,CASE WHEN T6.DD = 11 THEN T6.Total ELSE 0 END AS 'D11'
				,CASE WHEN T6.DD = 12 THEN T6.Total ELSE 0 END AS 'D12'
				,CASE WHEN T6.DD = 13 THEN T6.Total ELSE 0 END AS 'D13'
				,CASE WHEN T6.DD = 14 THEN T6.Total ELSE 0 END AS 'D14'
				,CASE WHEN T6.DD = 15 THEN T6.Total ELSE 0 END AS 'D15'
				,CASE WHEN T6.DD = 16 THEN T6.Total ELSE 0 END AS 'D16'
				,CASE WHEN T6.DD = 17 THEN T6.Total ELSE 0 END AS 'D17'
				,CASE WHEN T6.DD = 18 THEN T6.Total ELSE 0 END AS 'D18'
				,CASE WHEN T6.DD = 19 THEN T6.Total ELSE 0 END AS 'D19'
				,CASE WHEN T6.DD = 20 THEN T6.Total ELSE 0 END AS 'D20'
				FROM
				(
					SELECT T9.*,
					(T9.P1+T9.P2+T9.P3) TOTAL
					FROM
					(
						SELECT T8.CreateBy,T8.EmployeeID,T8.Name,T8.Charge,T8.DD,
						T8.P1,T8.P2,T8.P3
						FROM
						(
							SELECT T7.CreateBy,T7.EmployeeID,T7.Name,
							T7.Machine,T7.BuildTypeId,
							T7.Act,T7.DD,T7.Charge,T7.DAAY,R.RateType,
							R.Qty1,R.Qty2,R.Qty3,T7.SS,
							CASE
								WHEN T7.Act >= R.Qty1 THEN R.RatePrice1
							ELSE 0 END AS 'P1',
							CASE
								WHEN (T7.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
								WHEN  T7.Act > R.Qty1 AND ((T7.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T7.Act-R.Qty1)*R.RatePrice2
							ELSE 0 END AS 'P2',
							CASE R.RateType
								WHEN 'TBR' THEN
									CASE WHEN T7.Act >= R.Qty3 THEN (T7.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
								WHEN 'PCR' THEN
									CASE WHEN  T7.Act >= R.Qty2 THEN ((T7.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
							ELSE 0
							END AS 'P3'
							FROM
							(
								SELECT T6.*
								,CASE WHEN D.Charge IS NULL THEN 0
								ELSE D.Charge END AS Charge
								FROM
								(
									SELECT T5.CreateBy,U.EmployeeID,
									REPLACE(U.Name,'null','') Name,
									T5.DAAY,T5.SS,T5.DD,T5.Machine,T5.TT Act
									,B.BuildTypeId
									FROM
									(
										SELECT T4.CreateBy,T4.DAAY,T4.SS,T4.Machine,
										T4.Act,T4.DD ,T4.MacSize,
										CASE 
											WHEN T4.Row2 = 2 THEN ROW_NUMBER() OVER(PARTITION BY T4.CreateBy,T4.DAAY ORDER BY T4.MacSize,T4.Machine ASC)  
											ELSE ROW_NUMBER() OVER(PARTITION BY T4.CreateBy,T4.DAAY ORDER BY MAX(T4.Act) DESC) 
										END AS Row
										,SUM(T4.Act) OVER(PARTITION BY T4.CreateBy,T4.DAAY,T4.SS) AS TT
										FROM
										(
											SELECT T3.CreateBy,T3.DAAY,T3.SS,T3.Machine,
											T3.Act,T3.DD ,T3.MacSize,
											COUNT(T3.Row) OVER(PARTITION BY T3.CreateBy,T3.DAAY,T3.Row) AS Row2
											FROM
											(
												SELECT T2.CreateBy,T2.DAAY,T2.SS,T2.Machine,
												T2.Act,T2.DD ,R.MacSize
												,DENSE_RANK() OVER(PARTITION BY T2.CreateBy,T2.DAAY ORDER BY T2.ACT) AS Row  
												FROM
												(
													SELECT T1.CreateBy,T1.DAAY,
													RIGHT(T1.DAAY,2) DD,T1.SS,
													T1.Machine,SUM(T1.Act) Act
													
													FROM
													(
														SELECT T.CreateBy,
														CASE
															WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) <= 7
																THEN DATEADD(DAY,-1,CONVERT(DATE,T.LoginDate))
															ELSE CONVERT(DATE,T.LoginDate)
														END AS DAAY ,
														CASE
															WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) <= 7 OR
																 CONVERT(INT,LEFT(CONVERT(VARCHAR, T.LoginDate, 8),2)) >= 20
																THEN 2
															ELSE 1
														END AS SS,
														T.Machine,T.Act
														FROM
														(
															SELECT D2.CreateBy,D2.Machine,D2.LoginDate
															,COUNT(D2.BARCODE) Act
															FROM
															(
																SELECT D1.CreateBy,
																D1.Barcode,
																D1.Machine,
																D1.LoginDate,
																C = (SELECT Barcode
																FROM ExceptBuildRate
																WHERE Barcode = D1.Barcode)
																FROM
																(
																	SELECT B.CreateBy,
																	B.Barcode,
																	B.Machine,
																	R.LoginDate
																	FROM BuildTrans B JOIN
																	InventTable I ON B.Barcode = I.Barcode JOIN
																	RateTrans R ON B.CreateBy = R.UserId AND B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate
																	WHERE B.CreateDate BETWEEN ? AND ?
																	AND I.CheckBuild = 1
																)D1
															)D2
															WHERE D2.C IS NULL
															GROUP BY D2.CreateBy,D2.Machine,D2.LoginDate
														)T
													)T1
													GROUP BY T1.CreateBy,T1.DAAY,
													RIGHT(T1.DAAY,2),T1.SS,
													T1.Machine
												)T2
												JOIN RateMaster R ON T2.Machine = R.Machine
												GROUP BY T2.CreateBy,T2.DAAY,T2.SS,T2.Machine,
												T2.Act,T2.DD,R.MacSize
											)T3
										)T4
										GROUP BY T4.CreateBy,T4.DAAY,T4.SS,T4.Machine,
										T4.Act,T4.DD ,T4.MacSize,T4.Row2
									)T5 JOIN
									UserMaster U ON T5.CreateBy = U.ID
									JOIN
									(
										SELECT B.CreateBy,B.DAAY,B.BuildTypeId
										FROM
										(
											SELECT T.CreateBy,T.DAAY,T.BuildTypeId,
											ROW_NUMBER() OVER(PARTITION BY T.CreateBy,T.DAAY ORDER BY T.LoginDate DESC) AS Row
											FROM 
											(
												SELECT B.CreateBy,
												R.LoginDate,
												R.BuildTypeId,
												CASE
													WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
														THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
													ELSE CONVERT(DATE,R.LoginDate)
												END AS DAAY
												FROM BuildTrans B JOIN
												RateTrans R ON B.CreateBy = R.UserId AND B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate
												WHERE B.CreateDate BETWEEN ? AND ?
											)T
											GROUP BY T.CreateBy,T.DAAY,T.BuildTypeId,T.LoginDate
										)B
										WHERE B.Row = 1
									)B ON T5.CreateBy = B.CreateBy AND T5.DAAY = B.DAAY
									WHERE T5.Row = 1
								)T6 LEFT JOIN DeductRateBuild D ON T6.CreateBy = D.UserId AND
								T6.DAAY = CONVERT(DATE,D.DeductDate) AND T6.Machine = D.Machine
								AND T6.SS = D.Shift
							)T7
							JOIN RateMaster R ON T7.Machine = R.Machine AND T7.BuildTypeId = R.BuildTypeId
							WHERE T7.Act >= R.Qty1 AND R.RateGroupID = 1
						)T8
						WHERE  T8.RateType = ?
					)T9
				)T6
			)T7
			GROUP BY T7.CreateBy,T7.EmployeeID,T7.Name,T7.Total
			ORDER BY CreateBy
			",
			[
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$machine_type
			]
		);

		return $query;
	}

	public function RateMonthlyServicepdf_V2($date_start, $date_end, $machine_type)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T2.*,TT2.Act
			FROM
			(
				SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.LoginDate,T1.LogoutDate,
				T1.Shift,T1.BuildTypeId,T1.DAAY,T1.Machine,
				CASE WHEN T1.SS = 1 THEN 'D' ELSE 'N' END AS SS,
				CASE WHEN D.Charge IS NULL THEN 0 ELSE D.Charge END AS Charge
				FROM
				(
					SELECT *,
					ROW_NUMBER() OVER(partition by T.CreateBy,T.DAAY ORDER BY T.LoginDate DESC) AS Row
					FROM
					(
						SELECT B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null','') Name
						,B.Machine,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
						,CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
									THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
								ELSE CONVERT(DATE,R.LoginDate)
							END AS DAAY
						,CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 OR
								 CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) >= 20
								THEN 2
							ELSE 1
						END AS SS
						FROM BuildTrans B JOIN
						UserMaster U ON B.CreateBy = U.ID JOIN
						RateTrans R ON B.CreateBy = R.UserId
						AND B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate
						JOIN BuildingMaster BM ON B.Machine = BM.ID
						WHERE B.CreateDate BETWEEN ? AND ?
						AND R.LoginDate BETWEEN ? AND ?
						AND R.LogoutDate BETWEEN ? AND ?
						AND BM.Type = ?
						GROUP BY B.CreateBy,U.EmployeeID,U.Name,B.Machine,
						R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
					)T
				)T1
				LEFT JOIN DeductRateBuild D ON T1.CreateBy = D.UserId AND
				T1.DAAY = CONVERT(DATE,D.DeductDate) AND T1.Machine = D.Machine
				AND T1.SS = D.Shift
				WHERE T1.Row = 1
			)T2 JOIN
			(
				SELECT TT1.CreateBy,TT1.DAAY,TT1.SS,
				TT1.Act
				FROM
				(
					SELECT TT.CreateBy,TT.DAAY,TT.SS
					,SUM(TT.Act) OVER (PARTITION BY TT.CreateBy,TT.DAAY) Act
					,TT.Row
					FROM
					(
						SELECT T.CreateBy,T.DAAY,T.SS,SUM(Act)Act
						,ROW_NUMBER() OVER(PARTITION BY T.CreateBy,T.DAAY
						ORDER BY T.DAAY) AS Row
						FROM
						(
							SELECT CreateBy,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
									THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
								ELSE CONVERT(DATE,CreateDate)
							END AS DAAY,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
									 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
									THEN 'N'
								ELSE 'D'
							END AS SS,
							COUNT(Barcode) Act,Barcode
							FROM BuildTrans
							WHERE CreateDate BETWEEN ? AND ?
							AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
							GROUP BY CreateBy,CreateDate,Barcode
						)T JOIN
						InventTable I ON T.Barcode = I.Barcode
						WHERE I.CheckBuild = 1
						GROUP BY T.CreateBy,T.DAAY,T.SS
					)TT
				)TT1
				WHERE TT1.Row = 1
			)TT2 ON T2.CreateBy = TT2.CreateBy AND T2.DAAY = TT2.DAAY
			ORDER BY CreateBy,LoginDate
			",
			[
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$date_start,
				$date_end,
				$machine_type,
				$date_start,
				$date_end

			]
		);

		return $query;
	}

	// public function RateMaster_Month($tstart, $tend, $machine ,$buildtype)
	// {
	// 	$conn = Database::connect();
	// 	$ply = Sqlsrv::queryArray(
	// 		$conn,
	// 		"
	// 		",
	// 		[
	// 			$tstart,
	// 			$tstart,
	// 			$machine,
	// 			$buildtype
	// 		]
	// 	);

	// 	$countrow = Sqlsrv::queryArray(
	// 		$conn,
	// 		"SELECT COUNT(ID) COUNT_RATE
	// 		FROM RateMaster_V2
	// 		WHERE  RateGroupID = 1 AND
	// 		StartDate <= ? AND
	// 		EndDate >= ?
	// 		AND Machine = ? AND BuildTypeId = ?
	// 		",
	// 		[
	// 			$tstart,
	// 			$tstart,
	// 			$machine,
	// 			$buildtype
	// 		]
	// 	);

	// 	if($countrow[0]['COUNT_RATE'] === 0)
	// 	{
	// 		$query = Sqlsrv::queryArray(
	// 			$conn,
	// 			"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
	// 			M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
	// 			S.Payment,S.Remark
	// 			FROM RateMaster_V2 M JOIN
	// 			RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
	// 			WHERE  M.RateGroupID = 1 AND
	// 			M.isDefault = 1
	// 			AND M.Machine = ? AND M.BuildTypeId = ?
	// 			ORDER BY M.Machine,BuildTypeId
	// 			",
	// 			[
	// 				$machine,
	// 				$buildtype
	// 			]
	// 		);
	// 	}
	// 	else
	// 	{
	// 		$query = Sqlsrv::queryArray(
	// 			$conn,
	// 			"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
	// 			M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
	// 			S.Payment,S.Remark
	// 			FROM RateMaster_V2 M JOIN
	// 			RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
	// 			WHERE  M.RateGroupID = 1 AND
	// 			M.StartDate <= ? AND M.EndDate >= ?
	// 			AND M.Machine = ? AND M.BuildTypeId = ?
	// 			ORDER BY M.Machine,M.BuildTypeId
	// 			",
	// 			[
	// 				$tstart,
	// 				$tstart,
	// 				$machine,
	// 				$buildtype
	// 			]
	// 		);
	// 	}

	// 	return $query;
	// }

	//RateBuild_DailyGroup
	public function RateBuildServicepdf_GROUP($tstart, $tend, $group)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
				$macsize = " AND R.MacSize IS NOT NULL" ;
			} else {
				$group = "PCR";
				$macsize = " AND (R.MacSize IS NULL OR R.MacSize = '')" ;
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT T5.*,
				SUM(T5.Total_Diff) OVER (PARTITION BY T5.Machine) Sum_Total
				FROM
				(
					SELECT T4.*,
					T4.P1+T4.P2+T4.P3 AS Total,
					(T4.P1+T4.P2+T4.P3)-T4.Charge AS Total_Diff
					FROM
					(
						SELECT T3.CreateBy,T3.EmployeeID,T3.Name,T3.Machine,
						T3.BuildTypeId,T3.BuildType,T3.Type,T3.Act,T3.DAAY,T3.SS,
						T3.Charge,T3.Qty1,T3.Qty2,T3.Qty3,T3.RatePrice1,T3.RatePrice2,
						T3.RatePrice3,T3.RateType,T3.P1,T3.P2,T3.P3,
						SUM(T3.SCH) SCH
						FROM
						(
							SELECT T2.CreateBy,T2.EmployeeID,T2.Name,T2.Machine,
							T2.BuildTypeId,T2.BuildType,T2.Type,T2.Act,T2.DAAY,T2.SS
							,CASE
								WHEN D.Charge IS NULL THEN 0
							ELSE D.Charge
							END AS Charge
							,CASE T2.BuildTypeId
								WHEN 1 THEN 'เส้นที่ 1-'+CONVERT(VARCHAR,R.Qty1)
							END AS Qty1,
							CASE R.RateType
								WHEN 'TBR' THEN
									CASE T2.BuildTypeId WHEN 1 THEN 'เส้นที่ '+CONVERT(VARCHAR,R.Qty1+1)+'-'+CONVERT(VARCHAR,R.Qty2) END
							ELSE
								CASE T2.BuildTypeId WHEN 1 THEN '' END
							END AS Qty2,
							CASE R.RateType
							WHEN 'TBR' THEN
								CASE T2.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty3-1)END
							ELSE
								CASE T2.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty1) END
							END AS Qty3,
							R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
							CASE
								WHEN T2.Act >= R.Qty1 THEN R.RatePrice1
							ELSE 0 END AS 'P1',
							CASE
								WHEN (T2.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
								WHEN  T2.Act > R.Qty1 AND ((T2.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T2.Act-R.Qty1)*R.RatePrice2
							ELSE 0 END AS 'P2',
							CASE R.RateType
								WHEN 'TBR' THEN
									CASE WHEN T2.Act >= R.Qty3 THEN (T2.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
								WHEN 'PCR' THEN
									CASE WHEN  T2.Act >= R.Qty2 THEN ((T2.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
							ELSE 0
							END AS 'P3',
							--COUNT(T3.Machine) OVER (PARTITION BY T3.Machine) COUNT_MAC,
							CASE
								WHEN S.Total IS NULL THEN 0
							ELSE S.Total
							END AS 'SCH'
							FROM
							(
								SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DAAY,T1.SS,T1.BuildTypeId,
								T1.BuildType,T1.Type,T1.Machine,T1.Act
								,CASE WHEN T1.BuildTypeId = 2 AND T1.Row2 = 2 THEN 2
								ELSE 1 END AS 'SHOW'
								FROM
								(
									SELECT A.CreateBy,H.EmployeeID,H.Name,H.DAAY,H.SS,H.BuildTypeId,H.BuildType,A.Type,A.Machine,A.Act
									,ROW_NUMBER() OVER(partition by H.BuildTypeId,A.Machine ORDER BY A.Machine DESC) AS Row2
									FROM 
									(
										SELECT T1.CreateBy,T1.Machine,T1.Type,T1.TT Act
										FROM
										(
											SELECT T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,
											CASE 
												WHEN T3.Row2 = 2 THEN ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY T3.MacSize,T3.Machine)  
												ELSE ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY MAX(T3.Act) DESC) 
											END AS Row
											,SUM(T3.Act) OVER(PARTITION BY T3.CreateBy) AS TT
											FROM
											(
												SELECT T2.CreateBy,T2.Machine,T2.Type,T2.Act,T2.MacSize,T2.Row,
												COUNT(T2.Row) OVER(PARTITION BY T2.CreateBy,T2.Row) AS Row2
												FROM
												(
													SELECT T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
													,DENSE_RANK() OVER(PARTITION BY T.CreateBy ORDER BY T.Act) AS Row  
													FROM
													(
														SELECT B.CreateBy,COUNT(B.Barcode) 'Act',BM.ID AS Machine,BM.Type
														FROM BuildTrans B JOIN
														InventTable I ON B.Barcode = I.Barcode JOIN
														BuildingMaster BM ON B.Machine = BM.ID
														WHERE B.CreateDate BETWEEN ? AND ?
														AND I.CheckBuild = 1
														AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
														AND BM.Type = ?
														GROUP BY B.CreateBy,BM.Type,BM.ID
													)T JOIN RateMaster R ON T.Machine = R.Machine $macsize
													GROUP BY T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
												)T2
											)T3
											GROUP BY T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,T3.Row2
										)T1
										WHERE T1.Row = 1
									)A JOIN
									(
										SELECT T.CreateBy,T.EmployeeID,T.Name,T.DAAY,T.SS,T.BuildType,T.BuildTypeId
										FROM
										(
											SELECT B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null','') Name,
											R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description BuildType,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 OR
													 CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) >= 20
													THEN '2'
												ELSE '1'
											END AS SS,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
													THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
												ELSE CONVERT(DATE,R.LoginDate)
											END AS DAAY
											,ROW_NUMBER() OVER(PARTITION BY B.CreateBy ORDER BY R.LoginDate DESC) AS Row
											FROM BuildTrans B JOIN
											RateTrans R ON B.CreateBy = R.UserId AND 
											(B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
											JOIN UserMaster U ON B.CreateBy = U.ID JOIN
											BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
											WHERE B.CreateDate BETWEEN ? AND ?
											AND R.LoginDate BETWEEN ? AND ?
											AND R.LogoutDate BETWEEN ? AND ?
											AND R.RateGroupID = 1
											GROUP BY  B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null',''),
											R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description
										)T
										WHERE T.Row = 1
									)H ON A.CreateBy = H.CreateBy 
								)T1
							)T2 JOIN RateMaster R ON T2.Machine = R.Machine AND T2.BuildTypeId = R.BuildTypeId
							LEFT JOIN RateBuildSchedule S ON T2.Machine = S.Machine AND T2.SS = S.Shift AND
							CONVERT(DATE,T2.DAAY) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
							LEFT JOIN DeductRateBuild D ON T2.Machine = D.Machine
							AND CONVERT(DATE,T2.DAAY) = CONVERT(DATE,D.DeductDate)
							AND T2.CreateBy = D.UserId AND T2.SS = D.Shift
							WHERE T2.Act >= R.Qty1 AND R.RateGroupID = 1 AND T2.SHOW = 1
						)T3
						GROUP BY T3.CreateBy,T3.EmployeeID,T3.Name,T3.Machine,
						T3.BuildTypeId,T3.BuildType,T3.Type,T3.Act,T3.DAAY,T3.SS,
						T3.Charge,T3.Qty1,T3.Qty2,T3.Qty3,T3.RatePrice1,T3.RatePrice2,
						T3.RatePrice3,T3.RateType,T3.P1,T3.P2,T3.P3
					)T4
				)T5
				ORDER BY Machine,BuildTypeId				
				",
				[
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend
				]
			);
			// $query = Sqlsrv::queryArray(
			// 	$conn,
			// 	"SELECT T6.*,
			// 	SUM(T6.Total_Diff) OVER (PARTITION BY T6.Machine) Sum_Total
			// 	FROM
			// 	(
			// 		SELECT T5.*,
			// 		T5.P1+T5.P2+T5.P3 AS Total,
			// 		(T5.P1+T5.P2+T5.P3)-T5.Charge AS Total_Diff
			// 		FROM
			// 		(
			// 			SELECT T4.CreateBy,T4.Machine,T4.LoginDate,T4.LogoutDate,
			// 			T4.Shift,T4.SS,T4.EmployeeID,T4.Name,T4.BuildTypeId,T4.BuildType,
			// 			T4.Type,T4.Act,T4.Charge,T4.Qty1,T4.Qty2,T4.Qty3,
			// 			T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,T4.P1,T4.P2,T4.P3,
			// 			SUM(T4.SCH) SCH
			// 			--,T4.COUNT_MAC,
			// 			--CASE WHEN T4.RateType = 'PCR' AND (T4.COUNT_MAC < 3 )
			// 			--	 THEN (T4.P1+T4.P2+T4.P3)/2
			// 			--ELSE 0
			// 			--END AS P4
			// 			FROM
			// 			(
			// 				SELECT T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,
			// 				T3.Shift,T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,
			// 				T3.Type,T3.Act,
			// 				CASE
			// 					WHEN D.Charge IS NULL THEN 0
			// 				ELSE D.Charge
			// 				END AS Charge,
			// 				CASE T3.BuildTypeId
			// 					WHEN 1 THEN 'เส้นที่ 1-'+CONVERT(VARCHAR,R.Qty1)
			// 				END AS Qty1,
			// 				CASE R.RateType
			// 					WHEN 'TBR' THEN
			// 						CASE T3.BuildTypeId WHEN 1 THEN 'เส้นที่ '+CONVERT(VARCHAR,R.Qty1+1)+'-'+CONVERT(VARCHAR,R.Qty2) END
			// 				ELSE
			// 					CASE T3.BuildTypeId WHEN 1 THEN '' END
			// 				END AS Qty2,
			// 				CASE R.RateType
			// 				WHEN 'TBR' THEN
			// 					CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty3-1)END
			// 				ELSE
			// 					CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty1) END
			// 				END AS Qty3,
			// 				R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
			// 				CASE
			// 					WHEN T3.Act >= R.Qty1 THEN R.RatePrice1
			// 				ELSE 0 END AS 'P1',
			// 				CASE
			// 					WHEN (T3.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
			// 					WHEN  T3.Act > R.Qty1 AND ((T3.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T3.Act-R.Qty1)*R.RatePrice2
			// 				ELSE 0 END AS 'P2',
			// 				CASE R.RateType
			// 					WHEN 'TBR' THEN
			// 						CASE WHEN T3.Act >= R.Qty3 THEN (T3.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
			// 					WHEN 'PCR' THEN
			// 						CASE WHEN  T3.Act >= R.Qty2 THEN ((T3.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
			// 				ELSE 0
			// 				END AS 'P3',
			// 				--COUNT(T3.Machine) OVER (PARTITION BY T3.Machine) COUNT_MAC,
			// 				CASE
			// 					WHEN S.Total IS NULL THEN 0
			// 				ELSE S.Total
			// 				END AS 'SCH',T3.SS
			// 				FROM
			// 				(
			// 					SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
			// 					T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
			// 					T.Type,T.Act,
			// 					CASE
			// 						WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
			// 							 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
			// 							THEN '2'
			// 						ELSE '1'
			// 					END AS SS,
			// 					CASE
			// 						WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
			// 							THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
			// 						ELSE CONVERT(DATE,T2.LoginDate)
			// 					END AS DAAY
			// 					,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
			// 					ELSE 1 END AS 'SHOW'
			// 					FROM
			// 					(
			// 						SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
			// 						T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
			// 						ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
			// 						FROM
			// 						(
			// 							SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
			// 							,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
			// 							U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType
			// 							FROM BuildTrans B JOIN
			// 							RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
			// 							JOIN UserMaster U ON B.CreateBy = U.ID JOIN
			// 							BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
			// 							WHERE B.CreateDate BETWEEN ? AND ?
			// 							AND R.LoginDate BETWEEN ? AND ?
			// 							AND R.LogoutDate BETWEEN ? AND ?
			// 							AND R.RateGroupID = 1
			// 							GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
			// 							REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId
			// 						)T1
			// 						WHERE T1.Row = 1
			// 					)T2
			// 					JOIN
			// 					(
			// 						SELECT B.CreateBy,COUNT(B.Barcode) 'Act',BM.Type
			// 						FROM BuildTrans B JOIN
			// 						InventTable I ON B.Barcode = I.Barcode JOIN
			// 						BuildingMaster BM ON B.Machine = BM.ID
			// 						WHERE B.CreateDate BETWEEN ? AND ?
			// 						AND I.CheckBuild = 1
			// 						AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
			// 						AND BM.Type = ?
			// 						GROUP BY B.CreateBy,BM.Type
			// 					)T ON T2.CreateBy = T.CreateBy
			// 				)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
			// 				LEFT JOIN RateBuildSchedule S ON T3.Machine = S.Machine AND T3.SS = S.Shift AND
			// 				CONVERT(DATE,T3.DAAY) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
			// 				LEFT JOIN DeductRateBuild D ON T3.Machine = D.Machine
			// 				AND CONVERT(DATE,T3.DAAY) = CONVERT(DATE,D.DeductDate)
			// 				AND T3.CreateBy = D.UserId AND T3.SS = D.Shift
			// 				WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.SHOW = 1
			// 			)T4
			// 			GROUP BY T4.CreateBy,T4.Machine,T4.LoginDate,T4.LogoutDate,
			// 			T4.Shift,T4.SS,T4.EmployeeID,T4.Name,T4.BuildTypeId,T4.BuildType,
			// 			T4.Type,T4.Act,T4.Charge,T4.Qty1,T4.Qty2,T4.Qty3,
			// 			T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,T4.P1,T4.P2,T4.P3,
			// 			T4.RateType --T4.COUNT_MAC,
			// 		)T5
			// 	)T6
			// 	GROUP BY CreateBy,T6.Machine,T6.LoginDate,T6.LogoutDate,
			// 	T6.Shift,T6.SS,T6.EmployeeID,T6.Name,T6.BuildTypeId,T6.BuildType,
			// 	T6.Type,T6.Act,T6.Charge,T6.Qty1,T6.Qty2,T6.Qty3,
			// 	T6.RatePrice1,T6.RatePrice2,T6.RatePrice3,T6.P1,T6.P2,T6.P3,
			// 	T6.SCH,T6.Total,T6.Total_Diff
			// 	ORDER BY T6.Machine,T6.BuildTypeId,T6.LoginDate
			// 	",
			// 	[
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$group
			// 	]
			// );
		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT T6.*,
				SUM(T6.Total_Diff) OVER (PARTITION BY T6.Machine) Sum_Total
				FROM
				(
					SELECT T5.*,
					T5.P1+T5.P2+T5.P3+T5.P4 AS Total,
					(T5.P1+T5.P2+T5.P3+T5.P4)-T5.Charge AS Total_Diff
					FROM
					(
						SELECT T4.CreateBy,T4.Machine,T4.LoginDate,T4.LogoutDate,
						T4.Shift,T4.SS,T4.EmployeeID,T4.Name,T4.BuildTypeId,T4.BuildType,
						T4.Type,T4.Act,T4.Charge,T4.Qty1,T4.Qty2,T4.Qty3,
						T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,T4.P1,T4.P2,T4.P3,
						T4.COUNT_MAC,SUM(T4.SCH) SCH,
						CASE WHEN T4.RateType = 'PCR' AND (T4.COUNT_MAC < 3 )
							 THEN (T4.P1+T4.P2+T4.P3)/2
						ELSE 0
						END AS P4
						FROM
						(
							SELECT T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,
							T3.Shift,T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,
							T3.Type,T3.Act,
							CASE
								WHEN D.Charge IS NULL THEN 0
							ELSE D.Charge
							END AS Charge,
							CASE T3.BuildTypeId
								WHEN 1 THEN 'เส้นที่ 1-'+CONVERT(VARCHAR,R.Qty1)
							END AS Qty1,
							CASE R.RateType
								WHEN 'TBR' THEN
									CASE T3.BuildTypeId WHEN 1 THEN 'เส้นที่ '+CONVERT(VARCHAR,R.Qty1+1)+'-'+CONVERT(VARCHAR,R.Qty2) END
							ELSE
								CASE T3.BuildTypeId WHEN 1 THEN '' END
							END AS Qty2,
							CASE R.RateType
							WHEN 'TBR' THEN
								CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty3-1)END
							ELSE
								CASE T3.BuildTypeId WHEN 1 THEN 'มากกว่า >'+CONVERT(VARCHAR,R.Qty1) END
							END AS Qty3,
							R.RatePrice1,R.RatePrice2,R.RatePrice3,R.RateType,
							CASE
								WHEN T3.Act >= R.Qty1 THEN R.RatePrice1
							ELSE 0 END AS 'P1',
							CASE
								WHEN (T3.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2
								WHEN  T3.Act > R.Qty1 AND ((T3.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T3.Act-R.Qty1)*R.RatePrice2
							ELSE 0 END AS 'P2',
							CASE R.RateType
								WHEN 'TBR' THEN
									CASE WHEN T3.Act >= R.Qty3 THEN (T3.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
								WHEN 'PCR' THEN
									CASE WHEN  T3.Act >= R.Qty2 THEN ((T3.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
							ELSE 0
							END AS 'P3',
							COUNT(T3.Machine) OVER (PARTITION BY T3.Machine) COUNT_MAC,
							CASE
								WHEN S.Total IS NULL THEN 0
							ELSE S.Total
							END AS 'SCH',T3.SS
							,R.PLY
							FROM
							(
								SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
								T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
								T.Type,T.Act,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
										 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
										THEN '2'
									ELSE '1'
								END AS SS,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
										THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
									ELSE CONVERT(DATE,T2.LoginDate)
								END AS DAAY
								,T2.GT_Code,T2.ItemNumber
								FROM
								(
									SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
									T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
									T1.GT_Code,T1.ItemNumber
									FROM
									(
										SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
										,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
										U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType
										,I.GT_Code,G.ItemNumber
										FROM BuildTrans B JOIN
										RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate and R.LogoutDate)
										JOIN UserMaster U ON B.CreateBy = U.ID JOIN
										BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
										JOIN InventTable I ON B.Barcode = I.Barcode
										JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
										WHERE B.CreateDate BETWEEN ? AND ?
										AND R.LoginDate BETWEEN ? AND ?
										AND R.LogoutDate BETWEEN ? AND ?
										AND R.RateGroupID = 1
										GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
										REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId,I.GT_Code,G.ItemNumber
									)T1
									WHERE T1.Row = 1
								)T2
								JOIN
								(
									SELECT B.CreateBy,COUNT(B.Barcode) 'Act',BM.Type
									FROM BuildTrans B JOIN
									InventTable I ON B.Barcode = I.Barcode JOIN
									BuildingMaster BM ON B.Machine = BM.ID
									WHERE B.CreateDate BETWEEN ? AND ?
									AND I.CheckBuild = 1
									AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
									AND BM.Type = ? AND B.Machine NOT IN (SELECT Machine FROM RateMaster
									WHERE RateType = ? AND PLY = 0 AND BuildTypeId = 1 GROUP BY Machine)
									GROUP BY B.CreateBy,BM.Type
								)T ON T2.CreateBy = T.CreateBy
							)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
							LEFT JOIN RateBuildSchedule S ON T3.Machine = S.Machine AND T3.SS = S.Shift AND
							CONVERT(DATE,T3.DAAY) = CONVERT(DATE,S.DateRateBuild) AND S.Active = 1
							LEFT JOIN DeductRateBuild D ON T3.Machine = D.Machine
							AND CONVERT(DATE,T3.DAAY) = CONVERT(DATE,D.DeductDate)
							AND T3.CreateBy = D.UserId AND T3.SS = D.Shift
							JOIN
							(
								SELECT T1.ItemGT,T1.PLY
								FROM(
									SELECT ItemGT,PLY,
									ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
									FROM ItemPLY
								)T1
								WHERE T1.Row=1
							)P ON T3.ItemNumber = P.ItemGT AND R.PLY = P.PLY
							WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1
						)T4
						GROUP BY T4.CreateBy,T4.Machine,T4.LoginDate,T4.LogoutDate,
						T4.Shift,T4.SS,T4.EmployeeID,T4.Name,T4.BuildTypeId,T4.BuildType,
						T4.Type,T4.Act,T4.Charge,T4.Qty1,T4.Qty2,T4.Qty3,
						T4.RatePrice1,T4.RatePrice2,T4.RatePrice3,T4.P1,T4.P2,T4.P3,
						T4.COUNT_MAC,T4.RateType
					)T5
				)T6
				GROUP BY CreateBy,T6.Machine,T6.LoginDate,T6.LogoutDate,
				T6.Shift,T6.SS,T6.EmployeeID,T6.Name,T6.BuildTypeId,T6.BuildType,
				T6.Type,T6.Act,T6.Charge,T6.Qty1,T6.Qty2,T6.Qty3,
				T6.RatePrice1,T6.RatePrice2,T6.RatePrice3,T6.P1,T6.P2,T6.P3,
				T6.COUNT_MAC,T6.SCH,T6.P4,T6.Total,T6.Total_Diff
				ORDER BY T6.Machine,T6.BuildTypeId,T6.LoginDate
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$group
				]
			);
		}

		return $query;
	}

	public function RateBuildServicepdf_GROUP_V3($tstart, $tend, $group)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT T4.*,R.QtyMin
				FROM
				(
					SELECT T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,SUM(T3.Total)SCH,
					CASE WHEN T3.Charge IS NULL THEN 0 ELSE T3.Charge END AS Charge
					FROM
					(
						SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
						T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
								 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
								THEN 'N'
							ELSE 'D'
						END AS SS,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
								THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
							ELSE CONVERT(DATE,T2.LoginDate)
						END AS DAAY
						,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
						ELSE 1 END AS 'SHOW',
						TT2.Act,S.Total,D.Charge
						FROM
						(
							SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
							T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
							ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
							FROM
							(
								SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
								,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
								U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType,BM.Type
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
								JOIN UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId JOIN
								BuildingMaster BM ON B.Machine = BM.ID
								WHERE B.CreateDate BETWEEN ? AND ?
								AND R.LoginDate BETWEEN ? AND ?
								AND R.LogoutDate BETWEEN ? AND ?
								AND R.RateGroupID = 1 AND BM.Type = ?
								GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
								REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId,BM.Type
							)T1
							WHERE T1.Row = 1
						)T2 JOIN
						(
							SELECT TT1.CreateBy,TT1.DAAY,TT1.SS,
							TT1.Act
							FROM
							(
								SELECT TT.CreateBy,TT.DAAY,TT.SS
								,SUM(TT.Act) OVER (PARTITION BY TT.CreateBy,TT.DAAY) Act
								,TT.Row
								FROM
								(
									SELECT T.CreateBy,T.DAAY,T.SS,SUM(Act)Act
									,ROW_NUMBER() OVER(PARTITION BY T.SS,T.CreateBy,T.DAAY
									ORDER BY T.DAAY) AS Row
									FROM
									(
										SELECT CreateBy,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
												THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
											ELSE CONVERT(DATE,CreateDate)
										END AS DAAY,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
												 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
												THEN 2
											ELSE 1
										END AS SS,
										COUNT(Barcode) Act,Barcode
										FROM BuildTrans
										WHERE CreateDate BETWEEN ? AND ?
										AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
										GROUP BY CreateBy,CreateDate,Barcode
									)T JOIN
									InventTable I ON T.Barcode = I.Barcode
									WHERE I.CheckBuild = 1
									GROUP BY T.CreateBy,T.DAAY,T.SS
								)TT
							)TT1
							WHERE TT1.Row = 1
						)TT2 ON T2.CreateBy = TT2.CreateBy
						LEFT JOIN RateBuildSchedule S ON TT2.DAAY = S.DateRateBuild AND TT2.SS = S.Shift
						AND T2.Machine = S.Machine AND S.Active = 1
						LEFT JOIN DeductRateBuild D ON TT2.DAAY = D.DeductDate AND TT2.SS = D.Shift
						AND T2.Machine = D.Machine AND T2.CreateBy = D.UserId
					)T3
					WHERE T3.SHOW = 1
					GROUP BY T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,T3.Charge
				)T4
				JOIN
				(
					SELECT Machine,BuildTypeId,QtyMin
					FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND RateGroupID = 1
				)R ON T4.Machine = R.Machine AND T4.BuildTypeId = R.BuildTypeId
				WHERE T4.Act >= R.QtyMin
				GROUP BY T4.CreateBy,T4.Machine,T4.LoginDate,T4.LogoutDate,
				T4.Shift,T4.EmployeeID,T4.Name,T4.BuildTypeId,T4.BuildType,
				T4.SS,T4.DAAY,T4.SHOW,T4.Act,T4.SCH,T4.Charge,R.QtyMin
				ORDER BY T4.Machine,T4.BuildTypeId,T4.LoginDate
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend
				]
			);
		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT *
				FROM
				(
					SELECT T4.*
					,(SELECT QtyMin FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND T4.Machine = Machine AND
					T4.BuildTypeId = BuildTypeId AND T4.PLY = M.PLY) QtyMin
					FROM
					(
						SELECT T3.*
						FROM
						(
							SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
							T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
							T.Type,T.Act,T.SCH,T.Charge,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
									 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
									THEN '2'
								ELSE '1'
							END AS SS,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
									THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
								ELSE CONVERT(DATE,T2.LoginDate)
							END AS DAAY
							,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
							ELSE 1 END AS 'SHOW',T.PLY
							FROM
							(
								SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
								T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
								ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
								FROM
								(
									SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
									,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
									U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType
									FROM BuildTrans B JOIN
									RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
									JOIN UserMaster U ON B.CreateBy = U.ID JOIN
									BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
									WHERE B.CreateDate BETWEEN ? AND ?
									AND R.LoginDate BETWEEN ? AND ?
									AND R.LogoutDate BETWEEN ? AND ?
									AND R.RateGroupID = 1
									AND B.Machine NOT IN (SELECT Machine FROM RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
									GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
									REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId
								)T1
								WHERE T1.Row = 1
							)T2
							JOIN
							(
								SELECT T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,
								T2.Act,SUM(T2.SCH) SCH,T2.Charge,T2.Type,T2.PLY
								FROM
								(
									SELECT T1.*,
									CASE WHEN S.Total IS NULL THEN 0 ELSE S.Total END AS SCH,
									CASE WHEN D.Charge IS NULL THEN 0 ELSE D.Charge END AS Charge
									FROM
									(
										SELECT T.Machine,T.CreateBy,T.DAAY,T.SS,SUM(Act)Act,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
										FROM
										(
											SELECT Machine,CreateBy,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
													THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
												ELSE CONVERT(DATE,CreateDate)
											END AS DAAY,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
													 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
													THEN '2'
												ELSE '1'
											END AS SS,
											COUNT(Barcode) Act,Barcode
											FROM BuildTrans
											WHERE CreateDate BETWEEN ? AND ?
											AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
											GROUP BY Machine,CreateBy,CreateDate,Barcode
										)T JOIN
										InventTable I ON T.Barcode = I.Barcode JOIN
										BuildingMaster BM ON T.Machine = BM.ID JOIN
										GreentireCodeMaster G ON I.GT_Code = G.ID
										JOIN
										(
											SELECT T1.ItemGT,T1.PLY
											FROM(
												SELECT ItemGT,PLY,
												ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
												FROM ItemPLY
											)T1
											WHERE T1.Row=1 --AND T1.ItemGT = 'I-0075824'
										)P ON G.ItemNumber = P.ItemGT --AND R.PLY = P.PLY
										WHERE I.CheckBuild = 1
										AND BM.Type = ? AND BM.ID NOT IN (SELECT Machine FROM
										RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
										GROUP BY T.Machine,T.CreateBy,T.DAAY,T.SS,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
									)T1 LEFT JOIN RateBuildSchedule S ON T1.DAAY = S.DateRateBuild AND T1.SS = S.Shift
									AND T1.Machine = S.Machine AND S.Active = 1
									LEFT JOIN DeductRateBuild D ON T1.DAAY = D.DeductDate AND T1.SS = D.Shift
									AND T1.Machine = D.Machine AND T1.CreateBy = D.UserId
								)T2
								GROUP BY T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,T2.Act,T2.Charge,T2.Type,T2.PLY
							)T ON T2.CreateBy = T.CreateBy
						)T3
						WHERE T3.SHOW = 1
					)T4
				)T5
				WHERE T5.Act >= T5.QtyMin
				ORDER BY Machine,BuildTypeId,LoginDate
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group
				]
			);
		}

		return $query;
	}

	public function getMachineByGROUP($group)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT ID AS Machine,Type
				FROM BuildingMaster
				WHERE Type = ? AND ID IN (SELECT Machine FROM
				RateMaster WHERE RateType = ? AND PLY = 0 GROUP BY Machine)",
				[
					$group,
					$group
				]
			);
		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT ID AS Machine,Type
				FROM BuildingMaster
				WHERE Type = ? AND ID NOT IN (SELECT Machine FROM RateMaster
				WHERE RateType = ? AND PLY = 0 AND BuildTypeId = 1 GROUP BY Machine)",
				[
					$group,
					$group
				]
			);
		}

		return $query;
	}

	public function getMachineByGROUP_V3($group)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}
			if ($group === "TBR") {
				$query = Sqlsrv::queryArray(
					$conn,
					"SELECT ID AS Machine,Type
					FROM BuildingMaster
					WHERE Type = ?
					",
					[
						$group
					]
				);
			} else {
				$query = Sqlsrv::queryArray(
					$conn,
					"SELECT ID AS Machine,Type
					FROM BuildingMaster
					WHERE Type = ? AND ID IN (SELECT Machine
					FROM RateMaster_V2
					WHERE RateType = ? AND PLY = 0
					AND BuildTypeId = 1 AND RateGroupID = 1
					GROUP BY Machine)",
					[
						$group,
						$group
					]
				);
			}
		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT ID AS Machine,Type
				FROM BuildingMaster
				WHERE Type = ? AND ID NOT IN (SELECT Machine
				FROM RateMaster_V2
				WHERE RateType = ? AND PLY = 0
				AND BuildTypeId = 1 AND RateGroupID = 1
				GROUP BY Machine)",
				[
					$group,
					$group
				]
			);
		}

		return $query;
	}

	public function getMachinePLY($machine)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(Machine) Count_Mac
			FROM RateMaster_V2
			WHERE PLY = 0 AND Machine = ?
			AND RateGroupID = 1 ",
			[
				$machine
			]
		);

		return $query[0]['Count_Mac'];
	}

	public function RateMaster_V3($tstart, $tend, $machine, $buildtype)
	{
		$conn = Database::connect();
		$countrow = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) COUNT_RATE
			FROM RateMaster_V2
			WHERE  RateGroupID = 1 AND
			StartDate <= ? AND
			EndDate >= ?
			AND Machine = ? AND BuildTypeId = ?
			",
			[
				$tstart,
				$tstart,
				$machine,
				$buildtype
			]
		);

		if ($countrow[0]['COUNT_RATE'] === 0) {
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
				M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
				S.Payment,S.Remark
				FROM RateMaster_V2 M JOIN
				RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
				WHERE  M.RateGroupID = 1 AND
				M.isDefault = 1
				AND M.Machine = ? AND M.BuildTypeId = ?
				ORDER BY M.Machine,BuildTypeId
				",
				[
					$machine,
					$buildtype
				]
			);
		} else {
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
				M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
				S.Payment,S.Remark
				FROM RateMaster_V2 M JOIN
				RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
				WHERE  M.RateGroupID = 1 AND
				M.StartDate <= ? AND M.EndDate >= ?
				AND M.Machine = ? AND M.BuildTypeId = ?
				ORDER BY M.Machine,M.BuildTypeId
				",
				[
					$tstart,
					$tstart,
					$machine,
					$buildtype
				]
			);
		}

		return $query;
	}

	public function RateMaster_PLY_V3($tstart, $tend, $machine, $buildtype, $ply)
	{
		$conn = Database::connect();
		$countrow = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) COUNT_RATE
			FROM RateMaster_V2
			WHERE  RateGroupID = 1 AND
			StartDate <= ? AND
			EndDate >= ?
			AND Machine = ? AND BuildTypeId = ?
			AND RateGroupID = 1
			",
			[
				$tstart,
				$tstart,
				$machine,
				$buildtype
			]
		);

		// return $countrow[0]['COUNT_RATE'];

		if ($countrow[0]['COUNT_RATE'] === 0) {
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
				M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
				S.Payment,S.Remark
				FROM RateMaster_V2 M JOIN
				RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
				WHERE  M.RateGroupID = 1 AND
				M.isDefault = 1
				AND M.Machine = ? AND M.BuildTypeId = ?
				AND PLY = ?
				ORDER BY M.Machine,BuildTypeId
				",
				[
					$machine,
					$buildtype,
					$ply
				]
			);
		} else {
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT M.Machine,M.RateType,M.BuildTypeId,M.PLY,M.StartDate,M.EndDate,
				M.isDefault,M.RateSeqID,S.SeqID,S.QtyMin,S.QtyMax,S.Price,S.Formula,
				S.Payment,S.Remark
				FROM RateMaster_V2 M JOIN
				RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
				WHERE  M.RateGroupID = 1 AND
				M.StartDate <= ? AND M.EndDate >= ?
				AND M.Machine = ? AND M.BuildTypeId = ?
				AND PLY = ?
				ORDER BY M.Machine,M.BuildTypeId
				",
				[
					$tstart,
					$tstart,
					$machine,
					$buildtype,
					$ply
				]
			);
		}

		return $query;
	}

	public function Count_SeqID_V3($tstart, $tend, $machine, $buildtype)
	{
		$conn = Database::connect();
		$countrow = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(SeqID) CC
			FROM RateMaster_V2 R JOIN
			RateMaster_SEQ S ON R.RateSeqID = S.SeqGrpID
			WHERE  R.RateGroupID = 1 AND
			R.StartDate <= ? AND
			R.EndDate >= ?
			AND R.Machine = ? AND R.BuildTypeId = ?
			",
			[
				$tstart,
				$tstart,
				$machine,
				$buildtype
			]
		);

		return $countrow[0]['CC'];
	}

	public function Count_SeqIDPLY_V3($tstart, $tend, $machine, $buildtype, $ply)
	{
		$conn = Database::connect();
		$countrow = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(SeqID) CC
			FROM RateMaster_V2 R JOIN
			RateMaster_SEQ S ON R.RateSeqID = S.SeqGrpID
			WHERE  R.RateGroupID = 1 AND
			R.StartDate <= ? AND
			R.EndDate >= ?
			AND R.Machine = ? AND R.BuildTypeId = ?
			AND PLY = ?
			",
			[
				$tstart,
				$tstart,
				$machine,
				$buildtype,
				$ply
			]
		);

		return $countrow[0]['CC'];
	}

	public function countUser_ALLGROUP($tstart, $tend, $group)
	{
		$conn = Database::connect();

		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(T3.CreateBy) usertotal
				FROM
				(
					SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DAAY,T1.SS,T1.BuildTypeId,
					T1.BuildType,T1.Type,T1.Machine,T1.Act
					,CASE WHEN T1.BuildTypeId = 2 AND T1.Row2 = 2 THEN 2
					ELSE 1 END AS 'SHOW'
					FROM
					(
						SELECT A.CreateBy,H.EmployeeID,H.Name,H.DAAY,H.SS,H.BuildTypeId,H.BuildType,A.Type,A.Machine,A.Act
						,ROW_NUMBER() OVER(partition by H.BuildTypeId,A.Machine ORDER BY A.Machine DESC) AS Row2
						FROM 
						(
							SELECT T1.CreateBy,T1.Machine,T1.Type,T1.TT Act
							FROM
							(
								SELECT T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,
								CASE 
									WHEN T3.Row2 = 2 THEN ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY T3.MacSize,T3.Machine)  
									ELSE ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY MAX(T3.Act) DESC) 
								END AS Row
								,SUM(T3.Act) OVER(PARTITION BY T3.CreateBy) AS TT
								FROM
								(
									SELECT T2.CreateBy,T2.Machine,T2.Type,T2.Act,T2.MacSize,T2.Row,
									COUNT(T2.Row) OVER(PARTITION BY T2.CreateBy,T2.Row) AS Row2
									FROM
									(
										SELECT T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
										,DENSE_RANK() OVER(PARTITION BY T.CreateBy ORDER BY T.Act) AS Row  
										FROM
										(
											SELECT B.CreateBy,COUNT(B.Barcode) 'Act',
											BM.ID AS Machine,BM.Type
											FROM BuildTrans B JOIN
											InventTable I ON B.Barcode = I.Barcode JOIN
											BuildingMaster BM ON B.Machine = BM.ID
											WHERE B.CreateDate BETWEEN ? AND ?
											AND I.CheckBuild = 1
											AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
											AND BM.Type = ?
											GROUP BY B.CreateBy,BM.Type,BM.ID
										)T JOIN RateMaster R ON T.Machine = R.Machine
										GROUP BY T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
									)T2
								)T3
								GROUP BY T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,T3.Row2
							)T1
							WHERE T1.Row = 1
						)A JOIN
						(
							SELECT T.CreateBy,T.EmployeeID,T.Name,T.DAAY,T.SS,T.BuildType,T.BuildTypeId
							FROM
							(
								SELECT B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null','') Name,
								R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description BuildType,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 OR
										 CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) >= 20
										THEN '2'
									ELSE '1'
								END AS SS,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
										THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
									ELSE CONVERT(DATE,R.LoginDate)
								END AS DAAY
								,ROW_NUMBER() OVER(PARTITION BY B.CreateBy ORDER BY R.LoginDate DESC) AS Row
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND 
								(B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
								JOIN UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId 
								WHERE B.CreateDate BETWEEN ? AND ?
								AND R.LoginDate BETWEEN ? AND ?
								AND R.LogoutDate BETWEEN ? AND ?
								AND R.RateGroupID = 1
								GROUP BY  B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null',''),
								R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description
							)T
							WHERE T.Row = 1
						)H ON A.CreateBy = H.CreateBy 
					)T1
				)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
				WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.SHOW = 1
				",
				[
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend
				]
			);

			// $query = Sqlsrv::queryArray(
			// 	$conn,
			// 	"SELECT COUNT(T3.CreateBy) usertotal
			// 	FROM
			// 	(
			// 		SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
			// 		T2.Shift,T2.BuildTypeId,T.Act,T.Type
			// 		,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
			// 		ELSE 1 END AS 'SHOW'
			// 		FROM
			// 		(
			// 			SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
			// 			T1.Shift,T1.BuildTypeId
			// 			,ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
			// 			FROM
			// 			(
			// 				SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
			// 				,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row
			// 				FROM BuildTrans B JOIN
			// 				RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
			// 				WHERE B.CreateDate BETWEEN ? AND ?
			// 				AND R.LoginDate BETWEEN ? AND ?
			// 				AND R.LogoutDate BETWEEN ? AND ?
			// 				AND R.RateGroupID = 1
			// 				GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
			// 			)T1
			// 			WHERE T1.Row = 1
			// 		)T2
			// 		JOIN
			// 		(
			// 			SELECT B.CreateBy,BM.Type,COUNT(B.Barcode) 'Act'
			// 			FROM BuildTrans B JOIN
			// 			InventTable I ON B.Barcode = I.Barcode JOIN
			// 			BuildingMaster BM ON B.Machine = BM.ID
			// 			WHERE B.CreateDate BETWEEN ? AND ?
			// 			AND I.CheckBuild = 1
			// 			AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
			// 			AND BM.Type = ?
			// 			GROUP BY B.CreateBy,BM.Type
			// 		)T ON T2.CreateBy = T.CreateBy
			// 	)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
			// 	WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.SHOW = 1
			// 	",
			// 	[
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$group
			// 	]
			// );


		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(T3.CreateBy) usertotal
				FROM
				(
					SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
					T2.Shift,T2.BuildTypeId,T.Act,T.Type
					,T2.GT_Code,T2.ItemNumber
					FROM
					(
						SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
						T1.Shift,T1.BuildTypeId,T1.GT_Code,T1.ItemNumber
						FROM
						(
							SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
							,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row
							,I.GT_Code,G.ItemNumber
							FROM BuildTrans B JOIN
							RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
							JOIN InventTable I ON B.Barcode = I.Barcode
							JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
							WHERE B.CreateDate BETWEEN ? AND ?
							AND R.LoginDate BETWEEN ? AND ?
							AND R.LogoutDate BETWEEN ? AND ?
							AND R.RateGroupID = 1
							GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
							,I.GT_Code,G.ItemNumber
						)T1
						WHERE T1.Row = 1
					)T2
					JOIN
					(
						SELECT B.CreateBy,BM.Type,COUNT(B.Barcode) 'Act'
						FROM BuildTrans B JOIN
						InventTable I ON B.Barcode = I.Barcode JOIN
						BuildingMaster BM ON B.Machine = BM.ID
						WHERE B.CreateDate BETWEEN ? AND ?
						AND I.CheckBuild = 1
						AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
						AND BM.Type = ? AND B.Machine NOT IN (SELECT Machine FROM
						RateMaster WHERE RateType = ? AND PLY = 0 GROUP BY Machine)
						GROUP BY B.CreateBy,BM.Type
					)T ON T2.CreateBy = T.CreateBy
				)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
				JOIN
				(
					SELECT T1.ItemGT,T1.PLY
					FROM(
						SELECT ItemGT,PLY,
						ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
						FROM ItemPLY
					)T1
					WHERE T1.Row=1
				)P ON T3.ItemNumber = P.ItemGT AND R.PLY = P.PLY
				WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$group
				]
			);
		}

		return $query[0]['usertotal'];
	}

	public function countUser_ALLGROUP_V3($tstart, $tend, $group)
	{
		$conn = Database::connect();

		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(CreateBy) usertotal
				FROM
				(
					SELECT T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,SUM(T3.Total)SCH,
					CASE WHEN T3.Charge IS NULL THEN 0 ELSE T3.Charge END AS Charge
					FROM
					(
						SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
						T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
								 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
								THEN 'N'
							ELSE 'D'
						END AS SS,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
								THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
							ELSE CONVERT(DATE,T2.LoginDate)
						END AS DAAY
						,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
						ELSE 1 END AS 'SHOW',
						TT2.Act,S.Total,D.Charge
						FROM
						(
							SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
							T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
							ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
							FROM
							(
								SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
								,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
								U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType,BM.Type
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
								JOIN UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId JOIN
								BuildingMaster BM ON B.Machine = BM.ID
								WHERE B.CreateDate BETWEEN ? AND ?
								AND R.LoginDate BETWEEN ? AND ?
								AND R.LogoutDate BETWEEN ? AND ?
								AND R.RateGroupID = 1 AND BM.Type = ?
								GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
								REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId,BM.Type
							)T1
							WHERE T1.Row = 1
						)T2 JOIN
						(
							SELECT TT1.CreateBy,TT1.DAAY,TT1.SS,
							TT1.Act
							FROM
							(
								SELECT TT.CreateBy,TT.DAAY,TT.SS
								,SUM(TT.Act) OVER (PARTITION BY TT.CreateBy,TT.DAAY) Act
								,TT.Row
								FROM
								(
									SELECT T.CreateBy,T.DAAY,T.SS,SUM(Act)Act
									,ROW_NUMBER() OVER(PARTITION BY T.SS,T.CreateBy,T.DAAY
									ORDER BY T.DAAY) AS Row
									FROM
									(
										SELECT CreateBy,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
												THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
											ELSE CONVERT(DATE,CreateDate)
										END AS DAAY,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
												 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
												THEN 2
											ELSE 1
										END AS SS,
										COUNT(Barcode) Act,Barcode
										FROM BuildTrans
										WHERE CreateDate BETWEEN ? AND ?
										AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
										GROUP BY CreateBy,CreateDate,Barcode
									)T JOIN
									InventTable I ON T.Barcode = I.Barcode
									WHERE I.CheckBuild = 1
									GROUP BY T.CreateBy,T.DAAY,T.SS
								)TT
							)TT1
							WHERE TT1.Row = 1
						)TT2 ON T2.CreateBy = TT2.CreateBy
						LEFT JOIN RateBuildSchedule S ON TT2.DAAY = S.DateRateBuild AND TT2.SS = S.Shift
						AND T2.Machine = S.Machine AND S.Active = 1
						LEFT JOIN DeductRateBuild D ON TT2.DAAY = D.DeductDate AND TT2.SS = D.Shift
						AND T2.Machine = D.Machine AND T2.CreateBy = D.UserId
					)T3
					WHERE T3.SHOW = 1
					GROUP BY T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,T3.Charge
				)T4
				JOIN
				(
					SELECT Machine,BuildTypeId,QtyMin
					FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND RateGroupID = 1
				)R ON T4.Machine = R.Machine AND T4.BuildTypeId = R.BuildTypeId
				WHERE T4.Act >= R.QtyMin
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend

				]
			);
		} else {
			$group = "PCR";
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(T5.CreateBy) usertotal
				FROM
				(
					SELECT T4.*
					,(SELECT QtyMin FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND T4.Machine = Machine AND
					T4.BuildTypeId = BuildTypeId AND T4.PLY = M.PLY) QtyMin
					FROM
					(
						SELECT T3.*
						FROM
						(
							SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
							T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
							T.Type,T.Act,T.SCH,T.Charge,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
									 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
									THEN '2'
								ELSE '1'
							END AS SS,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
									THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
								ELSE CONVERT(DATE,T2.LoginDate)
							END AS DAAY
							,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
							ELSE 1 END AS 'SHOW',T.PLY
							FROM
							(
								SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
								T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
								ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
								FROM
								(
									SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
									,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
									U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType
									FROM BuildTrans B JOIN
									RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
									JOIN UserMaster U ON B.CreateBy = U.ID JOIN
									BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
									WHERE B.CreateDate BETWEEN ? AND ?
									AND R.LoginDate BETWEEN ? AND ?
									AND R.LogoutDate BETWEEN ? AND ?
									AND R.RateGroupID = 1
									AND B.Machine NOT IN (SELECT Machine FROM RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
									GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
									REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId
								)T1
								WHERE T1.Row = 1
							)T2
							JOIN
							(
								SELECT T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,
								T2.Act,SUM(T2.SCH) SCH,T2.Charge,T2.Type,T2.PLY
								FROM
								(
									SELECT T1.*,
									CASE WHEN S.Total IS NULL THEN 0 ELSE S.Total END AS SCH,
									CASE WHEN D.Charge IS NULL THEN 0 ELSE D.Charge END AS Charge
									FROM
									(
										SELECT T.Machine,T.CreateBy,T.DAAY,T.SS,SUM(Act)Act,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
										FROM
										(
											SELECT Machine,CreateBy,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
													THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
												ELSE CONVERT(DATE,CreateDate)
											END AS DAAY,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
													 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
													THEN '2'
												ELSE '1'
											END AS SS,
											COUNT(Barcode) Act,Barcode
											FROM BuildTrans
											WHERE CreateDate BETWEEN ? AND ?
											AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
											GROUP BY Machine,CreateBy,CreateDate,Barcode
										)T JOIN
										InventTable I ON T.Barcode = I.Barcode JOIN
										BuildingMaster BM ON T.Machine = BM.ID JOIN
										GreentireCodeMaster G ON I.GT_Code = G.ID
										JOIN
										(
											SELECT T1.ItemGT,T1.PLY
											FROM(
												SELECT ItemGT,PLY,
												ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
												FROM ItemPLY
											)T1
											WHERE T1.Row=1 --AND T1.ItemGT = 'I-0075824'
										)P ON G.ItemNumber = P.ItemGT --AND R.PLY = P.PLY
										WHERE I.CheckBuild = 1
										AND BM.Type = ? AND BM.ID NOT IN (SELECT Machine FROM RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
										GROUP BY T.Machine,T.CreateBy,T.DAAY,T.SS,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
									)T1 LEFT JOIN RateBuildSchedule S ON T1.DAAY = S.DateRateBuild AND T1.SS = S.Shift
									AND T1.Machine = S.Machine AND S.Active = 1
									LEFT JOIN DeductRateBuild D ON T1.DAAY = D.DeductDate AND T1.SS = D.Shift
									AND T1.Machine = D.Machine AND T1.CreateBy = D.UserId
								)T2
								GROUP BY T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,T2.Act,T2.Charge,T2.Type,T2.PLY
							)T ON T2.CreateBy = T.CreateBy
						)T3
						WHERE T3.SHOW = 1 --AND Machine = ?
					)T4
				)T5
				WHERE T5.Act >= T5.QtyMin
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group
				]
			);
		}

		return $query[0]['usertotal'];
	}

	public function countUserByMachine($tstart, $tend, $group, $machine)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
				$macsize = " AND R.MacSize IS NOT NULL" ;
			} else {
				$group = "PCR";
				$macsize = " AND (R.MacSize IS NULL OR R.MacSize = '')" ;
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT CASE
				WHEN COUNT(T3.CreateBy) IS NULL THEN 0
				ELSE COUNT(T3.CreateBy)
				END AS COUNT_USER
				FROM
				(
					SELECT T1.CreateBy,T1.EmployeeID,T1.Name,T1.DAAY,T1.SS,T1.BuildTypeId,
					T1.BuildType,T1.Type,T1.Machine,T1.Act
					,CASE WHEN T1.BuildTypeId = 2 AND T1.Row2 = 2 THEN 2
					ELSE 1 END AS 'SHOW'
					FROM
					(
						SELECT A.CreateBy,H.EmployeeID,H.Name,H.DAAY,H.SS,H.BuildTypeId,H.BuildType,A.Type,A.Machine,A.Act
						,ROW_NUMBER() OVER(partition by H.BuildTypeId,A.Machine ORDER BY A.Machine DESC) AS Row2
						FROM 
						(
							SELECT T1.CreateBy,T1.Machine,T1.Type,T1.TT Act
							FROM
							(
								SELECT T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,
								CASE 
									WHEN T3.Row2 = 2 THEN ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY T3.MacSize,T3.Machine)  
									ELSE ROW_NUMBER() OVER(PARTITION BY T3.CreateBy ORDER BY MAX(T3.Act) DESC) 
								END AS Row
								,SUM(T3.Act) OVER(PARTITION BY T3.CreateBy) AS TT
								FROM
								(
									SELECT T2.CreateBy,T2.Machine,T2.Type,T2.Act,T2.MacSize,T2.Row,
									COUNT(T2.Row) OVER(PARTITION BY T2.CreateBy,T2.Row) AS Row2
									FROM
									(
										SELECT T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
										,DENSE_RANK() OVER(PARTITION BY T.CreateBy ORDER BY T.Act) AS Row  
										FROM
										(
											SELECT B.CreateBy,COUNT(B.Barcode) 'Act',BM.ID AS Machine,BM.Type
											FROM BuildTrans B JOIN
											InventTable I ON B.Barcode = I.Barcode JOIN
											BuildingMaster BM ON B.Machine = BM.ID
											WHERE B.CreateDate BETWEEN ? AND ?
											AND I.CheckBuild = 1
											AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
											AND BM.Type = ?
											GROUP BY B.CreateBy,BM.Type,BM.ID 
										)T JOIN RateMaster R ON T.Machine = R.Machine $macsize
										GROUP BY T.CreateBy,T.Machine,T.Type,T.Act,R.MacSize
									)T2
								)T3
								GROUP BY T3.CreateBy,T3.Machine,T3.Type,T3.Act,T3.MacSize,T3.Row2
							)T1
							WHERE T1.Row = 1
						)A JOIN
						(
							SELECT T.CreateBy,T.EmployeeID,T.Name,T.DAAY,T.SS,T.BuildType,T.BuildTypeId
							FROM
							(
								SELECT B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null','') Name,
								R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description BuildType,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 OR
										 CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) >= 20
										THEN '2'
									ELSE '1'
								END AS SS,
								CASE
									WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7
										THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
									ELSE CONVERT(DATE,R.LoginDate)
								END AS DAAY
								,ROW_NUMBER() OVER(PARTITION BY B.CreateBy ORDER BY R.LoginDate DESC) AS Row
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND 
								(B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
								JOIN UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId 
								WHERE B.CreateDate BETWEEN ? AND ?
								AND R.LoginDate BETWEEN ? AND ?
								AND R.LogoutDate BETWEEN ? AND ?
								AND R.RateGroupID = 1
								GROUP BY  B.CreateBy,U.EmployeeID,REPLACE(U.Name,'null',''),
								R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId,T.Description
							)T
							WHERE T.Row = 1
						)H ON A.CreateBy = H.CreateBy 
					)T1
				)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
				WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.SHOW = 1 AND T3.Machine = ?
				",
				[
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$machine
				]
			);

			// $query = Sqlsrv::queryArray(
			// 	$conn,
			// 	"SELECT CASE
			// 	WHEN COUNT(T3.CreateBy) IS NULL THEN 0
			// 	ELSE COUNT(T3.CreateBy)
			// 	END AS COUNT_USER
			// 	FROM
			// 	(
			// 		SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
			// 		T2.Shift,T2.BuildTypeId,T.Act,T.Type
			// 		,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
			// 		ELSE 1 END AS 'SHOW'
			// 		FROM
			// 		(
			// 			SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
			// 			T1.Shift,T1.BuildTypeId
			// 			,ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
			// 			FROM
			// 			(
			// 				SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
			// 				,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row
			// 				FROM BuildTrans B JOIN
			// 				RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
			// 				WHERE B.CreateDate BETWEEN ? AND ?
			// 				AND R.LoginDate BETWEEN ? AND ?
			// 				AND R.LogoutDate BETWEEN ? AND ?
			// 				AND R.RateGroupID = 1
			// 				GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
			// 			)T1
			// 			WHERE T1.Row = 1
			// 		)T2
			// 		JOIN
			// 		(
			// 			SELECT B.CreateBy,BM.Type,COUNT(B.Barcode) 'Act'
			// 			FROM BuildTrans B JOIN
			// 			InventTable I ON B.Barcode = I.Barcode JOIN
			// 			BuildingMaster BM ON B.Machine = BM.ID
			// 			WHERE B.CreateDate BETWEEN ? AND ?
			// 			AND I.CheckBuild = 1
			// 			AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
			// 			AND BM.Type = ?
			// 			GROUP BY B.CreateBy,BM.Type
			// 		)T ON T2.CreateBy = T.CreateBy
			// 	)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
			// 	WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.SHOW = 1 AND T3.Machine = ?
			// 	",
			// 	[
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$tstart,
			// 		$tend,
			// 		$group,
			// 		$machine
			// 	]
			// );

		} else {
			$group = "PCR";

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT CASE
				WHEN COUNT(T3.CreateBy) IS NULL THEN 0
				ELSE COUNT(T3.CreateBy)
				END AS COUNT_USER
				FROM
				(
					SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
					T2.Shift,T2.BuildTypeId,T.Act,T.Type
					,T2.GT_Code,T2.ItemNumber
					FROM
					(
						SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
						T1.Shift,T1.BuildTypeId,T1.GT_Code,T1.ItemNumber
						FROM
						(
							SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
							,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row
							,I.GT_Code,G.ItemNumber
							FROM BuildTrans B JOIN
							RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
							JOIN InventTable I ON B.Barcode = I.Barcode
							JOIN GreentireCodeMaster G ON I.GT_Code = G.ID
							WHERE B.CreateDate BETWEEN ? AND ?
							AND R.LoginDate BETWEEN ? AND ?
							AND R.LogoutDate BETWEEN ? AND ?
							AND R.RateGroupID = 1
							GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
							,I.GT_Code,G.ItemNumber
						)T1
						WHERE T1.Row = 1
					)T2
					JOIN
					(
						SELECT B.CreateBy,BM.Type,COUNT(B.Barcode) 'Act'
						FROM BuildTrans B JOIN
						InventTable I ON B.Barcode = I.Barcode JOIN
						BuildingMaster BM ON B.Machine = BM.ID
						WHERE B.CreateDate BETWEEN ? AND ?
						AND I.CheckBuild = 1
						AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
						AND BM.Type = ? AND B.Machine NOT IN (SELECT Machine FROM
						RateMaster WHERE RateType = ? AND PLY = 0 GROUP BY Machine)
						GROUP BY B.CreateBy,BM.Type
					)T ON T2.CreateBy = T.CreateBy
				)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
				JOIN
				(
					SELECT T1.ItemGT,T1.PLY
					FROM(
						SELECT ItemGT,PLY,
						ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
						FROM ItemPLY
					)T1
					WHERE T1.Row=1
				)P ON T3.ItemNumber = P.ItemGT AND R.PLY = P.PLY
				WHERE T3.Act >= R.Qty1 AND R.RateGroupID = 1 AND T3.Machine = ?
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$group,
					$machine
				]
			);
		}

		return $query[0]['COUNT_USER'];
	}

	public function countUserByMachine_V3($tstart, $tend, $group, $machine)
	{
		$conn = Database::connect();
		if ($group === 'tbr' || $group === 'pcr_n') {
			if ($group === "tbr") {
				$group = "TBR";
			} else {
				$group = "PCR";
			}

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(CreateBy) COUNT_USER
				FROM
				(
					SELECT T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,SUM(T3.Total)SCH,
					CASE WHEN T3.Charge IS NULL THEN 0 ELSE T3.Charge END AS Charge
					FROM
					(
						SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
						T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
								 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
								THEN 'N'
							ELSE 'D'
						END AS SS,
						CASE
							WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
								THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
							ELSE CONVERT(DATE,T2.LoginDate)
						END AS DAAY
						,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
						ELSE 1 END AS 'SHOW',
						TT2.Act,S.Total,D.Charge
						FROM
						(
							SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
							T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
							ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
							FROM
							(
								SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
								,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
								U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType,BM.Type
								FROM BuildTrans B JOIN
								RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
								JOIN UserMaster U ON B.CreateBy = U.ID JOIN
								BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId JOIN
								BuildingMaster BM ON B.Machine = BM.ID
								WHERE B.CreateDate BETWEEN ? AND ?
								AND R.LoginDate BETWEEN ? AND ?
								AND R.LogoutDate BETWEEN ? AND ?
								AND R.RateGroupID = 1 AND BM.Type = ?
								GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
								REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId,BM.Type
							)T1
							WHERE T1.Row = 1
						)T2 JOIN
						(
							SELECT TT1.CreateBy,TT1.DAAY,TT1.SS,
							TT1.Act
							FROM
							(
								SELECT TT.CreateBy,TT.DAAY,TT.SS
								,SUM(TT.Act) OVER (PARTITION BY TT.CreateBy,TT.DAAY) Act
								,TT.Row
								FROM
								(
									SELECT T.CreateBy,T.DAAY,T.SS,SUM(Act)Act
									,ROW_NUMBER() OVER(PARTITION BY T.SS,T.CreateBy,T.DAAY
									ORDER BY T.DAAY) AS Row
									FROM
									(
										SELECT CreateBy,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
												THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
											ELSE CONVERT(DATE,CreateDate)
										END AS DAAY,
										CASE
											WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
												 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
												THEN 2
											ELSE 1
										END AS SS,
										COUNT(Barcode) Act,Barcode
										FROM BuildTrans
										WHERE CreateDate BETWEEN ? AND ?
										AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
										GROUP BY CreateBy,CreateDate,Barcode
									)T JOIN
									InventTable I ON T.Barcode = I.Barcode
									WHERE I.CheckBuild = 1
									GROUP BY T.CreateBy,T.DAAY,T.SS
								)TT
							)TT1
							WHERE TT1.Row = 1
						)TT2 ON T2.CreateBy = TT2.CreateBy
						LEFT JOIN RateBuildSchedule S ON TT2.DAAY = S.DateRateBuild AND TT2.SS = S.Shift
						AND T2.Machine = S.Machine AND S.Active = 1
						LEFT JOIN DeductRateBuild D ON TT2.DAAY = D.DeductDate AND TT2.SS = D.Shift
						AND T2.Machine = D.Machine AND T2.CreateBy = D.UserId
					)T3
					WHERE T3.SHOW = 1
					GROUP BY T3.CreateBy,T3.Machine,T3.LoginDate,T3.LogoutDate,T3.Shift,
					T3.EmployeeID,T3.Name,T3.BuildTypeId,T3.BuildType,T3.SS,T3.DAAY,
					T3.SHOW,T3.Act,T3.Charge
				)T4
				JOIN
				(
					SELECT Machine,BuildTypeId,QtyMin
					FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND RateGroupID = 1
				)R ON T4.Machine = R.Machine AND T4.BuildTypeId = R.BuildTypeId
				WHERE T4.Act >= R.QtyMin AND T4.Machine = ?
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$tstart,
					$tend,
					$machine
				]
			);
		} else {
			$group = "PCR";

			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT COUNT(T5.CreateBy) COUNT_USER
				FROM
				(
					SELECT T4.*
					,(SELECT QtyMin FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE S.SeqID = 1 AND T4.Machine = Machine AND
					T4.BuildTypeId = BuildTypeId AND T4.PLY = M.PLY) QtyMin
					FROM
					(
						SELECT T3.*
						FROM
						(
							SELECT T2.CreateBy,T2.Machine,T2.LoginDate,T2.LogoutDate,
							T2.Shift,T2.EmployeeID,T2.Name,T2.BuildTypeId,T2.BuildType,
							T.Type,T.Act,T.SCH,T.Charge,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7 OR
									 CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) >= 20
									THEN '2'
								ELSE '1'
							END AS SS,
							CASE
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, T2.LoginDate, 8),2)) <= 7
									THEN DATEADD(DAY,-1,CONVERT(DATE,T2.LoginDate))
								ELSE CONVERT(DATE,T2.LoginDate)
							END AS DAAY
							,CASE WHEN T2.BuildTypeId = 2 AND T2.Row2 = 2 THEN 2
							ELSE 1 END AS 'SHOW',T.PLY
							FROM
							(
								SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
								T1.Shift,T1.EmployeeID,T1.Name,T1.BuildTypeId,T1.BuildType,
								ROW_NUMBER() OVER(partition by T1.BuildTypeId,T1.Machine ORDER BY T1.Machine,T1.LoginDate DESC) AS Row2
								FROM
								(
									SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
									,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
									U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType
									FROM BuildTrans B JOIN
									RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
									JOIN UserMaster U ON B.CreateBy = U.ID JOIN
									BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId
									WHERE B.CreateDate BETWEEN ? AND ?
									AND R.LoginDate BETWEEN ? AND ?
									AND R.LogoutDate BETWEEN ? AND ?
									AND R.RateGroupID = 1
									AND B.Machine NOT IN (SELECT Machine FROM RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
									GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
									REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId
								)T1
								WHERE T1.Row = 1
							)T2
							JOIN
							(
								SELECT T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,
								T2.Act,SUM(T2.SCH) SCH,T2.Charge,T2.Type,T2.PLY
								FROM
								(
									SELECT T1.*,
									CASE WHEN S.Total IS NULL THEN 0 ELSE S.Total END AS SCH,
									CASE WHEN D.Charge IS NULL THEN 0 ELSE D.Charge END AS Charge
									FROM
									(
										SELECT T.Machine,T.CreateBy,T.DAAY,T.SS,SUM(Act)Act,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
										FROM
										(
											SELECT Machine,CreateBy,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7
													THEN DATEADD(DAY,-1,CONVERT(DATE,CreateDate))
												ELSE CONVERT(DATE,CreateDate)
											END AS DAAY,
											CASE
												WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) <= 7 OR
													 CONVERT(INT,LEFT(CONVERT(VARCHAR, CreateDate, 8),2)) >= 20
													THEN '2'
												ELSE '1'
											END AS SS,
											COUNT(Barcode) Act,Barcode
											FROM BuildTrans
											WHERE CreateDate BETWEEN ? AND ?
											AND Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
											GROUP BY Machine,CreateBy,CreateDate,Barcode
										)T JOIN
										InventTable I ON T.Barcode = I.Barcode JOIN
										BuildingMaster BM ON T.Machine = BM.ID JOIN
										GreentireCodeMaster G ON I.GT_Code = G.ID
										JOIN
										(
											SELECT T1.ItemGT,T1.PLY
											FROM(
												SELECT ItemGT,PLY,
												ROW_NUMBER() OVER(partition by ItemGT ORDER BY PLY DESC) AS Row
												FROM ItemPLY
											)T1
											WHERE T1.Row=1 --AND T1.ItemGT = 'I-0075824'
										)P ON G.ItemNumber = P.ItemGT --AND R.PLY = P.PLY
										WHERE I.CheckBuild = 1
										AND BM.Type = ? AND BM.ID NOT IN (SELECT Machine FROM RateMaster_V2 WHERE PLY = 0 GROUP BY Machine)
										GROUP BY T.Machine,T.CreateBy,T.DAAY,T.SS,
										BM.Type,I.GT_Code,G.ItemNumber,P.PLY
									)T1 LEFT JOIN RateBuildSchedule S ON T1.DAAY = S.DateRateBuild AND T1.SS = S.Shift
									AND T1.Machine = S.Machine AND S.Active = 1
									LEFT JOIN DeductRateBuild D ON T1.DAAY = D.DeductDate AND T1.SS = D.Shift
									AND T1.Machine = D.Machine AND T1.CreateBy = D.UserId
								)T2
								GROUP BY T2.Machine,T2.CreateBy,T2.DAAY,T2.SS,T2.Act,T2.Charge,T2.Type,T2.PLY
							)T ON T2.CreateBy = T.CreateBy
						)T3
						WHERE T3.SHOW = 1 AND Machine = ?
					)T4
				)T5
				WHERE T5.Act >= T5.QtyMin
				",
				[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$group,
					$machine
				]
			);
		}

		return $query[0]['COUNT_USER'];
	}

	//RateCure_Daily
	public function RateCureServicepdf($tstart, $tend)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T5.*,(SUM(T5.RatePay) OVER (PARTITION BY T5.CreateBy))/2 AS TOTAL
			FROM
			(
				SELECT T4.CreateBy,T4.EmployeeID,T4.Name,
				T4.PressNo,T4.PressSide,T4.CuringCode,T4.rate12,
				T4.Act,T4.CHK_QTY,T4.CHK,
				CASE WHEN T4.RatePay IS NULL THEN
				(
					SELECT TOP 1 S.Price
					FROM RateMaster_V2 M JOIN
					RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
					WHERE M.RateGroupID = 2 AND M.Machine = T4.PressNo
					AND M.isDefault = 1
				)
				ELSE T4.RatePay
				END AS RatePay
				FROM
				(
					SELECT T3.*,
					CASE WHEN T3.CHK >= 1 THEN
					(
						SELECT TOP 1 S.Price
						FROM RateMaster_V2 M JOIN
						RateMaster_SEQ S ON M.RateSeqID = S.SeqGrpID
						WHERE M.RateGroupID = 2 AND M.Machine = T3.PressNo
						AND ? BETWEEN M.StartDate AND M.EndDate
						AND M.isDefault = 0
						ORDER BY EndDate ASC

					)
					ELSE 0
					END AS RatePay
					FROM
					(
						SELECT T2.*,
						SUM(T2.CHK_QTY) OVER (PARTITION BY T2.CreateBy,T2.PressNo) CHK
						FROM
						(
							SELECT H2.*,D.CuringCode,D.rate12,D.Act,
							CASE WHEN D.Act > 0 THEN
								 CASE WHEN D.Act >= (D.rate12-1) THEN 1
								 ELSE 0 END
							ELSE 0
							END AS CHK_QTY
							FROM
							(
								SELECT H1.PressNo,H1.PressSide,H1.CreateBy,
									H1.EmployeeID,H1.Name--,H1.CREATE_TIME
								FROM
								(
									SELECT H.*
									,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY CREATE_TIME DESC) AS Row
									FROM
									(
										SELECT M1.PressNo,M1.ID PressSide,M2.CreateBy,M2.EmployeeID,
										M2.Name,M2.CREATE_TIME
										FROM
										(
											SELECT C.PressNo,P.ID
											FROM PressArmMaster P,CureTrans C
											WHERE C.CreateDate BETWEEN ? AND ?
											GROUP BY C.PressNo,P.ID
										)M1 LEFT JOIN
										(
											SELECT C1.PressNo,C1.PressSide,C1.CreateBy
											,U.EmployeeID,REPLACE(U.Name,'null','') Name
											,MAX(C1.CREATEDATE) CREATE_TIME
											FROM CureTrans C1 JOIN UserMaster U ON C1.CreateBy = U.ID
											WHERE C1.CreateDate BETWEEN ? AND ?
											GROUP BY C1.PressNo,C1.PressSide,C1.CreateBy,U.EmployeeID,U.Name
										)M2 ON M1.PressNo = M2.PressNo --AND M1.ID = M2.PressSide
									)H
								)H1
								WHERE H1.Row = 1
							)H2
							JOIN
							(
								SELECT C4.PressNo,C4.PressSide,C4.CuringCode2 CuringCode,C4.rate12,C4.Act
								FROM
								(
									SELECT C3.PressNo,C3.PressSide
									,SUM(C3.Act) OVER(PARTITION BY PressNo,PressSide) AS Act
									,C3.CuringCode,C3.rate12,C3.Row
									,CuringCode2 = STUFF((
									SELECT ',' + C2.CuringCode
									FROM CureTrans C2
									WHERE C3.PressNo = C2.PressNo
									AND C3.PressSide = C2.PressSide
									AND C2.CreateDate BETWEEN ? AND ?
									GROUP BY C2.CuringCode
									FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
									FROM
									(
										SELECT M.PressNo,M.ID PressSide,M2.CuringCode,
										CASE WHEN M2.Act IS NULL THEN 0 ELSE M2.Act END AS Act,
										CASE WHEN M2.rate12 IS NULL THEN 0 ELSE M2.rate12 END AS rate12,
										CASE WHEN M2.Row IS NULL THEN 1 ELSE M2.Row END AS Row
										FROM
										(
											SELECT C.PressNo,P.ID
											FROM PressArmMaster P,CureTrans C
											WHERE C.CreateDate BETWEEN ? AND ?
											GROUP BY C.PressNo,P.ID
										)M LEFT JOIN
										(
											SELECT C1.PressNo,C1.PressSide,COUNT(C1.Barcode) Act
											,C1.CuringCode,CM.rate12
											,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY rate12) AS Row
											FROM CureTrans C1 JOIN
											CureCodeMaster CM ON C1.CuringCode = CM.ID
											WHERE C1.CreateDate BETWEEN ? AND ?
											GROUP BY C1.PressNo,C1.PressSide,C1.CuringCode,CM.rate12
										)M2 ON M.PressNo = M2.PressNo AND M.ID = M2.PressSide
									)C3
								)C4
								WHERE C4.Row = 1
							)D ON H2.PressNo = D.PressNo AND H2.PressSide = D.PressSide
						)T2
					)T3
				)T4
			)T5
			ORDER BY CreateBy,PressNo,PressSide
			",
			[
				$tstart,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend
			]
		);

		return $query;
	}

	public function getUser($tstart, $tend)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT C.CreateBy
			FROM CureTrans C JOIN
			CureCodeMaster CM ON C.CuringCode = CM.ID
			WHERE C.CreateDate BETWEEN ? AND ?
			GROUP BY C.CreateBy
			ORDER BY C.CreateBy ",
			[
				$tstart,
				$tend
			]
		);

		return $query;
	}

	public function countRowByUser($tstart, $tend, $userid)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(T2.CreateBy)Row
			FROM
			(
				SELECT H2.*,D.CuringCode,D.rate12,D.Act,
				CASE WHEN D.Act > 0 THEN
					 CASE WHEN D.Act >= (D.rate12-1) THEN 1
					 ELSE 0 END
				ELSE 0
				END AS CHK_QTY
				FROM
				(
					SELECT H1.PressNo,H1.PressSide,H1.CreateBy,
						H1.EmployeeID,H1.Name--,H1.CREATE_TIME
					FROM
					(
						SELECT H.*
						,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY CREATE_TIME DESC) AS Row
						FROM
						(
							SELECT M1.PressNo,M1.ID PressSide,M2.CreateBy,M2.EmployeeID,
							M2.Name,M2.CREATE_TIME
							FROM
							(
								SELECT C.PressNo,P.ID
								FROM PressArmMaster P,CureTrans C
								WHERE C.CreateDate BETWEEN ? AND ?
								GROUP BY C.PressNo,P.ID
							)M1 LEFT JOIN
							(
								SELECT C1.PressNo,C1.PressSide,C1.CreateBy
								,U.EmployeeID,REPLACE(U.Name,'null','') Name
								,MAX(C1.CREATEDATE) CREATE_TIME
								FROM CureTrans C1 JOIN UserMaster U ON C1.CreateBy = U.ID
								WHERE C1.CreateDate BETWEEN ? AND ?
								GROUP BY C1.PressNo,C1.PressSide,C1.CreateBy,U.EmployeeID,U.Name
							)M2 ON M1.PressNo = M2.PressNo --AND M1.ID = M2.PressSide
						)H
					)H1
					WHERE H1.Row = 1
				)H2
				JOIN
				(
					SELECT C4.PressNo,C4.PressSide,C4.CuringCode2 CuringCode,C4.rate12,C4.Act
					FROM
					(
						SELECT C3.PressNo,C3.PressSide
						,SUM(C3.Act) OVER(PARTITION BY PressNo,PressSide) AS Act
						,C3.CuringCode,C3.rate12,C3.Row
						,CuringCode2 = STUFF((
						SELECT ',' + C2.CuringCode
						FROM CureTrans C2
						WHERE C3.PressNo = C2.PressNo
						AND C3.PressSide = C2.PressSide
						AND C2.CreateDate BETWEEN ? AND ?
						GROUP BY C2.CuringCode
						FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
						FROM
						(
							SELECT M.PressNo,M.ID PressSide,M2.CuringCode,
							CASE WHEN M2.Act IS NULL THEN 0 ELSE M2.Act END AS Act,
							CASE WHEN M2.rate12 IS NULL THEN 0 ELSE M2.rate12 END AS rate12,
							CASE WHEN M2.Row IS NULL THEN 1 ELSE M2.Row END AS Row
							FROM
							(
								SELECT C.PressNo,P.ID
								FROM PressArmMaster P,CureTrans C
								WHERE C.CreateDate BETWEEN ? AND ?
								GROUP BY C.PressNo,P.ID
							)M LEFT JOIN
							(
								SELECT C1.PressNo,C1.PressSide,COUNT(C1.Barcode) Act
								,C1.CuringCode,CM.rate12
								,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY rate12) AS Row
								FROM CureTrans C1 JOIN
								CureCodeMaster CM ON C1.CuringCode = CM.ID
								WHERE C1.CreateDate BETWEEN ? AND ?
								GROUP BY C1.PressNo,C1.PressSide,C1.CuringCode,CM.rate12
							)M2 ON M.PressNo = M2.PressNo AND M.ID = M2.PressSide
						)C3
					)C4
					WHERE C4.Row = 1
				)D ON H2.PressNo = D.PressNo AND H2.PressSide = D.PressSide
			)T2
			WHERE T2.CreateBy = ?
			",
			[
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$userid
			]
		);

		return $query[0]['Row'];
	}

	public function countArmByPress($tstart, $tend, $userid, $pressno)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(T2.CreateBy) Count_Arm
			FROM
			(
				SELECT H2.*,D.CuringCode,D.rate12,D.Act,
				CASE WHEN D.Act > 0 THEN
					 CASE WHEN D.Act >= (D.rate12-1) THEN 1
					 ELSE 0 END
				ELSE 0
				END AS CHK_QTY
				FROM
				(
					SELECT H1.PressNo,H1.PressSide,H1.CreateBy,
						H1.EmployeeID,H1.Name--,H1.CREATE_TIME
					FROM
					(
						SELECT H.*
						,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY CREATE_TIME DESC) AS Row
						FROM
						(
							SELECT M1.PressNo,M1.ID PressSide,M2.CreateBy,M2.EmployeeID,
							M2.Name,M2.CREATE_TIME
							FROM
							(
								SELECT C.PressNo,P.ID
								FROM PressArmMaster P,CureTrans C
								WHERE C.CreateDate BETWEEN ? AND ?
								GROUP BY C.PressNo,P.ID
							)M1 LEFT JOIN
							(
								SELECT C1.PressNo,C1.PressSide,C1.CreateBy
								,U.EmployeeID,REPLACE(U.Name,'null','') Name
								,MAX(C1.CREATEDATE) CREATE_TIME
								FROM CureTrans C1 JOIN UserMaster U ON C1.CreateBy = U.ID
								WHERE C1.CreateDate BETWEEN ? AND ?
								GROUP BY C1.PressNo,C1.PressSide,C1.CreateBy,U.EmployeeID,U.Name
							)M2 ON M1.PressNo = M2.PressNo --AND M1.ID = M2.PressSide
						)H
					)H1
					WHERE H1.Row = 1
				)H2
				JOIN
				(
					SELECT C4.PressNo,C4.PressSide,C4.CuringCode2 CuringCode,C4.rate12,C4.Act
					FROM
					(
						SELECT C3.PressNo,C3.PressSide
						,SUM(C3.Act) OVER(PARTITION BY PressNo,PressSide) AS Act
						,C3.CuringCode,C3.rate12,C3.Row
						,CuringCode2 = STUFF((
						SELECT ',' + C2.CuringCode
						FROM CureTrans C2
						WHERE C3.PressNo = C2.PressNo
						AND C3.PressSide = C2.PressSide
						AND C2.CreateDate BETWEEN ? AND ?
						GROUP BY C2.CuringCode
						FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
						FROM
						(
							SELECT M.PressNo,M.ID PressSide,M2.CuringCode,
							CASE WHEN M2.Act IS NULL THEN 0 ELSE M2.Act END AS Act,
							CASE WHEN M2.rate12 IS NULL THEN 0 ELSE M2.rate12 END AS rate12,
							CASE WHEN M2.Row IS NULL THEN 1 ELSE M2.Row END AS Row
							FROM
							(
								SELECT C.PressNo,P.ID
								FROM PressArmMaster P,CureTrans C
								WHERE C.CreateDate BETWEEN ? AND ?
								GROUP BY C.PressNo,P.ID
							)M LEFT JOIN
							(
								SELECT C1.PressNo,C1.PressSide,COUNT(C1.Barcode) Act
								,C1.CuringCode,CM.rate12
								,ROW_NUMBER() OVER(PARTITION BY PressNo,PressSide ORDER BY rate12) AS Row
								FROM CureTrans C1 JOIN
								CureCodeMaster CM ON C1.CuringCode = CM.ID
								WHERE C1.CreateDate BETWEEN ? AND ?
								GROUP BY C1.PressNo,C1.PressSide,C1.CuringCode,CM.rate12
							)M2 ON M.PressNo = M2.PressNo AND M.ID = M2.PressSide
						)C3
					)C4
					WHERE C4.Row = 1
				)D ON H2.PressNo = D.PressNo AND H2.PressSide = D.PressSide
			)T2
			WHERE T2.CreateBy = ? AND T2.PressNo = ?
			",
			[
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$tstart,
				$tend,
				$userid,
				$pressno
			]
		);

		return $query[0]['Count_Arm'];
	}

	//Report Log Building
	public function LogBuildingServicepdf_byMac($tstart, $tend, $machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			// "SELECT T1.UserId,T1.Machine,T1.EmployeeID,T1.Name,T1.BuildType,
			// CONVERT(VARCHAR, T1.LoginDate, 103) + ' '+CONVERT(VARCHAR, T1.LoginDate, 108) AS LoginDate,
			// CONVERT(VARCHAR, T1.LogoutDate, 103) + ' '+CONVERT(VARCHAR, T1.LogoutDate, 108) AS LogoutDate,
			// T2.Act
			// FROM
			// (
			// 	SELECT R.UserId,R.Machine,U.EmployeeID,REPLACE(U.Name,'null','') Name
			// 	,B.Description BuildType,R.LoginDate,R.LogoutDate
			// 	FROM RateTrans R JOIN
			// 	UserMaster U ON R.UserId = U.ID JOIN
			// 	BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId
			// 	WHERE R.LoginDate BETWEEN ? AND ?
			// 	AND R.Machine = ?
			// 	AND R.RateGroupID = 1
			// )T1 JOIN
			// (
			// 	SELECT Machine,CreateBy,COUNT(BARCODE) Act
			// 	FROM BuildTrans
			// 	WHERE CreateDate BETWEEN ? AND ?
			// 	AND Machine = ?
			// 	GROUP BY Machine,CreateBy
			// )T2 ON T1.MACHINE = T2.MACHINE AND T1.UserId = T2.CreateBy

			// ORDER BY BuildType,LoginDate
			// ",
			// [
			// 	$tstart,
			// 	$tend,
			// 	$machine,
			// 	$tstart,
			// 	$tend,
			// 	$machine
			// ]
			"SELECT BT.CreateBy,BT.Machine,U.EmployeeID,
				REPLACE(U.Name,'null','') Name,B.Description BuildType,
				CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108) AS LoginDate,
				CONVERT(VARCHAR, R.LogoutDate, 103) + ' '+CONVERT(VARCHAR, R.LogoutDate, 108) AS LogoutDate,
				COUNT(BT.BARCODE) Act
			FROM BuildTrans BT JOIN
			RateTrans R ON BT.Machine = R.Machine AND BT.CreateBy = R.UserId AND
			BT.CreateDate BETWEEN R.LoginDate AND R.LogoutDate JOIN
			UserMaster U ON R.UserId = U.ID JOIN
			BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId
			WHERE BT.CreateDate BETWEEN ? AND ?
			AND BT.Machine = ?
			GROUP BY BT.CreateBy,BT.Machine,U.EmployeeID,REPLACE(U.Name,'null',''),
			B.Description,
			CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108),
			CONVERT(VARCHAR, R.LogoutDate, 103) + ' '+CONVERT(VARCHAR, R.LogoutDate, 108)
			ORDER BY CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108)
			",
			[
				$tstart,
				$tend,
				$machine
			]
		);

		return $query;
	}

	public function Buildingt3Serviceallpdf($datebuilding, $shift, $group, $product_group, $pressBOI)
	{
		$datenight_original = str_replace('-', '/', $datebuilding);
		$datenight = date('Y-m-d 20:00:00', strtotime($datenight_original));
		$datebuildingnight = date('Y-m-d 08:00:00', strtotime($datenight . "+1 days"));
		$datebuildingnight_date_only = date('Y-m-d', strtotime($datenight_original . "+1 days"));
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}


		// $datebuildingnight = date('Y-m-d 08:00:00', strtotime($datebuildingnight));
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,RECEIVED_ALL
					-- ,SUM(QTY_5)[Q5]
					-- ,SUM(QTY_6)[Q6]
					,'day'[Shift]
					,BOI

				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description
						,BOT.ID AS BOI
						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '08:00:01' AND CONVERT(time,I.DateBuild) <= '11:00:00')[QTY_1]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '11:00:01' AND CONVERT(time,I.DateBuild) <= '14:00:00')[QTY_2]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '14:00:01' AND CONVERT(time,I.DateBuild) <= '17:00:00')[QTY_3]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '17:00:01' AND CONVERT(time,I.DateBuild) <= '20:00:00')[QTY_4]
						,(
							SELECT
							CASE
								WHEN I.CuringDate IS NOT NULL THEN 1
								ELSE 0
							END
						) as RECEIVED_ALL
						-- ,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '16:00:01' AND CONVERT(time,I.DateBuild) <= '18:00:00')[QTY_5]
						-- ,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '18:00:01' AND CONVERT(time,I.DateBuild) <= '20:00:00')[QTY_6]
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				WHERE CONVERT(date,I.DateBuild)=?
				AND I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				)Z
				WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.BOI
				,Z.RECEIVED_ALL ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$product_group
				]
			);
		} else {

			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					-- ,SUM(QTY_5)[Q5]
					-- ,SUM(QTY_6)[Q6]
					,'night'[Shift]
					,RECEIVED_ALL


				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description

						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 20:00:01' AND I.DateBuild <= '$datebuilding 23:00:00')[QTY_1]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 23:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 02:00:00')[QTY_2]
					--	,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 00:00:00' AND I.DateBuild <= '$datebuildingnight_date_only 02:00:00')[QTY_3]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 02:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 05:00:00')[QTY_3]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 05:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 08:00:00')[QTY_4]
						,(
							SELECT
							CASE
								WHEN I.CuringDate IS NOT NULL THEN 1
								ELSE 0
							END
						) as RECEIVED_ALL
					--	,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 06:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 08:00:00')[QTY_6]
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.QTY>0 AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				-- LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				WHERE
				I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				AND I.DateBuild between ? AND ?
				)Z WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.RECEIVED_ALL
				 ORDER BY BuildingNo ASC
				",
				[
					$product_group,
					$datebuilding,
					$datebuildingnight
				]
			);
		}
	}

	public function Buildingt3Batchpdf($datebuilding, $shift, $group, $product_group, $pressBOI)
	{
		$datenight_original = str_replace('-', '/', $datebuilding);
		$datenight = date('Y-m-d 20:00:00', strtotime($datenight_original));
		$datebuildingnight = date('Y-m-d 08:00:00', strtotime($datenight . "+1 days"));
		$datebuildingnight_date_only = date('Y-m-d', strtotime($datenight_original . "+1 days"));
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}


		// $datebuildingnight = date('Y-m-d 08:00:00', strtotime($datebuildingnight));
		$conn = Database::connect();
		if ($shift == 'day') {


			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT
					Batch
				FROM(
				SELECT
				T.Batch
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				WHERE CONVERT(date,I.DateBuild)=?
				AND I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				)Z

				GROUP BY
				Z.Batch",
				[
					$datebuilding,
					$product_group
				]
			);
		} else {



			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT
					Batch

				FROM(
				SELECT
				T.Batch
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.QTY>0 AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
				-- LEFT JOIN BOITable BOT ON BOT.ID = BMM.BOI
				WHERE
				I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				AND I.DateBuild between ? AND ?
				)Z
				GROUP BY
				Z.Batch",
				[
					$product_group,
					$datebuilding,
					$datebuildingnight
				]
			);
		}

		return $query[0]["Batch"];
	}
	public function LogBuildingServicepdf_byUser($tstart, $tend, $user)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			// "SELECT T1.UserId,T1.Machine,T1.EmployeeID,T1.Name,T1.BuildType,
			// CONVERT(VARCHAR, T1.LoginDate, 103) + ' '+CONVERT(VARCHAR, T1.LoginDate, 108) AS LoginDate,
			// CONVERT(VARCHAR, T1.LogoutDate, 103) + ' '+CONVERT(VARCHAR, T1.LogoutDate, 108) AS LogoutDate,
			// T2.Act
			// FROM
			// (
			// 	SELECT R.UserId,R.Machine,U.EmployeeID,REPLACE(U.Name,'null','') Name
			// 	,B.Description BuildType,R.LoginDate,R.LogoutDate
			// 	FROM RateTrans R JOIN
			// 	UserMaster U ON R.UserId = U.ID JOIN
			// 	BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId
			// 	WHERE R.LoginDate BETWEEN ? AND ?
			// 	AND R.UserId = ?
			// 	AND R.RateGroupID = 1
			// )T1 JOIN
			// (
			// 	SELECT Machine,COUNT(BARCODE) Act
			// 	FROM BuildTrans
			// 	WHERE CreateDate BETWEEN ? AND ?
			// 	AND CreateBy = ?
			// 	GROUP BY Machine
			// )T2 ON T1.MACHINE = T2.MACHINE

			// ORDER BY BuildType,LoginDate
			// ",
			// [
			// 	$tstart,
			// 	$tend,
			// 	$user,
			// 	$tstart,
			// 	$tend,
			// 	$user
			// ]
			"SELECT R.UserId,BT.Machine,U.EmployeeID,
				REPLACE(U.Name,'null','') Name,
				B.Description BuildType,
				CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108) AS LoginDate,
				CONVERT(VARCHAR, R.LogoutDate, 103) + ' '+CONVERT(VARCHAR, R.LogoutDate, 108) AS LogoutDate,
				COUNT(BT.BARCODE) Act
			FROM BuildTrans BT JOIN
			RateTrans R ON BT.CreateBy = R.UserId AND BT.Machine = R.Machine JOIN
			UserMaster U ON R.UserId = U.ID JOIN
			BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId
			AND BT.CreateDate BETWEEN R.LoginDate AND R.LogoutDate
			WHERE BT.CreateDate BETWEEN ? AND ?
			AND BT.CreateBy = ?
			GROUP BY R.UserId,BT.Machine,U.EmployeeID,REPLACE(U.Name,'null',''),
			B.Description,
			CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108) ,
			CONVERT(VARCHAR, R.LogoutDate, 103) + ' '+CONVERT(VARCHAR, R.LogoutDate, 108)
			ORDER BY CONVERT(VARCHAR, R.LoginDate, 103) + ' '+CONVERT(VARCHAR, R.LoginDate, 108)
			",
			[
				$tstart,
				$tend,
				$user
			]
		);

		return $query;
	}

	public function ScrapChecking($date, $product_group, $pressBOI)
	{
		$select_date = date('Y-m-d', strtotime($date)) . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}

		//echo $select_date."---".$next_date;exit();
		// return $select_date . ' = ' . $date_1;
		// if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
		// 	$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
		// 	$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		// }

		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT
			IT.Barcode,
			IT.CuringCode,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			GCM.ItemNumber [IDItem],
			IT.GT_Code [GT_Code],
			S.Description [Shift],
			ITS.CreateDate,
			IT.BuildingNo [MC],
			(
				SELECT TOP 1  S_S.Description [Shift] FROM InventTrans S_IT
				LEFT JOIN ShiftMaster S_S ON S_S.ID = S_IT.Shift
				WHERE S_IT.Barcode = IT.Barcode
				AND S_IT.CreateDate = IT.CreateDate
			) [Shift_Build],
			IT.DateBuild,
			ITS.CREATEDATE [DATECHECK]
			FROM InventTable IT
			LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
			AND IT.UpdateDate = ITS.CreateDate
			LEFT JOIN GreentireCodeMaster GCM ON GCM.ID = IT.GT_Code
			LEFT JOIN CureCodeMaster CCM ON CCM.GreentireID = GCM.ID
			LEFT JOIN ItemMaster IM ON IM.ID = CCM.ItemID
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			WHERE
			IT.UpdateDate BETWEEN ? AND ?
			AND IT.WarehouseID = 1
			AND IT.DisposalID = 27
			AND ITS.DisposalID = 27
			AND IM.ProductGroup = ?
			$whereBOI
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $next_date, $product_group]
		);
	}
	public function getReportloadtire($date, $product_group, $pressBOI, $brand)
	{

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	"AND CB.BOI ='$pressBOI' ";
		}


		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT 
				LT.Barcode,
				I.ItemID,
				I.Batch,
				LT.CreatedDate,
				LT.OrderId,
				IM.NameTH,
				IM.Brand
				FROM LoadingTrans LT
				LEFT JOIN InventTable I ON LT.Barcode = I.Barcode
				LEFT JOIN ItemMaster IM ON LT.ItemId = IM.ID
				LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
				LEFT JOIN BrandMaster B ON B.BrandName = IM.Brand
				LEFT JOIN CuringBOI CB ON CB.Barcode = I.Barcode
				 WHERE CONVERT(date,LT.CreatedDate)  = ? AND 
				  
				B.BrandID IN ($brand) AND
				LT.Status <> 6 AND
				 IM.ProductGroup = ?
				 $whereBOI",
			[$date, $product_group, $brand]
		);
	}

	public function ReporwarehouseOnhand($product_group, $pressBOI, $brand)
	{

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	"AND CB.BOI ='$pressBOI' ";
		}


		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT * FROM (SELECT  
				T.Brand,
				T.ItemID,
				T.NameTH,
				--LEFT(CONVERT(VARCHAR, T.Batch, 120), 10) AS Batch,
				T.Batch,
				SUM(T.FG)AS FG,
				SUM(T.BOM)AS BOM,
				SUM(T.Foil)AS Foil,
				SUM(T.Loading)AS Loading,
				SUM(T.RT)AS RT,
				SUM(T.FG+T.BOM+T.Foil+T.Loading+T.RT) AS Total
			FROM (
				SELECT
					M.Brand,
					I.ItemID,
					M.NameTH,
					I.Batch,
					ISNULL((SELECT 1 AS FG FROM InventTable IID WHERE IID.DisposalID = 6 AND IID.Barcode = I.Barcode
					),0) AS FG,
					ISNULL((SELECT 1 AS FG FROM InventTable IID WHERE IID.DisposalID = 18 AND IID.Barcode = I.Barcode
					),0) AS BOM,
					ISNULL((SELECT 1 AS FG FROM InventTable IID WHERE IID.DisposalID = 17 AND IID.Barcode = I.Barcode
					),0) AS Foil,
					ISNULL((SELECT 1 AS FG FROM InventTable IID WHERE IID.DisposalID = 14 AND IID.Barcode = I.Barcode
					),0) AS Loading,
					ISNULL((SELECT 1 AS RT FROM InventTable IID WHERE IID.DisposalID = 9 AND IID.Barcode = I.Barcode
					),0) AS RT
				FROM InventTable I
					LEFT JOIN ItemMaster M ON I.ItemID = M.ID
					LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
					LEFT JOIN BrandMaster B ON M.Brand = B.BrandName
					LEFT JOIN CuringBOI CB ON CB.Barcode = I.Barcode
					WHERE I.WarehouseID = '3' AND 
					I.Status  NOT IN (3,4)  AND 
					B.BrandID IN ($brand) AND 
					M.ProductGroup = ? 
				--	year(I.WarehouseReceiveDate) = '2019'
					$whereBOI
					) T
				GROUP BY
					T.Brand,
					T.ItemID,
					T.NameTH,
					T.Batch)T
			WHERE T.Total <> 0",
			[$product_group]
		);
	}

	public function Reportgreentirecode($product_group, $pressBOI, $pressGT)
	{

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	"AND BMM.BOI ='$pressBOI' ";
		}


		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT  
			I.GT_Code,
			I.Barcode,
			I.DateBuild,
			I.GT_InspectionDate,
			IM.ProductGroup
			FROM InventTable I
			LEFT JOIN BuildingMaster BMM ON I.BuildingNo = BMM.ID
			LEFT JOIN GreentireCodeMaster GM ON I.GT_Code = GM.ID
			LEFT JOIN ItemMaster IM ON GM.ItemNumber = IM.ID 
			WHERE I.WarehouseID = '1' AND I.GT_InspectionDate IS NOT NULL 
			AND I.Status IN ('1','5')
			AND I.GT_Code = ?
			--AND IM.SubGroup = ?
			$whereBOI 
			ORDER BY I.GT_InspectionDate ASC",
			[$pressGT]
		);
	}

	public function ReportBuildingcode($date, $shift, $pressGT)
	{
		if ($shift == 'day') {
			$next_is =  date('Y-m-d', strtotime($date)) . ' 08:00:01';

			$next_date =  date('Y-m-d', strtotime($date)) . ' 20:00:00';
		} else {
			$next_is =  date('Y-m-d', strtotime($date)) . ' 20:00:01';
			$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 08:00:00';
		}
		//return

		// 	"SELECT 
		// IT.Barcode,
		// IT.DateBuild,
		// IT.GT_InspectionDate,
		// IT.CuringDate,
		// CASE WHEN IT.DisposalID = 10 THEN (SELECT Top 1 CreateDate FROM InventTrans ITS WHERE ITS.Barcode = IT.Barcode and ITS.DocumentTypeID = 1 Order by id desc)
		// ELSE NULL END AS CreateDate,
		// D.DisposalDesc
		// FROM InventTable IT 
		//  LEFT JOIN DisposalToUseIn D ON IT.DisposalID = D.ID
		//  WHERE 
		//  IT.GT_Code = ? 
		//  AND IT.DateBuild BETWEEN '$next_is' AND '$next_date'
		//  ORDER BY IT.GT_InspectionDate ASC";
		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT 
			IT.Barcode,
			IT.DateBuild,
			IT.GT_InspectionDate,
			IT.CuringDate,
			CASE WHEN IT.DisposalID IN  (10,2,27)  THEN (SELECT Top 1 CreateDate FROM InventTrans ITS WHERE ITS.Barcode = IT.Barcode and ITS.DocumentTypeID = 1 and ITS.DisposalID = '10' and ITS.WarehouseID = 1 Order by id desc)
			ELSE NULL END AS CreateDateDis,
			D.DisposalDesc
			FROM InventTable IT 
			 LEFT JOIN DisposalToUseIn D ON IT.DisposalID = D.ID
			 WHERE 
			 IT.GT_Code = ? 
			 AND IT.DateBuild BETWEEN '$next_is' AND '$next_date'
			 ORDER BY IT.GT_InspectionDate ASC",
			[$pressGT]
		);
	}
	// building acc by saba
	public function BuildingAccpdf($datebuilding, $shift, $group, $product_group, $pressBOI)
	{
		$datenight_original = str_replace('-', '/', $datebuilding);
		$datenight = date('Y-m-d 20:00:00', strtotime($datenight_original));
		$datebuildingnight = date('Y-m-d 08:00:00', strtotime($datenight . "+1 days"));
		$datebuildingnight_date_only = date('Y-m-d', strtotime($datenight_original . "+1 days"));
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = " AND GT.BOI IS NOT NULL ";
		} else {
			$whereBOI =	" AND GT.BOI ='$pressBOI' ";
		}


		// $datebuildingnight = date('Y-m-d 08:00:00', strtotime($datebuildingnight));
		$conn = Database::connect();
		if ($shift == 'day') {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'day'[Shift]
					,BOI
					,Total

				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description
						,GT.BOI AS BOI
						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '08:00:01' AND CONVERT(time,I.DateBuild) <= '10:00:00')[QTY_1]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '10:00:01' AND CONVERT(time,I.DateBuild) <= '12:00:00')[QTY_2]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '12:00:01' AND CONVERT(time,I.DateBuild) <= '14:00:00')[QTY_3]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '14:00:01' AND CONVERT(time,I.DateBuild) <= '16:00:00')[QTY_4]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '16:00:01' AND CONVERT(time,I.DateBuild) <= '18:00:00')[QTY_5]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '18:00:01' AND CONVERT(time,I.DateBuild) <= '20:00:00')[QTY_6]
						,KP.Total
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			
				LEFT JOIN KeepBuilding KP ON I.BuildingNo = KP.BuildingMc AND I.GT_Code = KP.GTCode AND KP.DateBuild = ? AND KP.Shift = '1'
			
				-- LEFT JOIN CureCodeMaster CM ON CM.GreentireID = I.GT_Code AND CM.ID = I.CuringCode
				-- LEFT JOIN ( SELECT Curecode,PressNo FROM CureSchedule WHERE SchDate = ? GROUP BY Curecode,PressNo) CSH ON CSH.Curecode = CM.ID AND CSH.PressNo = I.BuildingNo
				-- LEFT JOIN PressMaster BOT ON  BOT.ID = CSH.PressNo
				LEFT JOIN (
					SELECT PM.BOI,CM.GreentireID FROM CureSchedule C
					LEFT JOIN CureCodeMaster  CM ON CM.ID = C.Curecode AND C.SchDate = ?
					LEFT JOIN PressMaster PM ON PM.ID = C.PressNo
					GROUP BY PM.BOI,CM.GreentireID
				)GT ON GT.GreentireID = I.GT_Code
				WHERE CONVERT(date,I.DateBuild)=?
				AND I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				)Z
				WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.BOI
				,Z.Total ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$datebuilding,
					$datebuilding,
					$product_group
				]
			);
		} else {

			return Sqlsrv::queryJson(
				$conn,
				"SELECT
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'night'[Shift]
					,Total


				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description

						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 20:00:01' AND I.DateBuild <= '$datebuilding 22:00:00')[QTY_1]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 22:00:01' AND I.DateBuild <= '$datebuilding 23:59:59')[QTY_2]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 00:00:00' AND I.DateBuild <= '$datebuildingnight_date_only 02:00:00')[QTY_3]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 02:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 04:00:00')[QTY_4]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 04:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 06:00:00')[QTY_5]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 06:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 08:00:00')[QTY_6]
						,KP.Total
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.QTY>0 AND T.DisposalID = '1' AND T.DocumentTypeID ='1'
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			
			
				-- LEFT JOIN CureCodeMaster CM ON CM.GreentireID = I.GT_Code AND CM.ID = I.CuringCode
				-- LEFT JOIN ( SELECT Curecode,PressNo FROM CureSchedule WHERE SchDate = ? GROUP BY Curecode,PressNo) CSH ON CSH.Curecode = CM.ID AND CSH.PressNo = I.BuildingNo
				-- LEFT JOIN PressMaster BOT ON  BOT.ID = CSH.PressNo
				LEFT JOIN (
					SELECT PM.BOI,CM.GreentireID FROM CureSchedule C
					LEFT JOIN CureCodeMaster  CM ON CM.ID = C.Curecode AND C.SchDate = ?
					LEFT JOIN PressMaster PM ON PM.ID = C.PressNo
					GROUP BY PM.BOI,CM.GreentireID
				)GT ON GT.GreentireID = I.GT_Code
				LEFT JOIN KeepBuilding KP ON I.BuildingNo = KP.BuildingMc AND I.GT_Code = KP.GTCode AND KP.DateBuild = ? AND KP.Shift = '2'
				
				WHERE
				I.CheckBuild = 1
				$whereBOI
				AND I.GT_Code IN
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				AND I.DateBuild between ? AND ?
				)Z WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY
				Z.BuildingNo
				,Z.GT_Code
				,Z.Total
				 ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$datebuilding,
					$product_group,
					$datebuilding,
					$datebuildingnight
				]
			);
		}
	}

	public function greentireScrapAcc($date, $product_group, $pressBOI)
	{
		$select_date = date('Y-m-d', strtotime($date)) . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = " AND BOT.BOI IS NOT NULL ";
		} else {
			$whereBOI =	" AND BOT.BOI ='$pressBOI' ";
		}
		//echo $select_date."---".$next_date;exit();
		// return $select_date . ' = ' . $date_1;
		// if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
		// 	$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
		// 	$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		// }

		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT
			IT.Barcode,
			IT.CuringCode,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			GCM.ItemNumber [IDItem],
			IT.GT_Code [GT_Code],
			S.Description [Shift],
			ITS.CreateDate,
			IT.BuildingNo [MC],
			(
				SELECT TOP 1  S_S.Description [Shift] FROM InventTrans S_IT
				LEFT JOIN ShiftMaster S_S ON S_S.ID = S_IT.Shift
				WHERE S_IT.Barcode = IT.Barcode
				AND S_IT.CreateDate = IT.CreateDate
			) [Shift_Build],
			IT.DateBuild,
			ITS2.CreateDate [CreateDateHold],
			D2.Description [DefectDescHold]
			
			FROM InventTable IT
			LEFT JOIN


			InventTrans ITS ON IT.Barcode = ITS.Barcode AND ITS.DisposalID = 2
			AND ITS.DocumentTypeID = 2
			AND ITS.TransID = ( select MAX(TransID) from InventTrans where Barcode = ITS.Barcode and DisposalID = '2' AND DocumentTypeID = 2 )

			LEFT JOIN GreentireCodeMaster GCM ON GCM.ID = IT.GT_Code
			LEFT JOIN
			--( SELECT TOP 1 * FROM CureCodeMaster

			--)CCM ON CCM.GreentireID = GCM.ID
			CureCodeMaster CCM ON CCM.GreentireID = GCM.ID AND  CCM.ID = ( select MAX(ID) from CureCodeMaster where GreentireID = GCM.ID  )
			LEFT JOIN ItemMaster IM ON IM.ID = CCM.ItemID
			LEFT JOIN InventTrans ITS2 ON IT.Barcode = ITS2.Barcode AND ITS2.DisposalID = '10' 
			AND	 ITS2.DocumentTypeID = '1'
			AND ITS2.TransID = ( select MAX(TransID) from InventTrans where Barcode = ITS.Barcode and DisposalID = '10' AND DocumentTypeID = 1 )
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN Defect D2 ON D2.ID = ITS2.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			--LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
			LEFT JOIN CureCodeMaster CM ON CM.GreentireID = IT.GT_Code
			LEFT JOIN CureSchedule CSH ON CSH.Curecode = CM.ID AND CSH.SchDate = ?
			LEFT JOIN PressMaster BOT ON  BOT.ID = CSH.PressNo
			WHERE
			ITS.CreateDate BETWEEN ? AND ?
			AND ITS.WarehouseID = 1
			--AND IT.DisposalID = 2
			AND ITS.DisposalID = 2
			AND IM.ProductGroup = ?
			AND IT.DisposalID IN ('2','27')
			--AND ITS.Barcode = '52000529033'
			$whereBOI
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $select_date, $next_date, $product_group]
		);
	}

	public function greentirefinalrepair($date, $product_group, $pressBOI)
	{
		$select_date = date('Y-m-d', strtotime($date)) . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "  CB.BOI IS NOT NULL ";
		} else {
			$whereBOI =	"  CB.BOI ='$pressBOI' ";
		}
		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT TT.Barcode
			,DST.DisposalDesc [Disposal]
			,TT.DateTimeRepair
			,T.CuringCode
			,T.CuringDate
			,T.GT_Code
			,TT.NameTH
			,TT.Pattern
			,T.PressNo
			,T.MoldNo
			,T.PressSide
			,T.TemplateSerialNo
			,T.CuringDate
			,UM.Name [CureMan]
			,T.BuildingNo
			,T.CreateDate [DateBuild]
			,Shift = (SELECT TOP 1 S.Description FROM InventTrans I 
			   LEFT JOIN ShiftMaster S ON S.ID = I.Shift
			   WHERE I.Barcode = T.Barcode AND I.DisposalID = 3
			   AND I.WarehouseID = 4
			   AND I.DocumentTypeID = 1
			   AND I.LocationID = 3 ORDER BY I.CreateDate DESC)
			,TT.DefectID [Defect]
			,D.Description [DefectDescriotion]
			,DS.DisposalDesc [Disposition]
			,TT.Week
			,TT.QTY
			FROM
			(
				 SELECT '1'AS RN,*
				 FROM
				 (
					  SELECT COUNT(I.Barcode) OVER(PARTITION BY I.Barcode ) AS CountRow,
					  I.Barcode,
					  I.CreateDate [DateTimeRepair],
					  IM.NameTH,
					  IM.Pattern,
					  I.DefectID,
					  I.DisposalID,
					  I.Batch [Week],
					  I.QTY
					  FROM InventTrans I  
					  LEFT JOIN InventTable IT ON IT.Barcode =  I.Barcode LEFT JOIN 
					  CureCodeMaster CCM ON  (CCM.ItemQ = I.CodeID OR CCM.ItemID = I.CodeID) AND CCM.GreentireID = IT.GT_Code LEFT JOIN  
					  ItemMaster IM ON IM.ID = CCM.ItemID
					  WHERE I.DisposalID =12 AND I.WarehouseID = 2
					 
					  AND I.LocationID = 12 AND IM.ProductGroup = ?
					  AND I.CreateDate >= ?
					  AND I.CreateDate <= ?

				 )T1
				 WHERE T1.CountRow = 1 AND T1.DefectID IS NOT NULL
				 UNION ALL
				 SELECT *
				 FROM
				 (
					 SELECT ROW_NUMBER() OVER (PARTITION BY T.Barcode ORDER BY T.DateTimeRepair DESC) AS RN,*
					 FROM
					 (
						  SELECT COUNT(I.Barcode) OVER(PARTITION BY I.Barcode ) AS CountRow,
						  I.Barcode,
						  I.CreateDate [DateTimeRepair],
						  IM.NameTH,
						  IM.Pattern,
						  I.DefectID,
						  I.DisposalID,
						  I.Batch [Week],
						  I.QTY
						  FROM InventTrans I LEFT JOIN 
						  CureCodeMaster CCM ON  CCM.ItemQ = I.CodeID OR CCM.ItemID = I.CodeID LEFT JOIN 
						  ItemMaster IM ON IM.ID = CCM.ItemID
						  WHERE I.DisposalID =12 AND I.WarehouseID = 2
						 
						  AND I.LocationID = 12 AND IM.ProductGroup = ?
						  AND I.CreateDate >= ?
						  AND I.CreateDate <= ?
					) T
					WHERE T.CountRow > 2 AND T.CountRow %2 !=0 
				 )T1
				 WHERE T1.RN = 1 AND T1.DefectID IS NOT NULL
			)TT LEFT JOIN
			InventTable T ON TT.Barcode = T.Barcode LEFT JOIN 
			DisposalToUseIn DST ON DST.ID = T.DisposalID LEFT JOIN  
			InventTrans IVT ON IVT.CreateDate = T.CuringDate AND IVT.Barcode = TT.Barcode LEFT JOIN 
			UserMaster UM ON UM.ID = IVT.CreateBy LEFT JOIN 
			Defect D ON D.ID = TT.DefectID LEFT JOIN 
			DisposalToUseIn DS ON DS.ID = TT.DisposalID 
			LEFT JOIN PressMaster PM ON PM.ID = T.PressNo
			LEFT JOIN CuringBOI CB ON CB.Barcode = T.Barcode
			WHERE $whereBOI AND T.DisposalID <> 2
			GROUP BY TT.Barcode
			,DST.DisposalDesc 
			,TT.DateTimeRepair
			,T.CuringCode
			,T.GT_Code
			,TT.NameTH
			,TT.Pattern
			,T.PressNo
			,T.MoldNo
			,T.PressSide
			,T.TemplateSerialNo
			,T.CuringDate
			,UM.Name 
			,T.BuildingNo
			,T.CreateDate 
			,TT.DefectID 
			,D.Description 
			,DS.DisposalDesc 
			,TT.Week
			,TT.QTY
			,T.Barcode
			ORDER BY TT.DateTimeRepair ASC",
			[$product_group, $select_date, $next_date, $product_group, $select_date, $next_date]
		);
	}

	public function greentirerepair($date, $product_group, $pressBOI)
	{
		$select_date = date('Y-m-d', strtotime($date)) . ' 10:00:00';
		$next_date = date('Y-m-d', strtotime($date . '+1 day')) . ' 10:00:00';
		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = " AND PM.BOI IS NOT NULL ";
		} else {
			$whereBOI =	" AND PM.BOI ='$pressBOI' ";
		}
		if ($product_group == 'TBR') {
			$product_group = 'SM0908';
		} else {
			$product_group = 'SM0907';
		}
		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT *
			FROM
			(SELECT 
			T.Barcode,
			COUNT(T.Barcode) OVER(PARTITION BY T.Barcode) AS CountRow,
			DST.DisposalDesc [Disposal],
			I.CreateDate [DateTimeRepair],
			T.CuringCode,
			T.GT_Code,
			IM.NameTH,
			IM.Pattern,
			T.PressNo,
			T.MoldNo,
			T.PressSide,
			T.TemplateSerialNo,
			T.CuringDate,
			UM.Name [CureMan],
			T.BuildingNo,
			T.CreateDate [DateBuild],
			-- Shift = (select Top 1 S.Description from InventTrans I 
			-- 	left join ShiftMaster S ON S.ID = I.Shift
				
			-- where I.Barcode = T.Barcode and I.DisposalID = 3
			-- and I.WarehouseID = 4
			-- and I.DocumentTypeID = 1
			-- and I.LocationID = 3 Order by I.CreateDate desc),
			CASE WHEN I.Shift = 1 THEN 'A' ELSE 'B' END AS Shift,
			I.DefectID [Defect],
			D.[Description] [DefectDescriotion],
			DS.DisposalDesc [Disposition],
			I.Batch [Week],
			I.QTY [QTY]
			from InventTrans I
			left join InventTable T ON T.Barcode = I.Barcode
			LEFT join GreentireCodeMaster CCM ON  CCM.ID = T.GT_Code 
			left join ItemMaster IM ON IM.ID = CCM.ItemNumber
			left join ShiftMaster S ON S.ID = I.Shift
			left join Defect D ON D.ID = I.DefectID
			left join DisposalToUseIn DS ON DS.ID = I.DisposalID
			LEFT JOIN DisposalToUseIn DST ON DST.ID = T.DisposalID
			left join InventTrans IVT ON IVT.CreateDate = T.CuringDate AND IVT.Barcode = I.Barcode
			left join UserMaster UM ON UM.ID = IVT.CreateBy
			left join BuildingMaster PM ON PM.ID = T.BuildingNo
			where I.DisposalID =12
			and I.WarehouseID = 1
			and I.DocumentTypeID = 1
			--and I.DefectID is not null
			--and I.LocationID = 12
			 $whereBOI
			 and IM.SubGroup = ?
			and I.CreateDate >= ?
			and I.CreateDate <= ?
			--and T.CuringCode IN (@CURINGCODE)
			
			GROUP BY
			T.Barcode,
			DST.DisposalDesc,
			I.CreateDate,
			T.CuringCode,
			T.GT_Code,
			IM.NameTH,
			IM.Pattern,
			T.PressNo,
			T.MoldNo,
			T.PressSide,
			T.TemplateSerialNo,
			UM.Name,
			T.BuildingNo,
			T.CreateDate,
			S.[Description],
			I.DefectID,
			D.[Description],
			DS.DisposalDesc,
			I.Batch,
			I.QTY,
			T.CuringDate,
			I.Shift
			)T
			WHERE T.CountRow=1
			and T.Defect is not null
			order by T.DateTimeRepair asc",
			[$product_group, $select_date, $next_date]
		);
	}

	public function Loadingexport($pickingListId, $orderId, $createDate)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT ItemId,NameTH,BatchNo,Barcode,Qty,OrderId,SerialName,CreatedDate
			FROM (
						SELECT LT.* ,
						WM.Description as warehouse_desc,
						L.Description as location_desc ,
						LS.Description as StatusDesc,
						UM.Name as Fullname,
						IT.TemplateSerialNo as SerialName,
						IM.NameTH				
						
						FROM LoadingTrans LT
						LEFT JOIN InventTable IT ON LT.Barcode = IT.Barcode
						LEFT JOIN WarehouseMaster WM ON WM.ID = IT.WarehouseID
						LEFT JOIN Location L ON L.ID = IT.LocationID
						LEFT JOIN LoadingStatus LS ON LS.ID = LT.Status
						LEFT JOIN UserMaster UM ON LT.CreatedBy = UM.ID
						LEFT JOIN ItemMaster IM ON LT.ItemId = IM.ID
						WHERE LT.OrderId = '$orderId'
						AND LT.PickingListId = '$pickingListId'
						AND LT.Status<>6
			) Z order by CreatedDate asc "
		);
	}

	public function MovementIssue($rowdata)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT IJ.CreateDate,IJ.InventJournalID,IJ.BarcodeID,IJ.ItemID,TB.Batch,IT.NameTH,IJ.QTY FROM InventJournalTrans IJ
			LEFT JOIN ItemMaster IT ON IJ.ItemID = IT.ID
			LEFT JOIN inventtable TB ON IJ.BarcodeID = TB.Barcode
			WHERE IJ.InventJournalID = ? 
			ORDER BY IJ.ItemID, IJ.CreateDate ASC ",
			[
				$rowdata
			]

		);
	}

	//quality
	public function qualityreport($timeset)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT QC.Barcode,
			QC.ItemID,
			IM.NameTH,
			QC.CureCode,
			QC.Batch,
			QC.CreateDate
			FROM QualityCheckingTable QC
			LEFT JOIN ItemMaster IM ON IM.ID = QC.ItemID
			WHERE $timeset "   
		);
	}

	public function finalreportins($date, $shift, $type, $pressBOI)
	{
		
		if ($type === 'pcr') {
			$type = 'RDT';
		} else {
			$type = 'TBR';
		}

		$date_today = date('Y-m-d', strtotime($date));
		$date_tom = date('Y-m-d', strtotime($date . "+1 days"));

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BOI ='$pressBOI' ";
		}

		// echo $date_tom;
		// echo $shift;
		// echo $type;
		// echo $pressBOI;
		// exit();

		if ($shift == '1') {
			return Sqlsrv::queryArray(
				$this->db->connect(),
				"SELECT U.Username,T3.*
				FROM
				(
					SELECT CreateBy
					,SUM(Q1) OVER(PARTITION BY CreateBy) Q1
					,SUM(Q2) OVER(PARTITION BY CreateBy) Q2
					,SUM(Q3) OVER(PARTITION BY CreateBy) Q3
					,SUM(Q4) OVER(PARTITION BY CreateBy) Q4
					,SUM(Q1) OVER(PARTITION BY CreateBy) + 
					 SUM(Q2) OVER(PARTITION BY CreateBy) +
					 SUM(Q3) OVER(PARTITION BY CreateBy) +
					 SUM(Q4) OVER(PARTITION BY CreateBy) AS TOTALQTY
					FROM 
					(
						SELECT T.CreateBy
						,CASE 
						 WHEN T.FinalReceiveDate BETWEEN '$date_today 08:00:00' AND '$date_today 11:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q1'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_today 11:00:01' AND '$date_today 14:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q2'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_today 14:00:01' AND '$date_today 17:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q3'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_today 17:00:01' AND '$date_today 20:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q4'
						,T.FinalReceiveDate
						,COUNT(T.Barcode) OVER(PARTITION BY T.CREATEBY) TOTALQTY
						,T.BOI
						FROM
						(
							SELECT 
							 IT.Barcode
							,IT.FinalReceiveDate
							,ITS.CreateBy
							,CB.BOI
							,ROW_NUMBER() OVER ( PARTITION BY IT.BARCODE  ORDER BY IT.FinalReceiveDate DESC)AS ROWNUM
							,ROW_NUMBER() OVER ( PARTITION BY CB.Barcode  ORDER BY CB.Id DESC)AS ROWBOI
							FROM InventTable IT
								LEFT JOIN InventTrans ITS
									ON ITS.Barcode = IT.Barcode
									AND ITS.DocumentTypeID = 1
									AND ITS.DisposalID = 4
									AND ITS.CreateDate = IT.FinalReceiveDate
								LEFT JOIN CureCodeMaster CCM
									ON CCM.GreentireID = IT.GT_Code
									AND CCM.ItemID = ITS.CodeID
									OR CCM.ItemQ = ITS.CodeID
									AND CCM.ID = IT.CuringCode
								LEFT JOIN ItemMaster I ON I.ID = CCM.ItemID
								LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
								LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
							WHERE I.ProductGroup = '$type'
							AND ITS.DisposalID NOT IN (23, 24)
							AND IT.FinalReceiveDate BETWEEN '$date_today 08:00:00' AND '$date_today 20:00:00'
							
						)T
						WHERE ROWNUM = 1
						$whereBOI
						--AND BOI = 'BOI1'
						AND ROWNUM = 1
						
					)T2
				)T3
				JOIN UserMaster U ON T3.CreateBy = U.ID
				
				GROUP BY CreateBy,Q1,Q2,Q3,Q4,TOTALQTY,U.Username
				ORDER BY CreateBy "
			);

		} else {

			return Sqlsrv::queryArray(
				$this->db->connect(),
				"SELECT U.Username,T3.*
				FROM
				(
					SELECT CreateBy
					,SUM(Q1) OVER(PARTITION BY CreateBy) Q1
					,SUM(Q2) OVER(PARTITION BY CreateBy) Q2
					,SUM(Q3) OVER(PARTITION BY CreateBy) Q3
					,SUM(Q4) OVER(PARTITION BY CreateBy) Q4
					,SUM(Q1) OVER(PARTITION BY CreateBy) + 
					 SUM(Q2) OVER(PARTITION BY CreateBy) +
					 SUM(Q3) OVER(PARTITION BY CreateBy) +
					 SUM(Q4) OVER(PARTITION BY CreateBy) AS TOTALQTY
					FROM 
					(
						SELECT T.CreateBy
						,CASE 
						 WHEN T.FinalReceiveDate BETWEEN '$date_today 20:00:01' AND '$date_today 23:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q1'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_today 23:00:01' AND '$date_tom 02:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q2'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_tom 02:00:01' AND '$date_tom 05:00:00' 
								THEN 1
							ELSE 0 
						 END AS 'Q3'
						,CASE 
						 WHEN  T.FinalReceiveDate BETWEEN '$date_tom 05:00:01' AND '$date_tom 07:59:59' 
								THEN 1
							ELSE 0 
						 END AS 'Q4'
						,T.FinalReceiveDate
						,COUNT(T.Barcode) OVER(PARTITION BY T.CREATEBY) TOTALQTY
						,T.BOI
						FROM
						(
							SELECT 
							 IT.Barcode
							,IT.FinalReceiveDate
							,ITS.CreateBy
							,CB.BOI
							,ROW_NUMBER() OVER ( PARTITION BY IT.BARCODE  ORDER BY IT.FinalReceiveDate DESC)AS ROWNUM
							,ROW_NUMBER() OVER ( PARTITION BY CB.Barcode  ORDER BY CB.Id DESC)AS ROWBOI
							FROM InventTable IT
								LEFT JOIN InventTrans ITS
									ON ITS.Barcode = IT.Barcode
									AND ITS.DocumentTypeID = 1
									AND ITS.DisposalID = 4
									AND ITS.CreateDate = IT.FinalReceiveDate
								LEFT JOIN CureCodeMaster CCM
									ON CCM.GreentireID = IT.GT_Code
									AND CCM.ItemID = ITS.CodeID
									OR CCM.ItemQ = ITS.CodeID
									AND CCM.ID = IT.CuringCode
								LEFT JOIN ItemMaster I ON I.ID = CCM.ItemID
								LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
								LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
							WHERE I.ProductGroup = '$type'
							AND ITS.DisposalID NOT IN (23, 24)
							AND IT.FinalReceiveDate BETWEEN '$date_today 20:00:00' AND '$date_tom 08:00:00'
							
						)T
						WHERE ROWNUM = 1
						$whereBOI
						--AND BOI = 'BOI1'
						AND ROWNUM = 1
						
					)T2
				)T3
				JOIN UserMaster U ON T3.CreateBy = U.ID
				
				GROUP BY CreateBy,Q1,Q2,Q3,Q4,TOTALQTY,U.Username
				ORDER BY CreateBy "
			);
		}
	}
	
	// nueng
	public function genshipdetailPDF($selectSingle,$selectMulti)
	{
		$conn = Database::connectDeviceWMSSTR();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT L.SERIALNUMBER,L.EXTERNORDERKEY,L.LOADID,L.ACTUALSHIPDATE,L.C_COMPANY,L.LOTTABLE01,L.DESCRIPTION,I.TemplateSerialNo
			From Openquery(
			  WMS_STR_LIVE,
			'SELECT L.SERIALNUMBER,L.EXTERNORDERKEY,O.LOADID,O.ACTUALSHIPDATE,O.C_COMPANY,L.LOTTABLE01,T.DESCRIPTION
			From AC2_Loaddetail L
			Join AC2_TIRE T
			ON T.SERIALNUMBER = L.SERIALNUMBER
			Join ORDERS O
			ON O.EXTERNORDERKEY = L.EXTERNORDERKEY
			WHERE L.EXTERNORDERKEY IN (''$selectMulti'')
			') L
			JOIN [BARATHEON].[STR_BARCODE].[dbo].[InventTable] I 
			ON I.Barcode = L.SERIALNUMBER
			WHERE L.EXTERNORDERKEY IN ('$selectSingle')
			ORDER BY L.EXTERNORDERKEY"
		);
		return $query;
	}

	public function repairinventory($product_group, $pressBOI)
	{
		// if ($pressBOI == "" || $pressBOI == 1) {
		// 	$whereBOI = "";
		// } else {
		// 	$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		// }

		// $conn = Database::connect();
		// return Sqlsrv::queryArray(
		// 	$conn,
		// 	  " SELECT
		// 			T.Barcode,
		// 			T.PressNo,
		// 			T.GT_Code,
		// 			T.CuringCode,
		// 			T.BuildingNo,
		// 			TR.CreateDate AS DateRepair,
		// 			D.Description,
		// 			T.Batch,
		// 			IM.ProductGroup,
		// 			CB.BOI
		// 		FROM InventTable T
				
		// 		JOIN InventTrans TR 
		// 		ON T.Barcode = TR.Barcode 
		// 		AND T.UpdateDate = TR.CreateDate
		// 		AND T.DisposalID = TR.DisposalID
				
		// 		JOIN Defect D 
		// 		ON TR.DefectID = D.ID
				
		// 		JOIN ItemMaster IM 
		// 		ON T.ItemID = IM.ID
				
		// 		JOIN CuringBOI CB
		// 		ON CB.Barcode = T.Barcode
				
		// 		WHERE T.DisposalID = '12'
		// 		AND T.WarehouseID = '2'
		// 		--AND CB.BOI = 'BOI1'
		// 		$whereBOI
		// 		AND IM.ProductGroup = '$product_group'
				
		// 		ORDER BY TR.CreateDate "
		// 		);

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND CB.BOI ='$pressBOI' ";
		}

		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
				" SELECT 
					T.Barcode,
					T.PressNo,
					T.GT_Code,
					T.CuringCode,
					T.BuildingNo,
					TR.CreateDate AS DateRepair,
					D.Description,
					T.Batch,
					IM.ProductGroup,
					CB.BOI,
					(SELECT MAX(RC.CreatedDate) FROM Repaircheck RC where RC.Barcode = T.Barcode AND RC.Type = 'IN' AND RC.CreatedDate >= T.UpdateDate)[Onhand],
					(SELECT MAX(RC.CreatedDate) FROM Repaircheck RC where RC.Barcode = T.Barcode AND RC.Type = 'OUT' AND RC.CreatedDate >= T.UpdateDate)[Finish]
				FROM InventTable T
				
				JOIN InventTrans TR 
				ON T.Barcode = TR.Barcode 
				AND T.UpdateDate = TR.CreateDate
				AND T.DisposalID = TR.DisposalID
				
				JOIN Defect D 
				ON TR.DefectID = D.ID
				
				JOIN ItemMaster IM 
				ON T.ItemID = IM.ID
				
				JOIN CuringBOI CB
				ON CB.Barcode = T.Barcode

				-- JOIN Repaircheck RC
				-- ON RC.Barcode = T.Barcode
				-- AND RC.CreatedDate >= T.CreateDate
				
				WHERE T.DisposalID = '12'
				AND T.WarehouseID = '2'
				--AND CB.BOI = 'BOI1'
				$whereBOI
				AND IM.ProductGroup = '$product_group'
				
				ORDER BY TR.CreateDate "
				);
	}
	
	public function dailyrepair($date, $shift, $type, $pressBOI)
	{
		
		if ($type === 'pcr') {
			$type = 'RDT';
		} else {
			$type = 'TBR';
		}

		$date_today = date('Y-m-d', strtotime($date));
		$date_tom = date('Y-m-d', strtotime($date . "+1 days"));

		if ($shift == '1') {
			$timeset = 'IT.CreateDate >= \'' . $date_today . ' 08:00:00\' AND IT.CreateDate <= ' . '\'' . $date_today . ' 19:59:59\'';
		} else {
			$timeset = 'IT.CreateDate >= \'' . $date_today . ' 20:00:00\' AND IT.CreateDate <= ' . '\'' . $date_tom . ' 07:59:59\'';
		}

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND T.BOI ='$pressBOI' ";
		}

		// echo $type;
		// exit();

			return Sqlsrv::queryArray(
				$this->db->connect(),
				"	SELECT T.*
					FROM 
					(
						SELECT
						I.GT_Code
						,I.BuildingNo
						,I.DateBuild
						,SM.Description AS Shift
						,I.PressNo + I.PressSide AS PressNo
						,I.CuringDate
						,I.Barcode
						,ROW_NUMBER() OVER(PARTITION BY IT.Barcode ORDER BY IT.CreateDate DESC) ROWNUM
						,D.Description
						,IT.Batch
						,CB.BOI
						,IT.CreateDate
						FROM InventTable I
					
						LEFT JOIN InventTrans IT ON I.BARCODE = IT.Barcode	
						LEFT JOIN Defect D ON D.ID = IT.DefectID
						LEFT JOIN ShiftMaster SM ON SM.ID = IT.Shift
						LEFT JOIN ItemMaster IM ON IM.ID = I.ItemID
						LEFT JOIN PressMaster PM ON PM.ID = I.PressNo
						LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
						
						--WHERE IT.CreateDate >= '2022-01-10 08:00:00' AND IT.CreateDate <= '2022-01-10 19:59:59'	
						WHERE $timeset
						AND IT.DisposalID = 12
						AND IT.WarehouseID = 2
						AND IT.DocumentTypeID = 1
						AND IM.ProductGroup = '$type'
						--AND IM.ProductGroup = 'PCR'
					
					)T
					WHERE T.ROWNUM = 1
					-- AND T.BOI = 'BOI3'
					$whereBOI
					ORDER BY T.CreateDate "
			);
		}

		public function BuffInventoryServiceallpdf($product_group, $pressBOI)
			{
				$conn = Database::connect();
				if ($pressBOI == "" || $pressBOI == 1) {
					$whereBOI = "";
				} else {
					$whereBOI =	" AND CB.BOI ='$pressBOI' ";
				}
				return Sqlsrv::queryJson(
					$conn,
					"SELECT 
						IT.Barcode
						,IT.CuringDate
						,IT.CuringCode
						,IT.UpdateDate
						,IT.Batch as Batchw
					FROM InventTable IT
					WHERE IT.DisposalID = 28  
						 $whereBOI
						 AND IT.GT_Code IN
								(
									SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
									LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
									WHERE CCM.GreentireID = IT.GT_Code
									AND IM.ProductGroup = ?
															
								)ORDER BY IT.UpdateDate ASC",
					[
						$product_group
					]
				);
		}

		public function BuffreportServiceallpdf($datelight,$shift, $product_group, $pressBOI)
			{
				
				$conn = Database::connect();
				if($shift == "day"){
					$shift = 1;
				}else{
					$shift = 2;
				}
				if ($pressBOI == "" || $pressBOI == 1) {
					$whereBOI = "";
				} else {
					$whereBOI =	" AND CB.BOI ='$pressBOI' ";
				}
				return Sqlsrv::queryJson(
					$conn,
					"SELECT 
					IT.TemplateSerialNo
					,IT.Barcode
					,IT.CuringCode
					,IM.NameTH
					,IT.PressNo+IT.PressSide AS Press
					,IT.CuringDate
					,UM.Name
					,ITS.Batch
					,ITS.CreateDate
					,IM.ProductGroup
					,ITS.Shift
					 FROM InventTable IT
					LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode AND ITS.DisposalID = 28 AND ITS.DocumentTypeID = 1 AND ITS.WarehouseID = 4
					LEFT JOIN CureCodeMaster CM ON CM.ItemQ = ITS.CodeID
					LEFT JOIN ItemMaster IM ON IM.ID = CM.ItemID
					LEFT JOIN UserMaster UM ON UM.ID = ITS.CreateBy
					LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
					WHERE ITS.DisposalID = 28
						 AND DocumentTypeID = 1
						 AND ITS.WarehouseID = 4
						 AND IM.ProductGroup = ?
						 $whereBOI
						 AND CONVERT(DATE,ITS.CreateDate) = ?
						 AND ITS.Shift = ?",
					[
						$product_group,
						$datelight,
						$shift
					]
				);
		}
}
