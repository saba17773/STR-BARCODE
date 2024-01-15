<?php

namespace App\V2\Batch;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;

class BatchAPI
{
  private $database = null;
  private $handler = null;

  public function __construct()
  {
    $this->database = new Connector();
    $this->handler = new Handler();
  }

  public function getBatchSetup()
  {
    $conn = $this->database->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, FormatBatch, FromDate, ToDate, Active, ProductGroup FROM BatchSetup"
    );
  }

  public function getBatchSetupActive()
  {
    $conn = $this->database->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, FormatBatch, FromDate, ToDate, Active 
      FROM BatchSetup
      WHERE Active = 1"
    );
  }

  public function createNewSetup()
  {
    $conn = $this->database->dbConnect();

    try {
      $create = sqlsrv_query(
        $conn,
        "INSERT INTO BatchSetup(FormatBatch, FromDate, ToDate, Active)
        VALUES(?, ?, ?, ?)",
        [
          $this->getBatch(date('Y-m-d H:i:s'), ''),
          date('Y-m-d'),
          date('Y-m-d'),
          0
        ]
      );

      if (!$create) {
        return $this->handler->dbError();
      } else {
        return true;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getBatch($datetime, $BOII, $isBuild = false, $check = '')
  {
    $conn = $this->database->dbConnect();
    // $datetime = "2022-01-05 11:00:00";
    if ($check == 0) {
      $getbatchweek = Sqlsrv::queryArray(
        $conn,
        "SELECT *
        FROM SetStratBatch
        WHERE [Type] = 1"
      );
      $ddcal = $getbatchweek[0]["CalDate"] . " day";
      $ddtime = $getbatchweek[0]["CalTime"] . " hours";
    } else if ($check == 1) {
      $getbatchweek = Sqlsrv::queryArray(
        $conn,
        "SELECT *
        FROM SetStratBatch
        WHERE [Type] = 2"
      );
      $ddcal = $getbatchweek[0]["CalDate"] . " day";
      $ddtime = $getbatchweek[0]["CalTime"] . " hours";
    } else {
      $ddcal = "0 day";
      $ddtime = "+4 hours";
    }



    $datecal = date('Y-m-d H:i:s', strtotime($datetime . $ddcal));
    $date =  date('Y-m-d H:i:s', strtotime($datecal . $ddtime));
    // return $datecal;
    $ddate = new \DateTime($date);
    $year = $ddate->format('Y');

    if ((int) $ddate->format('m') === 1 && (int) $ddate->format('W') === 52) {
      $year = (int) $ddate->format('Y') - 1;
    }

    if ((int) date('Y') >= 2021) {
      $dsc = BATCH_DSC;
    } else {
      $dsc = "";
    }

    $week = $ddate->format("W");

    if ((int) $week === 53) {

      $year = 2020;
      $week = 52;
    }

    // TEST: delete after test
    // $dsc = BATCH_DSC;

    if ($isBuild === true) {
      $w = $year . '-' . $week;
    } else {
      $w = $year . '-' . $week . $dsc . $BOII;
    }

    return $w;
  }

  public function saveBatchSetup($format, $from_date, $to_date, $setup_id, $form_type, $product_group)
  {
    $conn = $this->database->dbConnect();

    try {
      if ($form_type === 'update') {
        // Update
        $update = sqlsrv_query(
          $conn,
          "UPDATE BatchSetup
          SET FormatBatch = ?,
          FromDate = ?,
          ToDate = ?,
          ProductGroup = ?
          WHERE ID = ?",
          [
            $format,
            $from_date,
            $to_date,
            $product_group,
            $setup_id
          ]
        );

        if (!$update) {
          return $this->handler->dbError();
        } else {
          return true;
        }
      } else {
        // Create
        $create = sqlsrv_query(
          $conn,
          "INSERT INTO BatchSetup(FormatBatch, FromDate, ToDate, Active, ProductGroup)
          VALUES(?, ?, ?, ?, ?)",
          [
            $format,
            $from_date,
            $to_date,
            0,
            $product_group
          ]
        );

        if (!$create) {
          return $this->handler->dbError();
        } else {
          return true;
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function updateActiveBatch($id, $activeStatus, $product_group)
  {
    $conn = $this->database->dbConnect();
    try {
      $update = sqlsrv_query(
        $conn,
        "UPDATE BatchSetup
        SET Active = ?,
        ProductGroup = ?
        WHERE ID = ?",
        [
          $activeStatus,
          $product_group,
          $id
        ]
      );

      if (!$update) {
        return $this->handler->dbError();
      } else {
        // if ($activeStatus === 1) {
        //   sqlsrv_query(
        //     $conn,
        //     "UPDATE BatchSetup 
        //     SET Active = 0
        //     WHERE ID <> ?",
        //     [
        //       $id
        //     ]
        //   );
        // }
        return true;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function isManualBatchOn()
  {
    $conn = $this->database->dbConnect();

    $rows = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT * FROM BatchSetup
      WHERE Active = 1"
    ));

    return $rows;
  }

  public function getManualBatch($datetime, $item, $press_no, $check = '')
  {
    $conn = $this->database->dbConnect();

    $getPressBAtch = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 BOI
      FROM PressMaster
      WHERE ID = ?",
      [
        $press_no
      ]
    );

    //28/5/65
    if ($getPressBAtch[0]["BOI"] == 'BOI1') {
      //$BOI = 1;//tan_modify 28/5/65
      $BOI = '1';
    }
    if ($getPressBAtch[0]["BOI"] == 'BOI2') {
      //$BOI = 2;//tan_modify 28/5/65
      $BOI = '2';
    }
    if ($getPressBAtch[0]["BOI"] == 'BOI3') {
      //$BOI = 3;//tan_modify 28/5/65
      $BOI = '3';
    }
    //tan_modify 28/5/65
    if($getPressBAtch[0]["BOI"] == 'BOI1-3') {
      $BOI = '1-3';
    }


    if ($this->isManualBatchOn() === false) return $this->getBatch($datetime, $BOI, '', $check);

    if ($this->isBatchSetupActive() === false) return $this->getBatch($datetime, $BOI, '', $check);



    $isDateMatch = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT FormatBatch FROM BatchSetup
      WHERE '$datetime' BETWEEN FromDate AND ToDate
      AND Active = 1"
    ));


    if ($isDateMatch === false) return $this->getBatch($datetime, $BOI, '', $check);

    if (USE_ITEMQ === true) {

      $sqlCheckItemActive = "SELECT 
        IM.ID, 
        IM.ProductGroup, 
        CCM.ItemQ 
        FROM ItemMaster IM
        LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = IM.ID
        WHERE IM.ManualBatch = 1
        AND IM.ID = REPLACE(?, 'Q', 'I')";

      $sqlGetProductGroup = "SELECT 
        IM.ID, 
        IM.ProductGroup, 
        CCM.ItemQ 
        FROM ItemMaster IM
        LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = IM.ID
        WHERE IM.ManualBatch = 1
        AND IM.ID = REPLACE(?, 'Q', 'I')";
    } else {
      $sqlCheckItemActive = "SELECT ID FROM ItemMaster
        WHERE ManualBatch = 1
        AND ID = ?";

      $sqlGetProductGroup = "SELECT ProductGroup 
        FROM ItemMaster
        WHERE ManualBatch = 1
        AND ID = ?";
    }

    // $sql2 = "SELECT 
    // IM.ID, 
    // IM.ProductGroup, 
    // CCM.ItemQ 
    // FROM ItemMaster IM
    // LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = IM.ID
    // WHERE IM.ManualBatch = 1
    // AND IM.ID = ?";

    $isItemActive = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      $sqlCheckItemActive,
      [
        $item
      ]
    ));

    if ($isItemActive === false) return $this->getBatch($datetime, $BOI, '', $check);

    $_productGroup = Sqlsrv::queryArray(
      $conn,
      $sqlGetProductGroup,
      [
        $item
      ]
    );

    if (count($_productGroup) === 0) {
      return $this->getBatch($datetime, $BOI, '', $check);
    }

    $getManualBatchText = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 FormatBatch
      FROM BatchSetup
      WHERE ProductGroup = ?
      AND Active = 1",
      [
        $_productGroup[0]["ProductGroup"]
      ]
    );



    if ((int) date("Y") >= 2021) {
      $dsc = BATCH_DSC;
    } else {
      $dsc = "";
    }

    // TEST: delete after passed
    // $dsc = BATCH_DSC;

    if (count($getManualBatchText) === 0) {

      $getManualBatchTextNoProductGroup = Sqlsrv::queryArray(
        $conn,
        "SELECT TOP 1 FormatBatch
        FROM BatchSetup
        WHERE Active = 1",
        [
          $_productGroup[0]["ProductGroup"]
        ]
      );

      return $getManualBatchTextNoProductGroup[0]['FormatBatch'] . $dsc . $BOI;
    } else {
      return $getManualBatchText[0]['FormatBatch'] . $dsc . $BOI;
    }
  }

  public function getGreentireBatch($barcode)
  {
    $conn = $this->database->dbConnect();

    $batch = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 Batch FROM InventTrans
      WHERE Barcode = ?
      AND WarehouseID = 1 
      AND LocationID = 2
      AND DocumentTypeID = 1
      ORDER BY CreateDate ASC",
      [
        $barcode
      ]
    );

    if (count($batch) === 0) {
      return $this->getBatch(date('Y-m-d H:i:s'), '');
    } else {
      return $batch[0]['Batch'];
    }
  }

  public function isBatchSetupActive()
  {
    $conn = $this->database->dbConnect();

    $config = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 BatchSetupActive 
      FROM [Configuration]"
    );

    if ((int) $config[0]['BatchSetupActive'] === 1) {
      return true;
    } else {
      return false;
    }
  }

  public function setBatchSetupActive($_status)
  {
    $conn = $this->database->dbConnect();

    $set = sqlsrv_query(
      $conn,
      "UPDATE Configuration 
      SET BatchSetupActive = ?",
      [
        (int) $_status
      ]
    );

    if (!$set) {
      return "Update status failed!";
    } else {
      return true;
    }
  }
}
