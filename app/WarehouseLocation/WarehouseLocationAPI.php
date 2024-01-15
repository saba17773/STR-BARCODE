<?php

namespace App\WarehouseLocation;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;
use App\V2\Barcode\BarcodeAPI;
use App\V2\Inventory\InventoryAPI;
use Wattanar\SqlsrvHelper;

class WarehouseLocationAPI
{

  private $conn = null;

  public function __construct() {
    $this->conn = (new Connector)->dbConnect();
  }

  public function getPallettSeq() {

    $lastPallet = Sqlsrv::queryArray(
      $this->conn,
      "SELECT TOP 1 SeqValue
      FROM SeqNumber
      WHERE SeqName = 'wh_pallet'"
    );

    if ( count($lastPallet) === 0 ) {
      return 0;
    } else {
      return $lastPallet[0]['SeqValue'];
    }
  }

  public function createPallet($qty) {
    $qty = (int)$qty;
    $conn = (new Connector)->dbConnect();

    $currentPalletSeq = (int)self::getPallettSeq();

    $palletQty = (int)$qty + (int)$currentPalletSeq;

    if ($qty === 0) {
      return [
        'result' => false,
        'message' => 'number incorrect!'
      ];
    } 

    for ($i = $currentPalletSeq + 1; $i <= $palletQty; $i++) {
      $insertPallet = sqlsrv_query(
        $conn,
        "INSERT INTO PalletMaster(
          pallet_no, 
          pallet_status,
          pallet_item
        ) VALUES(
          ?, ?, ?
        )",
        [
          self::formatWHPalletCode($i),
          1,
          null
        ]
      );

      if ($insertPallet) {
        self::updateWHPalletSeq();
      }
    }

    return [
      'result' => true,
      'message' => 'create successful!'
    ];
  }

  public function formatWHPalletCode($number) {
    return WH_PALLET . str_pad($number, 7, "0", STR_PAD_LEFT);
  }

  public function updateWHPalletSeq() {
    $conn = (new Connector)->dbConnect();
    sqlsrv_query(
      $conn,
      "UPDATE SeqNumber
      SET SeqValue+=1
      WHERE SeqName = 'wh_pallet'"
    );
  }

  public function getAllPallet() {
    $conn = (new Connector)->dbConnect();
    return (new SqlsrvHelper)->getRows(sqlsrv_query(
      $conn,
      "SELECT 
      id,
      pallet_no,
      pallet_status,
      pallet_item 
      FROM PalletMaster
      ORDER BY id desc"
    ));
  }

  public function isPalletExists($pallet_no) {
    $conn = (new Connector)->dbConnect();
    $check = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT pallet_no 
      FROM PalletMaster
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));
    return $check;
  }

  public function isInventoryHasPalletNo($pallet_no, $barcode) {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT PalletNo 
      FROM InventTable 
      WHERE PalletNo = ? 
      AND Barcode = ?
      AND PalletNo is not null",
      [
        $pallet_no,
        $barcode
      ]
    ));
  }

  public function isBarcodeCanReceiveLPN($pallet_no, $barcode) {

    return false;
    
    $conn = (new Connector)->dbConnect();

    $pallet = Sqlsrv::queryArray(
      $conn,
      "SELECT ItemID, BatchNo
      FROM LPNMaster
      WHERE PalletNo = ?",
      [
        $pallet_no
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

    if ( $pallet[0]['BatchNo'] === $barcode[0]['Batch'] && $pallet[0]['ItemID'] === $barcode[0]['ItemID']) {
      return true;
    } else {
      return false;
    }
  }

  public function receiveLocation($pallet_no, $barcode) {

    _d();

    $conn = (new Connector)->dbConnect();

    $barcodeAPI = new BarcodeAPI;

    if ( self::isPalletExists($pallet_no) === false) {
      
      return [
        'result' => false,
        'message' => 'Pallet not found!'
      ];
    }

    $barcodeInfo = $barcodeAPI->barcodeInfo($barcode);

    $isBarcodeFree = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT PalletNo 
      FROM InventTable 
      WHERE Barcode = ?
      AND PalletNo is null",
      [
        $barcode
      ]
    ));

    if ($isBarcodeFree === false) {
      return [
        'result' => false,
        'message' => 'barcode not free from pallet!'
      ];
    }

    $_item = $barcodeInfo[0]['ItemID'];

    if ( count($barcodeInfo) === 0 ) {
      
      return [
        'result' => false,
        'message' => 'Barcode incorrect!'
      ];
    }

    // get qty per pallet
    $qtyPerPallet = self::getQtyPerPallet($_item);

    if ( $qtyPerPallet['result'] === false ) {
      
      return [
        'result' => false,
        'message' => 'qty per pallet not found!'
      ];;
    }

    // check pallet complete
    if ( self::isPalletComplete($pallet_no) === true ) {
      
      return [
        'result' => false,
        'message' => 'pallet already complete!'
      ];;
    }

    // start transaction
    if ( \sqlsrv_begin_transaction($conn) === false) {
      
      return [
        'result' => false,
        'message' => 'transaction begin failed!'
      ];
    }

    // pallet ใช้อยู่หรือป่าว ?
    if ( self::isPalletExistsInPalletTable($pallet_no) === false) {

      // ถ้าไม่ได้ใช้อยู่
      _d("pallet no ไม่อยู่ใน pallet table");

      // insert pallet table
      $insertPalletTable = sqlsrv_query(
        $conn,
        "INSERT INTO PalletTable(
          pallet_no,
          item_id,
          batch_no,
          location_id,
          qty_per_pallet,
          qty_in_use,
          remain,
          company,
          [status],
          complete_date,
          complete_by,
          create_date,
          create_by,
          update_date,
          update_by
        ) VALUES (
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?
        )",
        [
          $pallet_no,
          $_item,
          $barcodeInfo[0]['Batch'],
          null,
          $qtyPerPallet['data'],
          1,
          (int)$qtyPerPallet['data'] - 1,
          $barcodeInfo[0]['Company'],
          2, // process
          null,
          null,
          date('Y-m-d H:i:s'),
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $_SESSION['user_login']
        ]
      );

      if ( !$insertPalletTable ) {
        \sqlsrv_rollback($conn);
        
        return [
          'result' => false,
          'message' => 'insert pallet table failed!'
        ];
      }

    } else {

      // ถ้า pallet ใช้อยู่
      _d("pallet no มีอยู่ใน pallet table");

      $isPalletClosed = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT pallet_no, [status] 
        FROM PalletTable 
        WHERE [status] = 5 -- closed
        AND pallet_no = ?",
        [
          $pallet_no
        ]
      ));

      if ($isPalletClosed === false) {
        // เช็คว่า item ตรงกับ Item ที่อยู๋ pallet หรือป่าว
        $checkPalletItem = \sqlsrv_has_rows(\sqlsrv_query(
          $conn,
          "SELECT pallet_item 
          FROM PalletMaster
          WHERE pallet_no = ?
          AND pallet_item = ?
          AND pallet_status = 1",
          [
            $pallet_no,
            $_item
          ]
        ));

        // ถ้าไม่ตรงก็ error
        if ( $checkPalletItem === false ) {
          \sqlsrv_rollback($conn);
          
          return [
            'result' => false,
            'message' => 'pallet item not the same!' . $_SESSION['_d']
          ];;
        }
      }

      // insert pallet table
      $updatePalletTable = sqlsrv_query(
        $conn,
        "UPDATE PalletTable
        SET qty_in_use += 1,
        remain -= 1,
        update_by = ?,
        update_date = ?
        WHERE pallet_no = ?",
        [
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $pallet_no
        ]
      );
  
      if ( !$updatePalletTable ) {
        \sqlsrv_rollback($conn);
        
        return [
          'result' => false,
          'message' => 'update pallet table failed!'
        ];
      }
    }

    _d("update pallet master");

    // update item ใน pallet master
    $updatePalletItem = sqlsrv_query(
      $conn,
      "UPDATE PalletMaster 
      SET pallet_item = ?,
      pallet_status = 1
      WHERE pallet_no = ?",
      [
        $_item,
        $pallet_no
      ]
    );

    if ( !$updatePalletItem ) {
      \sqlsrv_rollback($conn);
      
      return [
        'result' => false,
        'message' => 'update pallet item failed!'
      ];
    }

    // insert pallet line
    $insertPalletLine  = sqlsrv_query(
      $conn,
      "INSERT INTO PalletLine(
        pallet_no,
        barcode,
        create_date,
        create_by,
        company
      ) VALUES (
        ?, ?, ?, ?, ?
      )",
      [
        $pallet_no,
        $barcode,
        date('Y-m-d H:i:s'),
        $_SESSION['user_login'],
        $_SESSION['user_company']
      ]
    );

    if ( !$insertPalletLine ) {
      \sqlsrv_rollback($conn);
      
      return [
        'result' => false,
        'message' => 'insert pallet line failed!'
      ];
    }

    // update invent table
    $updateInventTable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET PalletNo = ?
      WHERE Barcode = ?",
      [
        $pallet_no,
        $barcode
      ]
    );

    if ( !$updateInventTable ) {
      \sqlsrv_rollback($conn);
      
      return [
        'result' => false,
        'message' => 'update invent table failed!'
      ];
    }

    // check remain ที่ pallet table ว่าหมดหรือยัง
    $currentRemain = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT TOP 1 remain 
      FROM PalletTable
      WHERE pallet_no = ?
      AND remain = 0",
      [
        $pallet_no
      ]
    ));

    // ถ้า remain = 0 ที่ pallet table
    if ( $currentRemain === true ) {

      _d("remain = 0 ที่ pallet table");

      // เช็คว่าสถานะเป็น process หรือป่าว
      $isProcess = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT pallet_no 
        FROM PalletTable 
        WHERE pallet_no = ?
        AND [status] = 2",
        [
          $pallet_no
        ]
      ));

      // ถ้าสถานะ = process
      if ( $isProcess === true ) {

        _d("pallet status = process");
        _d("Complete pallet");

        // complete pallet
        $completePallet = sqlsrv_query(
          $conn,
          "UPDATE PalletTable 
          SET [status] = 3,
          complete_date = ?,
          complete_by = ?,
          location_id = ?
          WHERE pallet_no = ?
          AND [status] NOT IN (3,4,5)
          AND [status] = 2",
          [
            date('Y-m-d H:i:s'),
            $_SESSION['user_login'],
            null,
            $pallet_no
          ]
        );

        if ( !$completePallet ) {
          \sqlsrv_rollback($conn);
          
          return [
            'result' => false,
            'message' => 'complete pallet failed!'
          ];
        }

        // เช็คว่า pallet มีเพื่อนไปเก็บอยู่ก่อนแล้วหรือป่าว
        $hasPalletFriend = sqlsrv_has_rows(sqlsrv_query(
          $conn,
          "SELECT item_id
          FROM PalletTable
          WHERE item_id = ?
          AND [status] = 3
          AND pallet_no <> ?
          ORDER BY id asc", // complete
          [
            $_item,
            $pallet_no
          ]
        ));

        // pallet ไม่มีเพื่อน
        if ($hasPalletFriend === false) {
          _d("pallet has friend");


          // ใน item receive location มี item ที่ตรงหรือป่าว
          $isItemHasLocation = sqlsrv_has_rows(sqlsrv_query(
            $conn,
            "SELECT ItemID
            FROM ItemReceiveLocation
            WHERE ItemID = ?", 
            [
              $_item
            ]
          ));

          // ไม่มี item ที่ตรงกันใน item receive location เลย
          if ( $isItemHasLocation === false ) {

            _d("item has location = false");

            // update pallet table location = finish goods
            $updateLocationPalletTable = sqlsrv_query(
              $conn,
              "UPDATE PalletTable 
              SET location_id = ?
              WHERE pallet_no = ?",
              [
                7, // finish good
                $pallet_no
              ]
            );

            if ( !$updateLocationPalletTable ) {
              \sqlsrv_rollback($conn);
              return [
                'result' => false,
                'message' => 'update location pallet table failed!'
              ];
            }

            if ( !$updateInventTable ) {
              \sqlsrv_rollback($conn);
              return [
                'result' => false,
                'message' => 'update barcode location!'
              ];
            }

            // loop update invent trans
            $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT 
                PL.barcode,
                IT.ItemID as item_id,
                IT.Batch as batch,
                IT.DisposalID as disposal_id,
                IT.WarehouseID as warehouse_id,
                IT.LocationID as location_id,
                IT.Unit as unit
                FROM PalletLine PL
                LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                WHERE PL.pallet_no = ?",
              [
                $pallet_no
              ]
            ));

            if ( count($allBarcodeInPallet) > 0 ) {
              foreach ($allBarcodeInPallet as $v) {
                // update invent trans move out
                $inventTransMoveOut = sqlsrv_query(
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
                    (new InventoryAPI)->genTransId($v['barcode']) . 1,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    $v['disposal_id'],
                    null,
                    $v['warehouse_id'],
                    $v['location_id'],
                    -1, // qty
                    $v['unit'], // unit id
                    2, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"]
                  ]
                );

                if ( !$inventTransMoveOut ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move out failed!'
                  ];
                }

                // update invent trans move in
                $inventTransMoveIn = sqlsrv_query(
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
                    PalletNo
                  ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?
                  )",
                  [
                    (new InventoryAPI)->genTransId($v['barcode']) . 2,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    25,
                    null,
                    $v['warehouse_id'],
                    7, // location finish goods
                    1, // qty
                    $v['unit'], // unit id
                    1, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"],
                    $pallet_no
                  ]
                );

                if ( !$inventTransMoveIn ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move in failed!'
                  ];
                }

                // update invent table location = finish goods
                $updateInventTable = sqlsrv_query(
                  $conn,
                  "UPDATE InventTable
                  SET LocationID = ?
                  WHERE Barcode = ?",
                  [
                    7,
                    $v['barcode']
                  ]
                );

                if ( !$updateInventTable ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update invent table failed!'
                  ];
                }
                // end loop

              }
            }

          } else {

            _d("item has location = true");


            // check remain ที่ table Location
            $checkRemainLocation = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT RL.LocationID, RL.ItemID, L.Remain
              FROM ItemReceiveLocation RL
              LEFT JOIN Location L ON L.ID = RL.LocationID
              where L.Remain > 0
              AND RL.ItemID = ?
              ORDER BY RL.ID ASC",
              [
                $_item
              ]
            ));

            // Reamin ที่ table Location = 0
            if ( count($checkRemainLocation) === 0 ) {

              _d("remain location = 0");
              
              // update pallet table location = finish goods
              $updateLocationPalletTable = sqlsrv_query(
                $conn,
                "UPDATE PalletTable 
                SET location_id = ?
                WHERE pallet_no = ?",
                [
                  7, // finish good
                  $pallet_no
                ]
              );

              if ( !$updateLocationPalletTable ) {
                \sqlsrv_rollback($conn);
                return [
                  'result' => false,
                  'message' => 'update location pallet table failed!'
                ];
              }

              // loop update invent trans
              $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT 
                  PL.barcode,
                  IT.ItemID as item_id,
                  IT.Batch as batch,
                  IT.DisposalID as disposal_id,
                  IT.WarehouseID as warehouse_id,
                  IT.LocationID as location_id,
                  IT.Unit as unit
                  FROM PalletLine PL
                  LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                  WHERE PL.pallet_no = ?",
                [
                  $pallet_no
                ]
              ));

              if ( count($allBarcodeInPallet) > 0 ) {
                foreach ($allBarcodeInPallet as $v) {
                  // update invent trans move out
                  $inventTransMoveOut = sqlsrv_query(
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
                      (new InventoryAPI)->genTransId($v['barcode']) . 1,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      $v['disposal_id'],
                      null,
                      $v['warehouse_id'],
                      $v['location_id'],
                      -1, // qty
                      $v['unit'], // unit id
                      2, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"]
                    ]
                  );

                  if ( !$inventTransMoveOut ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move out failed!'
                    ];
                  }

                  // update invent trans move in
                  $inventTransMoveIn = sqlsrv_query(
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
                      PalletNo
                    ) VALUES (
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?
                    )",
                    [
                      (new InventoryAPI)->genTransId($v['barcode']) . 2,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      25,
                      null,
                      $v['warehouse_id'],
                      7, // location finish goods
                      1, // qty
                      $v['Unit'], // unit id
                      1, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"],
                      $pallet_no
                    ]
                  );

                  if ( !$inventTransMoveIn ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move in failed!'
                    ];
                  }

                  // update invent table location = finish goods
                  $updateInventTable = sqlsrv_query(
                    $conn,
                    "UPDATE InventTable
                    SET LocationID = ?
                    WHERE Barcode = ?",
                    [
                      7,
                      $v['barcode']
                    ]
                  );
      
                  if ( !$updateInventTable ) {
                    \sqlsrv_rollback($conn);
                    return [
                      'result' => false,
                      'message' => 'update barcode location!'
                    ];
                  }

                  // end loop
                }
              }

            } else {

              _d("remain location != 0");

              // update location ตาม table Location ที่ remain > 0

              // update location pallet table
              $updateLocationPalletTable = sqlsrv_query(
                $conn,
                "UPDATE PalletTable 
                SET location_id = ?
                WHERE pallet_no = ?",
                [
                  $checkRemainLocation[0]['LocationID'],
                  $pallet_no
                ]
              );

              if ( !$updateLocationPalletTable ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update location pallet table failed!'
                ];
              }

              $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT 
                  PL.barcode,
                  IT.ItemID as item_id,
                  IT.Batch as batch,
                  IT.DisposalID as disposal_id,
                  IT.WarehouseID as warehouse_id,
                  IT.LocationID as location_id,
                  IT.Unit as unit
                  FROM PalletLine PL
                  LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                  WHERE PL.pallet_no = ?",
                [
                  $pallet_no
                ]
              ));
  
              if ( count($allBarcodeInPallet) > 0 ) {
                foreach ($allBarcodeInPallet as $v) {
                  // update invent trans move out
                  $inventTransMoveOut = sqlsrv_query(
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
                      (new InventoryAPI)->genTransId($v['barcode']) . 1,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      $v['disposal_id'],
                      null,
                      $v['warehouse_id'],
                      $v['location_id'],
                      -1, // qty
                      $v['unit'], // unit id
                      2, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"]
                    ]
                  );
  
                  if ( !$inventTransMoveOut ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move out failed!'
                    ];
                  }
  
                  // update invent trans move in
                  $inventTransMoveIn = sqlsrv_query(
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
                      PalletNo
                    ) VALUES (
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?
                    )",
                    [
                      (new InventoryAPI)->genTransId($v['barcode']) . 2,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      25,
                      null,
                      $v['warehouse_id'],
                      $checkRemainLocation[0]['LocationID'], // location
                      1, // qty
                      $v['unit'], // unit id
                      1, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"],
                      $pallet_no
                    ]
                  );
  
                  if ( !$inventTransMoveIn ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move in failed!'
                    ];
                  }

                  // update invent table
                  $updateInventTable = sqlsrv_query(
                    $conn,
                    "UPDATE InventTable
                    SET LocationID = ?
                    WHERE Barcode = ?",
                    [
                      $checkRemainLocation[0]['LocationID'],
                      $v['barcode']
                    ]
                  );

                  _d($v['barcode'] . " location = " . $checkRemainLocation[0]['LocationID']);
      
                  if ( !$updateInventTable ) {
                    \sqlsrv_rollback($conn);
                    return [
                      'result' => false,
                      'message' => 'update barcode location!'
                    ];
                  }

                  // end loop
                }
              }

              // update remain location
              $updateRemainLocation = sqlsrv_query(
                $conn,
                "UPDATE Location 
                SET Remain -= 1,
                QtyInUse += 1
                WHERE ID = ?",
                [
                  $checkRemainLocation[0]['LocationID']
                ]
              );

              if ( !$updateRemainLocation ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update remain location failed!'
                ];
              }

            }

          }

        } else {
          _d("pallet has friend");
          // pallet มีเพื่อน

          // เช็คว่า ที่ Location Remain = 0 หรือป่าว
          $palletFriend = (new SqlsrvHelper)->getRows(sqlsrv_query(
            $conn,
            "SELECT 
            PT.pallet_no, 
            PT.item_id , 
            L.Remain, 
            RL.LocationID
            FROM PalletTable PT
            LEFT JOIN ItemReceiveLocation RL ON RL.LocationID = PT.location_id
            LEFT JOIN Location L ON L.ID = RL.LocationID AND L.Remain <> 0
            WHERE PT.pallet_no <> ?
            AND L.Remain <> 0
            AND PT.[status] = 3
            GROUP BY 
            PT.pallet_no, 
            PT.item_id, 
            L.Remain, 
            RL.LocationID",
            [
              $pallet_no
            ]
          ));

          // Remain ที่ Location เป็น 0 หมดเลย ไมมีที่เก็บแล้ว
          if ( count($palletFriend) === 0 ) {

            _d("remain location = 0");

            $isItemHasLocation = sqlsrv_has_rows(sqlsrv_query(
              $conn,
              "SELECT ItemID
              FROM ItemReceiveLocation
              WHERE ItemID = ?", // complete
              [
                $_item
              ]
            ));

            if ( $isItemHasLocation === false ) {

              _d("item has location = false");

              // update location = finish goods
              $updateLocationPalletTable = sqlsrv_query(
                $conn,
                "UPDATE PalletTable 
                SET location_id = ?
                WHERE pallet_no = ?",
                [
                  7, // finish good
                  $pallet_no
                ]
              );
  
              if ( !$updateLocationPalletTable ) {
                \sqlsrv_rollback($conn);
                return [
                  'result' => false,
                  'message' => 'update location pallet table failed!'
                ];
              }

              $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT 
                  PL.barcode,
                  IT.ItemID as item_id,
                  IT.Batch as batch,
                  IT.DisposalID as disposal_id,
                  IT.WarehouseID as warehouse_id,
                  IT.LocationID as location_id,
                  IT.Unit as unit
                  FROM PalletLine PL
                  LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                  WHERE PL.pallet_no = ?",
                [
                  $pallet_no
                ]
              ));
  
              if ( count($allBarcodeInPallet) > 0 ) {
                foreach ($allBarcodeInPallet as $v) {
                  // update invent trans move out
                  $inventTransMoveOut = sqlsrv_query(
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
                      (new InventoryAPI)->genTransId($v['barcode']) . 1,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      $v['disposal_id'],
                      null,
                      $v['warehouse_id'],
                      $v['location_id'],
                      -1, // qty
                      $v['unit'], // unit id
                      2, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"]
                    ]
                  );
  
                  if ( !$inventTransMoveOut ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move out failed!'
                    ];
                  }
  
                  // update invent trans move in
                  $inventTransMoveIn = sqlsrv_query(
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
                      PalletNo
                    ) VALUES (
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?
                    )",
                    [
                      (new InventoryAPI)->genTransId($v['barcode']) . 2,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      25,
                      null,
                      $v['warehouse_id'],
                      7, // location
                      1, // qty
                      $v['Unit'], // unit id
                      1, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"],
                      $pallet_no
                    ]
                  );
  
                  if ( !$inventTransMoveIn ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move in failed!'
                    ];
                  }

                  // update invent table location = finish goods
                  $updateInventTable = sqlsrv_query(
                    $conn,
                    "UPDATE InventTable
                    SET LocationID = ?
                    WHERE Barcode = ?",
                    [
                      7,
                      $v['barcode']
                    ]
                  );
      
                  if ( !$updateInventTable ) {
                    \sqlsrv_rollback($conn);
                    return [
                      'result' => false,
                      'message' => 'update barcode location!'
                    ];
                  }
                  // end loop
                }
              }
  
            } else {

              _d("item has location = true");
  
              $checkRemainLocation = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT RL.LocationID, RL.ItemID, L.Remain
                FROM ItemReceiveLocation RL
                LEFT JOIN Location L ON L.ID = RL.LocationID
                where L.Remain > 0
                AND RL.ItemID = ?
                ORDER BY RL.ID ASC",
                [
                  $_item
                ]
              ));
  
              if ( count($checkRemainLocation) === 0 ) {

                _d("remain location = 0");
                
                $updateLocationPalletTable = sqlsrv_query(
                  $conn,
                  "UPDATE PalletTable 
                  SET location_id = ?
                  WHERE pallet_no = ?",
                  [
                    7, // finish good
                    $pallet_no
                  ]
                );
  
                if ( !$updateLocationPalletTable ) {
                  \sqlsrv_rollback($conn);
                  return [
                    'result' => false,
                    'message' => 'update location pallet table failed!'
                  ];
                }

                $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                  $conn,
                  "SELECT 
                    PL.barcode,
                    IT.ItemID as item_id,
                    IT.Batch as batch,
                    IT.DisposalID as disposal_id,
                    IT.WarehouseID as warehouse_id,
                    IT.LocationID as location_id,
                    IT.Unit as unit
                    FROM PalletLine PL
                    LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                    WHERE PL.pallet_no = ?",
                  [
                    $pallet_no
                  ]
                ));
    
                if ( count($allBarcodeInPallet) > 0 ) {
                  foreach ($allBarcodeInPallet as $v) {
                    // update invent trans move out
                    $inventTransMoveOut = sqlsrv_query(
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
                        (new InventoryAPI)->genTransId($v['barcode']) . 1,
                        $v['barcode'],
                        $v['item_id'],
                        $v['batch'],
                        $v['disposal_id'],
                        null,
                        $v['warehouse_id'],
                        $v['location_id'],
                        -1, // qty
                        $v['unit'], // unit id
                        2, // docs type
                        $_SESSION["user_company"],
                        $_SESSION["user_login"],
                        date('Y-m-d H:i:s'),
                        $_SESSION["Shift"]
                      ]
                    );
    
                    if ( !$inventTransMoveOut ) {
                      \sqlsrv_rollback($conn);
                      
                      return [
                        'result' => false,
                        'message' => 'insert invent trans move out failed!'
                      ];
                    }
    
                    // update invent trans move in
                    $inventTransMoveIn = sqlsrv_query(
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
                        PalletNo
                      ) VALUES (
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?
                      )",
                      [
                        (new InventoryAPI)->genTransId($v['barcode']) . 2,
                        $v['barcode'],
                        $v['item_id'],
                        $v['batch'],
                        25,
                        null,
                        $v['warehouse_id'],
                        7, // location
                        1, // qty
                        $v['Unit'], // unit id
                        1, // docs type
                        $_SESSION["user_company"],
                        $_SESSION["user_login"],
                        date('Y-m-d H:i:s'),
                        $_SESSION["Shift"],
                        $pallet_no
                      ]
                    );
    
                    if ( !$inventTransMoveIn ) {
                      \sqlsrv_rollback($conn);
                      
                      return [
                        'result' => false,
                        'message' => 'insert invent trans move in failed!'
                      ];
                    }

                    // update invent table location = finish goods
                    $updateInventTable = sqlsrv_query(
                      $conn,
                      "UPDATE InventTable
                      SET LocationID = ?
                      WHERE Barcode = ?",
                      [
                        7,
                        $v['barcode']
                      ]
                    );
        
                    if ( !$updateInventTable ) {
                      \sqlsrv_rollback($conn);
                      return [
                        'result' => false,
                        'message' => 'update barcode location!'
                      ];
                    }
                    // end loop
                  }
                }
  
              } else {

                _d("remain location != 0");
                
                // update location
                $updateLocationPalletTable = sqlsrv_query(
                  $conn,
                  "UPDATE PalletTable 
                  SET location_id = ?
                  WHERE pallet_no = ?",
                  [
                    $checkRemainLocation[0]['LocationID'],
                    $pallet_no
                  ]
                );
  
                if ( !$updateLocationPalletTable ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update location pallet table failed!'
                  ];
                }

                _d("loop update invent trans");

                $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                  $conn,
                  "SELECT 
                    PL.barcode,
                    IT.ItemID as item_id,
                    IT.Batch as batch,
                    IT.DisposalID as disposal_id,
                    IT.WarehouseID as warehouse_id,
                    IT.LocationID as location_id,
                    IT.Unit as unit
                    FROM PalletLine PL
                    LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                    WHERE PL.pallet_no = ?",
                  [
                    $pallet_no
                  ]
                ));
    
                if ( count($allBarcodeInPallet) > 0 ) {
                  foreach ($allBarcodeInPallet as $v) {

                    // update invent trans move out
                    $inventTransMoveOut = sqlsrv_query(
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
                        (new InventoryAPI)->genTransId($v['barcode']) . 1,
                        $v['barcode'],
                        $v['item_id'],
                        $v['batch'],
                        $v['disposal_id'],
                        null,
                        $v['warehouse_id'],
                        $v['location_id'],
                        -1, // qty
                        $v['unit'], // unit id
                        2, // docs type
                        $_SESSION["user_company"],
                        $_SESSION["user_login"],
                        date('Y-m-d H:i:s'),
                        $_SESSION["Shift"]
                      ]
                    );
    
                    if ( !$inventTransMoveOut ) {
                      \sqlsrv_rollback($conn);
                      
                      return [
                        'result' => false,
                        'message' => 'insert invent trans move out failed!'
                      ];
                    }
    
                    // update invent trans move in
                    $inventTransMoveIn = sqlsrv_query(
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
                        PalletNo
                      ) VALUES (
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?
                      )",
                      [
                        (new InventoryAPI)->genTransId($v['barcode']) . 2,
                        $v['barcode'],
                        $v['item_id'],
                        $v['batch'],
                        25,
                        null,
                        $v['warehouse_id'],
                        $checkRemainLocation[0]['LocationID'], // location
                        1, // qty
                        $v['Unit'], // unit id
                        1, // docs type
                        $_SESSION["user_company"],
                        $_SESSION["user_login"],
                        date('Y-m-d H:i:s'),
                        $_SESSION["Shift"],
                        $pallet_no
                      ]
                    );
    
                    if ( !$inventTransMoveIn ) {
                      \sqlsrv_rollback($conn);
                      
                      return [
                        'result' => false,
                        'message' => 'insert invent trans move in failed!'
                      ];
                    }

                    $updateInventTable = sqlsrv_query(
                      $conn,
                      "UPDATE InventTable
                      SET LocationID = ?
                      WHERE Barcode = ?",
                      [
                        $checkRemainLocation[0]['LocationID'],
                        $v['barcode']
                      ]
                    );

                    _d($v['barcode'] . ", location = " . $checkRemainLocation[0]['LocationID']);
        
                    if ( !$updateInventTable ) {
                      \sqlsrv_rollback($conn);
                      return [
                        'result' => false,
                        'message' => 'update barcode location!'
                      ];
                    }

                    // end loop
                  }
                }
  
                // update remain at location table 
                $updateRemainLocation = sqlsrv_query(
                  $conn,
                  "UPDATE Location 
                  SET Remain -= 1,
                  QtyInUse += 1
                  WHERE ID = ?",
                  [
                    $checkRemainLocation[0]['LocationID']
                  ]
                );
  
                if ( !$updateRemainLocation ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update remain location failed!'
                  ];
                }
  
              }
  
            }

          } else {
            _d("location remain != 0");
            // Location Remain ยังไม่ได้เป็น 0

            // update pallet table Location ตาม table Location
            $updateLocationPalletTable = sqlsrv_query(
              $conn,
              "UPDATE PalletTable 
              SET location_id = ?
              WHERE pallet_no = ?",
              [
                $palletFriend[0]['LocationID'],
                $pallet_no
              ]
            );

            if ( !$updateLocationPalletTable ) {
              \sqlsrv_rollback($conn);
              
              return [
                'result' => false,
                'message' => 'update location pallet table failed!'
              ];
            }

            // update Location -1
            $updateRemainLocation = sqlsrv_query(
              $conn,
              "UPDATE Location 
              SET Remain -= 1,
              QtyInUse += 1
              WHERE ID = ?",
              [
                $palletFriend[0]['LocationID']
              ]
            );

            if ( !$updateRemainLocation ) {
              \sqlsrv_rollback($conn);
              
              return [
                'result' => false,
                'message' => 'update remain location failed!'
              ];
            }

            $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT 
                PL.barcode,
                IT.ItemID as item_id,
                IT.Batch as batch,
                IT.DisposalID as disposal_id,
                IT.WarehouseID as warehouse_id,
                IT.LocationID as location_id,
                IT.Unit as unit
                FROM PalletLine PL
                LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                WHERE PL.pallet_no = ?",
              [
                $pallet_no
              ]
            ));

            if ( count($allBarcodeInPallet) > 0 ) {
              foreach ($allBarcodeInPallet as $v) {

                // update invent trans move out
                $inventTransMoveOut = sqlsrv_query(
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
                    (new InventoryAPI)->genTransId($v['barcode']) . 1,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    $v['disposal_id'],
                    null,
                    $v['warehouse_id'],
                    $v['location_id'],
                    -1, // qty
                    $v['unit'], // unit id
                    2, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"]
                  ]
                );

                if ( !$inventTransMoveOut ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move out failed!'
                  ];
                }

                // update invent trans move in ...
                $inventTransMoveIn = sqlsrv_query(
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
                    PalletNo
                  ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?
                  )",
                  [
                    (new InventoryAPI)->genTransId($v['barcode']) . 2,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    25,
                    null,
                    $v['warehouse_id'],
                    $palletFriend[0]['LocationID'], // location
                    1, // qty
                    $v['unit'], // unit id
                    1, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"],
                    $pallet_no
                  ]
                );

                if ( !$inventTransMoveIn ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move in failed!'
                  ];
                }

                // update invent table Location ตาม table Location
                $updateInventTable = sqlsrv_query(
                  $conn,
                  "UPDATE InventTable
                  SET LocationID = ?
                  WHERE Barcode = ?",
                  [
                    $palletFriend[0]['LocationID'],
                    $v['barcode']
                  ]
                );

                _d($v['barcode'] . " location = " . $palletFriend[0]['LocationID']);

                if ( !$updateInventTable ) {
                  \sqlsrv_rollback($conn);
                  return [
                    'result' => false,
                    'message' => 'update barcode location!'
                  ];
                }
                // end loop
              }
            }

          }

        }
      }
    } else {
      
      //ถ้าเช็คแล้ว remain ของ pallet table !== 0
      _d("pallet table remain != 0");

      //update invent trans move out
      $inventTransMoveOut = sqlsrv_query(
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

      if ( !$inventTransMoveOut ) {
        \sqlsrv_rollback($conn);
        
        return [
          'result' => false,
          'message' => 'insert invent trans move out failed!'
        ];
      }

      // update invent trans move in
      $inventTransMoveIn = sqlsrv_query(
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
          PalletNo
        ) VALUES (
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?
        )",
        [
          (new InventoryAPI)->genTransId($barcode) . 2,
          $barcode,
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          25,
          null,
          $barcodeInfo[0]['WarehouseID'],
          $barcodeInfo[0]['LocationID'],
          1, // qty
          $barcodeInfo[0]['Unit'], // unit id
          1, // docs type
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          date('Y-m-d H:i:s'),
          $_SESSION["Shift"],
          $pallet_no
        ]
      );

      if ( !$inventTransMoveIn ) {
        \sqlsrv_rollback($conn);
        
        return [
          'result' => false,
          'message' => 'insert invent trans move in failed!'
        ];
      }
    }

    // commit all query
    // \sqlsrv_rollback($conn);
    \sqlsrv_commit($conn);
    
    return [
      'result' => true,
      'message' => 'pallet receive successful!',
      'extra' => [
        'batch' => $barcodeInfo[0]['Batch'],
        'curing_code' => $barcodeInfo[0]['CuringCode'],
        'barcode' => $barcodeInfo[0]['Barcode']
      ]
    ]; 

  }

  public function getQtyPerPallet($itemId) {
    
    $conn = (new Connector)->dbConnect();
    
    $qtyPerPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
      $conn,
      "SELECT IM.QtyPerPallet 
      FROM ItemMaster IM
      WHERE IM.ID = ?",
      [
        $itemId
      ]
    ));

    

    if ( count($qtyPerPallet) === 0 ) {
      return [
        'result' => false,
        'message' => 'Item not found.',
        'data' => 0
      ];
    } else {
      return [
        'result' => true,
        'message' => 'Success',
        'data' => $qtyPerPallet[0]['QtyPerPallet']
      ];
    }

  }

  public function isPalletExistsInPalletTable($pallet_no) {
    $conn = (new Connector)->dbConnect();

    $data = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT pallet_no 
      FROM PalletTable 
      WHERE pallet_no = ?
      AND [status] <> 5",
      [
        $pallet_no
      ]
    ));

    
    return $data;
  }

  public function getPalletTable() {
    $conn = (new Connector)->dbConnect();
    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT 
      L.pallet_no,
      L.item_id,
      IM.NameTH[item_name],
      L.batch_no,
      LO.ID [location_id],
      LO.[Description] [location],
      L.qty_per_pallet,
      L.qty_in_use,
      L.Remain [remain],
      S.[Description] [status],
      L.Company [company],
      U.Name [create_by],
      L.create_date,
      UU.Name [update_by],
      L.update_date,
      UUU.Name [complete_by],
      L.complete_date
      FROM PalletTable L
      LEFT JOIN Location LO ON L.location_id = LO.ID
      LEFT JOIN [Status] S ON S.ID = L.[Status]
      LEFT JOIN ItemMaster IM ON IM.ID = L.item_id
      LEFT JOIN UserMaster U ON U.ID = L.create_by AND L.create_by is not null
      LEFT JOIN UserMaster UU ON UU.ID = L.update_by AND L.update_by is not null
      LEFT JOIN UserMaster UUU ON UUU.ID = L.complete_by AND L.complete_date is not null"
    );

    

    if (count($data) === 0) {
      return [];
    }
    return $data;
  }

  public function getPalletLine($pallet_no) {
    $conn = (new Connector)->dbConnect();
    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT L.barcode, U.Name as username, L.create_date FROM PalletLine L
      LEFT JOIN UserMaster U ON U.ID = L.create_by
      WHERE L.pallet_no = ?
      AND L.Company = ?",
      [
        $pallet_no,
        $_SESSION['user_company']
      ]
    );
    
    return $data;
  }

  public function getLocationDataByItem($item_id) {
    $conn = (new Connector)->dbConnect();
    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT 
      IL.ItemID as item_id, 
      IL.LocationID as location_id, 
      L.QTY as qty_per_pallet, 
      L.QTYInUse as qty_in_use, 
      L.Remain as remain
      FROM ItemReceiveLocation IL
      left join Location L ON IL.LocationID = L.ID
      WHERE IL.ItemID = ?",
      [
        $item_id
      ]
    );

    

    if (count($data) === 0) {
      return [
        'result' => false,
        'data' => []
      ];
    } 
    return [
      'result' => true,
      'data' => $data
    ];
  }

  public function isPalletComplete($pallet_no) {
    $conn = (new Connector)->dbConnect();
    $data = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT pallet_no 
      FROM PalletTable
      WHERE pallet_no = ?
      AND [status] = 3",
      [
        $pallet_no
      ]
    ));
    
    return $data;
  }

  public function palletComplete($pallet_no) {

    _d();

    $conn = (new Connector)->dbConnect();

    if (\sqlsrv_begin_transaction($conn) === false) {
      return [
        'result' => false,
        'message' => 'begin transaction failed!'
      ];
    }

    $isPalletComplete = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT pallet_no 
      FROM PalletTable
      WHERE pallet_no = ?
      AND [status] = 3",
      [
        $pallet_no
      ]
    ));

    if ( $isPalletComplete === true ) {
      return [
        'result' => false,
        'message' => 'pallet already complete!'
      ];
    }

    $complete = sqlsrv_query(
      $conn,
      "UPDATE PalletTable
      SET [status] = 3,
      complete_by = ?,
      complete_date = ?
      WHERE pallet_no = ?
      AND [status] NOT IN (3,4,5)
      AND [status] = 2",
      [
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $pallet_no
      ]
    );

    if ($complete) {

      $palletData = (new SqlsrvHelper)->getRow(sqlsrv_query(
        $conn,
        "SELECT item_id 
        FROM PalletTable
        WHERE pallet_no = ?",
        [
          $pallet_no
        ]
      ));

      $hasPalletFriend = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT item_id
        FROM PalletTable
        WHERE item_id = ?
        AND [status] = 3
        AND pallet_no <> ?
        ORDER BY id asc", // complete
        [
          $palletData['item_id'],
          $pallet_no
        ]
      ));

      if ($hasPalletFriend === false) {

        _d("pallet haven't any friends.");

        $isItemHasLocation = sqlsrv_has_rows(sqlsrv_query(
          $conn,
          "SELECT ItemID
          FROM ItemReceiveLocation
          WHERE ItemID = ?", // complete
          [
            $palletData['item_id']
          ]
        ));

        if ( $isItemHasLocation === false ) {

          _d("item haven't any location");

          // update location = 7 finish goods
          $updateLocationPalletTable = sqlsrv_query(
            $conn,
            "UPDATE PalletTable 
            SET location_id = ?
            WHERE pallet_no = ?",
            [
              7, // finish good
              $pallet_no
            ]
          );

          if ( !$updateLocationPalletTable ) {
            \sqlsrv_rollback($conn);
            return [
              'result' => false,
              'message' => 'update location pallet table failed!'
            ];
          }

          // loop update invent trans
          $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
            $conn,
            "SELECT 
              PL.barcode,
              IT.ItemID as item_id,
              IT.Batch as batch,
              IT.DisposalID as disposal_id,
              IT.WarehouseID as warehouse_id,
              IT.LocationID as location_id,
              IT.Unit as unit
              FROM PalletLine PL
              LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
              WHERE PL.pallet_no = ?",
            [
              $pallet_no
            ]
          ));

          if ( count($allBarcodeInPallet) > 0 ) {
            foreach ($allBarcodeInPallet as $v) {
              // update invent trans move out
              $inventTransMoveOut = sqlsrv_query(
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
                  (new InventoryAPI)->genTransId($v['barcode']) . 1,
                  $v['barcode'],
                  $v['item_id'],
                  $v['batch'],
                  $v['disposal_id'],
                  null,
                  $v['warehouse_id'],
                  $v['location_id'],
                  -1, // qty
                  $v['unit'], // unit id
                  2, // docs type
                  $_SESSION["user_company"],
                  $_SESSION["user_login"],
                  date('Y-m-d H:i:s'),
                  $_SESSION["Shift"]
                ]
              );

              if ( !$inventTransMoveOut ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'insert invent trans move out failed!'
                ];
              }

              // update invent trans move in
              $inventTransMoveIn = sqlsrv_query(
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
                  PalletNo
                ) VALUES (
                  ?, ?, ?, ?, ?,
                  ?, ?, ?, ?, ?,
                  ?, ?, ?, ?, ?,
                  ?
                )",
                [
                  (new InventoryAPI)->genTransId($v['barcode']) . 2,
                  $v['barcode'],
                  $v['item_id'],
                  $v['batch'],
                  25,
                  null,
                  $v['warehouse_id'],
                  7, // location finish goods
                  1, // qty
                  $v['unit'], // unit id
                  1, // docs type
                  $_SESSION["user_company"],
                  $_SESSION["user_login"],
                  date('Y-m-d H:i:s'),
                  $_SESSION["Shift"],
                  $pallet_no
                ]
              );

              if ( !$inventTransMoveIn ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'insert invent trans move in failed!'
                ];
              }

              // update invent table location = finish goods
              $updateInventTable = sqlsrv_query(
                $conn,
                "UPDATE InventTable
                SET LocationID = ?
                WHERE Barcode = ?",
                [
                  7,
                  $v['barcode']
                ]
              );

              if ( !$updateInventTable ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update invent table failed!'
                ];
              }
              // end loop

            }
          }

        } else {

          _d("item have location");

          $checkRemainLocation = (new SqlsrvHelper)->getRows(sqlsrv_query(
            $conn,
            "SELECT RL.LocationID, RL.ItemID, L.Remain
            FROM ItemReceiveLocation RL
            LEFT JOIN Location L ON L.ID = RL.LocationID
            where L.Remain <> 0
            ORDER BY RL.ID ASC"
          ));

          if ( count($checkRemainLocation) === 0 ) {

            _d("remain location = 0");
            
            // update location = 7 finish goods
            $updateLocationPalletTable = sqlsrv_query(
              $conn,
              "UPDATE PalletTable 
              SET location_id = ?
              WHERE pallet_no = ?",
              [
                7, // finish good
                $pallet_no
              ]
            );

            if ( !$updateLocationPalletTable ) {
              \sqlsrv_rollback($conn);
              return [
                'result' => false,
                'message' => 'update location pallet table failed!'
              ];
            }

            // loop update invent trans
            $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT 
                PL.barcode,
                IT.ItemID as item_id,
                IT.Batch as batch,
                IT.DisposalID as disposal_id,
                IT.WarehouseID as warehouse_id,
                IT.LocationID as location_id,
                IT.Unit as unit
                FROM PalletLine PL
                LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                WHERE PL.pallet_no = ?",
              [
                $pallet_no
              ]
            ));

            if ( count($allBarcodeInPallet) > 0 ) {
              foreach ($allBarcodeInPallet as $v) {
                // update invent trans move out
                $inventTransMoveOut = sqlsrv_query(
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
                    (new InventoryAPI)->genTransId($v['barcode']) . 1,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    $v['disposal_id'],
                    null,
                    $v['warehouse_id'],
                    $v['location_id'],
                    -1, // qty
                    $v['unit'], // unit id
                    2, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"]
                  ]
                );

                if ( !$inventTransMoveOut ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move out failed!'
                  ];
                }

                // update invent trans move in
                $inventTransMoveIn = sqlsrv_query(
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
                    PalletNo
                  ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?
                  )",
                  [
                    (new InventoryAPI)->genTransId($v['barcode']) . 2,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    25,
                    null,
                    $v['warehouse_id'],
                    7, // location finish goods
                    1, // qty
                    $v['unit'], // unit id
                    1, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"],
                    $pallet_no
                  ]
                );

                if ( !$inventTransMoveIn ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move in failed!'
                  ];
                }

                // update invent table location = finish goods
                $updateInventTable = sqlsrv_query(
                  $conn,
                  "UPDATE InventTable
                  SET LocationID = ?
                  WHERE Barcode = ?",
                  [
                    7,
                    $v['barcode']
                  ]
                );

                if ( !$updateInventTable ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update invent table failed!'
                  ];
                }
                // end loop

              }
            }

          } else {

            _d("remain location > 0");

            // update location
            $updateLocationPalletTable = sqlsrv_query(
              $conn,
              "UPDATE PalletTable 
              SET location_id = ?
              WHERE pallet_no = ?",
              [
                $checkRemainLocation[0]['LocationID'], // finish good
                $pallet_no
              ]
            );

            if ( !$updateLocationPalletTable ) {
              \sqlsrv_rollback($conn);
              
              return [
                'result' => false,
                'message' => 'update location pallet table failed!'
              ];
            }

            // update remain at location table 
            $updateRemainLocation = sqlsrv_query(
              $conn,
              "UPDATE Location 
              SET Remain -= 1,
              QtyInUse += 1
              WHERE ID = ?",
              [
                $checkRemainLocation[0]['LocationID']
              ]
            );

            if ( !$updateRemainLocation ) {
              \sqlsrv_rollback($conn);
              
              return [
                'result' => false,
                'message' => 'update remain location failed!'
              ];
            }

            // loop update invent trans
            $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT 
                PL.barcode,
                IT.ItemID as item_id,
                IT.Batch as batch,
                IT.DisposalID as disposal_id,
                IT.WarehouseID as warehouse_id,
                IT.LocationID as location_id,
                IT.Unit as unit
                FROM PalletLine PL
                LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                WHERE PL.pallet_no = ?",
              [
                $pallet_no
              ]
            ));

            if ( count($allBarcodeInPallet) > 0 ) {
              foreach ($allBarcodeInPallet as $v) {
                // update invent trans move out
                $inventTransMoveOut = sqlsrv_query(
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
                    (new InventoryAPI)->genTransId($v['barcode']) . 1,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    $v['disposal_id'],
                    null,
                    $v['warehouse_id'],
                    $v['location_id'],
                    -1, // qty
                    $v['unit'], // unit id
                    2, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"]
                  ]
                );

                if ( !$inventTransMoveOut ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move out failed!'
                  ];
                }

                // update invent trans move in
                $inventTransMoveIn = sqlsrv_query(
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
                    PalletNo
                  ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?
                  )",
                  [
                    (new InventoryAPI)->genTransId($v['barcode']) . 2,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    25,
                    null,
                    $v['warehouse_id'],
                    $checkRemainLocation[0]['LocationID'], // location finish goods
                    1, // qty
                    $v['unit'], // unit id
                    1, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"],
                    $pallet_no
                  ]
                );

                if ( !$inventTransMoveIn ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move in failed!'
                  ];
                }

                // update invent table location = finish goods
                $updateInventTable = sqlsrv_query(
                  $conn,
                  "UPDATE InventTable
                  SET LocationID = ?
                  WHERE Barcode = ?",
                  [
                    $checkRemainLocation[0]['LocationID'],
                    $v['barcode']
                  ]
                );

                if ( !$updateInventTable ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update invent table failed!'
                  ];
                }
                // end loop

              }
            }

          }

        }

      } else {

        _d("pallet has friends");

        $palletFriend = (new SqlsrvHelper)->getRows(sqlsrv_query(
          $conn,
          "SELECT 
          PT.pallet_no, 
          PT.item_id , 
          L.Remain, 
          RL.LocationID
          FROM PalletTable PT
          LEFT JOIN ItemReceiveLocation RL ON RL.LocationID = PT.location_id
          LEFT JOIN Location L ON L.ID = RL.LocationID AND L.Remain <> 0
          WHERE PT.pallet_no <> ?
          AND L.Remain <> 0
          AND PT.[status] = 3
          GROUP BY 
          PT.pallet_no, 
          PT.item_id, 
          L.Remain, 
          RL.LocationID",
          [
            $pallet_no
          ]
        ));

        if ( count($palletFriend) === 0 ) {

          _d("pallet friends remain = 0");

          $isItemHasLocation = sqlsrv_has_rows(sqlsrv_query(
            $conn,
            "SELECT ItemID
            FROM ItemReceiveLocation
            WHERE ItemID = ?", // complete
            [
              $palletData['item_id']
            ]
          ));

          if ($isItemHasLocation === false) {
            _d("item has location = false, item = " . $palletData['item_id']);

            // update location = 7 finish goods
            $updateLocationPalletTable = sqlsrv_query(
              $conn,
              "UPDATE PalletTable 
              SET location_id = ?
              WHERE pallet_no = ?",
              [
                7, // finish good
                $pallet_no
              ]
            );

            if ( !$updateLocationPalletTable ) {
              \sqlsrv_rollback($conn);
              
              return [
                'result' => false,
                'message' => 'update location pallet table failed!'
              ];
            }

            // loop update invent trans
            $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT 
                PL.barcode,
                IT.ItemID as item_id,
                IT.Batch as batch,
                IT.DisposalID as disposal_id,
                IT.WarehouseID as warehouse_id,
                IT.LocationID as location_id,
                IT.Unit as unit
                FROM PalletLine PL
                LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                WHERE PL.pallet_no = ?",
              [
                $pallet_no
              ]
            ));

            if ( count($allBarcodeInPallet) > 0 ) {
              foreach ($allBarcodeInPallet as $v) {
                // update invent trans move out
                $inventTransMoveOut = sqlsrv_query(
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
                    (new InventoryAPI)->genTransId($v['barcode']) . 1,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    $v['disposal_id'],
                    null,
                    $v['warehouse_id'],
                    $v['location_id'],
                    -1, // qty
                    $v['unit'], // unit id
                    2, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"]
                  ]
                );

                if ( !$inventTransMoveOut ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move out failed!'
                  ];
                }

                // update invent trans move in
                $inventTransMoveIn = sqlsrv_query(
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
                    PalletNo
                  ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?
                  )",
                  [
                    (new InventoryAPI)->genTransId($v['barcode']) . 2,
                    $v['barcode'],
                    $v['item_id'],
                    $v['batch'],
                    25,
                    null,
                    $v['warehouse_id'],
                    7, // location finish goods
                    1, // qty
                    $v['unit'], // unit id
                    1, // docs type
                    $_SESSION["user_company"],
                    $_SESSION["user_login"],
                    date('Y-m-d H:i:s'),
                    $_SESSION["Shift"],
                    $pallet_no
                  ]
                );

                if ( !$inventTransMoveIn ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'insert invent trans move in failed!'
                  ];
                }

                // update invent table location = finish goods
                $updateInventTable = sqlsrv_query(
                  $conn,
                  "UPDATE InventTable
                  SET LocationID = ?
                  WHERE Barcode = ?",
                  [
                    7,
                    $v['barcode']
                  ]
                );

                if ( !$updateInventTable ) {
                  \sqlsrv_rollback($conn);
                  
                  return [
                    'result' => false,
                    'message' => 'update invent table failed!'
                  ];
                }
                // end loop

              }
            }
          } else {
            _d("item has location = true");
  
            $checkRemainLocation = (new SqlsrvHelper)->getRows(sqlsrv_query(
              $conn,
              "SELECT RL.LocationID, RL.ItemID, L.Remain
              FROM ItemReceiveLocation RL
              LEFT JOIN Location L ON L.ID = RL.LocationID
              where L.Remain > 0
              AND RL.ItemID = ?
              ORDER BY RL.ID ASC",
              [
                $palletData['item_id']
              ]
            ));

            if ( count($checkRemainLocation) === 0 ) {

              _d("remain location = 0");
              
              $updateLocationPalletTable = sqlsrv_query(
                $conn,
                "UPDATE PalletTable 
                SET location_id = ?
                WHERE pallet_no = ?",
                [
                  7, // finish good
                  $pallet_no
                ]
              );

              if ( !$updateLocationPalletTable ) {
                \sqlsrv_rollback($conn);
                return [
                  'result' => false,
                  'message' => 'update location pallet table failed!'
                ];
              }

              $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT 
                  PL.barcode,
                  IT.ItemID as item_id,
                  IT.Batch as batch,
                  IT.DisposalID as disposal_id,
                  IT.WarehouseID as warehouse_id,
                  IT.LocationID as location_id,
                  IT.Unit as unit
                  FROM PalletLine PL
                  LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                  WHERE PL.pallet_no = ?",
                [
                  $pallet_no
                ]
              ));
  
              if ( count($allBarcodeInPallet) > 0 ) {
                foreach ($allBarcodeInPallet as $v) {
                  // update invent trans move out
                  $inventTransMoveOut = sqlsrv_query(
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
                      (new InventoryAPI)->genTransId($v['barcode']) . 1,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      $v['disposal_id'],
                      null,
                      $v['warehouse_id'],
                      $v['location_id'],
                      -1, // qty
                      $v['unit'], // unit id
                      2, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"]
                    ]
                  );
  
                  if ( !$inventTransMoveOut ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move out failed!'
                    ];
                  }
  
                  // update invent trans move in
                  $inventTransMoveIn = sqlsrv_query(
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
                      PalletNo
                    ) VALUES (
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?
                    )",
                    [
                      (new InventoryAPI)->genTransId($v['barcode']) . 2,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      25,
                      null,
                      $v['warehouse_id'],
                      7, // location
                      1, // qty
                      $v['Unit'], // unit id
                      1, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"],
                      $pallet_no
                    ]
                  );
  
                  if ( !$inventTransMoveIn ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move in failed!'
                    ];
                  }

                  // update invent table location = finish goods
                  $updateInventTable = sqlsrv_query(
                    $conn,
                    "UPDATE InventTable
                    SET LocationID = ?
                    WHERE Barcode = ?",
                    [
                      7,
                      $v['barcode']
                    ]
                  );
      
                  if ( !$updateInventTable ) {
                    \sqlsrv_rollback($conn);
                    return [
                      'result' => false,
                      'message' => 'update barcode location!'
                    ];
                  }
                  // end loop
                }
              }

            } else {

              _d("remain location != 0");
              
              // update location
              $updateLocationPalletTable = sqlsrv_query(
                $conn,
                "UPDATE PalletTable 
                SET location_id = ?
                WHERE pallet_no = ?",
                [
                  $checkRemainLocation[0]['LocationID'],
                  $pallet_no
                ]
              );

              if ( !$updateLocationPalletTable ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update location pallet table failed!'
                ];
              }

              _d("loop update invent trans");

              $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
                $conn,
                "SELECT 
                  PL.barcode,
                  IT.ItemID as item_id,
                  IT.Batch as batch,
                  IT.DisposalID as disposal_id,
                  IT.WarehouseID as warehouse_id,
                  IT.LocationID as location_id,
                  IT.Unit as unit
                  FROM PalletLine PL
                  LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
                  WHERE PL.pallet_no = ?",
                [
                  $pallet_no
                ]
              ));
  
              if ( count($allBarcodeInPallet) > 0 ) {
                foreach ($allBarcodeInPallet as $v) {

                  // update invent trans move out
                  $inventTransMoveOut = sqlsrv_query(
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
                      (new InventoryAPI)->genTransId($v['barcode']) . 1,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      $v['disposal_id'],
                      null,
                      $v['warehouse_id'],
                      $v['location_id'],
                      -1, // qty
                      $v['unit'], // unit id
                      2, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"]
                    ]
                  );
  
                  if ( !$inventTransMoveOut ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move out failed!'
                    ];
                  }
  
                  // update invent trans move in
                  $inventTransMoveIn = sqlsrv_query(
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
                      PalletNo
                    ) VALUES (
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?, ?, ?, ?, ?,
                      ?
                    )",
                    [
                      (new InventoryAPI)->genTransId($v['barcode']) . 2,
                      $v['barcode'],
                      $v['item_id'],
                      $v['batch'],
                      25,
                      null,
                      $v['warehouse_id'],
                      $checkRemainLocation[0]['LocationID'], // location
                      1, // qty
                      $v['Unit'], // unit id
                      1, // docs type
                      $_SESSION["user_company"],
                      $_SESSION["user_login"],
                      date('Y-m-d H:i:s'),
                      $_SESSION["Shift"],
                      $pallet_no
                    ]
                  );
  
                  if ( !$inventTransMoveIn ) {
                    \sqlsrv_rollback($conn);
                    
                    return [
                      'result' => false,
                      'message' => 'insert invent trans move in failed!'
                    ];
                  }

                  $updateInventTable = sqlsrv_query(
                    $conn,
                    "UPDATE InventTable
                    SET LocationID = ?
                    WHERE Barcode = ?",
                    [
                      $checkRemainLocation[0]['LocationID'],
                      $v['barcode']
                    ]
                  );

                  _d($v['barcode'] . ", location = " . $checkRemainLocation[0]['LocationID']);
      
                  if ( !$updateInventTable ) {
                    \sqlsrv_rollback($conn);
                    return [
                      'result' => false,
                      'message' => 'update barcode location!'
                    ];
                  }

                  // end loop
                }
              }

              // update remain at location table 
              $updateRemainLocation = sqlsrv_query(
                $conn,
                "UPDATE Location 
                SET Remain -= 1,
                QtyInUse += 1
                WHERE ID = ?",
                [
                  $checkRemainLocation[0]['LocationID']
                ]
              );

              if ( !$updateRemainLocation ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update remain location failed!'
                ];
              }

            }
          }

        } else {

          _d("pallet friends remain > 0");
          
          // update location = 7 finish goods
          $updateLocationPalletTable = sqlsrv_query(
            $conn,
            "UPDATE PalletTable 
            SET location_id = ?
            WHERE pallet_no = ?",
            [
              $palletFriend[0]['LocationID'], // finish good
              $pallet_no
            ]
          );

          if ( !$updateLocationPalletTable ) {
            \sqlsrv_rollback($conn);
            
            return [
              'result' => false,
              'message' => 'update location pallet table failed!'
            ];
          }

          // update remain at location table 
          $updateRemainLocation = sqlsrv_query(
            $conn,
            "UPDATE Location 
            SET Remain -= 1,
            QtyInUse += 1
            WHERE ID = ?",
            [
              $palletFriend[0]['LocationID']
            ]
          );

          if ( !$updateRemainLocation ) {
            \sqlsrv_rollback($conn);
            
            return [
              'result' => false,
              'message' => 'update remain location failed!'
            ];
          }

          // loop update invent trans
          $allBarcodeInPallet = (new SqlsrvHelper)->getRows(sqlsrv_query(
            $conn,
            "SELECT 
              PL.barcode,
              IT.ItemID as item_id,
              IT.Batch as batch,
              IT.DisposalID as disposal_id,
              IT.WarehouseID as warehouse_id,
              IT.LocationID as location_id,
              IT.Unit as unit
              FROM PalletLine PL
              LEFT JOIN InventTable IT ON IT.Barcode = PL.barcode 
              WHERE PL.pallet_no = ?",
            [
              $pallet_no
            ]
          ));

          if ( count($allBarcodeInPallet) > 0 ) {
            foreach ($allBarcodeInPallet as $v) {
              // update invent trans move out
              $inventTransMoveOut = sqlsrv_query(
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
                  (new InventoryAPI)->genTransId($v['barcode']) . 1,
                  $v['barcode'],
                  $v['item_id'],
                  $v['batch'],
                  $v['disposal_id'],
                  null,
                  $v['warehouse_id'],
                  $v['location_id'],
                  -1, // qty
                  $v['unit'], // unit id
                  2, // docs type
                  $_SESSION["user_company"],
                  $_SESSION["user_login"],
                  date('Y-m-d H:i:s'),
                  $_SESSION["Shift"]
                ]
              );

              if ( !$inventTransMoveOut ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'insert invent trans move out failed!'
                ];
              }

              // update invent trans move in
              $inventTransMoveIn = sqlsrv_query(
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
                  PalletNo
                ) VALUES (
                  ?, ?, ?, ?, ?,
                  ?, ?, ?, ?, ?,
                  ?, ?, ?, ?, ?,
                  ?
                )",
                [
                  (new InventoryAPI)->genTransId($v['barcode']) . 2,
                  $v['barcode'],
                  $v['item_id'],
                  $v['batch'],
                  25,
                  null,
                  $v['warehouse_id'],
                  $palletFriend[0]['LocationID'], // location
                  1, // qty
                  $v['unit'], // unit id
                  1, // docs type
                  $_SESSION["user_company"],
                  $_SESSION["user_login"],
                  date('Y-m-d H:i:s'),
                  $_SESSION["Shift"],
                  $pallet_no
                ]
              );

              if ( !$inventTransMoveIn ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'insert invent trans move in failed!'
                ];
              }

              // update invent table location = finish goods
              $updateInventTable = sqlsrv_query(
                $conn,
                "UPDATE InventTable
                SET LocationID = ?
                WHERE Barcode = ?",
                [
                  $palletFriend[0]['LocationID'],
                  $v['barcode']
                ]
              );

              if ( !$updateInventTable ) {
                \sqlsrv_rollback($conn);
                
                return [
                  'result' => false,
                  'message' => 'update invent table failed!'
                ];
              }
              // end loop

            }
          }

        }

      }
      //  End Check Friend

      _d("pallet completed");

      $LocationByPalletNo = (new SqlsrvHelper)->getRow(sqlsrv_query(
        $conn,
        "SELECT L.Description as [Location] FROM PalletTable P
        LEFT JOIN [Location] L ON L.ID = P.location_id
        WHERE pallet_no = ?",
        [
          $pallet_no
        ]
      ));

      \sqlsrv_commit($conn);
      return [
        'result' => true,
        'message' => 'pallet complete successful!',
        'extra' => [
          'location' => $LocationByPalletNo['Location']
        ]
      ];

    } else {
      _d("complete failed");
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'pallet complete failed!'
      ];;
    }
  }


  public function checkPalletItem($pallet_no, $item) {
    $conn = (new Connector)->dbConnect();
    $data = \sqlsrv_has_rows(\sqlsrv_query(
      $conn,
      "SELECT pallet_item 
      FROM PalletMaster
      WHERE pallet_no = ?
      AND pallet_item = ?
      AND pallet_status = 1",
      [
        $pallet_no,
        $item
      ]
    ));
    
    return $data;
  }

  public function isRemainZero($pallet_no) {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT pallet_no 
      FROM PalletTable 
      WHERE pallet_no = ?
      AND remain <> 0",
      [
        $pallet_no
      ]
    ));
  }

  public function savePalletTransfer($pallet_no, $barcode) {

    _d();

    $conn = (new Connector)->dbConnect();

    if (\sqlsrv_begin_transaction($conn) === false) {
      return [
        'result' => false,
        'message' => 'begin transaction failed!'
      ];
    }

    $checkIsReceived = sqlsrv_has_rows(sqlsrv_query(
      $this->conn,
      "SELECT PalletNo 
      FROM InventTable 
      WHERE Barcode = ?
      AND PalletNo is not null
      AND PalletNo <> '' ",
      [
        $barcode
      ]
    ));

    if ( $checkIsReceived === false ) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'barcode not receive to pallet!'
      ];
    }

    _d('barcode already in pallet');

    _d('get barcode info');
    $barcodeInfo = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $conn,
      "SELECT * FROM InventTable 
      WHERE Barcode = ?",
      [
        $barcode
      ]
    ));

    if ( $barcodeInfo === null ) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'barcode not found!'
      ];
    }

    _d('barcode found.');

    $palletInfo = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $conn,
      "SELECT * FROM PalletTable
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));

    if ($palletInfo === false) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'pallet not found!'
      ];
    }

    _d('pallet found');

    $isItemSame = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $conn,
      "SELECT 
      PM.pallet_item [item_taget], 
      PM2.pallet_item [item_from]
      FROM PalletMaster PM, PalletLine PL
      LEFT JOIN PalletMaster PM2 ON PM2.pallet_no = PL.pallet_no
      WHERE PM.pallet_no = ?
      AND PL.barcode = ?",
      [
        $pallet_no,
        $barcode
      ]
    ));

    if ($isItemSame === null || $isItemSame === false) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'error compare pallet!'
      ];
    }

    if ($isItemSame['item_taget'] !== $isItemSame['item_from']) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'item not same!'
      ];
    }

    $updateOldPalletTable = sqlsrv_query(
      $conn,
      "UPDATE PalletTable 
      SET remain += 1,
      qty_in_use -= 1,
      update_by = ?,
      update_date = ?
      WHERE pallet_no = ?",
      [
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $barcodeInfo['PalletNo']
      ]
    );

    if ( !$updateOldPalletTable ) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'update old pallet table failed!'
      ];
    }

    _d('update old pallet success');

    $updateNewPalletTable = sqlsrv_query(
      $conn,
      "UPDATE PalletTable
      SET qty_in_use += 1,
      remain -= 1,
      update_by = ?,
      update_date = ?
      WHERE pallet_no = ?",
      [
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $pallet_no
      ]
    );

    if ( !$updateNewPalletTable ) {
      \sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'update pallet table failed!'
      ];
    }

    _d('update new pallet success');

    // update pallet line
    $updatePalletLine = sqlsrv_query(
      $conn,
      "UPDATE PalletLine
      SET pallet_no = ?
      WHERE barcode = ?",
      [
        $pallet_no,
        $barcode
      ]
    );

    if ( !$updatePalletLine ) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'update pallet line failed!'
      ];
    }

    _d('update pallet line success');

    _d("check pallet empty => " . $barcodeInfo['PalletNo']);
    
    $isPalletEmpty = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $conn,
      "SELECT qty_in_use
      FROM PalletTable
      WHERE pallet_no = ?
      AND qty_in_use = 0",
      [
        $barcodeInfo['PalletNo']
      ]
    ));

    
    if ($isPalletEmpty !== null && (int)$isPalletEmpty['qty_in_use'] === 0) {

      _d('old pallet info found and qty in use = 0');

      $closePallet = sqlsrv_query(
        $conn,
        "UPDATE PalletTable 
        SET [status] = 5
        WHERE pallet_no = ?",
        [
          $barcodeInfo['PalletNo']
        ]
      ); 

      if ( !$closePallet ) {
        sqlsrv_rollback($conn);
        return [
          'result' => false,
          'message' => 'update pallet table status failed!'
        ];
      }

      _d('close pallet success');

      $updatePalletMaster = sqlsrv_query(
        $conn,
        "UPDATE PalletMaster 
        SET pallet_item = null
        WHERE pallet_no = ?",
        [
          $barcodeInfo['PalletNo']
        ]
      );

      if ( !$updatePalletMaster ) {
        sqlsrv_rollback($conn);
        return [
          'result' => false,
          'message' => 'update pallet master failed!'
        ];
      }

      _d('update pallet master success');

      $updateLocationRemain = sqlsrv_query(
        $conn,
        "UPDATE [Location]
        SET QTYInUse -= 1,
        Remain += 1
        WHERE ID = ?",
        [
          $barcodeInfo['LocationID']
        ]
      );

      if ( !$updateLocationRemain ) {
        sqlsrv_rollback($conn);
        return [
          'result' => false,
          'message' => 'update location failed!'
        ];
      }

      _d('update location remain success');
    }

    // update invent table
    $updateInventTable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET PalletNo = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE Barcode = ?",
      [
        $pallet_no,
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $barcode
      ]
    );

    if ( !$updateInventTable ) {
      sqlsrv_rollback($conn);
      return [
        'result' => false,
        'message' => 'update invent table failed!'
      ];
    }

    _d('update invent table success');

    // update invent trans
    $inventTransMoveOut = sqlsrv_query(
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
        $barcodeInfo['ItemID'],
        $barcodeInfo['Batch'],
        $barcodeInfo['DisposalID'],
        null,
        $barcodeInfo['WarehouseID'],
        $barcodeInfo['LocationID'],
        -1, // qty
        $barcodeInfo['Unit'], // unit id
        2, // docs type
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        date('Y-m-d H:i:s'),
        $_SESSION["Shift"]
      ]
    );

    if ( !$inventTransMoveOut ) {
      \sqlsrv_rollback($conn);
      
      return [
        'result' => false,
        'message' => 'insert invent trans move out failed!'
      ];
    }

    _d('update invent trans move out success');

    $inventTransMoveIn = sqlsrv_query(
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
        PalletNo
      ) VALUES (
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?
      )",
      [
        (new InventoryAPI)->genTransId($barcode) . 2,
        $barcode,
        $barcodeInfo['ItemID'],
        $barcodeInfo['Batch'],
        25,
        null,
        $barcodeInfo['WarehouseID'],
        $barcodeInfo['LocationID'],
        1, // qty
        $barcodeInfo['Unit'], // unit id
        1, // docs type
        $_SESSION["user_company"],
        $_SESSION["user_login"],
        date('Y-m-d H:i:s'),
        $_SESSION["Shift"],
        $pallet_no
      ]
    );

    if ( !$inventTransMoveIn ) {
      \sqlsrv_rollback($conn);
      
      return [
        'result' => false,
        'message' => 'insert invent trans move in failed!'
      ];
    }

    _d('update invent trans move in success');

    
    _d('transfer successful');

    $old_location_ = self::getLocationInfo($barcodeInfo['LocationID']);
    $new_location_ = self::getLocationInfo($palletInfo['location_id']);

    sqlsrv_commit($conn);
    // sqlsrv_rollback($conn);
    return [
      'result' => true,
      'message' => 'pallet transfer successful!',
      'extra' => [
        'batch' => $barcodeInfo['Batch'],
        'curing_code' => $barcodeInfo['CuringCode'],
        'barcode' => $barcodeInfo['Barcode'],
        'old_location' => $old_location_['Description'],
        'new_location' => $new_location_['Description']
      ]
    ];
  }

  public function saveTransferLocation($pallet_no, $location) {

    _d();

    $location_ = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT TOP 1 ID 
      FROM [Location]
      WHERE [Description] = ?",
      [
        $location
      ]
    ));

    if ($location_ === false || $location_ === null) {
      return \jsonResult(false, 'new location not found!');
    }

    $location_pallet_ = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT TOP 1 location_id 
      FROM PalletTable
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));

    if ($location_pallet_ === false || $location_pallet_ === null) {
      return \jsonResult(false, 'old location not found!');
    }

    $isPalletComplete = sqlsrv_has_rows(sqlsrv_query(
      $this->conn,
      "SELECT [status]
      FROM PalletTable
      WHERE pallet_no = ?
      AND [status] = 3
      AND [status] NOT IN (1,2,4,5)",
      [
        $pallet_no
      ]
    ));

    if ($isPalletComplete === false ) {
      return \jsonResult(false, 'pallet not complete!');
    }

    _d($pallet_no . " is complete");

    $isLocationRemainZero = sqlsrv_has_rows(sqlsrv_query(
      $this->conn,
      "SELECT Remain 
      FROM [Location]
      WHERE ID = ?
      AND Remain <> 0",
      [
        $location_['ID']
      ]
    ));

    if ($isLocationRemainZero === false) {

      return [
        'result' => false,
        'message' => 'insert invent trans move in failed!'
      ];
    }

    if ( \sqlsrv_begin_transaction($this->conn) === false ) {
      return [
        'result' => false,
        'message' => 'begin transaction failed!'
      ];
    }

    $updatePalletTable = sqlsrv_query(
      $this->conn,
      "UPDATE PalletTable
      SET update_by = ?,
      update_date = ?,
      location_id = ?
      WHERE pallet_no = ?
      AND [status] NOT IN (4,5)",
      [
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $location_['ID'],
        $pallet_no
      ]
    );

    if (!$updatePalletTable) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'update pallet table failed!'
      ]; 
    }

    $rowsInLine = (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      "SELECT Barcode 
      FROM PalletLine 
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));

    if ( count($rowsInLine) === 0 ) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'pallet line is empty!'
      ]; 
    }

    foreach($rowsInLine as $r) {
      $updateInventTable = sqlsrv_query(
        $this->conn,
        "UPDATE InventTable
        SET LocationID = ?
        WHERE Barcode = ?",
        [
          $location_['ID'],
          $r['Barcode']
        ]
      );

      if (!$updateInventTable) {
        \sqlsrv_rollback($this->conn);
        return [
          'result' => false,
          'message' => 'update location failed!'
        ]; 
      }
    }

    $updateNewLocationRemain = sqlsrv_query(
      $this->conn,
      "UPDATE [Location]
      SET Remain -= 1,
      QtyInUse += 1
      WHERE ID = ?",
      [
        $location_['ID']
      ]
    );

    if (!$updateNewLocationRemain) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'update new location remain failed!'
      ]; 
    }

    _d('update new location success');

    $updateOldLocationRemain = sqlsrv_query(
      $this->conn,
      "UPDATE [Location]
      SET Remain += 1,
      QtyInUse -= 1
      WHERE ID = ?",
      [
        $location_pallet_['location_id']
      ]
    );

    if (!$updateOldLocationRemain) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'update old location remain failed!'
      ]; 
    }

    _d('update old location success');

    $old_location_ = self::getLocationInfo($location_pallet_['location_id']);
    $new_location_ = self::getLocationInfo($location_['ID']);

    sqlsrv_commit($this->conn);
    return [
      'result' => true,
      'message' => 'transfer location successful! ',
      'extra' => [
        'old_location' => $old_location_['Description'],
        'new_location' => $new_location_['Description']
      ]
    ];
  }

  public function getPalletInfo($pallet_no) {
    $conn = (new Connector)->dbConnect();

    return (new SqlsrvHelper)->getRow(sqlsrv_query(
      $conn,
      "SELECT * FROM PalletTable
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));
  }

  public function getLocationInfo($location) {
    return (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT * FROM [Location]
      WHERE ID = ?",
      [
        $location
      ]
    ));
  }

  public function getWarehouseNameFromLocation($location_id) {

    $wh_id = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT TOP 1 WarehouseID 
      FROM [Location] 
      WHERE ID = ?",
      [
        $location_id
      ]
    ));

    if ($wh_id === false) {
      return '';
    }

    $wh_name = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT [Description] 
      from WarehouseMaster
      WHERE ID = ?",
      [
        $wh_id['WarehouseID']
      ]
    ));

    if ($wh_name === false) {
      return '';
    }

    return $wh_name;
  }

  public function getItemInfo($item_id) {
    $conn = (new Connector)->dbConnect();
    return (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT * 
      FROM ItemMaster
      WHERE ID = ?",
      [
        $item_id
      ]
    ));
  }

  public function updateLocation($location, $location_temp, $pallet_no) {

    _d();

    if (\sqlsrv_begin_transaction($this->conn) === false) {
      return [
        'result' => false,
        'message' => 'cannot begin transaction'
      ];
    }

    $updatePalletLocation = self::updatePalletLocation($pallet_no, $location);

    if ($updatePalletLocation['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $updatePalletLocation;
    }

    $updateInUseOld = self::updateQtyInUseLocation($location_temp, "-=");

    if ($updateInUseOld['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $updateInUseOld;
    }

    $updateInUseNew = self::updateQtyInUseLocation($location, "+=");

    if ($updateInUseNew['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $updateInUseNew;
    }

    $updateRemainLocationOld = self::updateRemainLocation($location_temp, "+=");
    
    if ($updateRemainLocationOld['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $updateRemainLocationOld;
    }

    $updateRemainLocationNew = self::updateRemainLocation($location, "-=");

    if ($updateRemainLocationNew['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $updateRemainLocationNew;
    }

    $isPalletEmpty = self::isPalletEmpty($pallet_no);

    if ($isPalletEmpty['result'] === false) {
      \sqlsrv_rollback($this->conn);
      return $isPalletEmpty;
    }

    $barcodeInPallet = self::getBarcodeInPallet($pallet_no);

    foreach ($barcodeInPallet as $v) {

      $barcodeInfo = self::getBarcodeInfo($v['barcode']);

      $updateInventTable = sqlsrv_query(
        $this->conn,
        "UPDATE InventTable 
        SET WarehouseID = ?,
        LocationID = ?,
        UpdateBy = ?,
        UpdateDate = ?
        WHERE Barcode = ?",
        [
          $barcodeInfo[0]['WarehouseID'],
          $location,
          $_SESSION['user_login'],
          date('Y-m-d H:i:s'),
          $barcodeInfo[0]['Barcode']
        ] 
      );

      if(!$updateInventTable) {
        \sqlsrv_rollback($conn);
        return [
          'result' => false,
          'message' => 'Update invent table error.'
        ];
      }

      $moveOutInventTrans = sqlsrv_query(
        $this->conn,
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
          (new InventoryAPI)->genTransId($barcodeInfo[0]['Barcode']) . 1,
          $barcodeInfo[0]['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $barcodeInfo[0]['WarehouseID'],
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
        \sqlsrv_rollback($this->conn);
        return [
          'result' => false,
          'message' => 'move out invent trans error.'
        ];
      }

      $moveInInventTrans = sqlsrv_query(
        $this->conn,
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
          (new InventoryAPI)->genTransId($barcodeInfo[0]['Barcode']) . 2,
          $barcodeInfo[0]['Barcode'],
          $barcodeInfo[0]['ItemID'],
          $barcodeInfo[0]['Batch'],
          $barcodeInfo[0]['DisposalID'],
          null,
          $barcodeInfo[0]['WarehouseID'],
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
        \sqlsrv_rollback($this->conn);
        return [
          'result' => false,
          'message' => 'move in invent trans error.'
        ];
      }

      // $moveOutOnhand = sqlsrv_query(
      //   $this->conn,
      //   "UPDATE Onhand SET QTY -= 1
      //   WHERE CodeID = ?
      //   AND WarehouseID = ?
      //   AND LocationID = ?
      //   AND Batch = ?
      //   AND Company =?",
      //   [
      //     $barcodeInfo[0]["ItemID"],
      //     $barcodeInfo[0]['WarehouseID'],
      //     $location_temp,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //   ]
      // );

      // if(!$moveOutOnhand) {
      //   \sqlsrv_rollback($this->conn);
      //   return [
      //     'result' => false,
      //     'message' => 'move out onhand error.'
      //   ];
      // }

      // $moveInOnhand = sqlsrv_query(
      //   $this->conn,
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
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     $barcodeInfo[0]["Company"],
      //     $barcodeInfo[0]["ItemID"],
      //     $barcodeInfo[0]['WarehouseID'],
      //     $location,
      //     $barcodeInfo[0]["Batch"],
      //     1, // qty
      //     $barcodeInfo[0]["Company"]
      //   ]
      // );

      // if(!$moveInOnhand) {
      //   \sqlsrv_rollback($this->conn);
      //   return 'move in onhand error.';
      // }
    }

    sqlsrv_commit($this->conn);
    return [
      'result' => true,
      'message' => 'update success'
    ];

  }

  public function updateQtyInUseLocation($location_id, $type) {

    $updateInUse = sqlsrv_query(
      $this->conn,
      "UPDATE [Location]
      SET QTYInUse $type 1
      WHERE ID = ?",
      [
        $location_id
      ]
    );

    if ($updateInUse) {
      _d("update in use success");
      return [
        'result' => true,
        'message' => 'update qty in use success'
      ];
    } else {
      _d("update in use failed");
      return [
        'result' => false,
        'message' => 'update qty in use failed'
      ];
    }
  }

  public function updatePalletLocation($pallet_no, $location) {
    $updatePalletTableLocation = sqlsrv_query(
      $this->conn,
      "UPDATE PalletTable 
      SET location_id = ?
      WHERE pallet_no = ?",
      [
        $location,
        $pallet_no
      ]
    );

    if ($updatePalletTableLocation) {
      return [
        'result' => true,
        'message' => 'update pallet location success.'
      ];
    } else {
      return [
        'result' => failed,
        'message' => 'update pallet location failed.'
      ];
    }
  }

  public function updateRemainLocation($location_id, $type) {
    $updateLocation = sqlsrv_query(
      $this->conn,
      "UPDATE [Location]
      SET Remain $type 1
      WHERE ID = ?",
      [
        $location_id
      ]
    );

    if ($updateLocation) {
      _d("update remain location  success");
      return [
        'result' => true,
        'message' => 'update remain location success'
      ];
    } else {
      _d("update remain location failed");
      return [
        'result' => false,
        'message' => 'update remain location failed'
      ];
    }
  }

  public function isPalletEmpty($pallet_no) {

    $isPalletHasBarcode = \sqlsrv_has_rows(sqlsrv_query(
      $this->conn,
      "SELECT pallet_no FROM palletLine
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));

    if ($isPalletHasBarcode === true) {
      return [
        'result' => true,
        'message' => 'pallet have baracode'
      ];
    } else {
      return [
        'result' => false,
        'message' => 'pallet has no baracode'
      ];
    }

  }

  public function getBarcodeInPallet($pallet_no) {
    return (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      "SELECT barcode 
      FROM PalletLine
      WHERE pallet_no = ?",
      [
        $pallet_no
      ]
    ));
  }

  public function getBarcodeInfo($barcode) {
    return (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      "SELECT * FROM InventTable 
      WHERE Barcode = ?",
      [
        $barcode
      ]
    ));
  }
}