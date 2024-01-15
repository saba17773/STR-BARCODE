<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use Wattanar\SqlsrvHelper;
use App\V2\Barcode\BarcodeAPI;

class LoadingService
{
	private $barcodeApi = null;
	private $sql = null;

	public function __construct()
	{
		$this->barcodeApi = new BarcodeAPI();
		$this->sql = new SqlsrvHelper();
	}

	public function getLoadingTableAllStatus()
	{
		$sql = "SELECT TOP 100 IT.DocNo,
				IT.OrderId,
				C.Code [CustID],
				CASE
				 WHEN (SELECT TOP 1 CSO.SO_ID FROM CustomerSO CSO WHERE IT.OrderId = CSO.SO_FACTORY) IS NOT NULL
				 THEN (SELECT TOP 1 CSO.SO_ID FROM CustomerSO CSO WHERE IT.OrderId = CSO.SO_FACTORY)
				 ELSE IT.OrderId END[Sodsc],
				IT.PickingListId,
				CONVERT(date, IT.DeliveryDate) as DeliveryDate,
				IT.DeliveryName,
				IT.DeliveryAddress,
				IT.DeliveryZipCode,
				IT.InvoiceAccount,
				CASE
					WHEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
						AND CS.SO_ID IS NOT NULL
					) IS NOT NULL
					THEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
						AND CS.SO_ID IS NOT NULL
					)
					WHEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
						AND CS.SO_ID IS NOT NULL
					) IS NULL
					THEN C.Name
				END [CustName],
				IT.PickingListDate,
				IT.DSG_WO_NO,
				CONVERT(date, IT.ConfirmDate) as ConfirmDate,
				IT.RefPickingListId,
				IT.Status,
				IT.CreatedDate,
				IT.CreatedBy,
				IT.Company,
				IT.UpdateBy,
				IT.UpdateDate,
				LS.Description as StatusDesc,
				UM.Name as Fullname
				FROM LoadingTable IT
				LEFT JOIN LoadingStatus LS ON LS.ID = IT.Status
				-- LEFT JOIN Customer C ON C.Code = IT.CustAccount AND C.DATAAREAID = IT.DATAAREAID
				LEFT JOIN Customer C ON C.Code = IT.CustAccount AND CASE WHEN C.DATAAREAID <> 'DSC' THEN 'STR' ELSE 'DSC' END  = IT.DATAAREAID
				LEFT JOIN UserMaster UM ON UM.ID = IT.CreatedBy
				ORDER BY IT.CreatedDate DESC";

		if (isset($_GET['filterscount'])) {
			$filterscount = $_GET['filterscount'];

			if ($filterscount > 0) {
				$sql = "";
				$where = "WHERE (";
				$tmpdatafield = "";
				$tmpfilteroperator = "";
				for ($i = 0; $i < $filterscount; $i++) {
					// get the filter's value.
					$filtervalue = $_GET["filtervalue" . $i];
					// get the filter's condition.
					$filtercondition = $_GET["filtercondition" . $i];
					// get the filter's column.
					$filterdatafield = $_GET["filterdatafield" . $i];
					// get the filter's operator.
					$filteroperator = $_GET["filteroperator" . $i];

					if ($filterdatafield === 'CheckBuild') {
						if ((string) $filtervalue === 'true') {
							$tmp_value = 1;
						} else {
							$tmp_value = 0;
						}
						$filtervalue = $tmp_value;
					}

					if ($tmpdatafield == "") {
						$tmpdatafield = $filterdatafield;
					} else if ($tmpdatafield <> $filterdatafield) {
						$where .= ")AND(";
					} else if ($tmpdatafield == $filterdatafield) {
						if ($tmpfilteroperator == 0) {
							$where .= " AND ";
						} else $where .= " OR ";
					}

					// build the "WHERE" clause depending on the filter's condition, value and datafield.
					switch ($filtercondition) {
						case "CONTAINS":
							$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
							break;
						case "DOES_NOT_CONTAIN":
							$where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
							break;
						case "EQUAL":
							$where .= " " . $filterdatafield . " = '" . $filtervalue . "'";
							break;
						case "NOT_EQUAL":
							$where .= " " . $filterdatafield . " <> '" . $filtervalue . "'";
							break;
						case "GREATER_THAN":
							$where .= " " . $filterdatafield . " > '" . $filtervalue . "'";
							break;
						case "LESS_THAN":
							$where .= " " . $filterdatafield . " < '" . $filtervalue . "'";
							break;
						case "GREATER_THAN_OR_EQUAL":
							$where .= " " . $filterdatafield . " >= '" . $filtervalue . "'";
							break;
						case "LESS_THAN_OR_EQUAL":
							$where .= " " . $filterdatafield . " <= '" . $filtervalue . "'";
							break;
						case "STARTS_WITH":
							$where .= " " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
							break;
						case "ENDS_WITH":
							$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
							break;
					}

					if ($i == $filterscount - 1) {
						$where .= ")";
					}

					$tmpfilteroperator = $filteroperator;
					$tmpdatafield = $filterdatafield;
				}
				// build the query.
				$sql = "SELECT TOP 100 * FROM (SELECT IT.DocNo,
									IT.OrderId,
									C.Code [CustID],
									CASE
									WHEN (SELECT TOP 1 CSO.SO_ID FROM CustomerSO CSO WHERE IT.OrderId = CSO.SO_FACTORY) IS NOT NULL
									THEN (SELECT TOP 1 CSO.SO_ID FROM CustomerSO CSO WHERE IT.OrderId = CSO.SO_FACTORY)
									ELSE IT.OrderId END[Sodsc],
									IT.PickingListId,
									CONVERT(date, IT.DeliveryDate) as DeliveryDate,
									IT.DeliveryName,
									IT.DeliveryAddress,
									IT.DeliveryZipCode,
									IT.InvoiceAccount,
									CASE
										WHEN
										(
											SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
											WHERE CS.SO_FACTORY = IT.OrderId
										) IS NOT NULL
										THEN
										(
											SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
											WHERE CS.SO_FACTORY = IT.OrderId
										)
										WHEN
										(
											SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
											WHERE CS.SO_FACTORY = IT.OrderId
										) IS NULL
										THEN C.Name
									END [CustName],
									IT.PickingListDate,
									IT.DSG_WO_NO,
									CONVERT(date, IT.ConfirmDate) as ConfirmDate,
									IT.RefPickingListId,
									IT.Status,
									IT.CreatedDate,
									IT.CreatedBy,
									IT.Company,
									IT.UpdateBy,
									IT.UpdateDate,
									LS.Description as StatusDesc,
									UM.Name as Fullname
									FROM LoadingTable IT
									LEFT JOIN LoadingStatus LS ON LS.ID = IT.Status
									-- LEFT JOIN Customer C ON C.Code = IT.CustAccount AND C.DATAAREAID = IT.DATAAREAID
									LEFT JOIN Customer C ON C.Code = IT.CustAccount AND CASE WHEN C.DATAAREAID <> 'DSC' THEN 'STR' ELSE 'DSC' END  = IT.DATAAREAID
									LEFT JOIN UserMaster UM ON UM.ID = IT.CreatedBy) X " . $where;
			}
		}

		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function getLoadingTable($pickingListId)
	{
		$conn = (new Database)->connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT IT.DocNo,
			IT.OrderId,
			IT.PickingListId,
			IT.DeliveryDate,
			IT.DeliveryName,
			IT.DeliveryAddress,
			IT.DeliveryZipCode,
			IT.InvoiceAccount,
			CASE
					WHEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
					) IS NOT NULL
					THEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
					)
					WHEN
					(
						SELECT TOP 1 CS.CUSTOMER_NAME FROM CustomerSO CS
						WHERE CS.SO_FACTORY = IT.OrderId
					) IS NULL
					THEN C.Name
				END [CustName],
			IT.PickingListDate,
			IT.DSG_WO_NO,
			IT.ConfirmDate,
			IT.RefPickingListId,
			IT.Status,
			IT.CreatedDate,
			IT.CreatedBy,
			IT.Company,
			IT.UpdateBy,
			IT.UpdateDate,
			LS.Description as StatusDesc
			FROM LoadingTable IT
			LEFT JOIN LoadingStatus LS ON LS.ID = IT.Status
			LEFT JOIN Customer C ON C.Code = IT.CustAccount
			WHERE IT.PICKINGLISTID = ?
			AND IT.Status IN (1,2,3,5)", // status = Open, In-Progess, Confirm, Conpleted
			[$pickingListId]
		);
	}

	public function getLoadingTableAll()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"exec spLoading"
		);
	}

	public function getLoadingLine($pickingListId)
	{
		$conn = (new Database)->connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			L.LineID,
			L.PickingListId,
			L.InventTransId,
			L.ItemId,
			L.OrderQty,
			L.OrderUnit,
			L.Name,
			L.Remainder,
			L.LoadingQTY,
			L.OrderId,
			LS.ID as Status,
			LS.Description as StatusDesc,
			L.CreatedDate,
			L.CreatedBy,
			L.Company
			FROM LoadingLine L
			LEFT JOIN LoadingStatus LS ON L.Status = LS.ID
			WHERE L.PICKINGLISTID = ?
			ORDER BY (
	     CASE
	       WHEN L.Status = 2 THEN 0
	       WHEN L.Status = 1 THEN 1
	       WHEN L.Status = 5 THEN 2
	       ELSE 9
	     END
	    ) ASC",
			[$pickingListId]
		);
	}

	public function isPickingListIdExistsInPickingListJour($pickingListId)
	{
		$conn = (new Database)->connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT PICKINGLISTID FROM InventPickingListJour
			WHERE PICKINGLISTID = ?",
			[$pickingListId]
		);
	}

	public function isPickingListIdExistsInLoadingTable($pickingListId)
	{
		$conn = (new Database)->connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT PickingListId FROM LoadingTable
			WHERE PickingListId = ?
			AND Status <> 6", // cancel
			[$pickingListId]
		);
	}

	public function createLoadingTable($pickingListId)
	{
		$conn = (new Database)->connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'Transaction begin error.';
		}

		$pickingListJour = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventPickingListJour
			WHERE PICKINGLISTID = ?",
			[$pickingListId]
		);

		$pickingListTrans = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT * FROM InventPickingListTrans
			WHERE PICKINGLISTID = ?",
			[$pickingListId]
		);

		$date = date('Y-m-d H:i:s');
		$dateForDocNo = date('YmdHis');

		$docno = $pickingListJour[0]['ORDERID'] . $dateForDocNo;

		$addLoadingTable = (new Sqlsrv)->insert(
			$conn,
			"INSERT INTO LoadingTable(
				DocNo,
				OrderId,
				PickingListId,
				DeliveryDate,
				DeliveryName,
				DeliveryAddress,
				DeliveryZipCode,
				InvoiceAccount,
				CustAccount,
				PickingListDate,
				DSG_WO_NO,
				ConfirmDate,
				RefPickingListId,
				Status,
				CreatedDate,
				CreatedBy,
				Company,
				UpdateBy,
				UpdateDate,
				DATAAREAID
			) VALUES(
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$docno,
				$pickingListJour[0]['ORDERID'],
				$pickingListJour[0]['PICKINGLISTID'],
				$pickingListJour[0]['DELIVERYDATE'],
				$pickingListJour[0]['DELIVERYNAME'],
				$pickingListJour[0]['DELIVERYADDRESS'],
				$pickingListJour[0]['DELIVERYZIPCODE'],
				$pickingListJour[0]['INVOICEACCOUNT'],
				$pickingListJour[0]['CUSTACCOUNT'],
				$pickingListJour[0]['PICKINGLISTDATE'],
				$pickingListJour[0]['DSG_WO_NO'],
				null,
				null,
				1, // status open
				$date,
				$_SESSION['user_login'],
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				$date,
				$pickingListJour[0]['DATAAREAID']
			]
		);

		if (!$addLoadingTable) {
			sqlsrv_rollback($conn);
			return 'Add loading table error';
		}

		foreach ($pickingListTrans as $e) {

			if ((int) $e['DSG_LOADINGQTY'] > 0) {
				$__qty = (int) $e['DSG_LOADINGQTY'];
			} else {
				$__qty = (int) $e['ORDERQTY'];
			}

			$addLoadingLine = (new Sqlsrv)->insert(
				$conn,
				"INSERT INTO LoadingLine(
					LineID,
					PickingListId,
					InventTransId,
					ItemId,
					OrderQty,
					OrderUnit,
					Name,
					Remainder,
					LoadingQTY,
					OrderId,
					Status,
					CreatedDate,
					CreatedBy,
					Company
				) VALUES(
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?
				)",
				[
					(int) $e['LINENUM'],
					$e['PICKINGLISTID'],
					$e['INVENTTRANSID'],
					$e['ITEMID'],
					$__qty,
					$e['ORDERUNIT'],
					$e['NAME'],
					$__qty,
					0, //(int)$e['DSG_LOADINGQTY'],
					$e['ORDERID'],
					1, // open
					$date,
					$_SESSION['user_login'],
					$_SESSION['user_company']
				]
			);

			if (!$addLoadingLine) {
				sqlsrv_rollback($conn);
				exit(400);
			}
		}

		// $isPinkingListIdExists = (new Sqlsrv)->hasRows(
		// 	$conn,
		// 	"SELECT PICKINGLISTID FROM LoadingTable
		// 	WHERE PICKINGLISTID = ?",
		// 	[$pickingListId]
		// );

		// if ($isPinkingListIdExists === false) {
		// 	sqlsrv_rollback($conn);
		// 	return 'Picking list id in loading table not found.';
		// }

		// $getLoadingTable = (new Sqlsrv)->queryJson(
		// 	$conn,
		// 	"SELECT * FROM LoadingTable
		// 	WHERE PICKINGLISTID = ?",
		// 	[$pickingListId]
		// );

		// if (!$getLoadingTable) {
		// 	sqlsrv_rollback($conn);
		// 	return 'Get data error.';
		// }

		sqlsrv_commit($conn);
		return 200;
	}

	public function isItemMatch($pid, $inventTransId, $barcode)
	{
		$barcode_decode = (new Security)->_decode($barcode);
		$conn = (new Database)->connect();
		return (new Sqlsrv)->hasRows(
			$conn,
			"SELECT LL.ItemId FROM LoadingLine LL
			INNER JOIN InventTable IT ON LL.ItemId = IT.ItemID
			WHERE LL.InventTransId = ?
			AND LL.PickingListId = ?
			AND IT.Barcode = ?",
			[$inventTransId, $pid, $barcode_decode]
		);
	}

	public function savePick($pid, $inventTransId, $barcode)
	{
		$barcode_decode = $barcode;
		$conn = (new Database)->connect();
		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'error connection';
		}

		// get barcode info from invent table barcode
		$inventTable = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		if ($inventTable[0]['PalletNo'] !== null) {

			// update pallet table
			$updatePalletTable = sqlsrv_query(
				$conn,
				"UPDATE PalletTable
        SET qty_in_use -= 1,
        remain += 1,
        update_by = ?,
        update_date = ?
        WHERE pallet_no = ?",
				[
					$_SESSION['user_login'],
					date('Y-m-d H:i:s'),
					$inventTable[0]['PalletNo']
				]
			);

			if (!$updatePalletTable) {
				\sqlsrv_rollback($conn);
				return 'update pallet table failed!';
			}

			// update pallet line
			$deletePalletLine = sqlsrv_query(
				$conn,
				"DELETE FROM PalletLine
				WHERE barcode = ?",
				[
					$barcode
				]
			);

			if (!$deletePalletLine) {
				\sqlsrv_rollback($conn);
				return 'delete barcode form pallet line failed!';
			}

			$isLastOneInPallet = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT remain
				FROM PalletTable
				WHERE qty_in_use = 0
				AND pallet_no = ?",
				[
					$inventTable[0]['PalletNo']
				]
			));

			if ($isLastOneInPallet === true) {

				// complete pallet
				$completePallet = sqlsrv_query(
					$conn,
					"UPDATE PalletTable
          SET [status] = 5,
          complete_date = ?,
          complete_by = ?,
          location_id = ?
          WHERE pallet_no = ?
          AND [status] NOT IN (4,5)",
					[
						date('Y-m-d H:i:s'),
						$_SESSION['user_login'],
						null,
						$inventTable[0]['PalletNo']
					]
				);

				if (!$completePallet) {
					\sqlsrv_rollback($conn);
					return 'complete pallet failed!';
				}

				$resetPalletMaster = sqlsrv_query(
					$conn,
					"UPDATE PalletMaster
					SET pallet_item = null
					WHERE pallet_no = ?",
					[
						$inventTable[0]['PalletNo']
					]
				);

				if (!$resetPalletMaster) {
					\sqlsrv_rollback($conn);
					return 'reset pallet master failed!';
				}

				// update remain location
				$updateRemainLocation = sqlsrv_query(
					$conn,
					"UPDATE Location
					SET Remain += 1,
					QtyInUse -= 1
					WHERE ID = ?",
					[
						$inventTable[0]['LocationID']
					]
				);

				if (!$updateRemainLocation) {
					\sqlsrv_rollback($conn);
					return 'update remain location failed!';
				}
			}
		}

		// getLoadingTable
		$loadingTable = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 * FROM LoadingTable
			WHERE PickingListId = ?",
			[$pid]
		);

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		if (count($get_location) === 0) {
			sqlsrv_rollback($conn);
			return 'user location not found!';
		}

		$loadingLine = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT * FROM LoadingLine WHERE InventTransId = ? AND PickingListId = ?",
			[$inventTransId, $pid]
		);

		$currentRemainder = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT Remainder FROM LoadingLine
			WHERE InventTransId = ?
			AND Remainder = 0
			AND [Status] <> 6
			AND PickingListId = ?",
			[$inventTransId, $pid]
		);

		if ($currentRemainder === true) {
			sqlsrv_rollback($conn);
			return 901;
		}

		$updateLoadingTable = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTable
			SET UpdateBy = ?,
			UpdateDate = ?
			WHERE PickingListId = ?",
			[
				$_SESSION['user_login'],
				$date,
				$pid
			]
		);

		if (!$updateLoadingTable) {
			sqlsrv_rollback($conn);
			return 'Update LoadingTable error';
		}

		$trans_id = (new Utils)->genTransId($barcode_decode);

		// loading trans
		$loadingTrans = (new Sqlsrv)->insert(
			$conn,
			"UPDATE LoadingTrans
			SET Status = 7
			WHERE Barcode = ?
			AND PickingListId = ?
			IF @@ROWCOUNT = 0
			INSERT INTO LoadingTrans(
				TransId,
				Barcode,
				InventTransId,
				ItemId,
				BatchNo,
				Qty,
				OrderUnit,
				LineId,
				OrderId,
				PickingListId,
				Status,
				CreatedDate,
				CreatedBy,
				Company
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?
			)",
			[
				$barcode_decode,
				$pid,
				$trans_id . 1,
				$barcode_decode,
				$inventTransId,
				$loadingLine[0]['ItemId'],
				$inventTable[0]['Batch'],
				1,
				$loadingLine[0]['OrderUnit'],
				$loadingLine[0]['LineID'],
				$loadingLine[0]['OrderId'],
				$pid,
				7, // Picked
				$date,
				$_SESSION['user_login'],
				$_SESSION['user_company']
			]
		);

		if (!$loadingTrans) {
			sqlsrv_rollback($conn);
			return 'Insert loading trans error';
		}

		// invent trans move out
		$trans_id = (new Utils)->genTransId($barcode_decode);

		$inventTransMoveOut = (new Sqlsrv)->insert(
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
				$trans_id . 1,
				$barcode_decode,
				$inventTable[0]['ItemID'],
				$inventTable[0]["Batch"], // batch
				$inventTable[0]["DisposalID"], // disposal id
				null, // defect
				$inventTable[0]["WarehouseID"], // wh
				$inventTable[0]["LocationID"], // location
				-1, // qty
				1, // unit
				2, // docs type
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$inventTransMoveOut) {
			sqlsrv_rollback($conn);
			return "transaction move out error.";
		}

		// Update invent table
		$updateInventTable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			Status = ?
			WHERE Barcode = ?",
			[
				$get_location[0]["DisposalID"], // Disposal Curing
				$get_location[0]["WarehouseID"], // WH X-ray
				$get_location[0]["ReceiveLocation"], // LC X-ray
				$_SESSION["user_login"],
				$date,
				2, // Picked
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error.";
		}

		$trans_id = (new Utils)->genTransId($barcode_decode);
		// Transaction move in
		$inventTransMoveIn = (new Sqlsrv)->insert(
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
				Shift,
				RefDocId
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$inventTable[0]['ItemID'],
				$inventTable[0]["Batch"], // batch
				14, // disposal id picked
				null, // defect
				$inventTable[0]["WarehouseID"], // wh
				$get_location[0]["ReceiveLocation"], // location
				1, // qty
				1, // unit
				1, // docs type receive
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				$date,
				$_SESSION["Shift"],
				$loadingTable[0]['DocNo']
			]
		);

		if (!$inventTransMoveIn) {
			sqlsrv_rollback($conn);
			return "transaction move in error.";
		}

		// move out onhand -1
		// $move_out_onhand = sqlsrv_query(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$inventTable[0]["ItemID"],
		// 		$inventTable[0]["WarehouseID"],
		// 		$inventTable[0]["LocationID"],
		// 		$inventTable[0]["Batch"],
		// 		$inventTable[0]["Company"]
		// 	]
		// );

		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		// Onhand Move in
		// $move_in_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY += 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?
		// 	IF @@ROWCOUNT = 0
		// 	INSERT INTO Onhand
		// 	VALUES (?, ?, ?, ?, ?, ?)",
		// 	[
		// 		$inventTable[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["ReceiveLocation"],
		// 		$inventTable[0]["Batch"],
		// 		$_SESSION["user_company"],
		// 		$inventTable[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["ReceiveLocation"],
		// 		$inventTable[0]["Batch"],
		// 		1, // qty
		// 		$_SESSION["user_company"]
		// 	]
		// );

		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		$updateLoadingQty = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingLine
			SET Remainder -= 1 ,
			LoadingQTY += 1,
			Status = 2, -- in-progress
			UpdateBy = ?,
			UpdateDate = ?
			WHERE inventTransId = ?
			AND Remainder <> 0",
			[$_SESSION['user_login'], $date, $inventTransId]
		);

		$currentRemainder = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT Remainder FROM LoadingLine
			WHERE InventTransId = ?
			AND Remainder = 0",
			[$inventTransId]
		);

		if ($currentRemainder === true) {
			$updateLoadingLineStatus = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingLine
				SET Status = 5 -- Complete
				WHERE inventTransId = ?
				AND Remainder = 0",
				[$inventTransId]
			);

			if (!$updateLoadingLineStatus) {
				sqlsrv_rollback($conn);
				return 'Update Loding Line Status(Complete) Error.';
			}
		}

		$isAllStatusComplete = sqlsrv_num_rows(sqlsrv_query(
			$conn,
			"SELECT LL.Status FROM LoadingLine LL
			WHERE LL.PickingListId = ?
			AND LL.Status <> 5",
			[$pid],
			['Scrollable' => 'static']
		));

		if ($isAllStatusComplete === 0) {

			$updateStatusLoadingTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingTable
				SET Status = 5 -- Complete
				WHERE PickingListId = ?",
				[$pid]
			);
		} else {

			$updateStatusLoadingTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingTable
				SET Status = 2 -- In-progress
				WHERE PickingListId = ?",
				[$pid]
			);
		}

		// if ( !isset($inventTable[0]['LPNID']) ) {
		// 	sqlsrv_rollback($conn);
		// 	return 'LPN not found.';
		// }

		// $updateRemainderLPNMaster = sqlsrv_query(
		// 	$conn,
		// 	"UPDATE LPNMaster
		// 	SET QtyInUse -= 1,
		// 	Remain += 1,
		// 	[Status] = 5, -- close
		// 	UpdateDate = ?,
		// 	UpdateBy = ?
		// 	WHERE LPNID = ?",
		// 	[
		// 		date('Y-md- H:i:s'),
		// 		$_SESSION['user_login'],
		// 		$inventTable[0]['LPNID']
		// 	]
		// );

		// if (!$updateRemainderLPNMaster) {
		// 	sqlsrv_rollback($conn);
		// 	return 'Update Remain LPN Master';
		// }

		// $updateLPNLine = sqlsrv_query(
		// 	$conn,
		// 	"DELETE FROM LPNLine
		// 	WHERE LPNID = ?
		// 	AND Barcode = ? ",
		// 	[
		// 		$inventTable[0]['LPNID'],
		// 		$barcode
		// 	]
		// );

		// if (!$updateLPNLine) {
		// 	sqlsrv_rollback($conn);
		// 	return 'Update Remain LPN Line';
		// }

		if (!$updateStatusLoadingTable) {
			sqlsrv_rollback($conn);
			return 'Update Loading Table Status Error';
		}

		if ($updateLoadingQty) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 'error';
		}
	}

	public function saveUnpick($pid, $inventTransId, $barcode, $LineID)
	{
		$barcode_decode = (new Security)->_decode($barcode);
		$conn = (new Database)->connect();
		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'error connection';
		}

		// get barcode info from invent table barcode
		$inventTable = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		// getLoadingTable
		$loadingTable = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 * FROM LoadingTable
			WHERE PickingListId = ?",
			[$pid]
		);

		// get user location
		$get_location = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.UnpickReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		// get unpick disposal
		$getUnpickDisposal = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 DisposalID FROM Location
			WHERE ID = ?",
			[$get_location[0]['UnpickReceiveLocation']]
		);


		$loadingLine = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT * FROM LoadingLine WHERE InventTransId = ?",
			[$inventTransId]
		);

		$currentLoadingQTY = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT LoadingQTY FROM LoadingLine
			WHERE InventTransId = ?
			AND LoadingQTY = 0
			AND PickingListId = ?",
			[$inventTransId, $pid]
		);

		if ($currentLoadingQTY === true) {
			sqlsrv_rollback($conn);
			return 'Loading QTY = 0';
		}

		$updateLoadingTable = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTable
			SET UpdateBy = ?,
			UpdateDate = ?
			WHERE PickingListId = ?",
			[
				$_SESSION['user_login'],
				$date,
				$pid
			]
		);

		if (!$updateLoadingTable) {
			sqlsrv_rollback($conn);
			return 'Update LoadingTable error';
		}

		// $trans_id = (new Utils)->genTransId($barcode_decode);

		$isTrueBarcode = Sqlsrv::queryArray(
			$conn,
			"SELECT Barcode FROM LoadingTrans
			WHERE PickingListId = ?
			AND InventTransId = ?",
			[$pid, $inventTransId]
		);

		$barcodeStack = [];

		foreach ($isTrueBarcode as $value) {
			$barcodeStack[] = $value['Barcode'];
		}

		if (!in_array($barcode_decode, $barcodeStack)) {
			sqlsrv_rollback($conn);
			return 'Barcode incorrect!';
		}

		// loading trans
		$loadingTrans = (new Sqlsrv)->update( // 6 = cancel
			$conn,
			"UPDATE LoadingTrans
			SET Status = 6
			WHERE Barcode = ?
			AND PickingListId = ?
			AND InventTransId = ?",
			[$barcode_decode, $pid, $inventTransId]
		);

		if (!$loadingTrans) {
			sqlsrv_rollback($conn);
			return 'Update loading trans error';
		}

		// invent trans move out
		$trans_id = (new Utils)->genTransId($barcode_decode);

		$inventTransMoveOut = (new Sqlsrv)->insert(
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
				$trans_id . 1,
				$barcode_decode,
				$inventTable[0]['ItemID'],
				$inventTable[0]["Batch"], // batch
				$getUnpickDisposal[0]["DisposalID"], // disposal id
				null, // defect
				$inventTable[0]["WarehouseID"], // wh
				$inventTable[0]["LocationID"], // location
				-1, // qty
				1, // unit
				2, // docs type
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$inventTransMoveOut) {
			sqlsrv_rollback($conn);
			return "transaction move out error.";
		}

		// Update invent table
		$updateInventTable = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?,
			WarehouseID = ?,
			LocationID = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			[Status] = ?,
			PalletNo = null
			WHERE Barcode = ?",
			[
				$getUnpickDisposal[0]["DisposalID"], // Disposal Curing
				$get_location[0]["WarehouseID"], // WH X-ray
				$get_location[0]["UnpickReceiveLocation"], // LC X-ray
				$_SESSION["user_login"],
				$date,
				1, // Receive
				$barcode_decode
			]
		);

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			return "update invent table error.";
		}

		$trans_id = (new Utils)->genTransId($barcode_decode);
		// Transaction move in
		$inventTransMoveIn = (new Sqlsrv)->insert(
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
				$trans_id . 2,
				$barcode_decode,
				$inventTable[0]['ItemID'],
				$inventTable[0]["Batch"], // batch
				$getUnpickDisposal[0]['DisposalID'], // disposal id picked
				null, // defect
				$inventTable[0]["WarehouseID"], // wh
				$get_location[0]["UnpickReceiveLocation"], // location
				1, // qty
				1, // unit
				1, // docs type receive
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$inventTransMoveIn) {
			sqlsrv_rollback($conn);
			return "transaction move out error.";
		}

		// move out onhand -1
		// $move_out_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand
		// 	SET QTY -= 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?",
		// 	[
		// 		$inventTable[0]["ItemID"],
		// 		$inventTable[0]["WarehouseID"],
		// 		$inventTable[0]["LocationID"],
		// 		$inventTable[0]["Batch"],
		// 		$inventTable[0]["Company"]
		// 	]
		// );

		// if (!$move_out_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move out onhand error.";
		// }

		// Onhand Move in
		// $move_in_onhand = Sqlsrv::update(
		// 	$conn,
		// 	"UPDATE Onhand SET QTY += 1
		// 	WHERE CodeID = ?
		// 	AND WarehouseID = ?
		// 	AND LocationID = ?
		// 	AND Batch = ?
		// 	AND Company =?
		// 	IF @@ROWCOUNT = 0
		// 	INSERT INTO Onhand
		// 	VALUES (?, ?, ?, ?, ?, ?)",
		// 	[
		// 		$inventTable[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["UnpickReceiveLocation"],
		// 		$inventTable[0]["Batch"],
		// 		$_SESSION["user_company"],
		// 		$inventTable[0]["ItemID"],
		// 		$get_location[0]["WarehouseID"],
		// 		$get_location[0]["UnpickReceiveLocation"],
		// 		$inventTable[0]["Batch"],
		// 		1, // qty
		// 		$_SESSION["user_company"]
		// 	]
		// );

		// if (!$move_in_onhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "move in onhand error.";
		// }

		$updateLoadingQty = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingLine
			SET Remainder += 1 ,
			LoadingQTY -= 1,
			Status = 2, -- in-progress
			UpdateBy = ?,
			UpdateDate = ?
			WHERE inventTransId = ?
			AND LoadingQTY <> 0",
			[$_SESSION['user_login'], $date, $inventTransId]
		);

		$currentLoadingQTY = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT LoadingQTY FROM LoadingLine
			WHERE InventTransId = ?
			AND LoadingQTY = 0",
			[$inventTransId]
		);

		if ($currentLoadingQTY === true) {
			$updateLoadingLineStatus = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingLine
				SET Status = 1 -- Open
				WHERE inventTransId = ?
				AND loadingQTY = 0",
				[$inventTransId]
			);

			if (!$updateLoadingLineStatus) {
				sqlsrv_rollback($conn);
				return 'Update Loding Line Status(Complete) Error.';
			}
		}

		$isAllStatusComplete = sqlsrv_num_rows(sqlsrv_query(
			$conn,
			"SELECT LL.Status FROM LoadingLine LL
			WHERE LL.PickingListId = ?
			AND LL.Status <> 1",
			[$pid],
			['Scrollable' => 'static']
		));

		if ($isAllStatusComplete === 0) {

			$updateStatusLoadingTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingTable
				SET Status = 1 -- Open
				WHERE PickingListId = ?",
				[$pid]
			);
		} else {

			$updateStatusLoadingTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE LoadingTable
				SET Status = 2 -- In-progress
				WHERE PickingListId = ?",
				[$pid]
			);
		}

		if (!$updateStatusLoadingTable) {
			sqlsrv_rollback($conn);
			return 'Update Loading Table Status Error';
		}

		if ($updateLoadingQty) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 'error';
		}
	}

	public function isCustomRemainder($pid)
	{
		$conn = (new Database)->connect();
		$q = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT  LL.AuthorizeBy FROM LoadingLine LL
			WHERE LL.AuthorizeBy IS NOT NULL
			AND PickingListId = ?",
			[$pid]
		);

		if ($q === false) {
			return 200;
		} else {
			return 400;
		}
	}

	public function confirm($pickingListId, $isCustomRemainder)
	{
		$conn = (new Database)->connect();
		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'error begin transaction';
		}

		if ((bool) $isCustomRemainder === true) {
			// $status = 3; // Confirm -- old
			$status = 4; // confirmed
		} else {
			// $status = 4; // Confirmed -- old
			$status = 3; // confirm
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$updateLoadingTable = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTable
			SET Status = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			ConfirmDate = ?
			WHERE PickingListId = ?",
			[$status, $_SESSION['user_login'], $date, $date, $pickingListId]
		);

		if (!$updateLoadingTable) {
			sqlsrv_rollback($conn);
			return 'update loading table error';
		}

		$isItemExists = (new Sqlsrv)->hasRows(
			$conn,
			"SELECT * FROM LoadingTrans LT
			WHERE LT.Status = 7 -- picked
			AND LT.PickingListId = ?",
			[$pickingListId]
		);

		if ($isItemExists === false) {
			sqlsrv_rollback($conn);
			return 'ไม่พบ item ในระบบ';
		}

		// Get all Barcode
		$getBarcodes = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT * FROM LoadingTrans LT
			WHERE LT.Status = 7 -- picked
			AND LT.PickingListId = ?",
			[$pickingListId]
		);

		$loopState = [];

		foreach ($getBarcodes as $value) {

			$updateInventTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE InventTable
				SET LoadingDate = ?,
				PickingListID = ?,
				OrderID = ?,
				DisposalID = ?,
				WarehouseID = ?,
				LocationID = ?,
				Status = 3, -- Confirm
				UpdateBy = ?,
				UpdateDate = ?
				WHERE Barcode = ?",
				[
					$date,
					$pickingListId,
					$value['OrderId'],
					15, // sold
					$get_location[0]['WarehouseID'],
					$get_location[0]['ReceiveLocation'],
					$_SESSION['user_login'],
					$date,
					$value['Barcode']
				]
			);

			if (!$updateInventTable) {
				$loopState[] = 400;
			}

			// invent transe move out
			$trans_id = (new Utils)->genTransId($value['Barcode']);

			$inventTransMoveOut = (new Sqlsrv)->insert(
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
					$trans_id . 1,
					$value['Barcode'],
					$value['ItemId'],
					$value['BatchNo'], // batch
					15, // sold
					null, // defect
					$get_location[0]["WarehouseID"], // wh
					$get_location[0]["ReceiveLocation"], // location
					-1, // qty
					1, // unit
					2, // docs type
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$inventTransMoveOut) {
				$loopState[] = 400;
			}

			// move out onhand -1
			// $move_out_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand
			// 	SET QTY -= 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch = ?
			// 	AND Company =?",
			// 	[
			// 		$value['ItemId'],
			// 		$get_location[0]["WarehouseID"],
			// 		$get_location[0]["ReceiveLocation"],
			// 		$value['BatchNo'],
			// 		$value['Company']
			// 	]
			// );

			// if (!$move_out_onhand) {
			// 	$loopState[] = 400;
			// }
		}

		$updateLoadingTrans = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTrans
			SET Status = ?
			WHERE PickingListId = ?
			AND Status = 7", // Picked
			[4, $pickingListId] // 4 = confirmed
		);

		if (!$updateLoadingTrans) {
			sqlsrv_rollback($conn);
			return 'update loading trans error';
		}

		if (!in_array(400, $loopState)) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return var_dump($loopState);
		}
	}

	public function forceConfirm($pickingListId, $authorize_code)
	{
		$conn = Database::connect();
		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'error begin transaction';
		}

		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		$updateLoadingTable = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTable
			SET Status = 3, -- confirm
			UpdateBy = ?,
			UpdateDate = ?,
			ConfirmDate = ?
			WHERE PickingListId = ?",
			[$_SESSION['user_login'], $date, $date, $pickingListId]
		);

		if (!$updateLoadingTable) {
			sqlsrv_rollback($conn);
			return 'update loading table error';
		}

		$updateLoadingLine = Sqlsrv::update(
			$conn,
			"UPDATE LoadingLine
			SET Status = 5,
			AuthorizeBy = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE PickingListId = ?
			AND Remainder > 0",
			[
				$authorize_code,
				$_SESSION['user_login'],
				$date,
				$pickingListId
			]
		);

		if (!$updateLoadingLine) {
			sqlsrv_rollback($conn);
			return 'update loading line error';
		}

		// Get all Barcode
		$getBarcodes = (new Sqlsrv)->queryArray( // 7 = picked
			$conn,
			"SELECT * FROM LoadingTrans LT
			WHERE LT.Status = 7
			AND LT.PickingListId = ?",
			[$pickingListId]
		);

		$loopState = [];

		foreach ($getBarcodes as $value) {

			$updateInventTable = (new Sqlsrv)->update(
				$conn,
				"UPDATE InventTable
				SET LoadingDate = ?,
				PickingListID = ?,
				OrderID = ?,
				DisposalID = ?,
				WarehouseID = ?,
				LocationID = ?,
				[Status] = 3, -- Confirm
				UpdateBy = ?,
				UpdateDate = ?
				WHERE Barcode = ?",
				[
					$date,
					$pickingListId,
					$value['OrderId'],
					15, // sold
					$get_location[0]['WarehouseID'],
					$get_location[0]['ReceiveLocation'],
					$_SESSION['user_login'],
					$date,
					$value['Barcode']
				]
			);

			if (!$updateInventTable) {
				$loopState[] = 400;
			}

			// invent transe move out
			$trans_id = (new Utils)->genTransId($value['Barcode']);

			$inventTransMoveOut = (new Sqlsrv)->insert(
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
					$trans_id . 1,
					$value['Barcode'],
					$value['ItemId'],
					$value['BatchNo'], // batch
					15, // sold
					null, // defect
					$get_location[0]["WarehouseID"], // wh
					$get_location[0]["ReceiveLocation"], // location
					-1, // qty
					1, // unit
					2, // docs type
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$inventTransMoveOut) {
				$loopState[] = 400;
			}

			// move out onhand -1
			// $move_out_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand
			// 	SET QTY -= 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch = ?
			// 	AND Company =?",
			// 	[
			// 		$value['ItemId'],
			// 		$get_location[0]["WarehouseID"],
			// 		$get_location[0]["ReceiveLocation"],
			// 		$value['BatchNo'],
			// 		$value['Company']
			// 	]
			// );

			// if (!$move_out_onhand) {
			// 	$loopState[] = 400;
			// }
		}

		$updateLoadingTrans = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTrans
			SET Status = 4 -- confirm
			WHERE PickingListId = ?
			AND Status = 7", // Picked
			[$pickingListId]
		);

		if (!$updateLoadingTrans) {
			sqlsrv_rollback($conn);
			return 'update loading trans error';
		}

		if (!in_array(400, $loopState)) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function cancel($pickingListId)
	{
		$conn = (new Database)->connect();

		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction begin error';
		}

		// get user location
		$get_location = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID,
			L.UnpickReceiveLocation
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		// get cancel disposal
		$getCancelDisposal = (new Sqlsrv)->queryArray(
			$conn,
			"SELECT TOP 1 DisposalID FROM Location
			WHERE ID = ?",
			[$get_location[0]['UnpickReceiveLocation']]
		);

		// update loading table
		$updateLoadingTable = (new Sqlsrv)->update( // 6 = cancel
			$conn,
			"UPDATE LoadingTable
			SET Status = 6,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE PickingListId = ?",
			[
				$_SESSION['user_login'],
				$date,
				$pickingListId
			]
		);

		if (!$updateLoadingTable) {
			sqlsrv_rollback($conn);
			return 'update loading table error';
		}

		// update loading line
		$updateLoadingLine = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingLine
			SET Status = 6,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE PickingListId = ?",
			[
				$_SESSION['user_login'],
				$date,
				$pickingListId
			]
		);

		if (!$updateLoadingLine) {
			sqlsrv_rollback($conn);
			return 'update loading line error';
		}

		// Get all Barcode
		$getBarcodes = (new Sqlsrv)->queryArray( // 6 = cancel
			$conn,
			"SELECT * FROM LoadingTrans LT
			WHERE LT.Status <> 6
			AND LT.PickingListId = ?",
			[$pickingListId]
		);

		$loopState = [];

		foreach ($getBarcodes as $value) {

			$updateInventTable = (new Sqlsrv)->update( // 1 = receive
				$conn,
				"UPDATE InventTable
				SET DisposalID = ?,
				WarehouseID = ?,
				LocationID = ?,
				Status = 1,
				UpdateBy = ?,
				UpdateDate = ?
				WHERE Barcode = ?",
				[
					$getCancelDisposal[0]['DisposalID'], // Loading
					$get_location[0]['WarehouseID'],
					$get_location[0]['UnpickReceiveLocation'],
					$_SESSION['user_login'],
					$date,
					$value['Barcode']
				]
			);

			if (!$updateInventTable) {
				$loopState[] = 400;
			}

			// invent transe move out
			$trans_id = (new Utils)->genTransId($value['Barcode']);

			$inventTransMoveOut = (new Sqlsrv)->insert(
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
					$trans_id . 1,
					$value['Barcode'],
					$value['ItemId'],
					$value['BatchNo'], // batch
					$get_location[0]["DisposalID"], // sold
					null, // defect
					$get_location[0]["WarehouseID"], // wh
					$get_location[0]["ReceiveLocation"], // location
					-1, // qty
					1, // unit
					2, // docs type
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$inventTransMoveOut) {
				$loopState[] = 400;
			}

			// invent transe move in
			$trans_id = (new Utils)->genTransId($value['Barcode']);

			$inventTransMoveIn = (new Sqlsrv)->insert(
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
					$trans_id . 2,
					$value['Barcode'],
					$value['ItemId'],
					$value['BatchNo'], // batch
					$getCancelDisposal[0]["DisposalID"], // sold
					null, // defect
					$get_location[0]["WarehouseID"], // wh
					$get_location[0]["UnpickReceiveLocation"], // location
					1, // qty
					1, // unit
					1, // docs type
					$_SESSION['user_company'],
					$_SESSION['user_login'],
					$date,
					$_SESSION["Shift"]
				]
			);

			if (!$inventTransMoveIn) {
				$loopState[] = 400;
			}

			// move out onhand -1
			// $move_out_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand
			// 	SET QTY -= 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch = ?
			// 	AND Company =?",
			// 	[
			// 		$value['ItemId'],
			// 		$get_location[0]["WarehouseID"],
			// 		$get_location[0]["ReceiveLocation"],
			// 		$value['BatchNo'],
			// 		$value['Company']
			// 	]
			// );

			// if (!$move_out_onhand) {
			// 	$loopState[] = 400;
			// }

			// Move in onhand
			// $move_in_onhand = Sqlsrv::update(
			// 	$conn,
			// 	"UPDATE Onhand SET QTY += 1
			// 	WHERE CodeID = ?
			// 	AND WarehouseID = ?
			// 	AND LocationID = ?
			// 	AND Batch = ?
			// 	AND Company =?
			// 	IF @@ROWCOUNT = 0
			// 	INSERT INTO Onhand
			// 	VALUES (?, ?, ?, ?, ?, ?)",
			// 	[
			// 		$value["ItemId"],
			// 		$get_location[0]["WarehouseID"],
			// 		$get_location[0]["UnpickReceiveLocation"],
			// 		$value["BatchNo"],
			// 		$_SESSION["user_company"],
			// 		$value["ItemId"],
			// 		$get_location[0]["WarehouseID"],
			// 		$get_location[0]["UnpickReceiveLocation"],
			// 		$value["BatchNo"],
			// 		1, // qty
			// 		$_SESSION["user_company"]
			// 	]
			// );

			// if (!$move_in_onhand) {
			// 	$loopState[] = 400;
			// }

		}

		// update loading trans
		$updateLoadingTrans = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTrans
			SET Status = 6
			WHERE PickingListId = ?
			AND Status <> 6",
			[
				$pickingListId
			]
		);

		if (!$updateLoadingTrans) {
			sqlsrv_rollback($conn);
			return 'update loading trans error';
		}

		if (!in_array(400, $loopState)) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function isPicked($barcode, $pid)
	{
		$conn = Database::connect();
		$barcode_decode = Security::_decode($barcode);
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM LoadingTrans
			WHERE Status = 7
			AND Barcode = ?
			AND PickingListId = ?",
			[$barcode_decode, $pid]
		);
	}

	public function addRemainder($new_remainder, $inventTransId, $authorize_code)
	{
		$conn = (new Database)->connect();

		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction begin error';
		}

		$addRemainder = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingLine
			SET Remainder += ?,
			UpdateDate = ?,
			UpdateBy = ?
			WHERE InventTransId = ?",
			[
				$new_remainder,
				$date,
				$_SESSION['user_login'],
				$inventTransId
			]
		);

		if (!$addRemainder) {
			sqlsrv_rollback($conn);
			return 'Add Remainder Failed!';
		}

		$loadingLine = Sqlsrv::queryArray(
			$conn,
			"SELECT PickingListId FROM LoadingLine
			WHERE InventTransId = ?",
			[$inventTransId]
		);

		$updateLoadingLine = Sqlsrv::update( // 2 = In-progress
			$conn,
			"UPDATE LoadingLine
			SET Status = 2,
			AuthorizeBy = ?
			WHERE InventTransId = ?
			AND PickingListId = ?",
			[$authorize_code, $inventTransId, $loadingLine[0]['PickingListId']]
		);

		if (!$updateLoadingLine) {
			sqlsrv_rollback($conn);
			return 'update loading line failed!';
			// return [$authorize_code, $inventTransId];
		}

		$updateStatusLoadingTable = (new Sqlsrv)->update(
			$conn,
			"UPDATE LoadingTable
			SET Status = 2 -- In-progress
			WHERE PickingListId = ?",
			[$loadingLine[0]['PickingListId']]
		);

		if (!$updateStatusLoadingTable) {
			sqlsrv_rollback($conn);
			return 'update loading table failed!';
		}

		sqlsrv_commit($conn);
		return 200;
	}

	public function loadingTrans($pid, $itemid, $inventtranid)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			'SELECT LT.* ,
			WM.Description as warehouse_desc,
			L.Description as location_desc ,
			LS.Description as StatusDesc,
			UM.Name as Fullname,
			IT.TemplateSerialNo as SerialName,
			LT.InventTransId
			FROM LoadingTrans LT
			LEFT JOIN InventTable IT ON LT.Barcode = IT.Barcode
			LEFT JOIN WarehouseMaster WM ON WM.ID = IT.WarehouseID
			LEFT JOIN Location L ON L.ID = IT.LocationID
			LEFT JOIN LoadingStatus LS ON LS.ID = LT.Status
			LEFT JOIN UserMaster UM ON LT.CreatedBy = UM.ID
			WHERE LT.PickingListId = ?
			AND LT.ItemId = ?
			AND LT.InventTransId = ? ',
			[$pid, $itemid, $inventtranid]
		);
	}

	public function getPickingListByOrderId($order_id)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			'SELECT IJ.ORDERID, IJ.PICKINGLISTID
			FROM InventPickingListJour IJ
			WHERE IJ.ORDERID = ?',
			[$order_id]
		);
	}

	public function savePickingListRef($pickinglist_id_current, $pickinglist_id_ref)
	{
		$conn = Database::connect();
		$date = date('Y-m-d H:i:s');

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction connect error';
		}

		$query = Sqlsrv::update(
			$conn,
			'UPDATE LoadingTable
			SET RefPickingListId = ? ,
			Status = 4 -- confirmed
			WHERE PickingListId = ?',
			[$pickinglist_id_ref, $pickinglist_id_current]
		);

		if ($query) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function isDenyLoading($barcode)
	{
		$conn = Database::connect();

		$hasRow = sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT I.Barcode
			FROM InventTable I
			JOIN DenyLoading D ON D.Batch = I.Batch AND D.ItemId = I.ItemID
			WHERE I.Barcode = ?",
			[
				$barcode
			]
		));
		return $hasRow;
	}
}
