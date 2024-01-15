<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Security;
use Respect\Validation\Validator as V;

class TrackingService
{
	public function searchByBarcode()
	{
		$conn = Database::connect();
		$barcode = Security::_decode(trim($_POST["search"]));

		$str_len = strlen(trim($barcode));

		if ((int) $str_len === 9) {
			$field = 'TemplateSerialNo';
		} else {
			$isBarcodeFoil = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT BarcodeFoil FROM InventTable
				WHERE BarcodeFoil = ?
				AND BarcodeFoil IS NOT NULL",
				[$barcode]
			));

			if ($isBarcodeFoil === true) {
				$field = 'BarcodeFoil';
			} else {
				$field = 'Barcode';
			}
		}


		if (!$conn) {
			echo json_encode(["status" => 404, "message" => "connection failed"]);
			exit;
		}

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
			exit;
		}

		$query = sqlsrv_query(
			$conn,
			"SELECT * FROM InventTable WHERE $field = ?",
			array($barcode)
		);

		$hasRows = sqlsrv_has_rows($query);

		if ($hasRows === false) {
			echo json_encode(["status" => 404, "message" => "ไม่พบรายการ" . $str_len]);
			exit;
		}

		return Sqlsrv::queryJson(
			$conn,
			"SELECT TOP 1
			IT.Barcode as BARCODE,
			IT.BarcodeFoil as BARCODEFOIL,
			DM.DisposalDesc AS DISPOSAL,
			CASE
				WHEN DM.DisposalDesc = 'Final' THEN (
					SELECT TOP 1 DIS.DisposalDesc
					FROM InventTrans IVTN
					LEFT JOIN DisposalToUseIn DIS ON DIS.ID = IVTN.DisposalID
					WHERE IVTN.DocumentTypeID = 1
					AND IVTN.DisposalID <> 4
					AND IVTN.Barcode = ?
					ORDER BY IVTN.id DESC
				)
				ELSE ''
			END AS PREFIX,
			IT.BuildingNo AS BUILDINGMC,
			CONVERT(VARCHAR,IT.DateBuild,105) + ' ' + SUBSTRING(CONVERT(VARCHAR, IT.DateBuild,108),1,5) AS BUILDINGDATE_H,
			IT.DateBuild AS BUILDINGDATE,
			IT.GT_Code AS GTCODE,
			CONVERT(VARCHAR,IT.CuringDate,105) + ' ' + SUBSTRING(CONVERT(VARCHAR, IT.CuringDate,108),1,5) AS CURINGDATE_H,
			IT.CuringDate AS CURINGDATE,
			IT.CuringCode AS CURINGCODE,
			-- ITM.ID AS ITEMID,
			-- ITM.NameTH AS ITEMNAME,
			IT.ItemID AS ITEMID,
			-- ITM.NameTH AS ITEMNAME,
      		ITM.NameTH AS ITEMNAME,
			IT.Batch AS BATCH,
			IT.TemplateSerialNo AS TEMPLATE,
			IST.ID as STATUSID,
			IST.Description as STATUS,
			INJ.InventJournalID as InventJournalID,
			CONVERT(VARCHAR,IT.FinalReceiveDate,105) + ' ' + SUBSTRING(CONVERT(VARCHAR, IT.FinalReceiveDate,108),1,5) AS FinalReceiveDate_H,
			IT.FinalReceiveDate
			FROM InventTable IT
			LEFT JOIN DisposalToUseIn DM ON DM.ID = IT.DisposalID
			-- LEFT JOIN ItemMaster ITM ON ITM.ID = IT.ItemID
			LEFT JOIN InventStatus IST ON IT.Status = IST.ID
			LEFT JOIN (
				select  INN.InventJournalID,INN.id,INN.Barcode  from InventTrans INN
			) INJ ON  INJ.Barcode = IT.Barcode and INJ.InventJournalID is not null
			LEFT JOIN CureCodeMaster CM
			ON IT.ItemID = CASE
			   WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN CM.ItemQ
			   ELSE CM.ItemID
			   END
            LEFT JOIN ItemMaster ITM ON ITM.ID = CM.ItemID
           	 OR ITM.ID = IT.ItemID
			WHERE IT.$field = '$barcode'order by INJ.id desc",
			[
				$barcode
			]
		);
	}

	public function searchByBarcode2()
	{
		$conn = Database::connect();
		$barcode = Security::_decode(trim($_POST["search"]));

		$str_len = strlen(trim($barcode));

		if ((int) $str_len === 9) {
			$field = 'TemplateSerialNo';
		} else {
			$isBarcodeFoil = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT BarcodeFoil FROM InventTable
				WHERE BarcodeFoil = ?
				AND BarcodeFoil IS NOT NULL",
				[$barcode]
			));

			if ($isBarcodeFoil === true) {
				$field = 'BarcodeFoil';
			} else {
				$field = 'Barcode';
			}
		}

		if (!$conn) {
			echo json_encode(["status" => 404, "message" => "connection failed"]);
			exit;
		}

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
			exit;
		}

		$query = sqlsrv_query(
			$conn,
			"SELECT * FROM InventTable WHERE $field = ?",
			array($barcode)
		);

		$hasRows = sqlsrv_has_rows($query);

		if ($hasRows === false) {
			echo json_encode(["status" => 404, "message" => "ไม่พบรายการ"]);
			exit;
		}



		if ($field === 'BarcodeFoil') {

			$barcodeFromBarcodeFoil = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Barcode FROM InventTable
				WHERE BarcodeFoil = ?",
				[
					$barcode
				]
			);

			$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
				[
					$barcodeFromBarcodeFoil[0]['Barcode']
				]
			));
		} else if ($field === 'TemplateSerialNo') {

			$serialFromBarcode = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Barcode FROM InventTable
				WHERE TemplateSerialNo = ?",
				[
					$barcode
				]
			);

			$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
				[
					$serialFromBarcode[0]['Barcode']
				]
			));
		} else {
			$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
				[
					$barcode
				]
			));
		}

		if ($inLoadingTrans === true) {

			if ($field === 'BarcodeFoil') {

				$barcodeFromBarcodeFoil = Sqlsrv::queryArray(
					$conn,
					"SELECT TOP 1 Barcode FROM InventTable
					WHERE BarcodeFoil = ?",
					[
						$barcode
					]
				);

				$sql2 = "SELECT
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					-- CS.CUSTOMER_CODE,
					-- CS.CUSTOMER_NAME,
					CASE WHEN CS.CUSTOMER_CODE IS NULL THEN C.Code ELSE CS.CUSTOMER_CODE END [CUSTOMER_CODE],
					CASE WHEN CS.CUSTOMER_NAME IS NULL THEN C.Name ELSE CS.CUSTOMER_NAME END [CUSTOMER_NAME],
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LDT.DeliveryName AS DELIVERY_NAME,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL,
					'SALE' AS DATE_TYPE
					from LoadingTrans LT
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					left join Customer C ON C.Code = LDT.InvoiceAccount AND C.DATAAREAID = 'DSC'
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn,
					$sql2,
					[
						$barcodeFromBarcodeFoil[0]['Barcode']
					]
				);
			} else if ($field === 'TemplateSerialNo') {

				$serialFromBarcode = Sqlsrv::queryArray(
					$conn,
					"SELECT TOP 1 Barcode FROM InventTable
					WHERE TemplateSerialNo = ?",
					[
						$barcode
					]
				);

				$sql3 = "SELECT
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					-- CS.CUSTOMER_CODE,
					-- CS.CUSTOMER_NAME,
					CASE WHEN CS.CUSTOMER_CODE IS NULL THEN C.Code ELSE CS.CUSTOMER_CODE END [CUSTOMER_CODE],
					CASE WHEN CS.CUSTOMER_NAME IS NULL THEN C.Name ELSE CS.CUSTOMER_NAME END [CUSTOMER_NAME],
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LDT.DeliveryName AS DELIVERY_NAME,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL,
					'SALE' AS DATE_TYPE
					from LoadingTrans LT
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					left join Customer C ON C.Code = LDT.InvoiceAccount AND C.DATAAREAID = 'DSC'
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn,
					$sql3,
					[
						$serialFromBarcode[0]['Barcode']
					]
				);
			} else {
				$sql1 = "SELECT
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					-- CS.CUSTOMER_CODE,
					-- CS.CUSTOMER_NAME,
					CASE WHEN CS.CUSTOMER_CODE IS NULL THEN C.Code ELSE CS.CUSTOMER_CODE END [CUSTOMER_CODE],
					CASE WHEN CS.CUSTOMER_NAME IS NULL THEN C.Name ELSE CS.CUSTOMER_NAME END [CUSTOMER_NAME],
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL,
					'SALE' AS DATE_TYPE
					from LoadingTrans LT
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					left join Customer C ON C.Code = LDT.InvoiceAccount AND C.DATAAREAID = 'DSC'
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn,
					$sql1,
					[
						$barcode
					]
				);
			}
		} else {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT
				IT.Barcode as BARCODE,
				IT.BarcodeFoil as BARCODEFOIL,
				DM.DisposalDesc AS DISPOSAL,
				IT.BuildingNo AS BUILDINGMC,
				IT.DateBuild as BUILDINGDATE,
				IT.GT_Code AS GTCODE,
				IT.CuringDate AS CURINGDATE,
				IT.CuringCode AS CURINGCODE,
				ITM.ID AS ITEMID,
				ITM.NameTH AS ITEMNAME,
				IT.Batch AS BATCH,
				IT.TemplateSerialNo AS TEMPLATE,
				IST.ID as STATUSID,
				IST.Description as [STATUS],
				'PROD' AS DATE_TYPE
				FROM InventTable IT
				LEFT JOIN DisposalToUseIn DM ON DM.ID = IT.DisposalID
				LEFT JOIN ItemMaster ITM ON ITM.ID = IT.ItemID
				LEFT JOIN InventStatus IST ON IT.Status = IST.ID
				WHERE $field = '$barcode'"
			);
		}
	}

	public function searchByBarcodeSvoPCR()
	{
		$conn = Database::connect();
		$barcode = Security::_decode(trim($_POST["search"]));
		$str_len = strlen(trim($barcode));

		if ((int) $str_len === 9) {
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Barcode FROM InventTable
				WHERE TemplateSerialNo = ?",
				[
					$barcode
				]
			);
			
			$decode_barcode = $query[0]['Barcode'];
			$template = $barcode;
		}else{
			$query = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 TemplateSerialNo FROM InventTable
				WHERE Barcode = ?",
				[
					$barcode
				]
			);
			$decode_barcode = $barcode;
			$template = $query[0]['TemplateSerialNo'];
		}

		

		$conn2 = Database::connectDeviceWMSSTR();
		$result = Sqlsrv::queryJson($conn2, "SELECT *
		FROM OPENQUERY(WMS_STR_LIVE, '
		SELECT 
				A.Serialnumber,
				A.Externorderkey,
				A.Orderkey,
				A.Loadid,
				O.Trailernumber,
				o.consigneekey,
				C_Company,
				A.SKU,
				SKU.DESCR,
				sku.susr7,
				LA.Lottable01,
				O.Orderdate,
				O.ACTUALSHIPDATE,
				OSS.Description,
				''$template'' AS template
		
			FROM AC2_LOADDETAIL A 
			JOIN SKU ON A.SKU = SKU.SKU
			JOIN ITRNSERIAL ITS ON A.SERIALNUMBER = ITS.Serialnumber 
			JOIN (
				SELECT Serialnumber, sku, Max(Serialkey) as Serialkey FROM Itrnserial 
				GROUP BY Serialnumber, sku
			) CKS ON its.serialkey = cks.serialkey AND its.serialnumber = cks.serialnumber AND Its.sku = cks.sku
			JOIN ORDERS O ON A.ORDERKEY = O.ORDERKEY 
			JOIN lotattribute LA ON ITS.lot = LA.Lot AND ITS.SKU = LA.SKU
			JOIN Orderstatussetup OSS ON O.Status = OSS.Code
		
			WHERE A.Serialnumber = ''$decode_barcode''
		');
		");

		if ($result != "[]") {
			return $result;
		}



		$result = Sqlsrv::queryJson($conn2, "SELECT *
		FROM OPENQUERY(WMS_LIVE, '
		SELECT 
				A.Serialnumber,
				A.Externorderkey,
				A.Orderkey,
				A.Loadid,
				O.Trailernumber,
				o.consigneekey,
				C_Company,
				A.SKU,
				SKU.DESCR,
				sku.susr7,
				LA.Lottable01,
				O.Orderdate,
				O.ACTUALSHIPDATE,
				OSS.Description,
				''$template'' AS template
		
			FROM AC2_LOADDETAIL A 
			JOIN SKU ON A.SKU = SKU.SKU
			JOIN ITRNSERIAL ITS ON A.SERIALNUMBER = ITS.Serialnumber 
			JOIN (
				SELECT Serialnumber, sku, Max(Serialkey) as Serialkey FROM Itrnserial 
				GROUP BY Serialnumber, sku
			) CKS ON its.serialkey = cks.serialkey AND its.serialnumber = cks.serialnumber AND Its.sku = cks.sku
			JOIN ORDERS O ON A.ORDERKEY = O.ORDERKEY 
			JOIN lotattribute LA ON ITS.lot = LA.Lot AND ITS.SKU = LA.SKU
			JOIN Orderstatussetup OSS ON O.Status = OSS.Code
		
			WHERE A.Serialnumber = ''$decode_barcode''
		');
		");

		if ($result) {
			return $result;
		}

		echo json_encode(["status" => 404, "message" => "เกิดข้อผิดพลาด"]);
	}

	public function searchByBarcodeLine()
	{
		$conn = Database::connect();
		$barcode = trim($_POST["barcode"]);

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "เกิดข้อผิดพลาด"]);
			exit;
		}

		return Sqlsrv::queryJson($conn, "SELECT * FROM InventTrans WHERE Barcode='$barcode'");
	}

	public function aa($datatest)
	{
		$conn = Database::connect();
		// $test = Sqlsrv::queryArray(
		//
		// 	$conn,
		// 	"SELECT  IT.DisposalID ,
		// 	DTU.DisposalDesc
		// 	FROM InventTrans IT
		// 	JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
		// 	WHERE IT.Barcode = '51801305926'"
		//
		// );
		return Sqlsrv::queryJson(
			$conn,
			"SELECT top 2  IT.DisposalID ,
			DTU.DisposalDesc,
			IT.CreateDate
			FROM InventTrans IT
			JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
			WHERE IT.Barcode = ? order by IT.id desc",
			[
				$datatest
			]
		);
		// echo json_encode(["status" => 404, "message" =>$datatest]);
	}

	public function showDefcet($datatest)
	{
		$conn = Database::connect();
		// $test = Sqlsrv::queryArray(
		//
		// 	$conn,
		// 	"SELECT  IT.DisposalID ,
		// 	DTU.DisposalDesc
		// 	FROM InventTrans IT
		// 	JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
		// 	WHERE IT.Barcode = '51801305926'"
		//
		// );
		return Sqlsrv::queryJson(
			$conn,
			"SELECT top 1  IT.DisposalID ,
			DTU.DisposalDesc,
			IT.CreateDate,
			DF.Description AS DeName
			FROM InventTrans IT
			LEFT JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
			LEFT JOIN Defect DF ON IT.DefectID = DF.ID
			
			WHERE IT.Barcode = ? 
				AND IT.DisposalID IN (10,12) AND DocumentTypeID = 1
				order by IT.id desc
			",
			[
				$datatest
			]
		);
		// echo json_encode(["status" => 404, "message" =>$datatest]);
	}

	public function showDefcetScrap($datatest)
	{
		$conn = Database::connect();
		// $test = Sqlsrv::queryArray(
		//
		// 	$conn,
		// 	"SELECT  IT.DisposalID ,
		// 	DTU.DisposalDesc
		// 	FROM InventTrans IT
		// 	JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
		// 	WHERE IT.Barcode = '51801305926'"
		//
		// );
		return Sqlsrv::queryJson(
			$conn,
			"SELECT top 2  IT.DisposalID ,
			DTU.DisposalDesc,
			IT.CreateDate,
			IT.DefectID,
			D.Description,
			CONVERT(VARCHAR,IB.UpdateDate,105) + ' ' + SUBSTRING(CONVERT(VARCHAR, IB.UpdateDate,108),1,5) AS UpdateDate_H,
			IB.UpdateDate
			FROM InventTrans IT
			JOIN DisposalToUseIn DTU ON DTU.ID = IT.DisposalID
			JOIN Defect D ON D.ID = IT.DefectID
			JOIN InventTable IB ON IB.Barcode = IT.Barcode
			WHERE IT.Barcode = ? order by IT.id desc
			",
			[
				$datatest
			]
		);
		// echo json_encode(["status" => 404, "message" =>$datatest]);
	}
}
