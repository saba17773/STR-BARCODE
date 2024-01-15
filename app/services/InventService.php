<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use Wattanar\Sqlsrv;

class InventService
{
    public function __construct()
    {
        $this->db = new Database;
    }

    public function allInventTable()
    {
        $conn = Database::connect();

        $sql = "SELECT TOP 100
        IT.ID,
        IT.Barcode,
        IT.BarcodeFoil,
        IT.DateBuild,
        BM.Description AS BuildingNo,
        IT.GT_Code,
        IT.CuringDate,
        IT.CuringCode,
        IT.ItemID,
        IM.NameTH,
        IT.Batch,
        IT.QTY,
        UN.Description [Unit],
        IT.PressNo,
        IT.PressSide,
        IT.MoldNo,
        IT.TemplateSerialNo,
        IT.CuredTireReciveDate,
        IT.CuredTireLineNo,
        IT.XrayDate,
        IT.XrayNo,
        IT.FinalReceiveDate,
        G.Description [GateDescription],
        IT.WarehouseReceiveDate,
        IT.WarehouseTransReceiveDate,
        IT.LoadingDate,
        IT.DONo,
        IT.PickingListID,
        IT.OrderID,
        D.DisposalDesc [Disposal],
        WH.Description [WH],
        LC.Description [LC],
        S.Description [Status],
        IT.Company,
        U.Name,
        U.Username,
        IT.UpdateDate,
        IT.CreateDate,
        IT.Weight,
        IT.CheckBuild,
        IT.CuredTireReciveDate,
        IT.GT_InspectionDate,
        case when IT.CuredTireReciveDate is null then 0
	           when IT.CuredTireReciveDate IS not null then 1
	            end [checkcur],
	    case when WHC.Barcode is null then 0
	           else 1
	            end [checkcounting]
        FROM InventTable IT
        LEFT JOIN ItemMaster IM ON (
        CASE
            WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN REPLACE(IT.ItemID, 'Q', 'I')
            ELSE IT.ItemID
        END
        ) = IM.ID
        LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
        LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
        LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
        LEFT JOIN WarehouseCounting WHC ON WHC.Barcode = IT.Barcode
        LEFT JOIN Location LC ON LC.ID = IT.LocationID
        LEFT JOIN InventStatus S ON S.ID = IT.Status
        LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
        LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
        LEFT JOIN BuildingMaster BM ON BM.ID = IT.BuildingNo
        ORDER BY IT.ID DESC";

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

                    if ($filterdatafield === 'checkcounting') {
                        if ((string) $filtervalue === 'true') {
                            $tmp_value2 = 1;
                        } else {
                            $tmp_value2 = 0;
                        }
                        $filtervalue = $tmp_value2;
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
                    SELECT IT.ID,
                    IT.Barcode,
                    IT.BarcodeFoil,
                    IT.DateBuild,
                    IT.BuildingNo,
                    IT.GT_Code,
                    IT.CuringDate,
                    IT.CuringCode,
                    IT.ItemID,
                    IM.NameTH,
                    IT.Batch,
                    IT.QTY,
                    UN.Description [Unit],
                    IT.PressNo,
                    IT.PressSide,
                    IT.MoldNo,
                    IT.TemplateSerialNo,
                    IT.CuredTireReciveDate,
                    IT.CuredTireLineNo,
                    IT.XrayDate,
                    IT.XrayNo,
                    IT.FinalReceiveDate,
                    G.Description [GateDescription],
                    IT.WarehouseReceiveDate,
                    IT.WarehouseTransReceiveDate,
                    IT.LoadingDate,
                    IT.DONo,
                    IT.PickingListID,
                    IT.OrderID,
                    D.DisposalDesc [Disposal],
                    WH.Description [WH],
                    LC.Description [LC],
                    S.Description [Status],
                    IT.Company,
                    U.Name,
                    U.Username,
                    IT.UpdateDate,
                    IT.CreateDate,
                    IT.Weight,
                    IT.CheckBuild,
                    IT.GT_InspectionDate,
                    case when IT.CuredTireReciveDate is null then 0
            	           when IT.CuredTireReciveDate IS not null then 1
            	            end [checkcur],
                    case when WHC.Barcode is null then 0
	                       else 1
	                         end [checkcounting]
                    FROM InventTable IT
                    LEFT JOIN ItemMaster IM ON (
                    CASE
                        WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN REPLACE(IT.ItemID, 'Q', 'I')
                        ELSE IT.ItemID
                    END
                    ) = IM.ID
                    LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
                    LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
                    LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
                    LEFT JOIN WarehouseCounting WHC ON WHC.Barcode = IT.Barcode
                    LEFT JOIN Location LC ON LC.ID = IT.LocationID
                    LEFT JOIN InventStatus S ON S.ID = IT.Status
                    LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
                    LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
                    ) X " . $where . "ORDER BY X.ID DESC";
            }
        }

        $query = Sqlsrv::queryJson(
            $conn,
            $sql
        );

        // $result = [];

        // foreach ($query as $v) {
        //     $v["BarcodeEncode"] = Security::_encode($v["Barcode"]);
        //     $result[] = $v;
        // }

        // return json_encode($result);

        return $query;
    }

    public function transDetail($barcode)
    {
        $conn = Database::connect();
        $query = Sqlsrv::queryJson(
            $conn,
            "SELECT
                IT.TransID,
                IT.RefDocId,
                IT.Barcode,
                IT.CodeID,
                IT.Batch,
                D.DisposalDesc [Disposal],
                DF.Description [Defect],
                IT.QTY,
                IM.NameTH,
                UN.Description [Unit],
                WH.Description [WH],
                LC.Description [LC],
                IT.Company,
                DT.Description [Document],
                U.Name [CreateBy],
                U.Username,
                SM.Description [Shift],
                IT.InventJournalID,
                IT.AuthorizeBy as AuthorizeName,
                SS.Side,
                IT.CreateDate,
                CASE 
                    WHEN TD.HoldType = 1 THEN 'Normal'
                    WHEN TD.HoldType = 2 THEN 'Mode Light Buff'
                ELSE '' END HoldType
                FROM InventTrans IT
                LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
                LEFT JOIN ItemMaster IM ON (
                CASE
                    WHEN SUBSTRING(IT.CodeID, 1, 1) = 'Q' THEN REPLACE(IT.CodeID, 'Q', 'I')
                    ELSE IT.CodeID
                END
                ) = IM.ID
                LEFT JOIN Defect DF ON DF.ID = IT.DefectID
                LEFT JOIN UnitMaster UN ON UN.ID = IT.UnitID
                LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
                LEFT JOIN Location LC ON LC.ID = IT.LocationID
                LEFT JOIN DocumentTypeMaster DT ON DT.ID = IT.DocumentTypeID
                LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
                LEFT JOIN ShiftMaster SM ON SM.ID = IT.Shift
                LEFT JOIN ScrapSide SS ON SS.ID = IT.ScrapSide
                LEFT JOIN TransDefect TD ON TD.Barcode = IT.Barcode
                AND TD.DisposalID = IT.DisposalID AND TD.LocationID = IT.LocationID AND TD.WarehouseID = IT.WarehouseID
                AND TD.CreateDate = IT.CreateDate
                WHERE IT.Barcode = ?
                ORDER BY IT.id ASC",
            [$barcode]
        );
        return $query;
    }

    public function isScrap($barcode)
    {
        // select disposal id from barcode in inventtable
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT DisposalID FROM InventTable
            WHERE Barcode = ?
            AND DisposalID = 2",
            [Security::_decode($barcode)]
        );
    }

    public function isCuringCodeNull($barcode)
    {
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT CuringCode FROM InventTable
            WHERE Barcode = ?
            AND CuringCode is null",
            [Security::_decode($barcode)]
        );
    }

    public function checkGreenTireCodeAndCuringCode($barcode, $curing_code_master)
    {
        $conn = Database::connect();
        $get_from_inventtable = Sqlsrv::queryArray(
            $conn,
            "SELECT TOP 1 GT_Code
            FROM InventTable
            WHERE Barcode = ?",
            [Security::_decode($barcode)]
        );

        if (!$get_from_inventtable) {
            return false;
        }

        $greentire_code = $get_from_inventtable[0]["GT_Code"];

        $check = Sqlsrv::hasRows(
            $conn,
            "SELECT ID, GreentireID
            FROM CureCodeMaster
            WHERE ID = ?
            AND GreentireID = ?",
            [$curing_code_master, $greentire_code]
        );

        return $check;
    }

    public function isExist($barcode)
    {
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?
            AND Status <> 3", // Confirmed
            [Security::_decode($barcode)]
        );
    }

    public function checkWarehouseReceiveDate($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $q = Sqlsrv::hasRows(
            $conn,
            "SELECT Barcode FROM InventTable
            WHERE Barcode = ?
            AND WarehouseReceiveDate IS NOT NULL",
            [$barcode_decode]
        );
        return $q;
    }

    public function checkWarehouseReceiveData($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $q = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?
            AND WarehouseReceiveDate IS NOT NULL",
            [$barcode_decode]
        );
        return $q;
    }

    public function checkItemId($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?
            AND ItemID IS NOT NULL",
            [$barcode_decode]
        );
    }

    public function checkWarehouseTransReceiveData($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $q = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?
            AND WarehouseTransReceiveDate IS NOT NULL",
            [$barcode_decode]
        );
        return $q;
    }

    public function checkWarehouseTransReceiveDate($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $q = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?
            AND WarehouseTransReceiveDate IS NOT NULL",
            [$barcode_decode]
        );
        return $q;
    }

    public function isIssued($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Status = 4 -- issued
            AND Barcode = ?", // issued
            [$barcode_decode]
        );
        return $query;
    }

    public function isReceived($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Status = 1 -- receive
            AND Barcode = ?",
            [$barcode_decode]
        );
        return $query;
    }

    public function isPicked($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT Barcode FROM InventTable
            WHERE Status = 2 -- picked
            AND Barcode = ?",
            [$barcode_decode]
        );
        return $query;
    }

    public function isReverse($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
          WHERE DisposalID = 13 -- receive
          AND Barcode = ?",
            [$barcode_decode]
        );
    }

    public function isIssuedByWarehouseFinal($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
        WHERE WarehouseID = ? -- receive
        AND Barcode = ?",
            [$_SESSION['user_warehouse'], $barcode_decode]
        );
    }

    public function countReceiveToWarehouseFromFinal($product_group)
    {

        if ($product_group === 'RDT') {
            $sendSVODate = ' AND SendSVODate IS NULL';
        } else {
            $sendSVODate = '';
        }

        $conn = Database::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT IT.WarehouseTransReceiveDate AS CountReceive
            FROM InventTable IT
            LEFT JOIN ItemMaster IM ON IM.ID = IT.ItemID
            WHERE IT.WarehouseTransReceiveDate IS NOT NULL
            AND IT.WarehouseReceiveDate IS NULL
            AND IM.ProductGroup = '$product_group' $sendSVODate"
        );
    }

    public function isStatusConfirmedOrIssue($barcode)
    {
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Barcode FROM InventTable
            WHERE Status IN (3,4) -- Confirmed, issue
            AND Barcode = ?",
            [$barcode]
        );
    }
    public function CheckGreentireQc($barcode)
    {
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT GT_InspectionDate FROM InventTable
            WHERE Barcode = ?
            AND GT_InspectionDate is null
            AND WarehouseID = 1",
            [Security::_decode($barcode)]
        );
    }

    public function countFinalToWh($Id)
    {

        $conn = Database::connect();
        return Sqlsrv::queryJson(
            $conn,
            " SELECT *  FROM SendToWHLine SL
            LEFT JOIN  SendToWHTable  ST ON ST.JournalID = SL.JournalID
            WHERE ST.Id = '$Id'
          "
        );
    }

    public function ischeckboi($press_no)
    {

        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM PressMaster
            WHERE ID = ? -- receive
            AND BOI IS NOT NULL ",
            [$press_no]
        );
        return $query;
    }
    public function isRecivefinal($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE CuringDate IS NOT NULL AND FinalReceiveDate IS NOT NULL
            AND Barcode = ?", // issued
            [$barcode_decode]
        );
        return $query;
    }

    public function isExistInventTable($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 Barcode FROM InventTable
            WHERE  Barcode = ?", // issued
            [$barcode_decode]
        );
        return $query;
    }

    public function isNotCuringFinal($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 Barcode FROM InventTable
            WHERE  Barcode = ?
            AND CuringDate IS  NULL  AND DisposalID <> 3", // issued
            [$barcode_decode]
        );
        return $query;
    }
    public function isSave($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $query = Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 Barcode FROM InventTable
            WHERE  Barcode = ?
             AND CuringDate IS NOT NULL  AND DisposalID = 3 AND FinalReceiveDate IS NULL AND Status = 1",
            [$barcode_decode]
        );
        return $query;
    }

    public function checktrans($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
       $query = Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 Barcode FROM InventTable
            WHERE  Barcode = ?
             AND WarehouseID = 4  AND LocationID = 3",
            [$barcode_decode]
        );

        return $query;
    }

    public function txtSuccess($barcode)
    {
        $barcode_decode = Security::_decode($barcode);
        $conn = Database::connect();
        $get_txt = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);


        return $get_txt[0]["CuringCode"].",".$get_txt[0]["Batch"]."=> ".$get_txt[0]["Barcode"];
    }

    public function CheckGreentireActive()
    {
        $conn = Database::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Active FROM SetupMaster
            WHERE Active = 0 AND [Type] = 1"
        );
    }


}
