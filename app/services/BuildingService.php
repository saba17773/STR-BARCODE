<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Libs\InventTable;
use App\Libs\Onhand;

class BuildingService
{
	public function all()
	{
		$conn = DB::connect();
		return Sqlsrv::queryJson($conn, "SELECT BM.ID,BM.Description,BM.BOI,BT.ID
			 AS BOIName FROM BuildingMaster BM
			 LEFT JOIN BOITable BT ON BT.ID = BM.BOI");
	}

	public function boi()
	{
		$conn = DB::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM BOITable");
	}

	public function create($id, $desc, $boi)
	{
		if (self::isExist($id) === true) {
			return false;
		}

		$conn = DB::connect();
		$query =  Sqlsrv::insert(
				$conn,
				"INSERT INTO BuildingMaster(ID, Description, Company, BOI) VALUES (?, ?, ?, ?)",
				[$id, $desc, $_SESSION["user_company"], $boi]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $desc, $boi)
	{

		$conn = DB::connect();
		$query = Sqlsrv::update(
				$conn,
				"UPDATE BuildingMaster
				SET	Description = ?,
						BOI = ?
		        WHERE ID = ?",
				[
					$desc,
					$boi,
					$id
				]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function isExist($building_code)
	{
		$conn = DB::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT BM.ID
			FROM BuildingMaster BM
			WHERE BM.ID = ?",
			[$building_code]
		);
	}

	public function delete($id)
	{
		$conn = DB::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return false;
		}

		$hasRow = Sqlsrv::hasRows(
			$conn,
			'SELECT IT.BuildingNo
			FROM InventTable IT
			WHERE IT.BuildingNo = ?',
			[$id]
		);

		if ($hasRow === true) {
			return false;
		}

		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM BuildingMaster WHERE ID = ?",
			[$id]
		);

		if ($q) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
	}

	public function changeCodeV2($copy_barcode, $barcode)
	{
		$conn = DB::connect();

		if (!(new InventTable)->isCuring($barcode)) {
     	$mode = 1;
    } else {
    	$mode = 2;
    }

    if (!(new InventTable)->isCuring($copy_barcode)) {
    	$mode_of_copy = 1;
    } else {
    	$mode_of_copy = 2;
    }

    if (!(new InventTable)->isBarcodeInRange($barcode)) {
      return [
        "result" => false,
        "message" => "Barcode ไม่ถูกต้อง"
      ];
    }

		$copy_barcode_detail = (new InventTable)->getBarcodeDetail($copy_barcode);
		$barcode_detail = (new InventTable)->getBarcodeDetail($barcode);

		if (!(new InventTable)->isBarcodeExist($copy_barcode)) {
			return [
        "result" => false,
        "message" => "ไม่มีข้อมูลใน Invent Table"
      ];
		}

		if(sqlsrv_begin_transaction($conn) === false) {
			return [
        "result" => false,
        "message" => "Transaction failed!"
      ];
		}

		$checkstatusbarcode = Sqlsrv::hasRows(
			$conn,
			'SELECT barcode
			FROM InventTable
			WHERE barcode = ? AND Status = ?',
			[$barcode,1]
		);
		if ($checkstatusbarcode == false) {
 		 return [
 			 "result" => false,
 			 "message" => "Barcode Status ไม่เท่ากับ Receive"
 		 ];
 	 }



		if ($mode === 1) { // if warehouse = greentire

			if ($copy_barcode_detail[0]['GT_Code'] === $barcode_detail[0]['GT_Code']) {
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Barcode is duplicate!"
	      ];
			}

			$changeCodeInventTable = sqlsrv_query(
				$conn,
				"UPDATE InventTable
	      SET GT_Code = ?
	      WHERE Barcode = ?",
	      [
	      	$copy_barcode_detail[0]['GT_Code'],
	      	$barcode
	      ]
			);

			if (!$changeCodeInventTable) {
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Update invent table failed!"
	      ];
			}

			// invent trans move out
			$moveout_inventtrans = sqlsrv_query(
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
	        Utils::genTransId($barcode) . 1,
	        $barcode_detail[0]['Barcode'],
	        $barcode_detail[0]['GT_Code'],
	        $barcode_detail[0]['Batch'],
	        $barcode_detail[0]['DisposalID'],
	        null, // defect
	        $barcode_detail[0]['WarehouseID'],
	        $barcode_detail[0]['LocationID'],
	        -1, // qty
	       	1, // unit
	        2, // doc type
	        $_SESSION['user_company'],
	        $_SESSION['user_login'],
	       	date('Y-m-d H:i:s'),
	        $_SESSION['Shift'],
	        null,
	        null,
	        null,
	        null
	      ]
			);

			if (!$moveout_inventtrans) {
				sqlsrv_rollback($conn);
				return [
	          "result" => false,
	          "message" => "create invent trans move out failed!"
	      ];
			}

			// invent trans move in
			$movein_inventtrans = sqlsrv_query(
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
	        Utils::genTransId($barcode) . 2,
	        $barcode_detail[0]['Barcode'],
	        $copy_barcode_detail[0]['GT_Code'],
	        $barcode_detail[0]['Batch'],
	        16, // adjust
	       	null, // defect
	        $barcode_detail[0]['WarehouseID'],
	        $barcode_detail[0]['LocationID'],
	        1, // qty
	       	1, // unit
	        1, // doc type
	        $_SESSION['user_company'],
	        $_SESSION['user_login'],
	       	date('Y-m-d H:i:s'),
	        $_SESSION['Shift'],
	        null,
	        null,
	        null,
	        null
	      ]
			);

			if (!$movein_inventtrans) {
				sqlsrv_rollback($conn);
				return [
	          "result" => false,
	          "message" => "create invent trans move in failed!"
	      ];
			}

			// Move out onhand
			// $update_onhand_moveout = sqlsrv_query(
			// 	$conn,
   //      "UPDATE Onhand
   //      SET QTY += ?
   //      WHERE CodeID = ?
   //      AND WarehouseID = ?
   //      AND LocationID = ?
   //      AND Batch = ?
   //      AND Company = ?",
   //      [
   //          -1,
   //          $barcode_detail[0]['GT_Code'],
   //          $barcode_detail[0]['WarehouseID'],
   //          $barcode_detail[0]['LocationID'],
   //          $barcode_detail[0]['Batch'],
   //          $barcode_detail[0]['Company']
   //      ]
			// );

			// if (!$update_onhand_moveout) {
			// 	sqlsrv_rollback($conn);
			// 	return [
	  //         "result" => false,
	  //         "message" => "Update onhand failed!"
		 //      ];
			// }

			// ##############################################################
			if ($mode_of_copy === 1) { // check if copy from final to greentire

				$is_onhand_exists =	sqlsrv_has_rows(sqlsrv_query(
					$conn,
					"SELECT QTY
					FROM Onhand
					WHERE WarehouseID = ?
					AND LocationID = ?
					AND Batch = ?
					AND Company = ?
					AND CodeID  = ?
					AND QTY >= 0",
					[
						$barcode_detail[0]['WarehouseID'],
						$barcode_detail[0]['LocationID'],
						$barcode_detail[0]['Batch'],
						$barcode_detail[0]['Company'],
						$copy_barcode_detail[0]['GT_Code']
					]
				));

				if ($is_onhand_exists === false) {

					// $create_onhand = sqlsrv_query(
					// 	$conn,
					// 	"INSERT INTO Onhand(
     //          CodeID,
     //          WarehouseID,
     //          LocationID,
     //          Batch,
     //          QTY,
     //          Company
     //        ) VALUES(?, ?, ?, ?, ?, ?)",
     //        [
     //          $copy_barcode_detail[0]['GT_Code'],
     //          $barcode_detail[0]['WarehouseID'],
     //          $barcode_detail[0]['LocationID'],
     //          $barcode_detail[0]['Batch'],
     //          1,
     //          $barcode_detail[0]['Company']
     //        ]
					// );

					// if (!$create_onhand) {
					// 	sqlsrv_rollback($conn);
					// 	return [
		   //        "result" => false,
		   //        "message" => "Create onhand failed!"
			  //     ];
					// }

				} else {
					// Move in onhand
					// $update_onhand_movein = sqlsrv_query(
					// 	$conn,
		   //      "UPDATE Onhand
		   //      SET QTY += ?
		   //      WHERE CodeID = ?
		   //      AND WarehouseID = ?
		   //      AND LocationID = ?
		   //      AND Batch = ?
		   //      AND Company = ?",
		   //      [
		   //          1,
		   //          $copy_barcode_detail[0]['GT_Code'],
		   //          $barcode_detail[0]['WarehouseID'],
		   //          $barcode_detail[0]['LocationID'],
		   //          $barcode_detail[0]['Batch'],
		   //          $barcode_detail[0]['Company']
		   //      ]
					// );

					// if (!$update_onhand_movein) {
					// 	sqlsrv_rollback($conn);
					// 	return [
		   //        "result" => false,
		   //        "message" => "Update onhand failed!"
			  //     ];
					// }

				}
			} else {

				$is_onhand_exists =	sqlsrv_has_rows(sqlsrv_query(
					$conn,
					"SELECT QTY
					FROM Onhand
					WHERE WarehouseID = ?
					AND LocationID = ?
					AND Batch = ?
					AND Company = ?
					AND CodeID  = ?
					AND QTY >= 0",
					[
						$barcode_detail[0]['WarehouseID'],
						$barcode_detail[0]['LocationID'],
						$barcode_detail[0]['Batch'],
						$barcode_detail[0]['Company'],
						$copy_barcode_detail[0]['GT_Code']
					]
				));

				if ($is_onhand_exists === false) {

					// $create_onhand = sqlsrv_query(
					// 	$conn,
					// 	"INSERT INTO Onhand(
     //          CodeID,
     //          WarehouseID,
     //          LocationID,
     //          Batch,
     //          QTY,
     //          Company
     //        ) VALUES(?, ?, ?, ?, ?, ?)",
     //        [
     //          $copy_barcode_detail[0]['GT_Code'],
     //          $barcode_detail[0]['WarehouseID'],
     //          $barcode_detail[0]['LocationID'],
     //          $barcode_detail[0]['Batch'],
     //          1,
     //          $barcode_detail[0]['Company']
     //        ]
					// );

					// if (!$create_onhand) {
					// 	sqlsrv_rollback($conn);
					// 	return [
		   //        "result" => false,
		   //        "message" => "Create onhand failed!"
			  //     ];
					// }
				} else {

					// Move in onhand
					// $update_onhand_movein = sqlsrv_query(
					// 	$conn,
		   //      "UPDATE Onhand
		   //      SET QTY += ?
		   //      WHERE CodeID = ?
		   //      AND WarehouseID = ?
		   //      AND LocationID = ?
		   //      AND Batch = ?
		   //      AND Company = ?",
		   //      [
		   //          1,
		   //          $copy_barcode_detail[0]['GT_Code'],
		   //          $barcode_detail[0]['WarehouseID'],
		   //          $barcode_detail[0]['LocationID'],
		   //          $barcode_detail[0]['Batch'],
		   //          $barcode_detail[0]['Company']
		   //      ]
					// );

					// if (!$update_onhand_movein) {
					// 	sqlsrv_rollback($conn);
					// 	return [
		   //        "result" => false,
		   //        "message" => "Update onhand failed!"
			  //     ];
					// }
				} // end of check onhand code id exists
			} // end of if mode_of_copy
			###########################3
		} else if ($mode === 2 && $_SESSION['user_warehouse'] === 2) { // if warehouse = final

			if ($copy_barcode_detail[0]['ItemID'] === $barcode_detail[0]['ItemID']) {
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Barcode is duplicate!"
	      ];
			}

			if (!(new InventTable)->isCuringCode($copy_barcode_detail[0]['Barcode'])) {
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Curing code not found!"
	      ];
			}
			$itemprimary = $barcode_detail[0]['ItemID'];
			$itemcoppy = $copy_barcode_detail[0]['ItemID'];
			$item1 = explode("-",$itemprimary);
			$item2 = explode("-",$itemcoppy);
			if($item1[0] !== $item2[0]){
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Item Barcode Not Match"
	      ];
			}

			$changeCodeInventTable = sqlsrv_query(
				$conn,
				"UPDATE InventTable
	      SET ItemID = ?,
	      CuringCode = ?
	      WHERE Barcode = ?",
	      [
	      	$copy_barcode_detail[0]['ItemID'],
	      	$copy_barcode_detail[0]['CuringCode'],
	      	$barcode
	      ]
			);

			if (!$changeCodeInventTable) {
				sqlsrv_rollback($conn);
				return [
	        "result" => false,
	        "message" => "Update invent table failed!"
	      ];
			}

		  // invent trans move out
			$moveout_inventtrans = sqlsrv_query(
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
	        Utils::genTransId($barcode) . 1,
	        $barcode_detail[0]['Barcode'],
	        $barcode_detail[0]['ItemID'],
	        $barcode_detail[0]['Batch'],
	        $barcode_detail[0]['DisposalID'],
	        null,
	        $barcode_detail[0]['WarehouseID'],
	        $barcode_detail[0]['LocationID'],
	        -1, // qty
	       	1, // unit
	        2, // doc type
	        $_SESSION['user_company'],
	        $_SESSION['user_login'],
	       	date('Y-m-d H:i:s'),
	        $_SESSION['Shift'],
	        null,
	        null,
	        null,
	        null
	      ]
			);

			if (!$moveout_inventtrans) {
				sqlsrv_rollback($conn);
				return [
	          "result" => false,
	          "message" => "create invent trans move out failed!"
	      ];
			}

			// invent trans move in
			$movein_inventtrans = sqlsrv_query(
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
	        Utils::genTransId($barcode) . 2,
	        $barcode_detail[0]['Barcode'],
	        $copy_barcode_detail[0]['ItemID'],
	        $barcode_detail[0]['Batch'],
	        16, // adjust
	        null,
	        $barcode_detail[0]['WarehouseID'],
	        $barcode_detail[0]['LocationID'],
	        1, // qty
	       	1, // unit
	        1, // doc type
	        $_SESSION['user_company'],
	        $_SESSION['user_login'],
	       	date('Y-m-d H:i:s'),
	        $_SESSION['Shift'],
	        null,
	        null,
	        null,
	        null
	      ]
			);

			if (!$movein_inventtrans) {
				sqlsrv_rollback($conn);
				return [
	          "result" => false,
	          "message" => "create invent trans move in failed!"
	      ];
			}

			// Move out onhand
			// $update_onhand_moveout = sqlsrv_query(
			// 		$conn,
   //        "UPDATE Onhand
   //        SET QTY -= 1
   //        WHERE CodeID = ?
   //        AND WarehouseID = ?
   //        AND LocationID = ?
   //        AND Batch = ?
   //        AND Company = ?",
   //        [
   //            $barcode_detail[0]['ItemID'],
   //            $barcode_detail[0]['WarehouseID'],
   //            $barcode_detail[0]['LocationID'],
   //            $barcode_detail[0]['Batch'],
   //            $barcode_detail[0]['Company']
   //        ]
			// );

			// if (!$update_onhand_moveout) {
			// 	sqlsrv_rollback($conn);
			// 	return [
   //        "result" => false,
   //        "message" => "Update onhand failed!"
	  //     ];
			// }

			// Move in onhand
			// $update_onhand_movein = sqlsrv_query(
			// 	$conn,
   //      "UPDATE Onhand
   //      SET QTY += 1
   //      WHERE CodeID = ?
   //      AND WarehouseID = ?
   //      AND LocationID = ?
   //      AND Batch = ?
			// 	AND Company = ?
			// 	IF @@ROWCOUNT = 0
			// 	INSERT INTO Onhand
			// 	VALUES (?, ?, ?, ?, ?, ?)",
   //      [
   //          $copy_barcode_detail[0]['ItemID'],
   //          $barcode_detail[0]['WarehouseID'],
   //          $barcode_detail[0]['LocationID'],
   //          $barcode_detail[0]['Batch'],
			// 			$barcode_detail[0]['Company'],
			// 			$copy_barcode_detail[0]['ItemID'],
   //          $barcode_detail[0]['WarehouseID'],
			// 			$barcode_detail[0]['LocationID'],
			// 			$barcode_detail[0]['Batch'],
			// 			1,
			// 			$barcode_detail[0]['Company'],
   //      ]
			// );

			// if (!$update_onhand_movein) {
			// 	sqlsrv_rollback($conn);
			// 	return [
   //        "result" => false,
   //        "message" => "Update onhand failed!"
	  //     ];
			// }

		} else {
			//
		}

		sqlsrv_commit($conn);
		return [
			'result' => true,
			'message' => 'Change Code Successful!'
		];
	}

	public function updateCheckBuild($barcode)
	{
		$conn = DB::connect();

		$update = sqlsrv_query(
			$conn,
			"UPDATE InventTable SET CheckBuild = 1
			WHERE Barcode = ?",
			[$barcode]
		);

		$insertlog = sqlsrv_query(
			$conn,
			"INSERT INTO LogCheckBuild(
					Barcode,
					CheckBuild_Date
			) VALUES(
				?, ?
			)",
			[
				$barcode,
				date('Y-m-d H:i:s')
			]
		);

		if ($update) {
			return true;
		} else {
			return false;
		}
	}

	public function updateCheckcure($barcode)
	{
		$conn = DB::connect();

		$update = sqlsrv_query(
			$conn,
			"UPDATE InventTable SET CuredTireReciveDate = ?
			WHERE Barcode = ?",
			[ date('Y-m-d H:i:s'),
				$barcode
			]
		);

		if ($update) {
			return true;
		} else {
			return false;
		}
	}

	public function forceChangeCode($item_id, $barcode, $cure_code) {

		$conn = DB::connect();

		if (count($_SESSION) === 0) {
			$_SESSION['user_company'] = "STR";
			$_SESSION['user_login'] = 457;
			$_SESSION['Shift'] = 3;
		}

		$barcode_detail = (new InventTable)->getBarcodeDetail($barcode);

		if (!(new InventTable)->isBarcodeExist($barcode)) {
			return [
        "result" => false,
        "message" => "Barcode not found"
      ];
		}

		if(sqlsrv_begin_transaction($conn) === false) {
			return [
        "result" => false,
        "message" => "Transaction failed!"
      ];
		}

		$checkstatusbarcode = Sqlsrv::hasRows(
			$conn,
			'SELECT barcode
			FROM InventTable
			WHERE barcode = ? AND Status = ?',
			[$barcode,1]
		);

		if ($checkstatusbarcode == false) {
 		 return [
 			 "result" => false,
 			 "message" => "Barcode Status != Receive"
 		 ];
 	 	}

		$changeCodeInventTable = sqlsrv_query(
			$conn,
			"UPDATE InventTable
			SET ItemID = ?,
			CuringCode = ?
			WHERE Barcode = ?",
			[
				$item_id,
				$cure_code,
				$barcode
			]
		);

		if (!$changeCodeInventTable) {
			sqlsrv_rollback($conn);
			return [
				"result" => false,
				"message" => "Update invent table failed!"
			];
		}

		// invent trans move out
		$moveout_inventtrans = sqlsrv_query(
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
				Utils::genTransId($barcode) . 1,
				$barcode_detail[0]['Barcode'],
				$barcode_detail[0]['ItemID'],
				$barcode_detail[0]['Batch'],
				$barcode_detail[0]['DisposalID'],
				null,
				$barcode_detail[0]['WarehouseID'],
				$barcode_detail[0]['LocationID'],
				-1, // qty
				1, // unit
				2, // doc type
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				date('Y-m-d H:i:s'),
				$_SESSION['Shift'],
				null,
				null,
				null,
				null
			]
		);

		if (!$moveout_inventtrans) {
			sqlsrv_rollback($conn);
			return [
					"result" => false,
					"message" => "create invent trans move out failed!"
			];
		}

		// invent trans move in
		$movein_inventtrans = sqlsrv_query(
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
				Utils::genTransId($barcode) . 2,
				$barcode_detail[0]['Barcode'],
				$item_id,
				$barcode_detail[0]['Batch'],
				16, // adjust
				null,
				$barcode_detail[0]['WarehouseID'],
				$barcode_detail[0]['LocationID'],
				1, // qty
				1, // unit
				1, // doc type
				$_SESSION['user_company'],
				$_SESSION['user_login'],
				date('Y-m-d H:i:s'),
				$_SESSION['Shift'],
				null,
				null,
				null,
				null
			]
		);

		if (!$movein_inventtrans) {
			sqlsrv_rollback($conn);
			return [
					"result" => false,
					"message" => "create invent trans move in failed!"
			];
		}

		sqlsrv_commit($conn);

		return [
			"result" => true,
			"message" => "Change Code Success"
		];
	}
}
