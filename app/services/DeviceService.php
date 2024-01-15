<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use Wattanar\Sqlsrv;

class DeviceService
{
  public function __construct()
  {
    $this->db = new Database;
  }

  public function allInventTable()
  {
    $conn = Database::connectDeviceMOrmont(); //connectDeviceMOrmont
    $sql = "SELECT
            DT.ID,
            DT.FixedAssetNo,
            DT.SN,
            DM.Name AS NameDevice,
            DT.Devicetype,
            DT.StartWarranty,
            DT.EndWarranty,
            DT.IPAdress,
            DT.MacAddress,
            DT.PO,
            EM.FirstName + ' ' + EM.LastName   AS NameReceive,
            DT.ReceiveDate,
            UM.Name AS NameCreateBy,
            DT.CreateDate,
            UMM.Name AS NameUpdateBy,
            DT.UpdateDate,
            VM.Name,
            DT.Remark
            FROM  DSG_DeviceTable DT
            LEFT JOIN DSG_DeviceMaster DM ON DM.ID = DT.Devicetype
            LEFT JOIN DSG_VendorMaster VM ON VM.VendorID = DT.Vendor
            LEFT JOIN [STR_BARCODE_DEV3].[dbo].[UserMaster] UM ON UM.ID = DT.CreateBy
            LEFT JOIN [STR_BARCODE_DEV3].[dbo].[UserMaster] UMM ON UMM.ID = DT.UpdateBy
            LEFT JOIN [STR_BARCODE_DEV3].[dbo].[Employee] EM ON EM.Code = DT.ReceiveBy
            ORDER BY DT.ID DESC";

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
          //
          if ($filterdatafield === 'checkcur') {
            if ((string) $filtervalue === 'true') {
              $tmp_value1 = 1;
            } else {
              $tmp_value1 = 0;
            }
            $filtervalue = $tmp_value1;
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
        $sql =  "SELECT TOP 100 * FROM (
                      SELECT
                              DT.ID,
                              DT.FixedAssetNo,
                              DT.SN,
                              DM.Name AS NameDevice,
                              DT.Devicetype,
                              DT.StartWarranty,
                              DT.EndWarranty,
                              DT.IPAdress,
                              DT.MacAddress,
                              DT.PO,
                              UMMM.Name  AS NameReceive,
                              DT.ReceiveDate,
                              UM.Name AS NameCreateBy,
                              DT.CreateDate,
                              UMM.Name AS NameUpdateBy,
                              DT.UpdateDate,
                              VM.Name,
                              DT.Remark
                              FROM  DSG_DeviceTable DT
                              LEFT JOIN DSG_DeviceMaster DM ON DM.ID = DT.Devicetype
                              LEFT JOIN DSG_VendorMaster VM ON VM.VendorID = DT.Vendor
                              LEFT JOIN [STR_BARCODE_DEV3].[dbo].[UserMaster] UM ON UM.ID = DT.CreateBy
                              LEFT JOIN [STR_BARCODE_DEV3].[dbo].[UserMaster] UMM ON UMM.ID = DT.UpdateBy
                              LEFT JOIN [STR_BARCODE_DEV3].[dbo].[UserMaster] UMMM ON UMMM.ID = DT.ReceiveBy
                        ) X " . $where . "ORDER BY X.ID DESC";
      }
    }

    $query = Sqlsrv::queryJson(
      $conn,
      $sql
    );
    return $query;
  }

  public function transDetail($barcode)
  {
    $conn = Database::connectDeviceMOrmont();
    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT
          DT.ID,
          DT.Mac,
          DT.SN,
          DT.ReceiveUserDate,
          DT.Detail,
          DT.SendSVODate,
          DT.ReturnDate,
          DT.SendUserDate,
          DT.Remark,
          DT.CreateDate
          FROM DSG_DeviceTrans DT
              WHERE DT.DeviceID = ?
              ORDER BY DT.CreateDate ASC",
      [$barcode]
    );
    return $query;
  }

  public function vedorall()
  {
    $conn = Database::connectDeviceMOrmont();
    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT
        VM.VendorID,
        VM.Name
        FROM DSG_VendorMaster VM
        ORDER BY VM.VendorID ASC"
    );
    return $query;
  }

  public function deviceall()
  {
    $conn = Database::connectDeviceMOrmont();
    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT DM.ID,
        DM.Name
        FROM DSG_DeviceMaster DM
        ORDER BY DM.ID ASC"
    );
    return $query;
  }

  public function userall()
  {
    $conn = Database::connect();
    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT
      EM.Code AS ID,
      EM.FirstName + ' ' + EM.LastName AS Name
      FROM Employee EM
      ORDER BY EM.Code ASC"
    );
    return $query;
  }

  public function saveDeviceTable(
    $add_FixedAssetNo,
    $add_SN,
    $add_MacAddr,
    $add_IpAddr,
    $add_VendorID,
    $add_ReceiveBy,
    $add_ReceiveDate,
    $add_startWarrantyDate,
    $add_EndWarrantyDate,
    $add_DeviceID,
    $add_Remark,
    $add_PO,
    $checkTYpeInsert,
    $add_IDupdate
  ) {
    $conn = Database::connectDeviceMOrmont();
    $date = date("m-d-Y H:i:s");
    $time = (explode(" ", $date));
    $dataadd_ReceiveDate = $add_ReceiveDate . " " . $time[1];
    $dataadd_startWarrantyDate = $add_startWarrantyDate . " " . $time[1];
    $dataadd_EndWarrantyDate = $add_EndWarrantyDate . " " . $time[1];

    // ติดตรงคิดแยกfunction ของdate เพราะไม่สามารถInsert ได้
    $dataReceiveDate = date_format(date_create($dataadd_ReceiveDate), "Y-m-d H:i:s");
    $datastartWarrantyDate = date_format(date_create($dataadd_startWarrantyDate), "Y-m-d H:i:s");
    $dataEndWarrantyDate = date_format(date_create($dataadd_EndWarrantyDate), "Y-m-d H:i:s");
    // return [
    //   "status" => 404,
    //   "message" => $datastartWarrantyDate
    // ]; exit();

    if (sqlsrv_begin_transaction($conn) === false) {
      return "transaction failed!";
    }

    // return [
    //   "status" => 404,
    //   "message" => $dataReceiveDate
    // ];
    if ($checkTYpeInsert == 0) {
      $checkSN = Sqlsrv::queryArray(
        $conn,
        "SELECT SN FROM DSG_DeviceTable
          WHERE SN =?",
        [$add_SN]
      );

      if ($checkSN[0]["SN"] == $add_SN) {
        sqlsrv_rollback($conn);
        return [
          "status" => 404,
          "message" => "SN ซ้ำ"
        ];
      }
      $checkMc = Sqlsrv::queryArray(
        $conn,
        "SELECT MacAddress FROM DSG_DeviceTable
          WHERE MacAddress =?",
        [$add_MacAddr]
      );

      if ($checkMc[0]["MacAddress"] == $add_MacAddr) {
        sqlsrv_rollback($conn);
        return [
          "status" => 404,
          "message" => "MacAddress ซ้ำ"
        ];
      }



      $InsertDeviceTable = Sqlsrv::insert(
        $conn,
        "INSERT INTO DSG_DeviceTable(
              Devicetype,
              FixedAssetNo,
              SN,
              MacAddress,
              IPAdress,
              PO,
              Vendor,
              StartWarranty,
              EndWarranty,
              ReceiveBy,
              ReceiveDate,
              Remark,
              CreateDate,
              CreateBy,
              Status,
              ProjectID



            ) VALUES (?, ?, ?, ?,
                      ?, ?, ?, ?,
                      ?, ?, ?, ?,
                      ?, ?, ?, ?)",
        [
          $add_DeviceID,
          $add_FixedAssetNo,
          $add_SN,
          $add_MacAddr,
          $add_IpAddr,
          $add_PO,
          $add_VendorID,
          $dataadd_startWarrantyDate,
          $dataadd_EndWarrantyDate,
          $add_ReceiveBy,
          $dataReceiveDate,
          $add_Remark,
          $date,
          $_SESSION["user_login"],
          1,
          2

        ]
      );

      if ($InsertDeviceTable) {
        sqlsrv_commit($conn);
        return [
          "status" => 200,
          "message" => "True"
        ];
      } else {
        sqlsrv_rollback($conn);
        return [
          "status" => 404,
          "message" => $checkMc
        ];
      }
    } else {
      $checkdate = Sqlsrv::queryArray(
        $conn,
        "SELECT StartWarranty,EndWarranty,ReceiveDate FROM DSG_DeviceTable
          WHERE ID =?",
        [$add_IDupdate]
      );
      $startwar = date_format(date_create($add_startWarrantyDate), "Y-m-d");
      $EndWar = date_format(date_create($add_EndWarrantyDate), "Y-m-d");
      $recivecheck = date_format(date_create($add_ReceiveDate), "Y-m-d");
      $datetimestartwar = (explode(" ", $checkdate[0]["StartWarranty"]));
      $datetimeendtwar = (explode(" ", $checkdate[0]["EndWarranty"]));
      $datetimerecivecheck = (explode(" ", $checkdate[0]["ReceiveDate"]));
      if ($datetimestartwar[0] == $startwar) {
        $datastartWarrantyDate = $checkdate[0]["StartWarranty"];
      } else {
        $datastartWarrantyDate = $datastartWarrantyDate;
      }
      if ($datetimeendtwar[0] == $EndWar) {
        $dataEndWarrantyDate = $checkdate[0]["EndWarranty"];
      } else {
        $dataEndWarrantyDate = $dataEndWarrantyDate;
      }
      if ($datetimerecivecheck[0] == $recivecheck) {
        $dataReceiveDate = $checkdate[0]["ReceiveDate"];
      } else {
        $dataReceiveDate = $dataReceiveDate;
      }
      // return [
      //   "status" => 404,
      //   "message" =>$datastartWarrantyDate."+".$dataEndWarrantyDate."+".$dataReceiveDate
      // ];

      $updateDeviceTable = Sqlsrv::update(
        $conn,
        "UPDATE DSG_DeviceTable
          SET Devicetype = ?,
          FixedAssetNo = ?,
          SN =?,
          MacAddress = ?,
          IPAdress = ?,
          PO = ?,
          Vendor = ?,
          StartWarranty = ?,
          EndWarranty = ?,
          ReceiveBy = ?,
          ReceiveDate = ?,
          Remark = ?,
          UpdateDate = ?,
          UpdateBy = ?

          WHERE ID = ?",
        [
          $add_DeviceID,
          $add_FixedAssetNo,
          $add_SN,
          $add_MacAddr,
          $add_IpAddr,
          $add_PO,
          $add_VendorID,
          $datastartWarrantyDate,
          $dataEndWarrantyDate,
          $add_ReceiveBy,
          $dataReceiveDate,
          $add_Remark,
          $date,
          $_SESSION["user_login"],
          $add_IDupdate

        ]

      );

      if ($updateDeviceTable) {
        sqlsrv_commit($conn);
        return [
          "status" => 200,
          "message" => "True"
        ];
      } else {
        sqlsrv_rollback($conn);
        return [
          "status" => 404,
          "message" => "errorupdadte"
        ];
      }
    }
  }

  public function saveDeviceTabletrans(
    $add_MacAddr_trans,
    $add_SN_trans,
    $add_ReceiveDate_trans,
    $add_SVODate_trans,
    $add_ReturnDate_trans,
    $add_SendUserdate_trans,
    $add_Detail_trans,
    $add_Remark_trans,
    $add_IDupdate_trans
  ) {
    $conn = Database::connectDeviceMOrmont();
    $date = date("m-d-Y H:i:s");


    if (sqlsrv_begin_transaction($conn) === false) {
      return "transaction failed!";
    }

    $InsertDeviceTableTrans = Sqlsrv::insert(
      $conn,
      "INSERT INTO DSG_DeviceTrans(
            DeviceID,
            Mac,
            SN,
            ReceiveUserDate,
            Detail,
            SendSVODate,
            ReturnDate,
            SendUserDate,
            Remark,
            CreateDate




          ) VALUES (?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?)",
      [
        $add_IDupdate_trans,
        $add_MacAddr_trans,
        $add_SN_trans,
        $add_ReceiveDate_trans,
        $add_Detail_trans,
        $add_SVODate_trans,
        $add_ReturnDate_trans,
        $add_SendUserdate_trans,
        $add_Remark_trans,
        $date



      ]
    );

    if ($InsertDeviceTableTrans) {
      sqlsrv_commit($conn);
      return [
        "status" => 200,
        "message" => "Successful"
      ];
    } else {
      sqlsrv_rollback($conn);
      return [
        "status" => 404,
        "message" => "error"
      ];
    }
    //  }
    // if ($check_datainsert_trans == 1) {
    //   $updateDeviceTabletrans = Sqlsrv::update(
    //     $conn,
    //     "UPDATE DSG_DeviceTrans
    //     SET Mac = ?,
    //     SN = ?,
    //     ReceiveUserDate = ?,
    //     Detail = ?,
    //     SendSVODate = ?,
    //     ReturnDate = ?,
    //     SendUserDate = ?,
    //     Remark = ?,
    //     CreateDate = ?
    //
    //     WHERE ID = ?",
    //     [
    //
    //       $add_MacAddr_trans,
    //       $add_SN_trans,
    //       $add_ReceiveDate_trans,
    //       $add_Detail_trans,
    //       $add_SVODate_trans,
    //       $add_ReturnDate_trans,
    //       $add_SendUserdate_trans,
    //       $add_Remark_trans,
    //       $date,
    //       $add_IDupdate_trans
    //
    //
    //     ]
    //
    //   );
    //
    //   if ($updateDeviceTabletrans) {
    //     sqlsrv_commit($conn);
    //     return [
    //       "status" => 200,
    //       "message" => "Successful"
    //     ];
    //   } else {
    //     sqlsrv_rollback($conn);
    //     return [
    //       "status" => 404,
    //       "message" => "Error Update"
    //     ];
    //   }
    // }
    // if ($check_datainsert_trans == 2) {
    //   $DELETEDeviceTabletrans = sqlsrv_query(
    //     $conn,
    //     "DELETE DSG_DeviceTrans WHERE ID = ?",
    //     [
    //       $add_IDupdate_trans
    //     ]
    //   );
    //   if ($DELETEDeviceTabletrans) {
    //     sqlsrv_commit($conn);
    //     return [
    //       "status" => 200,
    //
    //       "message" => "Successful"
    //     ];
    //   } else {
    //     sqlsrv_rollback($conn);
    //     return [
    //       "status" => 404,
    //       "message" => "Error Delete"
    //     ];
    //   }
    // }
  }
}
