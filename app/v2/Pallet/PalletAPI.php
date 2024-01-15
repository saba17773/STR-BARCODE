<?php

namespace App\V2\Pallet;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Item\ItemAPI;
use App\V2\Helper\BatchHelper;
use App\V2\Location\LocationAPI;
use App\V2\Inventory\InventoryAPI;

class PalletAPI
{
  public function __construct() {}

  public function getSeqNumberLPN()
  {
  	$conn = (new Connector)->dbConnect();
  	$format = 'LPN' . date('ymd');
  	$date_now = date('Ymd');

  	$date_current_seq = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 SeqDate FROM SeqNumber WHERE SeqName = 'lpn'"
    );

    // return $date_now . ' <br> ' . date('Ymd', strtotime($date_current_seq[0]['SeqDate']));

    if ($date_now !== date('Ymd', strtotime($date_current_seq[0]['SeqDate']))) {

    	sqlsrv_query(
    		$conn,
    		"UPDATE SeqNumber SET SeqDate = ?, SeqValue = 1 WHERE SeqName = 'lpn'",
    		[
    			date('Y-m-d')
    		]
    	);

    	$date_seq_updated = Sqlsrv::queryArray(
	      $conn,
	      "SELECT TOP 1 SeqValue FROM SeqNumber WHERE SeqName = 'lpn'"
	    );

	    if (count($date_seq_updated) !== 0) {
	      return $format . str_pad((int)$date_seq_updated[0]['SeqValue'], 4, "0", STR_PAD_LEFT);
	    } else {
	    	return $format . str_pad(0, 4, "0", STR_PAD_LEFT);
	    }
    }

    sqlsrv_query(
      $conn,
      "UPDATE SeqNumber SET SeqValue += 1 WHERE SeqName = 'lpn'"
    );

    $number = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 SeqValue FROM SeqNumber WHERE SeqName = 'lpn'"
    );

    if (count($number) !== 0) {
      return $format . str_pad((int)$number[0]['SeqValue'], 4, "0", STR_PAD_LEFT);
    }
    
    return $format . str_pad(0, 4, "0", STR_PAD_LEFT);
  }

  public function createManualLPN($item = null, $batch = null)
  {
    if (is_null($item) || is_null($batch)) return "Item or Batch is null";

    // validate item
    if ( (new ItemAPI)->hasItem($item) === false ) return "Item not found";
    
    // validate batch
    if ( (new BatchHelper)->isBatchFormat($batch) === false ) return "Batch format incorrect!";

    $conn = (new Connector)->dbConnect();

    if (sqlsrv_begin_transaction($conn) === false) return "error connect transaction.";

    $createLPN = sqlsrv_query(
      $conn,
      "INSERT INTO LPNMaster(
        LPNID,
        ItemID,
        BatchNo,
        LocationID,
        QtyPerPallet,
        QtyInUse,
        Remain,
        Status,
        CompleteDate,
        CreateDate,
        CreateBy,
        Company,
        UpdateDate,
        UpdateBy
      ) VALUES(
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?
      )",
      [
        self::getSeqNumberLPN(),
        $item,
        $batch,
        null,
        self::getQtyPerPalletByItem($item),
        0,
        self::getQtyPerPalletByItem($item),
        1, // Open
        null,
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $_SESSION['user_company'],
        date('Y-m-d H:i:s'),
        $_SESSION['user_login']
      ]
    );

    if (!$createLPN) {
      sqlsrv_rollback($conn); 
      return 'Create LPN error. : ' . (new Handler)->dbError();
    }
    
    if ($createLPN) {
      sqlsrv_commit($conn);
      return true;
    }
  }

  public function getQtyPerPalletByItem($item)
  {
    $conn = (new Connector)->dbConnect();
    $qtyPerPallet = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 QtyPerPallet FROM ItemMaster
      WHERE ID = ?",
      [$item]
    );

    if (count($qtyPerPallet) === 0) return 0;

    return $qtyPerPallet[0]['QtyPerPallet'];
  }

  public function getAllItemReceiveLocation($location_id)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT IR.ID, L.Description[Location], IR.ItemID 
      FROM ItemReceiveLocation IR
      LEFT JOIN Location L ON L.ID = IR.LocationID
      WHERE IR.LocationID = ?",
      [
        $location_id
      ]
    );
  }

  public function createItemReceiveLocation($id, $location_id, $item_id, $type)
  {
    $conn = (new Connector)->dbConnect();

    if ($type === 'create') {

      $hasRow = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT ItemID FROM ItemReceiveLocation 
        WHERE ItemID = ? AND LocationID = ?",
        [
          $item_id,
          $location_id
        ]
      ));

      if ($hasRow === true) {
        return "Item นี้ถูกสร้างไปแล้ว";
      }

      $create = sqlsrv_query(
        $conn,
        "INSERT INTO ItemReceiveLocation(LocationID, ItemID) 
        VALUES(?, ?)",
        [
          $location_id,
          $item_id
        ]
      );

      if ($create) {
        return true;
      } else {
        return 'Error';
      }

    } else {

        $hasRow = sqlsrv_has_rows(sqlsrv_query(
          $conn,
          "SELECT ItemID FROM ItemReceiveLocation 
          WHERE ItemID = ? AND LocationID = ?",
          [
            $item_id,
            $location_id
          ]
        ));

        if ($hasRow === true) {
          return "Item นี้ถูกสร้างไปแล้ว";
        }

        $update = sqlsrv_query(
          $conn,
          "UPDATE ItemReceiveLocation SET ItemID = ?
          WHERE ID = ?",
          [
            $item_id,
            $id
          ]
        );

        if ($update) {
          return true;
        } else {
          return "Error";
        }

    } 
  }

  public function deleteItemReceiveLocation($id)
  {
    $conn = (new Connector)->dbConnect();
    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ItemReceiveLocation 
      WHERE ID = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return true;
    } else {
      return (new Handler)->DBError();
    }
  }

  public function getAllLPNMaster()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT 
      L.LPNID,
      L.ItemID,
      IM.NameTH[ItemDesc],
      L.BatchNo,
      LO.ID [LocationID],
      LO.Description [Location],
      L.QtyPerPallet,
      L.QtyInUse,
      L.Remain,
      S.Description [Status],
      L.CompleteDate,
      L.CreateDate,
      UU.Name [CreateBy],
      L.Company,
      L.UpdateDate,
      U.Name [UpdateBy]
      FROM LPNMaster L
      LEFT JOIN Location LO ON L.LocationID = LO.ID
      LEFT JOIN Status S ON S.ID = L.Status
      LEFT JOIN ItemMaster IM ON IM.ID = L.ItemID
      LEFT JOIN UserMaster U ON U.ID = L.CreateBy
      LEFT JOIN UserMaster UU ON UU.ID = L.UpdateBy"
    );
  }

  public function getLPNLine($pallet_no)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT L.Barcode, U.Name FROM LPNLine L
      LEFT JOIN UserMaster U ON U.ID = L.CreateBy
      WHERE L.LPNID = ?
      AND L.Company = ?",
      [
        $pallet_no,
        $_SESSION['user_company']
      ]
    );
  }

  public function generateAuto()
  {
    $conn = (new Connector)->dbConnect();

    $finalWipOnhand = Sqlsrv::queryArray(
      $conn,
      "SELECT IT.Batch, IM.ID [ItemID], SUM(IT.QTY)[QTY], IM.QtyPerPallet
      from InventTable IT 
      left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
      left join ItemMaster IM ON IM.ID = CCM.ItemID
      where IT.WarehouseID = 2 
      and IT.LocationID = 4 
      and DisposalID <> 11
      and Status <> 4
      group by IT.Batch, IM.ID, IM.QtyPerPallet"
    );

    $generateList = [];

    foreach ($finalWipOnhand as $w) {
      if (self::isCreated($w['ItemID'], $w['Batch']) === false) {
        $generateList[] = [
          'batch' => $w['Batch'],
          'item' => $w['ItemID'],
          'qty' => $w['QTY'],
          'qty_pp' => $w['QtyPerPallet'],
          'count_pallet' => ceil($w['QTY']/$w['QtyPerPallet'])
        ];
      } else {
        
      }
    }

    if (sqlsrv_begin_transaction($conn) === false) return "error connect transaction.";

    foreach ($generateList as $data) {

      for($x = 1; $x <= (int)$data['count_pallet']; $x++) {
        
        $createLPN = sqlsrv_query(
          $conn,
          "INSERT INTO LPNMaster(
            LPNID,
            ItemID,
            BatchNo,
            LocationID,
            QtyPerPallet,
            QtyInUse,
            Remain,
            Status,
            CompleteDate,
            CreateDate,
            CreateBy,
            Company,
            UpdateDate,
            UpdateBy
          ) VALUES(
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
          )",
          [
            self::getSeqNumberLPN(),
            $data['item'],
            $data['batch'],
            null,
            $data['qty_pp'],
            0,
            $data['qty_pp'],
            1, // Open
            null,
            date('Y-m-d H:i:s'),
            $_SESSION['user_login'],
            $_SESSION['user_company'],
            date('Y-m-d H:i:s'),
            $_SESSION['user_login']
          ]
        );

        if (!$createLPN) {

          sqlsrv_rollback($conn); 
          return 'Create LPN error.';
        }
      }
    }
    
    sqlsrv_commit($conn);
    return true;
  }

  public function isCreated($item, $batch)
  {
    $conn = (new Connector)->dbConnect();
    $query = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ItemID FROM LPNMaster
      WHERE ItemID = ? 
      AND BatchNo = ?
      AND Status = 1",
      [
        $item,
        $batch
      ]
    ));
    return $query;
  }

  public function isLPNExists($LPNNo)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ?
      AND Status NOT IN (3, 5)", // complete & close
      [
        $LPNNo
      ]
    ));
  }

  public function receiveLocation($lpnId, $barcode)
  {
    if ((int)self::isRemainLPNZero($lpnId) === 0) {
      return 'LPN is complete already.';
    }

    $conn = (new Connector)->dbConnect();

    \sqlsrv_begin_transaction($conn);

    $updateLpn = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster
      SET QtyInUse += 1,
      Remain -= 1,
      Status = 2,
      UpdateDate = ?,
      UpdateBy = ?
      WHERE LPNID = ?",
      [
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $lpnId
      ]
    );

    if (!$updateLpn) {
      sqlsrv_rollback($conn);
      return 'Update Lpn Master Error.';
    }

    $insertLPNLine = sqlsrv_query(
      $conn,
      "INSERT INTO LPNLine(
        LPNID,
        Barcode,
        Company,
        CreateDate,
        CreateBy
      ) VALUES(
        ?, ?, ?, ?, ?
      )",
      [
        $lpnId,
        $barcode,
        $_SESSION['user_company'],
        date('Y-m-d H:i:s'),
        $_SESSION['user_login']
      ]
    );

    if (!$insertLPNLine) {
      sqlsrv_rollback($conn);
      return 'update LPN line error.';
      // return ['result' => 'update LPN line error.', 'type' => 0];
    }

    $updateInventTable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET LPNID = ?,
      UpdateDate = ?,
      UpdateBy = ?
      WHERE Barcode = ?",
      [
        $lpnId,
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $barcode
      ]
    );

    if (!$updateInventTable) {
      sqlsrv_rollback($conn);
      return 'update invent table error.';
      // return ['result' => 'update invent table error.', 'type' => 0];
    }

    sqlsrv_commit($conn);

    if ((int)self::isRemainLPNZero($lpnId) === 0) {

      if (self::isComplete($lpnId) === true) {
        return 'LPN is complete already.';
        // return ['result' => 'LPN is complete already.', 'type' => 0];
      }

      $LPNInfo = self::getLpnInfo($lpnId);

      $isNewLPN = self::isNewItemOnLPN($LPNInfo[0]['ItemID'], $LPNInfo[0]['BatchNo']);

      if ( count($isNewLPN) !== 0 ) {

        $op1_location = self::getLocationByExistsLPN($LPNInfo[0]['ItemID'], $LPNInfo[0]['BatchNo']);
      
        if ( count($op1_location) !== 0 ) {

          if ( self::setComplete($op1_location[0]['LocationID'], $lpnId, $barcode) === true ) {
            
            self::updateRemainLocation($op1_location[0]['LocationID']);
            self::setCompleteOnlyLPN($op1_location[0]['LocationID'], $lpnId);
            // return ['result' => true, 'type' => 1];
            return true;
          } else {
            
            return 'Complete error 1';
          }
        }

        $loc = self::getLocationByLocationRemain($LPNInfo[0]['ItemID']);

        if ($loc !== '') {
          if ( self::isBlankLocation($loc) === true ) {
            if ( self::setComplete($loc, $lpnId, $barcode) === true) {
              self::updateRemainLocation($loc);
              self::setCompleteOnlyLPN($loc, $lpnId);
              return true;
              // return ['result' => true, 'type' => 1];
            } else {
              // return ['result' => 'Complete error 2', 'type' => 1];
              return 'Complete error 2';
            }
          } else {
            if ( self::setComplete(7, $lpn, $barcode) === true ) {  // 7 = finish good
              self::updateRemainLocation(7);
              self::setCompleteOnlyLPN(7, $lpnId);
              return true;
              // return ['result' => true, 'type' => 1];
            } else {
              return 'Complete erro 3';
              // return ['result' => 'Complete error 3', 'type' => 1];
            }
          }
        } else {
          if ( self::setComplete(7, $lpnId, $barcode) === true ) {  // 7 = finish good
            self::updateRemainLocation(7);
            self::setCompleteOnlyLPN(7, $lpnId);
            return true;
            //  return ['result' => true, 'type' => 1];
          } else {
            return 'Complete erro 3';
            // return ['result' => 'Complete error 3', 'type' => 1];
          }
        }
      }

      // $loc = self::getLocationByLocationRemain($lpnInfo[0]['ItemID']);

      // if ($loc === '') {

      //   if ( self::setComplete(7, $lpn, $barcode) === true ) {  // 7 = finish good
      //     self::updateRemainLocation(7);
      //     self::setCompleteOnlyLPN(7, $lpnId);
      //     return true;
      //   } else {
      //     return 'Complete erro 3';
      //   }
      // }

      // if ( self::isBlankLocation($loc) === true ) {
      //   if ( self::setComplete($loc, $lpnId, $barcode) === true) {
      //     self::updateRemainLocation($loc);
      //     self::setCompleteOnlyLPN($loc, $lpnId);
      //     return true;
      //   } else {
      //     return 'Complete error 2';
      //   }
      // } else {
      //   if ( self::setComplete(7, $lpn, $barcode) === true ) {  // 7 = finish good
      //     self::updateRemainLocation(7);
      //     self::setCompleteOnlyLPN(7, $lpnId);
      //     return true;
      //   } else {
      //     return 'Complete erro 3';
      //   }
      // }

      
    }

    return true;
    // return ['result' => true, 'type' => 0];

    // $LPNInfo = self::getLpnInfo($lpnId);

    // if (self::isComplete($lpnId) === true) {
    //   return 'LPN is complete already.';
    // }

    // if (self::isNewItemOnLPN($LPNInfo[0]['BatchNo'], $LPNInfo[0]['ItemID']) === true) {

    //   $searchRemain = self::checkRemainByBatchAndItem($LPNInfo[0]['BatchNo'], $LPNInfo[0]['ItemID']);

    //   if (count($searchRemain) !== 0) {
    //     // มี item และ batch อยู่ใน LPN Master และ remain != 0
    //     $completeOption1 = self::setComplete($searchRemain[0]['LocationID'], $lpnId, $barcode);
    //     if ($completeOption1 === true) {
    //       return true;
    //     } else {
    //       return 'complete option 1 error';
    //     }
    //   }
    // }

    // if(count(self::getLocationByLocationRemain($LPNInfo[0]['ItemID'])) !== 0) {
    //   $locationForOption2 = self::getLocationByLocationRemain($LPNInfo[0]['ItemID']);

    //   // return 'Option 2 selected : location = ' . $locationForOption2[0]['Location'];
    //   if ( self::isBlankLocation($locationForOption2) === true ) {
    //     $completeOption2 = self::setComplete($locationForOption2, $lpnId, $barcode);
    //     if ($completeOption2 === true) {
    //       return true;
    //     } else {
    //       return 'complete option 2 error';
    //     }
    //   }

    // }

    // $completeOption3 = self::setComplete(7, $lpnId, $barcode); // 7 = finish good
    
    // if ($completeOption3 === true) {
    //   self::updateRemainLocation(7);
    //   return true;
    // } else {
    //   return 'complete option 2 error';
    // }
  }

  public function isBlankLocation($location) {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Remain FROM [Location]
      WHERE QTY = Remain AND ID = ?",
      [
        $location
      ]
    ));
  }

  public function verifyTransferLPN($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE Remain > 0 
      AND Status = 3 -- complete
      AND LPNID = ?", 
      [
        $lpn
      ]
    );
  }

  public function isBarcodeAlreadyExistsInLPN($lpn, $barcode) {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNLine
      WHERE LPNID = ? AND Barcode = ?",
      [
        $lpn,
        $barcode
      ]
    ));
  }

  public function isRemainLPNZero($lpn)
  {
    $conn = (new Connector)->dbConnect();
    $remain = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 Remain FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );
    return (int)$remain[0]['Remain'];
  }

  public function isRemainInLocationIsZero($location) {
    $conn = (new Connector)->dbConnect();
    $remain = Sqlsrv::queryArray(
      $conn,
      "SELECT Remain FROM [Location]
      WHERE ID = ?",
      [
        $location
      ]
    );

    if ( (int)$remain[0]['Remain'] === 0 ) {
      return true;
    } else {
      return false;
    }
  }

  public function getLpnInfo($lpn)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 * FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );
    return $query;
  }

  public function checkRemainByBatchAndItem($batch, $item)
  {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 LO.ID [LocationID], LO.Remain FROM LPNMaster L
      left join Location LO ON LO.ID = L.LocationID
      WHERE L.ItemID = ? 
      AND L.BatchNo = ?
      AND L.Remain > 0
      AND L.Status = 3 -- complete
      ORDER BY L.ID ASC",
      [
        $item,
        $batch
      ]
    );

    return $query;
  }

  public function isNewItemOnLPN($item, $batch) 
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LocationID, Remain FROM LPNMaster
      WHERE ItemID = ? 
      AND BatchNo = ?
      AND [Status] = 3 -- complete
      ORDER BY ID ASC",
      [
        $item,
        $batch
      ]
    ));
  }

  public function getLocationByExistsLPN($item, $batch) {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 L.LocationID, LO.Remain FROM LPNMaster L
      LEFT JOIN Location LO ON LO.ID = L.LocationID 
      WHERE L.ItemID = ? 
      AND L.BatchNo = ?
      AND LO.Remain > 0
      AND L.Status = 3 -- complete
      ORDER BY L.ID ASC",
      [
        $item,
        $batch
      ]
    );
    return $query;
  }

  public function getLocationLPNMaster($lpn) {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT LocationID WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    return $query;
  }

  public function setComplete($location, $lpn, $barcode)
  {
    $conn = (new Connector)->dbConnect();

    $allBarcodeByLPN = Sqlsrv::queryArray(
      $conn,
      "SELECT Barcode FROM InventTable 
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    \sqlsrv_begin_transaction($conn);

    foreach($allBarcodeByLPN as $v) {

      $barcodeInfo = (new InventoryAPI)->getBarcodeInfo($v['Barcode']);

      if ( count($barcodeInfo) === 0 ) {
        return 'Barcode not found';
      }

      $updateCompleteInventTable = sqlsrv_query(
        $conn,
        "UPDATE InventTable 
        SET DisposalID = 21, -- LPN Receive
        WarehouseID = ?,
        LocationID = ?,
        [Status] = 1,
        UpdateBy = ?,
        UpdateDate = ?
        WHERE Barcode = ?",
        [
          (new LocationAPI)->getWHIDFromLocation($location),
          $location,
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $v['Barcode']
        ] 
      );

      if(!$updateCompleteInventTable) {
        \sqlsrv_rollback($conn);
        return 'Update complete invent table error.';
      }

      $moveOutInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 2,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $barcodeInfo[0]['WarehouseID'],
          $barcodeInfo[0]['LocationID'],
          -1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          2, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveOutInventTrans) {
        \sqlsrv_rollback($conn);
        return 'move out invent trans error.';
      }

      $moveInInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 1,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          21, // lpn receive
          null,
          (new LocationAPI)->getWHIDFromLocation($location),
          $location,
          1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          1, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveInInventTrans) {
        \sqlsrv_rollback($conn);
        return 'move in invent trans error.';
      }

      // $moveOutOnhand = sqlsrv_query(
      //   $conn,
      //   "UPDATE Onhand SET QTY -= 1
      //   WHERE CodeID = ?
      //   AND WarehouseID = ?
      //   AND LocationID = ?
      //   AND Batch = ?
      //   AND Company =?",
      //   [
      //     $barcodeInfo[0]["ItemID"],
      //     $barcodeInfo[0]["WarehouseID"],
      //     $barcodeInfo[0]["LocationID"],
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //   ]
      // );

      // if(!$moveOutOnhand) {
      //   \sqlsrv_rollback($conn);
      //   return 'move out onhand error.';
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
      //     $barcodeInfo[0]["ItemID"],
      //     (new LocationAPI)->getWHIDFromLocation($location),
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //     $barcodeInfo[0]["ItemID"],
      //     (new LocationAPI)->getWHIDFromLocation($location),
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     1, // qty
      //     $barcodeInfo[0]["Company"]
      //   ]
      // );

      // if(!$moveInOnhand) {
      //   \sqlsrv_rollback($conn);
      //   return 'move in onhand error.';
      // }
    }

    \sqlsrv_commit($conn);
    return true;
  }

  public function updateItemReceiveLocationQTY($location, $qty)
  {
    $conn = (new Connector)->dbConnect();
    $updateQty = sqlsrv_query(
      $conn,
      "UPDATE Location 
      SET QTY = ?,
      Remain = ? - QTYInUse
      WHERE ID = ?",
      [
        $qty,
        $qty,
        $location
      ]
    );

    if (!$updateQty) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function getLocationByLocationRemain($item)
  {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT top 1 L.ID[Location] from Location L 
      left join ItemReceiveLocation IR ON IR.LocationID = L.ID
      where IR.ItemID is not null
      and IR.ItemID = ?
      and L.Remain <> 0
      and L.Remain = L.QTY
      order by L.Description asc",
      [
        $item
      ]
    );

    if ( count($query) === 0 )  {
      return '';
    } else {
      return $query[0]['Location'];
    }
  }

  public function completeReceiveLocation($lpnId, $barcode)
  {
    if (self::isStatusProcess($lpnId) === false) {
      return 'LPN status != Process';
    }

    $LPNInfo = self::getLpnInfo($lpnId);

    if (count($LPNInfo) === 0) {
      return 'LPN not found.';
    }

    if (self::isNewItemOnLPN($LPNInfo[0]['BatchNo'], $LPNInfo[0]['ItemID']) === true) {

      $searchRemain = self::checkRemainByBatchAndItem($LPNInfo[0]['BatchNo'], $LPNInfo[0]['ItemID']);

      if (count($searchRemain) !== 0) {
        // มี item และ batch อยู่ใน LPN Master และ remain != 0
        // $completeOption1 = self::setComplete($LPNInfo[0]['LocationID'], $lpnId, $barcode);
        $completeOption1 = self::setLPNComplete($lpnId, $LPNInfo[0]['LocationID']);
        if ($completeOption1 === true) {

          if (self::isRemainInLocationIsZero($LPNInfo[0]['LocationID']) === false) {
            self::updateRemainLocation($LPNInfo[0]['LocationID']);
          }
          
          return true;
        } else {
          return 'complete option 1 error';
        }
      }
    }

    if (count(self::getLocationByLocationRemain($LPNInfo[0]['ItemID'])) !== 0) {
      $locationForOption2 = self::getLocationByLocationRemain($LPNInfo[0]['ItemID']);
      // $completeOption2 = self::setComplete($locationForOption2[0]['Location'], $lpnId, $barcode);
      $completeOption2 = self::setLPNComplete($lpnId, $locationForOption2);
      if ($completeOption2 === true) {

        if (self::isRemainInLocationIsZero($locationForOption2) === false) {
          self::updateRemainLocation($locationForOption2);
        }

        return true;
      } else {
        return 'complete option 2 error';
      }
    }

    // $completeOption3 = self::setComplete(3, $lpnId, $barcode); // 7 = finish good
    $completeOption3 = self::setLPNComplete($lpnId, 7);  // 7 = finish good
    
    if ($completeOption3 === true) {

      self::updateRemainLocation($completeOption3[0]['Location']);
      return true;
    } else {
      return 'complete option 3 error';
    }
  }

  public function updateRemainLocation($location) {
    $conn = (new Connector)->dbConnect();
    $updateLocationRemain = sqlsrv_query(
      $conn,
      "UPDATE Location SET QTYInUse += 1, Remain -= 1 
      WHERE ID = ? AND Remain > 0",
      [
        $location
      ]
    );

    if(!$updateLocationRemain) {
      return false;
    } 
    return true;
  }

  public function updateQtyInuseLocation($location) {
    $conn = (new Connector)->dbConnect();
    $updateLocationQtyInUse = sqlsrv_query(
      $conn,
      "UPDATE Location SET QTYInUse -= 1, Remain += 1 
      WHERE ID = ?",
      [
        $location
      ]
    );

    if(!$updateLocationQtyInUse) {
      return false;
    } 
    return true;
  }

  public function isStatusProcess($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT L.Status 
      FROM LPNMaster L
      WHERE L.Status = 2
      AND LPNID = ?",
      [
        $lpn
      ]
    ));
  }

  public function isStatusOpen($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT L.Status 
      FROM LPNMaster L
      WHERE L.Status = 1
      AND LPNID = ?",
      [
        $lpn
      ]
    ));
  }

  public function getLPNFromBarcode($barcode) {
    $conn = (new Connector)->dbConnect();

    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT LPNID FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if ( count($data) === 0 ) {
      $_lpn = [['']];
      return $_lpn;
    }
    return $data[0]['LPNID'];
  }

  public function transferLPN($lpn, $barcode)
  {
    $conn = (new Connector)->dbConnect();

    $barcodeInfo = (new inventoryAPI)->getBarcodeInfo($barcode);
    $lpnInfo = self::getLpnInfo($lpn);
    $from_lpn = self::getLPNFromBarcode($barcode);

    $isComplete = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ? 
      AND [Status] = 3", // complete
      [
        $lpnInfo[0]['LPNID']
      ]
    ));

    if ( $isComplete === false ) {
      return 'LPN status <> complete';
    }

    sqlsrv_begin_transaction($conn);

    $updateLpn = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster
      SET QtyInUse += 1,
      Remain -= 1,
      UpdateDate = ?,
      UpdateBy = ?
      WHERE LPNID = ?
      AND Remain <> 0",
      [
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $lpn
      ]
    );

    if (!$updateLpn) {
      sqlsrv_rollback( $conn );
      return 'update LPN master error.';
    }

    $updateLpnFrom = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster
      SET QtyInUse -= 1,
      Remain += 1,
      UpdateDate = ?,
      UpdateBy = ?
      WHERE LPNID = ?
      AND QtyInUse <> 0",
      [
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $from_lpn
      ]
    );

    if (!$updateLpnFrom) {
      sqlsrv_rollback( $conn );
      return 'update LPN master error.';
    }

    $deleteLine = sqlsrv_query(
      $conn,
      "DELETE FROM LPNLine 
      WHERE LPNID =?
      AND Barcode = ?",
      [
        $from_lpn,
        $barcode
      ]
    );

    if (!$deleteLine) {
      sqlsrv_rollback( $conn );
      return 'update LPN Line  error.';
    }

    $insertLPNLine = sqlsrv_query(
      $conn,
      "INSERT INTO LPNLine(
        LPNID,
        Barcode,
        Company,
        CreateDate,
        CreateBy
      ) VALUES(
        ?, ?, ?, ?, ?
      )",
      [
        $lpn,
        $barcode,
        $_SESSION['user_company'],
        date('Y-m-d H:i:s'),
        $_SESSION['user_login']
      ]
    );

    if (!$insertLPNLine) {
      sqlsrv_rollback( $conn );
      return 'update LPN line error.';
    }

    $updateInventTable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET LPNID = ?,
      LocationID = ?,
      UpdateDate = ?,
      UpdateBy = ?,
      DisposalID = 21 -- lpn receive
      WHERE Barcode = ?",
      [
        $lpn,
        $lpnInfo[0]['LocationID'],
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $barcode
      ]
    );

    if (!$updateInventTable) {
      sqlsrv_rollback( $conn );
      return 'update invent table error.';
    }

    $moveOutInventTrans = sqlsrv_query(
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
        (new InventoryAPI)->genTransId($barcode) . 1,
        $barcode,
        $barcodeInfo[0]['ItemID'],
        $barcodeInfo[0]['Batch'],
        $barcodeInfo[0]['DisposalID'],
        null,
        $barcodeInfo[0]['WarehouseID'],
        $barcodeInfo[0]['LocationID'],
        -1, // qty
        $barcodeInfo[0]['Unit'], // unit id
        2, // docs type
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        date('Y-m-d H:i:s'),
        $_SESSION["Shift"]
      ]
    );

    if(!$moveOutInventTrans) {
      sqlsrv_rollback( $conn );
      return 'move out invent trans error.';
    }

    $moveInInventTrans = sqlsrv_query(
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
        (new InventoryAPI)->genTransId($barcode) . 2,
        $barcode,
        $barcodeInfo[0]['ItemID'],
        $barcodeInfo[0]['Batch'],
        21, // lpn receive
        null,
        $barcodeInfo[0]['WarehouseID'],
        $lpnInfo[0]['LocationID'],
        1, // qty
        $barcodeInfo[0]['Unit'], // unit id
        1, // docs type
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        date('Y-m-d H:i:s'),
        $_SESSION["Shift"]
      ]
    );

    if(!$moveInInventTrans) {
      sqlsrv_rollback( $conn );
      return 'move in invent trans error.';
    }

    // $moveOutOnhand = sqlsrv_query(
    //   $conn,
    //   "UPDATE Onhand SET QTY -= 1
    //   WHERE CodeID = ?
    //   AND WarehouseID = ?
    //   AND LocationID = ?
    //   AND Batch = ?
    //   AND Company =?",
    //   [
    //     $barcodeInfo[0]["ItemID"],
    //     $barcodeInfo[0]['WarehouseID'],
    //     $barcodeInfo[0]['LocationID'],
    //     $barcodeInfo[0]["Batch"],
    //     $barcodeInfo[0]["Company"],
    //   ]
    // );

    // if(!$moveOutOnhand) {
    //   sqlsrv_rollback( $conn );
    //   return 'move out onhand error.';
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
    //     $barcodeInfo[0]["ItemID"],
    //     $barcodeInfo[0]['WarehouseID'],
    //     $lpnInfo[0]['LocationID'],
    //     $barcodeInfo[0]["Batch"],
    //     $barcodeInfo[0]["Company"],
    //     $barcodeInfo[0]["ItemID"],
    //     $barcodeInfo[0]['WarehouseID'],
    //     $lpnInfo[0]['LocationID'],
    //     $barcodeInfo[0]["Batch"],
    //     1, // qty
    //     $barcodeInfo[0]["Company"]
    //   ]
    // );

    // if(!$moveInOnhand) {
    //   sqlsrv_rollback( $conn );
    //   return 'move in onhand error.';
    // }

    $isInUseZero = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT QtyInUse FROM LPNMaster
      WHERE QtyInUse = 0 AND LPNID = ?",
      [
        $barcodeInfo[0]['LPNID']
      ]
    ));

    if ( $isInUseZero === true ) {

      $setClose = sqlsrv_query(
        $conn,
        "UPDATE LPNMaster 
        SET [Status] = 5,
        UpdateBy = ?,
        UpdateDate = ?
        WHERE LPNID = ?",
        [
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $barcodeInfo[0]['LPNID']
        ]
      );

      $addRemain = sqlsrv_query(
        $conn,
        "UPDATE [Location] 
        SET Remain += 1,
        QTYInUse -= 1
        WHERE ID = ?",
        [
          $barcodeInfo[0]['LocationID']
        ]
      );

    }

    sqlsrv_commit($conn);
    return true;
  }

  public function saveUpdateLocation($location, $location_temp, $lpn)
  {
    $conn = (new Connector)->dbConnect();

    if (sqlsrv_begin_transaction($conn) === false) {
      return 'Error';
    }

    $updateLPNLocation = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster 
      SET LocationID = ?
      WHERE LPNID = ?",
      [
        $location,
        $lpn
      ]
    );

    if ( !$updateLPNLocation ) {
      sqlsrv_rollback($conn);
      return 'update lpn master error.';
    }

    $updateQTYNew = sqlsrv_query(
      $conn,
      "UPDATE [Location]
      SET QTYInUse += 1
      WHERE ID = ?",
      [
        $location
      ]
    );

    if ( !$updateQTYNew ) {
      sqlsrv_rollback($conn);
      return 'update qty new location error.';
    }

    $updateQTYOld = sqlsrv_query(
      $conn,
      "UPDATE [Location]
      SET QTYInUse -= 1
      WHERE ID = ?",
      [
        $location_temp
      ]
    );

    if ( !$updateQTYOld ) {
      sqlsrv_rollback($conn);
      return 'update qty old location error.';
    }

    $updateRemain = sqlsrv_query(
      $conn,
      "UPDATE [Location]
      SET Remain = QTY - QTYInUse
      WHERE ID IN (?, ?)",
      [
        $location,
        $location_temp
      ]
    );

    if ( !$updateRemain ) {
      sqlsrv_rollback($conn);
      return 'update remain location error.';
    }

    $allBarcodeFromLPN = Sqlsrv::queryArray(
      $conn,
      "SELECT Barcode 
      FROM InventTable 
      WHERE LPNID = ? AND LPNID is not null",
      [
        $lpn
      ]
    );

    if ( count($allBarcodeFromLPN) === 0 ) {
      sqlsrv_commit($conn);
      return true;
    }

    $whid_new = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 WarehouseID FROM [Location] 
      WHERE ID = ?",
      [
        $location
      ]
    );

    $whid_old = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 WarehouseID FROM [Location] 
      WHERE ID = ?",
      [
        $location_temp
      ]
    );

    foreach ($allBarcodeFromLPN as $v) {

      $barcodeInfo = (new InventoryAPI)->getBarcodeInfo($v['Barcode']);

      $updateInventTable = sqlsrv_query(
        $conn,
        "UPDATE InventTable 
        SET WarehouseID = ?,
        LocationID = ?,
        UpdateBy = ?,
        UpdateDate = ?
        WHERE Barcode = ?",
        [
          $whid_new[0]['WarehouseID'],
          $location,
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $v['Barcode']
        ] 
      );

      if(!$updateInventTable) {
        \sqlsrv_rollback($conn);
        return 'Update invent table error.';
      }

      $moveOutInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 1,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $whid_old[0]['WarehouseID'],
          $location_temp,
          -1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          2, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveOutInventTrans) {
        \sqlsrv_rollback($conn);
        return 'move out invent trans error.';
      }

      $moveInInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 2,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $whid_new[0]['WarehouseID'],
          $location,
          1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          1, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveInInventTrans) {
        \sqlsrv_rollback($conn);
        return 'move in invent trans error.';
      }

      // $moveOutOnhand = sqlsrv_query(
      //   $conn,
      //   "UPDATE Onhand SET QTY -= 1
      //   WHERE CodeID = ?
      //   AND WarehouseID = ?
      //   AND LocationID = ?
      //   AND Batch = ?
      //   AND Company =?",
      //   [
      //     $barcodeInfo[0]["ItemID"],
      //     $whid_old[0]['WarehouseID'],
      //     $location_temp,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //   ]
      // );

      // if(!$moveOutOnhand) {
      //   \sqlsrv_rollback($conn);
      //   return 'move out onhand error.';
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
      //     $barcodeInfo[0]["ItemID"],
      //     $whid_new[0]['WarehouseID'],
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //     $barcodeInfo[0]["ItemID"],
      //     $whid_new[0]['WarehouseID'],
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     1, // qty
      //     $barcodeInfo[0]["Company"]
      //   ]
      // );

      // if(!$moveInOnhand) {
      //   \sqlsrv_rollback($conn);
      //   return 'move in onhand error.';
      // }

    }

    sqlsrv_commit($conn);
    return true;
  }

  public function setLPNComplete($lpn, $location)
  {
    $conn = (new Connector)->dbConnect();

    $allBarcode = self::getBarcodeByLPN($lpn);

    if (count($allBarcode) !== 0) {
      $update = [];
      foreach ($allBarcode as $barcode) {
        $update[] = self::setComplete($location, $lpn, $barcode['Barcode']); 
      }
    }

    $completeOnlyLPN = self::setCompleteOnlyLPN($location, $lpn);

    return true;
  }

  public function getBarcodeByLPN($lpn) 
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT Barcode FROM InventTable
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );
  }

  public function setCompleteOnlyLPN($location, $lpn)
  {
    $conn = (new Connector)->dbConnect();

    $updateLocationLPNMaster = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster 
      SET LocationID = ?,
      Status = 3, -- complete
      UpdateDate = ?,
      UpdateBy = ?,
      CompleteDate = ?
      WHERE LPNID = ?",
      [
        $location,
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $lpn
      ]
    );

    if(!$updateLocationLPNMaster) {
      \sqlsrv_rollback($conn);
      return 'Update complete LPN master error.';
    }
  }

  public function isRealLPN($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ?", // open & close
      [
        $lpn
      ]
    ));
  }

  public function isComplete($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ? 
      AND [Status] = 3", // complete
      [
        $lpn
      ]
    ));
  }

  public function isLPNProcess($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ? 
      AND [Status] = 2", // process
      [
        $lpn
      ]
    ));
  }

  public function isLPNClosed($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM LPNMaster
      WHERE LPNID = ? 
      AND [Status] = 5", // close
      [
        $lpn
      ]
    ));
  }

  public function isQtyInUseMatchLPNLine($lpn)
  {
     $conn = (new Connector)->dbConnect();
     $qtyInUse = Sqlsrv::queryArray(
       $conn,
       "SELECT QtyInUse FROM LPNMaster
       WHERE LPNID = ?",
       [
         $lpn
       ]
     );

     $sumOfLine = Sqlsrv::queryArray(
       $conn,
       "SELECT SUM(*)[Total] FROM LPNLine 
       WHERE LPNID = ?",
       [
         $lpn
       ] 
     );

     if ((int)$qtyInUse[0]['QtyInUse'] <= (int)$sumOfLine[0]['Total']) {
      return true;
     } else {
      return false;
     }
  }

  public function getBarcodeFromLPNLine($lpn)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT Barcode FROM LPNLine
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );
  }

  public function isLPNMatchedItemAndBatch($lpn_1, $lpn_2) {
    $conn = (new Connector)->dbConnect();

    $lpn1 = Sqlsrv::queryArray(
      $conn,
      "SELECT ItemID, BatchNo
      FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn_1
      ]
    );

    $lpn2 = Sqlsrv::queryArray(
      $conn,
      "SELECT ItemID, BatchNo
      FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn_2
      ]
    );

    if ( $lpn1[0]['ItemID'] === $lpn2[0]['ItemID'] && $lpn1[0]['BatchNo'] === $lpn2[0]['BatchNo']) {
      return true;
    } else {
      return false;
    }
  }

  public function isBarcodeCanReceiveLPN($lpn, $barcode) {
    $conn = (new Connector)->dbConnect();

    $lpn1 = Sqlsrv::queryArray(
      $conn,
      "SELECT ItemID, BatchNo
      FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    $barcode = Sqlsrv::queryArray(
      $conn,
      "SELECT Batch, ItemID 
      FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if ( $lpn1[0]['BatchNo'] === $barcode[0]['Batch'] && $lpn1[0]['ItemID'] === $barcode[0]['ItemID']) {
      return true;
    } else {
      return false;
    }
  }

  public function transferLocation($lpn, $location) {

    $conn = (new Connector)->dbConnect();

    $barcodeAll = Sqlsrv::queryArray(
      $conn,
      "SELECT Barcode FROM InventTable
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    $state_query = '';

    sqlsrv_begin_transaction($conn);
    
    $updateLpn = sqlsrv_query(
      $conn,
      "UPDATE LPNMaster
      SET UpdateDate = ?,
      UpdateBy = ?,
      LocationID = ?
      WHERE LPNID = ?",
      [
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $location,
        $lpn
      ]
    );

    if (!$updateLpn) {
      $state_query = 'error';
    }

    foreach($barcodeAll as $v) {

      $barcodeInfo = (new inventoryAPI)->getBarcodeInfo($v['Barcode']);

      $updateInventTable = sqlsrv_query(
        $conn,
        "UPDATE InventTable 
        SET DisposalID = ?,
        WarehouseID = ?,
        LocationID = ?,
        [Status] = 1,
        UpdateBy = ?,
        UpdateDate= ?
        WHERE Barcode = ?",
        [
          22, // trans location
          (new LocationAPI)->getWHIDFromLocation($location),
          $location,
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $v['Barcode']
        ]
      );

      if (!$updateInventTable) {
        $state_query = 'error';
      }

      $moveOutInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 1,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $barcodeInfo[0]['WarehouseID'],
          $barcodeInfo[0]['LocationID'],
          -1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          2, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveOutInventTrans) {
        $state_query = 'error';
      }

      $moveInInventTrans = sqlsrv_query(
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
          (new InventoryAPI)->genTransId($v['Barcode']) . 2,
          $v['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          22, // trans location
          null,
          (new LocationAPI)->getWHIDFromLocation($location),
          $location,
          1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          1, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"]
        ]
      );

      if(!$moveInInventTrans) {
        $state_query = 'error';
      }

      // $moveOutOnhand = sqlsrv_query(
      //   $conn,
      //   "UPDATE Onhand SET QTY -= 1
      //   WHERE CodeID = ?
      //   AND WarehouseID = ?
      //   AND LocationID = ?
      //   AND Batch = ?
      //   AND Company =?",
      //   [
      //     $barcodeInfo[0]["ItemID"],
      //     $barcodeInfo[0]['WarehouseID'],
      //     $barcodeInfo[0]['LocationID'],
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //   ]
      // );

      // if(!$moveOutOnhand) {
      //   $state_query = 'error';
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
      //     $barcodeInfo[0]["ItemID"],
      //     (new LocationAPI)->getWHIDFromLocation($location),
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //     $barcodeInfo[0]["ItemID"],
      //     (new LocationAPI)->getWHIDFromLocation($location),
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     1, // qty
      //     $barcodeInfo[0]["Company"]
      //   ]
      // );

      // if(!$moveInOnhand) {
      //   $state_query = 'error';
      // }

      
    }

    $downFrom = sqlsrv_query(
      $conn,
      "UPDATE [Location] 
      SET Remain += 1,
      QTYInUse -= 1
      WHERE ID = ?",
      [
        $barcodeInfo[0]['LocationID']
      ]
    );

    $upTo = sqlsrv_query(
      $conn,
      "UPDATE [Location] 
      SET Remain -= 1,
      QTYInUse += 1
      WHERE ID = ?",
      [
        $location
      ]
    );

    if ( $state_query === '' ) {
      sqlsrv_commit($conn);
      return true;
    } else {
      sqlsrv_rollback($conn);
      return false;
    }
  }

  public function printLPN($lpn_arr) {
    $conn = (new Connector)->dbConnect();

    $data = [];

    foreach($lpn_arr as $v) {
      $lpnInfo = (new PalletAPI)->getLpnInfo($v);
      $item_name = (new ItemAPI)->getItemInfo($lpnInfo[0]['ItemID']);

      $data[] =  [
        'lpn' => $lpnInfo[0]['LPNID'],
        'item_id' => $lpnInfo[0]['ItemID'],
        'item_name' => $item_name[0]['NameTH'],
        'batch' => $lpnInfo[0]['BatchNo']
      ];
    }

    return $data;
  }

  public function deleteLPN($lpn) {
    $conn = (new Connector)->dbConnect();
    $q = sqlsrv_query(
      $conn,
      "DELETE FROM LPNMaster 
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    if ($q) {
      return true;
    } else {
      return false;
    }
  }

  public function getLocationFromLPN($lpn) {
    $conn = (new Connector)->dbConnect();
    $q = Sqlsrv::queryArray(
      $conn,
      "SELECT LocationID FROM LPNMaster
      WHERE LPNID = ?",
      [
        $lpn
      ]
    );

    if ( count($q) !== 0 ) {
      return $q[0]['LocationID'];
    } else {
      return '';
    }
  }
}