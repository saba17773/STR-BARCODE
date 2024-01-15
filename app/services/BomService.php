<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Components\Utils;
use App\Services\InventService;
use App\Services\BarcodeService;
use App\Services\OnhandService;
use App\Services\ItemSetService;
use Wattanar\Sqlsrv;

class BomService
{
  public function saveBom($item_id, $barcode) 
  {
    $conn = DB::connect();

    $item_id_by_item_set = (new ItemSetService)->getItemIdFromItemSet($item_id);
    $barcode_detail = (new BarcodeService)->getBarcodeInfoV2($barcode);

    if (count($barcode_detail) === 0) {
      return 'can\'t get barcode detail.';
    }

    if (count($item_id_by_item_set) === 0) {
      return 'can\'t get item id by item set.';
    }

    if(sqlsrv_begin_transaction($conn) === false) {
        return "Transaction failed.";
    }

    $update_inventtable = sqlsrv_query(
        $conn,
        "UPDATE InventTable
        SET Unit = ?,
        ItemID = ?,
        RefItemId = ?,
        DisposalID = ?,
        WarehouseID = ?,
        LocationID = ?,
        UpdateBy = ?,
        UpdateDate = ?
        WHERE Barcode = ?",
        [
           2,
           $item_id,
           $barcode_detail[0]["ItemID"],
           18, // finish goof
           $barcode_detail[0]["WarehouseID"],
           $barcode_detail[0]["LocationID"],
           $_SESSION["user_login"],
           Date('Y-m-d H:i:s'),
           $barcode
        ]
    );

    if (!$update_inventtable) {
      sqlsrv_rollback($conn);
      return "Update invent table failed.";
    }

    $create_inventtrans_moveout = sqlsrv_query(
            $conn,
            "INSERT INTO InventTrans(
                TransID,
                Barcode,
                CodeID,
                Batch,
                DisposalID,
                WarehouseID,
                LocationID,
                QTY,
                UnitID,
                DocumentTypeID,
                Company,
                CreateBy,
                CreateDate,
                Shift
            ) VALUES(
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?
            )",
            [
                (new Utils)->genTransId($barcode) . 1,
                $barcode,
                $barcode_detail[0]["ItemID"],
                $barcode_detail[0]["Batch"],
                6,  // fg
                3, // warehouse
                7, // location
                -1, // qty
                1, // unit
                2, // doc type
                $_SESSION["user_company"],
                $_SESSION["user_login"],
                date('Y-m-d H:i:s'),
                $_SESSION["Shift"]
            ]
    );

    if (!$create_inventtrans_moveout) {
        sqlsrv_rollback($conn);
        return "Update invent trans move out Failed.";
    }

    $create_inventtrans_movein = sqlsrv_query(
        $conn,
        "INSERT INTO InventTrans(
            TransID,
            Barcode,
            CodeID,
            Batch,
            DisposalID,
            WarehouseID,
            LocationID,
            QTY,
            UnitID,
            DocumentTypeID,
            Company,
            CreateBy,
            CreateDate,
            Shift
        ) VALUES(
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )",
        [
            (new Utils)->genTransId($barcode) . 2,
            $barcode,
            $item_id,
            $barcode_detail[0]["Batch"],
            18,  // BOM
            3, // warehouse
            7, // location
            1, // qty
            2, // unit
            1, // doc type
            $_SESSION["user_company"],
            $_SESSION["user_login"],
            date('Y-m-d H:i:s'),
            $_SESSION["Shift"]
        ]
    );

    if (!$create_inventtrans_movein) {
        sqlsrv_rollback($conn);
        return "Update invent trans move out Failed.";
    }

    // $moveout_onhand = sqlsrv_query(
    //         $conn,
    //         "UPDATE Onhand 
    //         SET QTY += ?
    //         WHERE CodeID = ?
    //         AND WarehouseID = ?
    //         AND LocationID = ?
    //         AND Batch = ?
    //         AND Company = ?",
    //         [
    //             -1,
    //             $barcode_detail[0]["ItemID"],
    //             $barcode_detail[0]["WarehouseID"],
    //             $barcode_detail[0]["LocationID"],
    //             $barcode_detail[0]["Batch"],
    //             $barcode_detail[0]["Company"]
    //         ]
    // );

    // if (!$moveout_onhand) {
    //     sqlsrv_rollback($conn);
    //     return "Update onhand failed.";
    // }

    // $moveInOnhand = sqlsrv_query(
    //   $conn,
    //   "UPDATE Onhand SET QTY += 1
    //   WHERE CodeID = ?
    //   AND WarehouseID = ?
    //   AND LocationID = ?
    //   AND Batch = ?
    //   AND Company =?
    //   IF @@ROWCOUNT = 0
    //   INSERT INTO Onhand 
    //   VALUES (?, ?, ?, ?, ?, ?)",
    //   [
    //     $item_id,
    //     $barcode_detail[0]["WarehouseID"],
    //     $barcode_detail[0]["LocationID"],
    //     $barcode_detail[0]["Batch"],
    //     $barcode_detail[0]["Company"],
    //     $item_id,
    //     $barcode_detail[0]["WarehouseID"],
    //     $barcode_detail[0]["LocationID"],
    //     $barcode_detail[0]["Batch"],
    //     1, // qty
    //     $barcode_detail[0]["Company"]
    //   ]
    // );

    // if(!$moveInOnhand) {
    //   sqlsrv_rollback( $conn );
    //   return 'move in onhand error.';
    // }

    $create_bom = sqlsrv_query(
        $conn,
        "INSERT INTO Bom(
            barcode,
            item_id,
            item_set_id,
            create_by,
            create_date
        ) VALUES (?, ?, ?, ?, ?)",
        [
            $barcode,
            $barcode_detail[0]["ItemID"],
            $item_id,
            $_SESSION["user_login"],
            date("Y-m-d H:i:s")
        ]
    );
    
    if (!$create_bom) {
        sqlsrv_rollback($conn);
        return "Bom ไม่สำเร็จ.";
    }

    sqlsrv_commit($conn);
    return true;
  }

	public function saveUnbom($authorize_by, $barcode)
	{
		$conn = DB::connect();

		$barcode_info = (new BarcodeService)->getBarcodeInfoV2($barcode);

		if (sqlsrv_begin_transaction($conn) === false) {
            return "transaction failed!";
        }

        $updateInventTable = sqlsrv_query(
            $conn,
            "UPDATE InventTable
            SET Unit = ?,
            ItemID = ?,
            RefItemId = ?,
            DisposalID = ?,
            WarehouseID = ?,
            LocationID = ?,
            UpdateBy = ?,
            UpdateDate = ?
            WHERE Barcode = ?",
            [
                1,
                $barcode_info[0]["RefItemId"],
                null,
                6,
                $barcode_info[0]["WarehouseID"],
                $barcode_info[0]["LocationID"],
                $_SESSION["user_login"],
                Date('Y-m-d H:i:s'),
                $barcode
            ]
        );

		if (!$updateInventTable) {
			sqlsrv_rollback($conn);
			
			return "Update invent table failed.";
		}

		$stmt = sqlsrv_query(
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
          InventJournalID,
          AuthorizeBy,
          ScrapSide,
          RefDocId
      ) VALUES(
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?
      )",
      [
        (new Utils)->genTransId($barcode) . 1,
        $barcode,
        $barcode_info[0]["ItemID"],
        $barcode_info[0]["Batch"],
        6,
        null,
        3,
        7,
        -1, // qty
        2, // SET
        2, // issue
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        Date("Y-m-d H:i:s"),
        $_SESSION['Shift'],
        null,
        $authorize_by,
        null,
        null
      ]
    );

    if (!$stmt) {
			sqlsrv_rollback($conn);
			
			return "create invent trans move out failed.";
		}

		$stmt = sqlsrv_query(
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
          InventJournalID,
          AuthorizeBy,
          ScrapSide,
          RefDocId
      ) VALUES(
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?
      )",
      [
        (new Utils)->genTransId($barcode) . 2,
        $barcode,
        $barcode_info[0]["RefItemId"],
        $barcode_info[0]["Batch"],
        6,
        null,
        3,
        7,
        1, // qty
        1, // PCS
        1, // receive
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        Date("Y-m-d H:i:s"),
        $_SESSION['Shift'],
        null,
        null,
        null,
        null
      ]
    );

    if (!$stmt) {
        sqlsrv_rollback($conn);
        return "create invent trans move in failed.";
    }

    // $moveOutOnhand = sqlsrv_query(
    //   $conn,
    //   "UPDATE Onhand 
    //   SET QTY -= 1
    //   WHERE CodeID = ?
    //   AND WarehouseID = ?
    //   AND LocationID = ?
    //   AND Batch = ?
    //   AND Company = ?",
    //   [
    //     $barcode_info[0]["ItemID"],
    //     $barcode_info[0]["WarehouseID"],
    //     $barcode_info[0]["LocationID"],
    //     $barcode_info[0]["Batch"],
    //     $barcode_info[0]["Company"]
    //   ]
    // );

    // if (!$moveOutOnhand) {
    //   sqlsrv_rollback($conn);
    //   return "Move Out Onhand Failed!";
    // }
        
  //   $moveInOnhand = sqlsrv_query(
  //     $conn,
  //     "UPDATE Onhand 
  //     SET QTY += 1
  //     WHERE CodeID = ?
  //     AND WarehouseID = ?
  //     AND LocationID = ?
  //     AND Batch = ?
  //     AND Company = ?
  //     IF @@ROWCOUNT = 0
  //     INSERT INTO Onhand 
  //     VALUES (?, ?, ?, ?, ?, ?)",
  //     [
  //       $barcode_info[0]["RefItemId"],
  //       $barcode_info[0]["WarehouseID"],
  //       $barcode_info[0]["LocationID"],
  //       $barcode_info[0]['Batch'],
  //       $_SESSION["user_company"],
  //       $barcode_info[0]["RefItemId"],
  //       $barcode_info[0]["WarehouseID"],
  //       $barcode_info[0]["LocationID"],
  //       $barcode_info[0]['Batch'],
  //       1, // qty
  //       $_SESSION["user_company"]
  //     ]
  //   );

		// if (!$moveInOnhand) {
		// 	sqlsrv_rollback($conn);
		// 	return "update onhand move in error.";
		// }

		if (self::delete($barcode) === true) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return "Unbom ไม่สำเร็จ";
		}
	}

	public function isBarcodeBomExist($barcode)
  {
      $conn = DB::connect();
      return Sqlsrv::hasRows(
          $conn,
          "SELECT barcode 
          FROM Bom
          WHERE barcode = ?",
          [
              $barcode
          ]
      );
  }

  public function delete($barcode)
  {
      $conn = DB::connect();
      $stmt = sqlsrv_query(
          $conn,
          "DELETE FROM Bom
          WHERE barcode = ?",
          [
              $barcode
          ]
      );

      if ($stmt) {
          return true;
      } else {
          return false;
      }
  }

  public function reportBom($time)
  {
    $conn = DB::connect();
    $sqltime = '';
    foreach ($time as $v) {
      $sqltime .= ' (ITS.CreateDate BETWEEN ' . $v . ') OR ';
    }
    $sqltime = trim($sqltime, ' OR ');
    return Sqlsrv::queryJson(
      $conn,
      "SELECT
      IT.CuringCode,
      IT.Barcode,
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      SM.Description [Shift]
      FROM InventTable IT 
      left join ItemMaster IM ON IM.ID = IT.ItemID
      left join InventTrans ITS ON ITS.Barcode = IT.Barcode AND ITS.DisposalID = 18
      left join ShiftMaster SM ON SM.ID = ITS.Shift
      WHERE ITS.DisposalID = 18 AND (" . $sqltime . ") 
      GROUP BY IT.CuringCode,
      IT.Barcode,
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      SM.Description"
    );
  } 
}