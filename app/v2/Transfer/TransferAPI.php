<?php

namespace App\V2\Transfer;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use App\V2\Barcode\BarcodeAPI;
use App\V2\Helper\Helper;
use App\V2\Item\ItemAPI;
use Wattanar\Sqlsrv;
use App\Components\Utils;
use App\V2\Sequeue\SequeueAPI;
use Wattanar\SqlsrvHelper;

class TransferAPI
{

	private $db = null;
	private $barcodeApi = null;
	private $helper = null;
	private $sequeue = null;
	private $sqlsrvHelper = null;

	public function __construct()
	{
		$this->db = new Connector();
		$this->barcodeApi = new BarcodeAPI();
		$this->helper = new Helper();
		$this->sequeue = new SequeueAPI();
		$this->sqlsrvHelper = new SqlsrvHelper();
	}

	public function saveSTRToSVOFinal($barcode)
	{

		$db = $this->db; //new Connector;
		$barcodeAPI = $this->barcodeApi;
		$helper = $this->helper;

		$conn = $db->dbConnect();
		$conn_svo = $db->connectSVO();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction begin error';
		}

		$barcodeInfo = $barcodeAPI->barcodeInfo($barcode);

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable
        SET DisposalID = ?,
        WarehouseID = ?,
        LocationID = ?,
        [Status] = 3, -- Confirmed
        UpdateBy = ?,
        Batch = ?,
        UpdateDate = ?,
				WarehouseTransReceiveDate =?,
				SendSVODate = ?
        WHERE Barcode = ?",
			[
				23, // send to SVO
				2, // Final
				4, // Final inspection
				$_SESSION['user_login'],
				$barcodeInfo[0]['Batch'],
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				$barcode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return 'update inventtable error.';
		}

		$insertTransMoveOut = sqlsrv_query(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$helper->getTransId($barcode) . 1,
				$barcode,
				$barcodeInfo[0]["ItemID"],
				$barcodeInfo[0]["Batch"],
				23, // send to SVO
				null,
				$barcodeInfo[0]["WarehouseID"],
				$barcodeInfo[0]["LocationID"],
				-1, // qty
				$barcodeInfo[0]["Unit"], // unit id
				2, // docs type
				$barcodeInfo[0]['Company'],
				$_SESSION['user_login'],
				date('Y-m-d H:i:s'),
				$_SESSION['Shift']
			]
		);

		if (!$insertTransMoveOut) {
			sqlsrv_rollback($conn);
			return 'insert trans moveout error';
		}

		// $moveOutOnhand = sqlsrv_query(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$barcodeInfo[0]["ItemID"],
		// 		$barcodeInfo[0]["WarehouseID"],
		// 		$barcodeInfo[0]["LocationID"],
		// 		$barcodeInfo[0]["Batch"],
		// 		$barcodeInfo[0]["Company"]
		// 	]
		// );

		// if (!$moveOutOnhand) {
		// 	sqlsrv_rollback($conn);
		// 	return 'moveout onhand error';
		// }

		$itemAPI = new ItemAPI;

		$itemInfo = $itemAPI->getItemInfo($barcodeInfo[0]["ItemID"]);

		if (count($itemInfo) === 0) {
			sqlsrv_rollback($conn);
			return 'item not found!';
		}

		// Insert to temp SVO
		if (
			substr($barcodeInfo[0]['PressNo'], 0, 1) === "I" ||
			substr($barcodeInfo[0]['PressNo'], 0, 1) === "J" ||
			$itemInfo[0]['ProductGroup'] === 'RDT'
		) {

			$move_barcode = sqlsrv_query(
				$conn_svo,
				"INSERT INTO STR_BARCODE_TEMP(
						Barcode,
						ItemID,
						Batch,
						CreateDate
					) VALUES (?, ?, ?, ?) ",
				[
					$barcode,
					$barcodeInfo[0]["ItemID"],
					$barcodeInfo[0]["Batch"],
					date('Y-m-d H:i:s')
				]
			);

			if (!$move_barcode) {
				sqlsrv_rollback($conn);
				return 'Barcode PCR can\'t move';
			}
		} else {
			sqlsrv_rollback($conn);
			return 'Barcode not PCR';
		}

		sqlsrv_commit($conn);
		return true;
	}

	public function saveSTRToSVOWH($barcode, $journal)
	{

		$barcodeAPI = $this->barcodeApi; //new BarcodeAPI;
		$helper = $this->helper; //new Helper;

		$conn = $this->db->dbConnect();
		// $conn_wms = $this->db->connectWMS();
		$conn_svo = $this->db->connectSVO();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction begin error';
		}

		$barcodeInfo = $barcodeAPI->barcodeInfo($barcode);

		$updateInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable
        SET DisposalID = ?,
        WarehouseID = ?,
        LocationID = ?,
        [Status] = 3, -- Confirmed
        UpdateBy = ?,
        Batch = ?,
        UpdateDate = ?,
				SendSVODate = ?
        WHERE Barcode = ?",
			[
				24, // send to SVO (WH)
				3, // WH
				7, // Finish good
				$_SESSION['user_login'],
				$barcodeInfo[0]['Batch'],
				date('Y-m-d H:i:s'),
				date('Y-m-d H:i:s'),
				$barcode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return 'update inventtable error.';
		}

		$insertTransMoveOut = sqlsrv_query(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$helper->getTransId($barcode) . 1,
				$barcode,
				$barcodeInfo[0]["ItemID"],
				$barcodeInfo[0]["Batch"],
				24, // send to SVO (WH)
				null,
				$barcodeInfo[0]["WarehouseID"],
				$barcodeInfo[0]["LocationID"],
				-1, // qty
				$barcodeInfo[0]["Unit"], // unit id
				2, // docs type
				$barcodeInfo[0]['Company'],
				$_SESSION['user_login'],
				date('Y-m-d H:i:s'),
				$_SESSION['Shift']
			]
		);

		if (!$insertTransMoveOut) {
			sqlsrv_rollback($conn);
			return 'insert trans moveout error';
		}

		// $moveOutOnhand = sqlsrv_query(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$barcodeInfo[0]["ItemID"],
		// 		$barcodeInfo[0]["WarehouseID"],
		// 		$barcodeInfo[0]["LocationID"],
		// 		$barcodeInfo[0]["Batch"],
		// 		$barcodeInfo[0]["Company"]
		// 	]
		// );

		// if (!$moveOutOnhand) {
		// 	sqlsrv_rollback($conn);
		// 	return 'moveout onhand error';
		// }

		$itemAPI = new ItemAPI;

		$itemInfo = $itemAPI->getItemInfo($barcodeInfo[0]["ItemID"]);

		if (count($itemInfo) === 0) {
			sqlsrv_rollback($conn);
			return 'item not found!';
		}

		// interface WMS
		if (
			substr($barcodeInfo[0]['PressNo'], 0, 1) === "I" ||
			substr($barcodeInfo[0]['PressNo'], 0, 1) === "J" ||
			$itemInfo[0]['ProductGroup'] === 'RDT'
		) {

			\sqlsrv_begin_transaction($conn_svo);

			$_itemName = Sqlsrv::queryArray(
				$conn,
				"SELECT IM.NameTH FROM InventTable I
					LEFT JOIN ItemMaster IM ON IM.ID = I.ItemID
					WHERE I.Barcode = ?",
				[
					$barcode
				]
			);

			if (count($_itemName) === 0) {
				$_itemName[0]['NameTH'] = null;
			}

			// $manuDate = date('Y-m-d', strtotime($barcodeInfo[0]['CuringDate']));
			// $expireDate = date('Y-m-d', strtotime("+180 day", strtotime($barcodeInfo[0]['CuringDate'])));

			// $getManuDateFromSVO = self::getManufactoringDateFromBatch($barcodeInfo[0]['Batch']);
			// $getExpireDateFromSVO = self::getExpireDateFromBatch($barcodeInfo[0]['Batch']);

			// if ( $getManuDateFromSVO !== null) {
			// 	$manuDate = $getManuDateFromSVO;
			// }

			// if ( $getExpireDateFromSVO !== null ) {
			// 	$expireDate = $getExpireDateFromSVO;
			// }

			$wmsBatch = str_replace("-D51", "", strtoupper($barcodeInfo[0]['Batch']));

			$wmsBatch = str_replace("-D52", "", strtoupper($wmsBatch));

			$wmsBatch = str_replace(BATCH_DSC, "", strtoupper($wmsBatch));

			// $manuDate = $this->getProdDateFromAx($barcodeInfo[0]['Batch'], $barcodeInfo[0]["ItemID"]);
			$manuDate = $this->getManufactoringDateFromBatch(str_replace(BATCH_DSC, "", $wmsBatch));
			// $manuDate = $this->getManufactoringDateFromBatch($barcodeInfo[0]['Batch']);

				

			// $getInventBatchFromAx = $this->getInterfaceBatch($barcodeInfo[0]['Batch'], $barcodeInfo[0]["ItemID"]);
			// if (count($getInventBatchFromAx) > 0) {
				// $wmsBatch = $getInventBatchFromAx[0]["DSG_ORIGINVENTBATCHID"];
			// }

			if ($manuDate === null) {
				throw new \Exception("production date not found. Item = " . $barcodeInfo[0]["ItemID"] . ", Batch = " . $barcodeInfo[0]['Batch']);
			}

			$expireDate = date('Y-m-d', strtotime("+180 day", strtotime($manuDate)));

			$insertWMSTempInterface = sqlsrv_query(
				$conn_svo,
				"INSERT INTO WMS_BarcodeTemp(
						ForceID,
						Barcode,
						ItemNo,
						BatchNo,
						ManufacturingDate,
						ExpiryDate,
						Flage,
						[Type],
						CreateDate,
						Gate,
						ITEM_NAME,
						CreateBy
					) VALUES (
						?, ?, ?, ?, ?,
						?, ?, ?, ?, ?,
						?, ?
					); SELECT scope_identity() as TempId",
				[
					'51',
					$barcode,
					$barcodeInfo[0]['ItemID'],
					$wmsBatch,
					$manuDate,
					$expireDate,
					'N',
					0,
					date('Y-m-d H:i:s'),
					51,
					substr($_itemName[0]['NameTH'], 0, 50),
					$_SESSION['user_login']
				]
			);

			if (!$insertWMSTempInterface) {
				sqlsrv_rollback($conn_svo);
				return "Insert WMS barcode temp error";
			}

			sqlsrv_next_result($insertWMSTempInterface);
			$row = sqlsrv_fetch_array($insertWMSTempInterface);
			$tempId = $row['TempId'];

			sqlsrv_commit($conn_svo);

			$insertWMSTemp = sqlsrv_query(
				$conn_svo,
				"EXEC uspInsertBarcodeWms ?",
				[$tempId]
			);

			// $error = sqlsrv_errors();

			// if (!$insertWMSTemp) {
			// 	sqlsrv_rollback($conn_svo);
			// 	return 'insert WMS temp failed.' . var_dump($error);
			// }
		} else {
			sqlsrv_rollback($conn);
			return 'This barcode not PCR';
		}
		//end of interface WMS
		
		// Check Barcode

		// $checkBarcode = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT JournalID,Barcode FROM JournalPCRLine WHERE JournalID = ? AND Barcode =  = ? ",
		// 	[
		// 		$journal,$barcode
		// 	]
		// );
		
		// if(count($checkBarcode) > 0){
		// 	return false;
		// }

		// $barcodeInfo[0]["ItemID"] . ", Batch = " . $barcodeInfo[0]['Batch']

		$updateJournalLine = sqlsrv_query(
			$conn,
			"INSERT INTO JournalPCRLine(
				JournalID,
				Barcode,
				ItemId,
				Batch,
				CreateBy,
				CreateDate
			) VALUES (
				?, ?, ?, ?, ?,
				?
			)",
			[
				$journal,
				$barcode,
				$barcodeInfo[0]["ItemID"],
				$barcodeInfo[0]['Batch'],
				$_SESSION['user_login'],
				date('Y-m-d H:i:s')
			]
		);

		if (!$updateJournalLine) {
			sqlsrv_rollback($conn);
			return 'Update Journal Failed!';
		}

		sqlsrv_commit($conn);
		return true;
	}

	public function getInterfaceBatch($batch, $item)
	{
		$conn = $this->db->dbConnect();

		$inventBatch = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1
			IB.DSG_ORIGINVENTBATCHID
			FROM [frey\live].[DSL_AX40_SP1_LIVE].dbo.InventBatch IB
			WHERE IB.DATAAREAID = 'str'
			and IB.INVENTBATCHID = ?
			and IB.ITEMID = ?",
			[
				$batch,
				$item
			]
		);

		return $inventBatch;
	}

	public function getTruck()
	{
		$conn = $this->db->dbConnect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM TruckMaster"
		);
	}

	public function createJournalPCR($desc, $truck)
	{
		try {
			// code
			$conn = $this->db->dbConnect();

			if (sqlsrv_begin_transaction($conn) === false) {
				throw new \Exception("Begin transaction failed.");
			}

			$updateSeq = $this->sequeue->updateSequeue('journal_pcr');
			if (!$updateSeq) {
				throw new \Exception("Update sequeue failed.");
			}

			$lastestSeq = $this->sequeue->getLatestSequeue('journal_pcr');

			$create = sqlsrv_query(
				$conn,
				"INSERT INTO JournalPCRTable(
				JournalID,
				JournalDescription,
				TruckID,
				CreateDate,
				CreateBy
			) VALUES (
				?, ?, ?, ?, ?
			)",
				[
					$lastestSeq,
					$desc,
					$truck,
					date('Y-m-d H:i:s'),
					$_SESSION['user_login']
				]
			);

			if (!$create) {
				sqlsrv_rollback($conn);
				return 'Create journal failed';
			}

			$createJournalMapping = sqlsrv_query(
				$conn,
				"INSERT INTO WMS_TransferJournal(
				STR_JournalId,
				CreateAt
			) VALUES (
				?, ?
			)",
				[
					$lastestSeq,
					date("Y-m-d H:i:s")
				]
			);

			if (!$createJournalMapping) {
				sqlsrv_rollback($conn);
				return 'Create journal mapping failed'  . var_dump(sqlsrv_errors());
			}

			sqlsrv_commit($conn);
			return true;

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getJournalPCR($device)
	{
		$conn = $this->db->dbConnect();

		if ($device === 'mobile') {
			$where = 'WHERE J.Complete = 0';
		} else {
			$where = '';
		}

		return Sqlsrv::queryArray(
			$conn,
			"SELECT
			J.JournalID,
			J.JournalDescription,
			J.TruckID,
			J.CreateDate,
			J.Complete,
			J.CompleteDate,
			WMST.AX_JournalId,
			J.AxConfirmed,
			J.AxConfirmedDate,
			J.AxPosted,
			J.AxPostedDate,
			UM2.Name AS CompleteBy,
			UM.Name AS UserName,
			(
				SELECT COUNT(JL.Barcode)
				FROM JournalPCRLine JL
				WHERE JL.JournalID = J.JournalID
			) AS [Count]
			FROM JournalPCRTable J
			LEFT JOIN UserMaster UM ON UM.ID = J.CreateBy
			LEFT JOIN UserMaster UM2 ON UM2.ID = J.CompleteBy 
			left join WMS_TransferJournal WMST ON WMST.STR_JournalId = J.JournalID 
			$where
			ORDER BY J.CreateDate DESC"
		);
	}

	public function getJournalPCRNoComplete()
	{
		$conn = $this->db->dbConnect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT
			J.JournalID,
			J.JournalDescription,
			J.TruckID,
			J.CreateDate,
			Tm.PlateNumber [Plate],
			UM.Name AS UserName,
			(
				SELECT COUNT(JL.Barcode)
				FROM JournalPCRLine JL
				WHERE JL.JournalID = J.JournalID
			) AS [Count]
			FROM JournalPCRTable J
			LEFT JOIN UserMaster UM ON UM.ID = J.CreateBy
			LEFT JOIN TruckMaster TM ON TM.PlateNumber = J.TruckID
			WHERE J.Complete = 0"
		);
	}

	public function getJournalPCRLine($journal)
	{
		$conn = $this->db->dbConnect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT
			JL.JournalID,
			JL.Barcode,
			T.ItemID,
			IM.NameTH AS ItemName,
			T.Batch,
			JL.CreateDate,
			UM.Name AS UserName
			FROM JournalPCRLine JL
			LEFT JOIN UserMaster UM ON UM.ID = JL.CreateBy
			LEFT JOIN InventTable T ON T.Barcode = JL.Barcode
			LEFT JOIN ItemMaster IM ON IM.ID = T.ItemID
			where JL.JournalID = ?
			ORDER BY JL.CreateDate ASC",
			[
				$journal
			]
		);
	}

	public function getJournalPCRLineSummary($journal)
	{
		$conn = $this->db->dbConnect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT
			T.ItemID,
			IM.NameTH AS ItemName,
			T.Batch,
			SUM(1) AS QTY
			FROM JournalPCRLine JL
			JOIN InventTable T ON T.Barcode = JL.Barcode
			LEFT JOIN ItemMaster IM ON IM.ID = T.ItemID
			where JL.JournalID = ?
			group by
			T.ItemID,
			IM.NameTH,
			T.Batch
			ORDER BY T.ItemID ASC",
			[
				$journal
			]
		);
	}

	public function updateJournal($journal_id, $journal_description)
	{
		$conn = $this->db->dbConnect();

		$update = sqlsrv_query(
			$conn,
			"UPDATE JournalPCRTable
			SET JournalDescription = ?
			WHERE JournalID = ?",
			[
				$journal_description,
				$journal_id
			]
		);

		if ($update) {
			return true;
		} else {
			return 'Update failed!';
		}
	}

	public function completeJournal($journal_id)
	{
		$conn = $this->db->dbConnect();

		$update = sqlsrv_query(
			$conn,
			"UPDATE JournalPCRTable
			SET Complete = ?,
			CompleteBy = ?,
			CompleteDate = ?
			WHERE JournalID = ?",
			[
				1,
				$_SESSION['user_login'],
				date('Y-m-d H:i:s'),
				$journal_id
			]
		);

		if ($update) {
			return true;
		} else {
			return 'Update failed!';
		}
	}

	public function countJournalLine($journal_id)
	{
		$conn = $this->db->dbConnect();
		$count =  Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(JL.Barcode) AS QTY
			FROM JournalPCRLine JL
			WHERE JL.JournalID = ?",
			[
				$journal_id
			]
		);

		if (count($count) === 0) {
			return 0;
		} else {
			return $count[0]['QTY'];
		}
	}

	public function getJournalDetail($journal_id)
	{
		$conn = $this->db->dbConnect();

		$data = Sqlsrv::queryArray(
			$conn,
			"SELECT
				JournalID,
				JournalDescription,
				TruckID,
				CreateDate,
				CreateBy,
				Complete,
				CompleteBy,
				CompleteDate
			FROM JournalPCRTable
			WHERE JournalID = ?",
			[
				$journal_id
			]
		);

		if (count($data) === 0) {
			return [];
		} else {
			return $data;
		}
	}

	public function getFirstScanPCR($journal_id)
	{
		$conn = $this->db->dbConnect();
		$data =  Sqlsrv::queryArray(
			$conn,
			"SELECT
			JL.CreateDate
			FROM JournalPCRLine JL
			where JL.JournalID = ?
			ORDER BY ID ASC",
			[
				$journal_id
			]
		);

		if (count($data) !== 0) {
			return $data[0]['CreateDate'];
		} else {
			return '-';
		}
	}

	public function getLastScanPCR($journal_id)
	{
		$conn = $this->db->dbConnect();
		$data =  Sqlsrv::queryArray(
			$conn,
			"SELECT
			TOP 1 JL.CreateDate
			FROM JournalPCRLine JL
			where JL.JournalID = ?
			ORDER BY JL.ID DESC",
			[
				$journal_id
			]
		);

		if (count($data) !== 0) {

			if ($data[0]['CreateDate'] === null) {
				return '-';
			}

			return $data[0]['CreateDate'];
		} else {
			return '-';
		}
	}

	public function printJournalPCR($journal_id)
	{
		return [
			'journal_info' => $this->getJournalDetail($journal_id),
			'journal_line' => $this->getJournalPCRLineSummary($journal_id),
			'first_scan' => $this->getFirstScanPCR($journal_id),
			'last_scan' => $this->getLastScanPCR($journal_id)
		];
	}

	public function getManufactoringDateFromBatch($batch)
	{
		$conn = $this->db->connectSVO();
		$manuDate = Sqlsrv::queryArray(
			$conn,
			"SELECT manufacturing_date
			FROM Batch
			WHERE batch_no = ?",
			[
				$batch
			]
		);

		if (count($manuDate) === 0) {
			return null;
		} else {
			return $manuDate[0]['manufacturing_date'];
		}
	}

	public function getExpireDateFromBatch($batch)
	{
		$conn = $this->db->connectSVO();
		$expireDate = Sqlsrv::queryArray(
			$conn,
			"SELECT expire_date
			FROM Batch
			WHERE batch_no = ?",
			[
				$batch
			]
		);

		if (count($expireDate) === 0) {
			return null;
		} else {
			return $expireDate[0]['expire_date'];
		}
	}

	//test
	public function getProdDateFromAx($batch, $itemId)
	{
		$conn = $this->db->connectSVO();
		$manuDate = Sqlsrv::queryArray(
			$conn,
			"SELECT
			IB.INVENTBATCHID,
			IB.PRODDATE,
			IB.ITEMID
			FROM [frey\live].[DSL_AX40_SP1_LIVE].dbo.InventBatch IB
			WHERE IB.ITEMID = ?
			AND IB.INVENTBATCHID = ?
			and IB.DATAAREAID = 'str'",
			[
				$itemId,
				$batch
			]
		);

		if (count($manuDate) === 0) {
			return null;
		} else {
			return $manuDate[0]['PRODDATE'];
		}
	}

	public function manualInterface()
	{
		exit(":)");
		// try {
		// 	$conn_svo = $this->db->connectSVO();
		// 	$conn = $this->db->dbConnect();

		// 	$barcodes = $this->sqlsrvHelper->getRows(sqlsrv_query(
		// 		$conn,
		// 		"SELECT IT.Barcode
		// 		from InventTable IT
		// 		where IT.SendSVODate >= '2019-12-15 08:00:00'
		// 		and IT.SendSVODate <= '2019-12-15 20:00:00'
		// 		"
		// 	));


		// 	if (sqlsrv_begin_transaction($conn_svo) === false) {
		// 		throw new \Exception(var_dump(sqlsrv_errors()));
		// 	}
		// 	foreach ($barcodes as $data) {

		// 		$barcode = $data["Barcode"];

		// 		$wmsTemp = sqlsrv_has_rows(sqlsrv_query(
		// 			$conn_svo,
		// 			"SELECT Barcode FROM WMS_BarcodeTemp
		// 			WHERE Barcode = ?",
		// 			[
		// 				$barcode
		// 			]
		// 		));

		// 		if ($wmsTemp === false) {

		// 			$barcodeInfo = $this->barcodeApi->barcodeInfo($barcode);

		// 			$itemName = Sqlsrv::queryArray(
		// 				$conn,
		// 				"SELECT IM.NameTH FROM InventTable I
		// 			LEFT JOIN ItemMaster IM ON IM.ID = I.ItemID
		// 			WHERE I.Barcode = ?",
		// 				[
		// 					$barcode
		// 				]
		// 			);

		// 			if (count($itemName) === 0) {
		// 				$itemName[0]['NameTH'] = null;
		// 			}

		// 			$manuDate = self::getProdDateFromAx($barcodeInfo[0]['Batch'], $barcodeInfo[0]["ItemID"]);

		// 			if ($manuDate === null) {
		// 				$manuDate = date('Y-m-d');
		// 			}

		// 			$expireDate = date('Y-m-d', strtotime("+180 day", strtotime($manuDate)));

		// 			$insertWMSTempInterface = sqlsrv_query(
		// 				$conn_svo,
		// 				"INSERT INTO WMS_BarcodeTemp(
		// 				ForceID,
		// 				Barcode,
		// 				ItemNo,
		// 				BatchNo,
		// 				ManufacturingDate,
		// 				ExpiryDate,
		// 				Flage,
		// 				[Type],
		// 				CreateDate,
		// 				Gate,
		// 				ITEM_NAME,
		// 				CreateBy
		// 			) VALUES (
		// 				?, ?, ?, ?, ?,
		// 				?, ?, ?, ?, ?,
		// 				?, ?
		// 			); SELECT scope_identity() as TempId",
		// 				[
		// 					'51',
		// 					$barcode,
		// 					$barcodeInfo[0]['ItemID'],
		// 					$barcodeInfo[0]['Batch'],
		// 					$manuDate,
		// 					$expireDate,
		// 					'N',
		// 					0,
		// 					date('Y-m-d H:i:s'),
		// 					51,
		// 					substr($itemName[0]['NameTH'], 0, 50),
		// 					$_SESSION['user_login']
		// 				]
		// 			);

		// 			if (!$insertWMSTempInterface) {
		// 				sqlsrv_rollback($conn_svo);
		// 				throw new \Exception("Insert WMS barcode temp error");
		// 			}

		// 			sqlsrv_next_result($insertWMSTempInterface);
		// 			$row = sqlsrv_fetch_array($insertWMSTempInterface);
		// 			$tempId = $row['TempId'];

		// 			sqlsrv_commit($conn_svo);

		// 			$insertWMSTemp = sqlsrv_query(
		// 				$conn_svo,
		// 				"EXEC uspInsertBarcodeWms ?",
		// 				[$tempId]
		// 			);

		// 			$error = sqlsrv_errors();

		// 			if (!$insertWMSTemp) {
		// 				sqlsrv_rollback($conn_svo);
		// 				throw new \Exception('insert WMS temp failed.' . var_dump($error));
		// 			}

		// 			echo $barcode . " Success. <br/>";
		// 		}
		// 	}
		// } catch (\Exception $e) {
		// 	echo $e->getMessage() . "<br/>";
		// }
	}
}
