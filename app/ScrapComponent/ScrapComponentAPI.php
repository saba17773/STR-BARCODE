<?php

namespace App\ScrapComponent;

use App\V2\Database\Connector;
use Wattanar\SqlsrvHelper;

class ScrapComponentAPI
{
  private $conn = null;

  public function __construct() {
    $this->conn = (new Connector)->dbConnect();
  }

  public function saveScrapComponent(
    $area,
    $defect,
    $part_code,
    $qty,
    $scrap_location
  ) {

    if (\sqlsrv_begin_transaction($this->conn) === false) {
      return [
        'result' => false,
        'message' => 'begin trabsaction error'
      ];
    }

    $save = sqlsrv_query(
      $this->conn,
      "INSERT INTO ComponentScrap(
        PartCode,
        ScrapVolume,
        Unit,
        Defect,
        [Status],
        CreateBy,
        CreateDate
      ) VALUES(
        ?, ?, ?, ?, ?, 
        ?, ?
      )",
      [
        $part_code,
        $qty,
        3, // kg
        $defect,
        1,
        $_SESSION['user_login'],
        date('Y-m-d H:i:s')
      ]
    );

    if (!$save) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'save component scrap failed'
      ];
    }

    $partCodeData = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT CT.*, CS.OperationID 
      FROM ItemComponent CT 
      LEFT JOIN ComponentSection CS ON CS.SectionID = CT.SectionID
      WHERE CT.PastCodeID = ?
      ",
      [
        $part_code
      ]
    ));

    if ($partCodeData === null) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'item not found.'
      ];
    }

    $insertComponentTable = sqlsrv_query(
      $this->conn,
      "INSERT INTO ComponentTable (
        ItemID,
        SCH,
        GoodQty,
        ErrorQty,
        UnitGoodQty,
        UnitErrorQty,
        ErrorQtyConvert,
        Batch,
        DefectID,
        CreateBy,
        CreateDate,
        Shift,
        SCHDate,
        UpdateDate,
        UpdateBy,
        Company,
        OperationID,
        StartTime,
        EndTime,
        [Status],
        ComponentScrapArea,
        ComponentScrapLocation
      ) VALUES(
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?
      )",
      [
        $partCodeData['ItemID'],
        null,
        null,
        $qty,
        3,
        3,
        null,
        null,
        $defect,
        $_SESSION['user_login'],
        date("Y-m-d H:i:s"),
        $_SESSION['Shift'],
        date("Y-m-d H:i:s"),
        null,
        null,
        $_SESSION['user_company'],
        $partCodeData['OperationID'],
        null,
        null,
        1,
        $area,
        $scrap_location
      ]
    );

    if (!$insertComponentTable) {
      \sqlsrv_rollback($this->conn);
      return [
        'result' => false,
        'message' => 'item not found.'
      ];
    }

    sqlsrv_commit($this->conn);
    return [
      'result' => true,
      'message' => 'save success.'
    ];
  }

  public function getAll() {
    return (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      "SELECT 
      CS.ID,
      CS.PartCode,
      CS.ScrapVolume,
      U.[Description] AS Unit,
      CS.Defect,
      D.[Description] DefectName,
      UM.Name AS UserName,
      CS.CreateDate,
      S.[Description] AS [Status]
      from ComponentScrap CS
      left join UnitMaster U ON U.ID = CS.Unit
      left join Defect D ON D.ID = CS.Defect
      LEFT JOIN UserMaster UM ON UM.ID = CS.CreateBy
      LEFT JOIN [Status] S ON S.ID = CS.[Status]"
    ));
  }

  public function saveCancel($scrapId) {

    if (\sqlsrv_begin_transaction($this->conn) === false) {
      return jsonResult(false, 'begin transaction failed');
    }

    $updateComponentScrap = sqlsrv_query(
      $this->conn,
      "UPDATE ComponentScrap 
      SET [Status] = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE ID = ?",
      [
        3,
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $scrapId
      ]
    );

    if (!$updateComponentScrap) {
      sqlsrv_rollback($this->conn);
      return \jsonResult(false, 'update component scrap failed');
    }

    $updateComponentTable = sqlsrv_query(
      $this->conn,
      "UPDATE ComponentTable 
      SET [Status] = ?,
      ComponentScrapArea = ?,
      ComponentScrapLocation = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE ID = ?",
      [
        3,
        null,
        null,
        $_SESSION['user_login'],
        date('Y-m-d H:i:s'),
        $scrapId
      ]
    );

    if (!$updateComponentTable) {
      sqlsrv_rollback($this->conn);
      return \jsonResult(false, 'update component Table failed');
    }

    sqlsrv_commit($this->conn);
    return \jsonResult(false, 'cancel successful');
  }

  public function saveComplete($SCHDate, $partCode) {

    $componentList = (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      "SELECT 
      CI.PastCodeID [PartCode],
      CT.ID [ID]
      FROM ComponentTable CT
      LEFT JOIN  ItemComponent CI ON CT.ItemID = CI.ItemID
      WHERE CONVERT(DATE, CT.SCHDate) = ?
      AND CT.SCHDate is not null
      AND CI.PastCodeID = ?",
      [
        date('Y-m-d', strtotime($SCHDate)),
        $partCode
      ]
    ));

    if (count($componentList) === 0) {
      return \jsonResult(false, 'part code in select date not found!');
    }

    if (\sqlsrv_begin_transaction($this->conn) === false) {
      return \jsonResult(false, 'begin transaction failed');
    }

    foreach ($componentList as $v) {
      $update = sqlsrv_query(
        $this->conn,
        "UPDATE ComponentTable
        SET [Status] = 3,
        ComponentScrapArea = ?,
        ComponentScrapLocation = ?
        WHERE ID = ?
        ",
        [
          null,
          null,
          $v['ID']
        ]
      );

      if (!$update) {
        sqlsrv_rollback($this->conn);
        return \jsonResult(false, 'update failed!');
      }
    }

    sqlsrv_commit($this->conn);
    return \jsonResult(true, 'complete successful');
  }
}