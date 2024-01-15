<?php

namespace App\V2\Item;

use App\V2\Database\Connector;
use App\V2\Database\Handler;

use Wattanar\Sqlsrv;

class ItemAPI
{
  private $conn = null;
  private $handler = null;

  public function __construct()
  {
    $this->conn = new Connector();
    $this->handler = new Handler();
  }

  public function hasItem(string $itemId)
  {
    $conn = $this->conn->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ID FROM ItemMaster
      WHERE ID = ?",
      [
        $itemId
      ]
    ));
  }

  public function getAllItemFG()
  {
    $conn = $this->conn->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, NameTH FROM ItemMaster
      WHERE ItemGroup = 'FG'"
    );
  }

  public function getAllItem()
  {
    $conn = $this->conn->dbConnect();
    $sql =  "SELECT TOP 100
      IM.ID,
      IM.NameTH,
      IM.Pattern,
      IM.Brand,
      IM.UnitID,
      IM.SubGroup,
      IM.ProductGroup,
      IM.ItemGroup,
      IM.InternalNumber,
      IM.QtyPerPallet,
      IM.ManualBatch,
      IM.CheckSerial,
      IM.Channel,
      CCM.ItemQ
      FROM ItemMaster IM
      LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = IM.ID
      ORDER BY ID ASC";

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
        $sql = "SELECT TOP 100 * FROM (
          SELECT
            IM.ID,
            IM.NameTH,
            IM.Pattern,
            IM.Brand,
            IM.UnitID,
            IM.SubGroup,
            IM.ProductGroup,
            IM.ItemGroup,
            IM.InternalNumber,
            IM.QtyPerPallet,
            IM.ManualBatch,
            IM.CheckSerial,
            IM.Channel,
            CCM.ItemQ
            FROM ItemMaster IM
            LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = IM.ID
          ) X " . $where . " ORDER BY X.ID DESC";
      }
    }

    $query = Sqlsrv::queryJson(
      $conn,
      $sql
    );

    return $query;
  }

  public function setManualBatch($itemId, $manualBatch)
  {
    $conn = $this->conn->dbConnect();
    $setManual = sqlsrv_query(
      $conn,
      "UPDATE ItemMaster
      SET ManualBatch = ?
      WHERE ID = ?",
      [
        $manualBatch,
        $itemId
      ]
    );

    if (!$setManual) {
      return $this->handler->dbError();
    } else {
      return true;
    }
  }

  public function getItemInfo($itemId)
  {
    $conn = $this->conn->dbConnect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ItemMaster
      WHERE ID = ?",
      [
        $itemId
      ]
    );
  }

  public function setCheckSerial($itemId, $checkSerial)
  {
    $conn = $this->conn->dbConnect();
    $setC = sqlsrv_query(
      $conn,
      "UPDATE ItemMaster
      SET CheckSerial = ?
      WHERE ID = ?",
      [
        $checkSerial,
        $itemId
      ]
    );

    if (!$setC) {
      return $this->handler->dbError();
    } else {
      return true;
    }
  }

  public function updateChannel($itemId, $channel)
  {
    $update = sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ItemMaster
      SET Channel = ?
      WHERE ID = ?",
      [
        $channel,
        $itemId
      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }
}
