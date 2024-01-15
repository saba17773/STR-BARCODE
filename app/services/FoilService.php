<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Components\Utils;
use App\Services\BarcodeService;
use Wattanar\Sqlsrv;

class FoilService
{
	public function saveFoil($old_barcode, $new_barcode)
	{
		$conn = DB::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'transaction faiiled.';
		}



		$barcode_detail = (new BarcodeService)->getBarcodeInfoV2($old_barcode);
		// Update invent table
		$update_inventtable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET BarcodeFoil = ?,
      DisposalID = ?,
      WarehouseID = ?,
      LocationID = ?,
      Status = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE Barcode = ?",
      [
        $new_barcode,
        17, // foil
        3, // fg
        14, // foil
        1, // status receive
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $old_barcode	
      ]
    );
  //   if ($update_inventtable === false) {
  //     // Query execution failed, retrieve and display the errors
  //     $errors = sqlsrv_errors();
  //     foreach ($errors as $error) {
  //         echo "SQLSTATE: ".$error['SQLSTATE']."<br>";
  //         echo "Code: ".$error['code']."<br>";
  //         echo "Message: ".$error['message']."<br>";
  //     }
  // }
    if (!$update_inventtable) {
    	sqlsrv_rollback($conn);
    	return 'update inventtable failed';	
    }

    // create trans move out
    $create_trans_movein = sqlsrv_query(
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
          (new Utils)->genTransId($old_barcode) . 1,
          $old_barcode,
          $barcode_detail[0]["ItemID"],
          $barcode_detail[0]["Batch"],
          6, // fg
          null,
          3, // 
          7, // 
          -1,
          $barcode_detail[0]["Unit"], /// PCS
          2, // issue
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          Date("Y-m-d H:i:s"),
          $_SESSION["Shift"],
          null,
          null,
          null,
          null
      ]
    );

    if (!$create_trans_movein) {
    	sqlsrv_rollback($conn);
    	return 'create inventtrans move out failed';	
    }

    // create trans move in
    $create_trans_moveout = sqlsrv_query(
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
          (new Utils)->genTransId($old_barcode) . 2,
          $old_barcode,
          $barcode_detail[0]["ItemID"],
          $barcode_detail[0]["Batch"],
          17, // foil
          null,
          3, // 
          14, // foil 
          1,
          $barcode_detail[0]["Unit"],
          1, // receive
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          Date("Y-m-d H:i:s"),
          $_SESSION["Shift"],
          null,
          null,
          null,
          null
      ]
    );

    if (!$create_trans_moveout) {
    	sqlsrv_rollback($conn);
    	return 'create inventtrans move out failed';	
    }

    // ############ ONHAND ###########
    // $stmt = sqlsrv_query(
    //   $conn,
    //   "UPDATE Onhand 
    //   SET QTY += ?
    //   WHERE CodeID = ?
    //   AND WarehouseID = ?
    //   AND LocationID = ?
    //   AND Batch = ?
    //   AND Company = ?",
    //   [
    //       -1,
    //       $barcode_detail[0]["ItemID"],
    //       $barcode_detail[0]["WarehouseID"],
    //       $barcode_detail[0]["LocationID"],
    //       $barcode_detail[0]["Batch"],
    //       $barcode_detail[0]["Company"]
    //   ]
    // );

    // if (!$stmt) {
    //     sqlsrv_rollback($conn);
    //     return "Update move out onhand failed.";
    // }

    $isItemSetExists = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT CodeID 
        FROM Onhand
        WHERE WarehouseID = ?
        AND LocationID = ?
        AND Batch = ?
        AND Company = ?
        AND CodeID  = ?
        AND QTY >= 0",
        [
            $barcode_detail[0]["WarehouseID"],
            $barcode_detail[0]["LocationID"],
            $barcode_detail[0]["Batch"],
            $barcode_detail[0]["Company"],
            $barcode_detail[0]["ItemID"]
        ]
    ));

    if ($isItemSetExists === true) {

	    $check_item = sqlsrv_has_rows(sqlsrv_query(
	        $conn,
	        "SELECT CodeID 
	        FROM Onhand
	        WHERE WarehouseID = ?
	        AND LocationID = ?
	        AND Batch = ?
	        AND Company = ?
	        AND CodeID  = ?
	        AND QTY >= 0",
	        [
	            $barcode_detail[0]["WarehouseID"],
	            14,
	            $barcode_detail[0]["Batch"],
	            $barcode_detail[0]["Company"],
	            $barcode_detail[0]["ItemID"]
	        ]
	    ));

	    if ($check_item === true) {

	    	// $update_onhand = sqlsrv_query(
	     //    $conn,
	     //    "UPDATE Onhand 
	     //    SET QTY += ?
	     //    WHERE CodeID = ?
	     //    AND WarehouseID = ?
	     //    AND LocationID = ?
	     //    AND Batch = ?
	     //    AND Company = ?",
	     //    [
	     //        1,
	     //        $barcode_detail[0]["ItemID"],
	     //        $barcode_detail[0]["WarehouseID"],
	     //        14, // foil
	     //        $barcode_detail[0]["Batch"],
	     //        $barcode_detail[0]["Company"]
	     //    ]
	     //  );

	     //  if (!$update_onhand) {
	     //      sqlsrv_rollback($conn);
	     //      return "Update onhand failed.";
	     //  }
	    } else {
	    	// create onhand
	    	// $create_onhand = sqlsrv_query(
	     //      $conn,
	     //      "INSERT INTO Onhand(
	     //          CodeID,
	     //          WarehouseID,
	     //          LocationID,
	     //          Batch,
	     //          QTY,
	     //          Company
	     //      ) VALUES(?, ?, ?, ?, ?, ?)",
	     //      [
	     //          $barcode_detail[0]["ItemID"],
	     //          $barcode_detail[0]["WarehouseID"],
	     //          14, // foil
	     //          $barcode_detail[0]["Batch"],
	     //          1,
	     //          $barcode_detail[0]["Company"]
	     //      ]
	     //  );

	     //  if (!$create_onhand) {
	     //      sqlsrv_rollback($conn);
	     //      return "Update onhand failed.";
	     //  }
	    }
    } else {
    	// $create_onhand = sqlsrv_query(
     //      $conn,
     //      "INSERT INTO Onhand(
     //          CodeID,
     //          WarehouseID,
     //          LocationID,
     //          Batch,
     //          QTY,
     //          Company
     //      ) VALUES(?, ?, ?, ?, ?, ?)",
     //      [
     //          $barcode_detail[0]["ItemID"],
     //          $barcode_detail[0]["WarehouseID"],
     //          14, // foil
     //          $barcode_detail[0]["Batch"],
     //          1,
     //          $barcode_detail[0]["Company"]
     //      ]
     //  );

     //  if (!$create_onhand) {
     //      sqlsrv_rollback($conn);
     //      return "Update onhand failed.";
     //  }
    }

    sqlsrv_commit($conn);
    return true;
	}

	public function saveUnfoil($barcode_foil)
	{
		$conn = DB::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return json_encode([
	      "result" => false,
	      "message" => "Transaction begin failed."
		  ]);
		}

		$barcode = (new BarcodeService)->getBarcodeFromBarcodeFoil($barcode_foil);
		$barcode_detail = (new BarcodeService)->getBarcodeFoilInfo($barcode_foil);

		// Update invent table
		$update_inventtable = sqlsrv_query(
      $conn,
      "UPDATE InventTable 
      SET BarcodeFoil = ?,
      DisposalID = ?,
      WarehouseID = ?,
      LocationID = ?,
      Status = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE Barcode = ?",
      [
        null,
        6, // fg
        3, // fg
        7, // fg
        1, // status receive
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $barcode	
      ]
    );

    if (!$update_inventtable) {
    	sqlsrv_rollback($conn);
    	return 'update inventtable failed';	
    }

    // create trans move out
    $create_trans_movein = sqlsrv_query(
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
          $barcode_detail[0]["ItemID"],
          $barcode_detail[0]["Batch"],
          17, // foil
          null,
          3, // 
          14, // 
          -1,
          $barcode_detail[0]["Unit"], /// PCS
          2, // issue
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          Date("Y-m-d H:i:s"),
          $_SESSION["Shift"],
          null,
          null,
          null,
          null
      ]
    );

    if (!$create_trans_movein) {
    	sqlsrv_rollback($conn);
    	return 'create inventtrans move out failed';	
    }

    // create trans move in
    $create_trans_moveout = sqlsrv_query(
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
          $barcode_detail[0]["ItemID"],
          $barcode_detail[0]["Batch"],
          6, // foil
          null,
          3, // 
          7, // 
          1,
          $barcode_detail[0]["Unit"], /// PCS
          1, // issue
          $_SESSION["user_company"],
          $_SESSION["user_login"],
          Date("Y-m-d H:i:s"),
          $_SESSION["Shift"],
          null,
          null,
          null,
          null
      ]
    );

    if (!$create_trans_moveout) {
    	sqlsrv_rollback($conn);
    	return 'create inventtrans move out failed';	
    }

    // ############ ONHAND ###########
    // $stmt = sqlsrv_query(
    //   $conn,
    //   "UPDATE Onhand 
    //   SET QTY += ?
    //   WHERE CodeID = ?
    //   AND WarehouseID = ?
    //   AND LocationID = ?
    //   AND Batch = ?
    //   AND Company = ?",
    //   [
    //       -1,
    //       $barcode_detail[0]["ItemID"],
    //       $barcode_detail[0]["WarehouseID"],
    //       $barcode_detail[0]["LocationID"],
    //       $barcode_detail[0]["Batch"],
    //       $barcode_detail[0]["Company"]
    //   ]
    // );

    // if (!$stmt) {
    //     sqlsrv_rollback($conn);
    //     return "Update move out onhand failed.";
    // }

    $isItemSetExists = sqlsrv_has_rows(sqlsrv_query(
        $conn,
        "SELECT CodeID 
        FROM Onhand
        WHERE WarehouseID = ?
        AND LocationID = ?
        AND Batch = ?
        AND Company = ?
        AND CodeID  = ?
        AND QTY >= 0",
        [
            $barcode_detail[0]["WarehouseID"],
            $barcode_detail[0]["LocationID"],
            $barcode_detail[0]["Batch"],
            $barcode_detail[0]["Company"],
            $barcode_detail[0]["ItemID"]
        ]
    ));

    if ($isItemSetExists === true) {

	    $check_item = sqlsrv_has_rows(sqlsrv_query(
	        $conn,
	        "SELECT CodeID 
	        FROM Onhand
	        WHERE WarehouseID = ?
	        AND LocationID = ?
	        AND Batch = ?
	        AND Company = ?
	        AND CodeID  = ?
	        AND QTY >= 0",
	        [
	            3,
	            7,
	            $barcode_detail[0]["Batch"],
	            $barcode_detail[0]["Company"],
	            $barcode_detail[0]["ItemID"]
	        ]
	    ));

	    if ($check_item === true) {

	    	// $update_onhand = sqlsrv_query(
	     //    $conn,
	     //    "UPDATE Onhand 
	     //    SET QTY += ?
	     //    WHERE CodeID = ?
	     //    AND WarehouseID = ?
	     //    AND LocationID = ?
	     //    AND Batch = ?
	     //    AND Company = ?",
	     //    [
	     //        1,
	     //        $barcode_detail[0]["ItemID"],
	     //        3,
	     //        7, // foil
	     //        $barcode_detail[0]["Batch"],
	     //        $barcode_detail[0]["Company"]
	     //    ]
	     //  );

	     //  if (!$update_onhand) {
	     //      sqlsrv_rollback($conn);
	     //      return "Update onhand failed.";
	     //  }
	    } else {
	    	// create onhand
	    	// $create_onhand = sqlsrv_query(
	     //      $conn,
	     //      "INSERT INTO Onhand(
	     //          CodeID,
	     //          WarehouseID,
	     //          LocationID,
	     //          Batch,
	     //          QTY,
	     //          Company
	     //      ) VALUES(?, ?, ?, ?, ?, ?)",
	     //      [
	     //          $barcode_detail[0]["ItemID"],
	     //         	3,
	     //          7, // foil
	     //          $barcode_detail[0]["Batch"],
	     //          1,
	     //          $barcode_detail[0]["Company"]
	     //      ]
	     //  );

	     //  if (!$create_onhand) {
	     //      sqlsrv_rollback($conn);
	     //      return "Update onhand failed.";
	     //  }
	    }
    } else {
    	// $create_onhand = sqlsrv_query(
     //      $conn,
     //      "INSERT INTO Onhand(
     //          CodeID,
     //          WarehouseID,
     //          LocationID,
     //          Batch,
     //          QTY,
     //          Company
     //      ) VALUES(?, ?, ?, ?, ?, ?)",
     //      [
     //          $barcode_detail[0]["ItemID"],
     //          $barcode_detail[0]["WarehouseID"],
     //          $barcode_detail[0]["LocationID"], // foil
     //          $barcode_detail[0]["Batch"],
     //          1,
     //          $barcode_detail[0]["Company"]
     //      ]
     //  );

     //  if (!$create_onhand) {
     //      sqlsrv_rollback($conn);
     //      return "Update onhand failed.";
     //  }
    }

    if ($update_inventtable) {
    	sqlsrv_commit($conn);
    	return true;
    } else {
    	sqlsrv_rollback($conn);
    	return false;
    }
   
	}

  public function isBarcodeFoilNull($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM InventTable
      WHERE Barcode = ? 
      AND BarcodeFoil IS NULL",
      [
          $barcode
      ]
    );	
  }

  public function isBarcodeFoilUsed($barcode_foil)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      "SELECT BarcodeFoil FROM InventTable
      WHERE BarcodeFoil = ?",
      [
          $barcode_foil
      ]
    );  
  }

  public function isBarcodeFoilExists($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM InventTable
      WHERE BarcodeFoil = ? 
      AND BarcodeFoil IS NOT NULL",
      [
          $barcode
      ]
    );	
  }

  public function isBarcodeFoilStatusReceive($barcode)
  {
      $conn = DB::connect();
      return Sqlsrv::hasRows(
          $conn,
          "SELECT Status FROM InventTable
          WHERE BarcodeFoil = ? 
          AND Status = 1", // Receive
          [
              $barcode
          ]
      );
  }

  public function reportFoil($time)
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
      (
        CASE 
          WHEN 
          (
            SELECT COUNT(*)[Barcode] FROM InventTrans I 
            WHERE I.DisposalID = 17
            AND I.DocumentTypeID = 1
            AND I.Barcode = IT.Barcode
            AND I.CreateDate > ITS.CreateDate
          ) = 0
          THEN IT.BarcodeFoil
          ELSE ''
        END
      ) [BarcodeFoil],
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      SM.Description [Shift]
      FROM InventTable IT 
      left join ItemMaster IM ON IM.ID = IT.ItemID
      left join InventTrans ITS ON ITS.Barcode = IT.Barcode 
        AND ITS.DisposalID = 17
        AND ITS.DocumentTypeID = 1
      left join ShiftMaster SM ON SM.ID = ITS.Shift
      WHERE ITS.DisposalID = 17 
      AND (" . $sqltime .")
      GROUP BY IT.CuringCode,
      IT.Barcode,
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      SM.Description,
      IT.BarcodeFoil,
      ITS.CreateDate
      ORDER BY ITS.CreateDate DESC
      "
    );
  } 
}