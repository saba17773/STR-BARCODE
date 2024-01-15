<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Item
{
    public $ID = null;
    public $NameTH = null;
    public $Pattern = null;
    public $Brand = null;
    public $UnitID = null;

    public function isItemExist()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT ID FROM ItemMaster
            WHERE ID = ?",
            [
                $this->ID
            ]
        );
    }

    public function isItemExists($item_id)
    {
        $conn = DB::connect();
        return sqlsrv_has_rows(sqlsrv_query(
            $conn,
            "SELECT ID FROM ItemMaster
            WHERE ID = ?",
            [
                $item_id
            ]
        ));
    }

    public function getItemSet()
    {
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM ItemMaster
            WHERE UnitID = ?",
            [
                "SET"
            ]
        );
    }

    public function getItemNormal()
    {
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM ItemMaster
            WHERE UnitID = ?",
            [
                "PCS"
            ]
        );
    }

    public function getItemGroupSM($value = '')
    {
        $conn = DB::connect();
        $sql =  "SELECT TOP 100 * FROM ItemMaster
            WHERE ItemGroup = ?";

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

                    // if ($filterdatafield === 'CheckBuild') {
                    //     if ((string) $filtervalue === 'true') {
                    //         $tmp_value = 1;
                    //     } else {
                    //         $tmp_value = 0;
                    //     }
                    //     $filtervalue = $tmp_value;
                    // }
                    //
                    // if ($filterdatafield === 'checkcur') {
                    //     if ((string) $filtervalue === 'true') {
                    //         $tmp_value1 = 1;
                    //     } else {
                    //         $tmp_value1 = 0;
                    //     }
                    //     $filtervalue = $tmp_value1;
                    // }

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
                    SELECT * FROM ItemMaster
                    WHERE ItemGroup = ? 
                ) X $where ORDER BY X.ID DESC";
            }
        }

        return Sqlsrv::queryJson(
            $conn,
            $sql,
            [
                "SM"
            ]
        );
    }
}
