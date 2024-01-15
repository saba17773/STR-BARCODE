<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use Wattanar\Sqlsrv;

class OnhandService
{
	public function all()
	{
    $warehouse = $_SESSION["user_warehouse"];
		$conn = Database::connect();

    if ($_SESSION["user_permission"] !== 11) {

      return Sqlsrv::queryJson(
        $conn,
        "SELECT
        (
          case
            when ITM.NameTH is null then I.GT_Code
            else ITM.ID
          end
          ) [CodeID],
        ITM.NameTH [ItemName],
        WM.Description [Warehouse],
        L.Description [Location],
        I.Batch,
        SUM(I.QTY) [QTY]
        from InventTable I
        left join WarehouseMaster WM ON WM.ID = I.WarehouseID
        left join Location L ON L.ID = I.LocationID
        left join ItemMaster ITM ON ITM.ID = I.ItemID
        where I.Status NOT IN (3,4) AND I.WarehouseID = '$warehouse'
        group by
        I.GT_Code,
        ITM.ID,
        ITM.NameTH,
        WM.Description,
        L.Description,
        I.Batch
        order by I.GT_Code asc"
      );

    } else {
      return Sqlsrv::queryJson($conn,
        "SELECT
          (
          case
            when ITM.NameTH is null then I.GT_Code
            else ITM.ID
          end
          ) [CodeID],
        ITM.NameTH [ItemName],
        WM.Description [Warehouse],
        L.Description [Location],
        I.Batch,
        SUM(I.QTY) [QTY]
        from InventTable I
        left join WarehouseMaster WM ON WM.ID = I.WarehouseID
        left join Location L ON L.ID = I.LocationID
        left join ItemMaster ITM ON ITM.ID = I.ItemID
        where I.Status NOT IN (3,4)
        group by
        I.GT_Code,
        ITM.ID,
        ITM.NameTH,
        WM.Description,
        L.Description,
        I.Batch
        order by I.GT_Code asc"
      );
    }
	}

	public function getGreentireHold()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
      IT.ItemID,
      IM.NameTH,
			IM.ProductGroup,
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
      (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        ORDER BY CreateDate DESC
    	) as DefectID,
      (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
          SELECT TOP 1 DefectID FROM InventTrans
          WHERE DefectID IS NOT NULL
          AND IT.Barcode = InventTrans.Barcode
          ORDER BY CreateDate DESC
        )
      ) as DefectDesc,
      (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        ORDER BY X.CreateDate ASC
      ) as Shift
      FROM InventTable IT
      LEFT JOIN ItemMaster IM ON IT.ItemID = IM.ID
      LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
      LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
      LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      WHERE IT.Status = 5 -- hold
      AND IT.WarehouseID = 1 -- greentire
      AND IT.DisposalID = 10  -- hold
      ORDER BY IT.UpdateDate DESC"
		);

		$result = [];

		foreach ($query as $v) {
			$v["Barcode"] = Security::_encode($v["Barcode"]);
			$result[] = $v;
		}

		return json_encode($result);
	}

	public function getFinalHold($data)
	{

		$conn = Database::connect();

    if (isset($_GET['filterscount']))
        {
            $filterscount = $_GET['filterscount'];

            if ($filterscount > 0)
            {
                $sql = "";
                $where = "WHERE (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i=0; $i < $filterscount; $i++)
                {
                    // get the filter's value.
                    $filtervalue = $_GET["filtervalue" . $i];
                    // get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = $_GET["filterdatafield" . $i];
                    // get the filter's operator.
                    $filteroperator = $_GET["filteroperator" . $i];

                    if ($tmpdatafield == "")
                    {
                        $tmpdatafield = $filterdatafield;
                    }
                    else if ($tmpdatafield <> $filterdatafield)
                    {
                        $where .= ")AND(";
                    }
                    else if ($tmpdatafield == $filterdatafield)
                    {
                        if ($tmpfilteroperator == 0)
                        {
                            $where .= " AND ";
                        }
                        else $where .= " OR ";
                    }

                    // build the "WHERE" clause depending on the filter's condition, value and datafield.
                    switch($filtercondition)
                    {
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
                            break;
                    }

                    if ($i == $filterscount - 1)
                    {
                        $where .= ")";
                    }

                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;
                }
                // build the query.
                $sql = "SELECT TOP 100 * FROM () X " . $where . "ORDER BY X.ID DESC";
            }
        }
				if($data=='RDT' or $data=='TBR' )
				{
				$where_producGroup = "AND IM.ProductGroup = '$data'";
				}
				else {
					$where_producGroup = "AND IM.ProductGroup IS NOT NULL";
				}

		$query =  Sqlsrv::queryArray($conn,
			"SELECT
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
      IT.ItemID,
      IM.NameTH,
	    IM.ProductGroup,
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
      (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        ) as DefectID,
        (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        )
        ) as DefectDesc,
        (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        AND IT.UpdateDate = X.CreateDate
        ORDER BY X.CreateDate ASC
        ) as Shift
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
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      WHERE IT.Status = 5 -- hold
      AND IT.WarehouseID = 2 -- final
      AND IT.DisposalID IN (9, 10) -- return, hold
		 	 $where_producGroup
      ORDER BY IT.UpdateDate DESC"
		);

		$result = [];

		foreach ($query as $v) {
			$v["Barcode"] = Security::_encode($v["Barcode"]);
			$result[] = $v;
		}

		return json_encode($result);
	}

	public function updateOnhand($item_code, $type)
	{
		return false;
	}

  public function isItemExist($WarehouseID, $LocationID, $Batch, $Company, $CodeID)
  {
      $conn = Database::connect();
      return Sqlsrv::hasRows(
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
              $WarehouseID,
              $LocationID,
              $Batch,
              $Company,
              $CodeID
          ]
      );
  }

  public function getGtCureFinal($producGroup,$locationtype)
  {

    $conn = Database::connect();

    if($producGroup=='RDT' or $producGroup=='TBR' )
    {
      $where_producGroup = "AND IM.ProductGroup = '$producGroup'";
    }else {
      $where_producGroup = "AND IM.ProductGroup IS NOT NULL";
    }

    if($locationtype=='cure'){
      $where_wh = "WHERE IT.WarehouseID =4 AND IT.DisposalID =3 ".$where_producGroup;
    }else if($locationtype=='final'){
      $where_wh = "WHERE IT.WarehouseID =2 AND IT.DisposalID =4 ".$where_producGroup;
    }else{
      $where_wh = "WHERE IT.WarehouseID =1 AND IT.DisposalID =1 AND IMG.ProductGroup='SEM' ";
    }
        // $where_all = $where_wh.$where_producGroup;

    $query =  Sqlsrv::queryArray($conn,
      "SELECT TOP 200
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
  
      CASE
      WHEN IT.WarehouseID =1 THEN GT.ItemNumber
      ELSE IT.ItemID 
      END AS ItemID,
      CASE
      WHEN IT.WarehouseID =1 THEN IMG.NameTH
      ELSE IM.NameTH 
      END AS NameTH,
      CASE
      WHEN IT.WarehouseID =1 THEN IMG.ProductGroup
      ELSE IM.ProductGroup 
      END AS ProductGroup,
    
   --   IT.ItemID,
   --   IM.NameTH,
    --IM.ProductGroup,
    
    --GT.ItemNumber,
    --IMG.NameTH,
    --IMG.ProductGroup,
    
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
      
      IT.Status,
      IT.WarehouseID,
      IT.DisposalID,
      
      D.DisposalDesc [Disposal],
      WH.Description [WH],
      LC.Description [LC],
      S.Description [Status],
      IT.Company,
      U.Name,
      U.Username,
      IT.UpdateDate,
      IT.CreateDate,
      (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        ) as DefectID,
        (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        )
        ) as DefectDesc,
        (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        AND IT.UpdateDate = X.CreateDate
        ORDER BY X.CreateDate ASC
        ) as Shift
      
      FROM InventTable IT
      
      LEFT JOIN ItemMaster IM ON (
        CASE
            WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN REPLACE(IT.ItemID, 'Q', 'I')
            ELSE IT.ItemID
        END
        ) = IM.ID
        
      LEFT JOIN GreentireCodeMaster GT ON GT.ID = IT.GT_Code
      LEFT JOIN ItemMaster IMG ON IMG.ID = GT.ItemNumber
             
      LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
      LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
      LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      $where_wh
      ORDER BY IT.UpdateDate DESC"
    );

    return json_encode($query);
  }

  public function getGtCureFinal2($producGroup,$locationtype)
  {
      $conn = Database::connect();

      if($producGroup=='RDT' or $producGroup=='TBR' )
      {
        $where_producGroup = "AND IM.ProductGroup = '$producGroup'";
      }
      else {
        $where_producGroup = "AND IM.ProductGroup IS NOT NULL";
      }

      if($locationtype=='cure'){
        $where_wh = "WHERE IT.WarehouseID =4 AND IT.DisposalID =3 ".$where_producGroup;
        $where_whx = "AND IT.WarehouseID =4 AND IT.DisposalID =3 ".$where_producGroup;
      }else if($locationtype=='final'){
        $where_wh = "WHERE IT.WarehouseID =2 AND IT.DisposalID =4 ".$where_producGroup;
        $where_whx = "AND IT.WarehouseID =2 AND IT.DisposalID =4 ".$where_producGroup;
      }else{
        $where_wh = "WHERE IT.WarehouseID =1 AND IT.DisposalID =1 AND IMG.ProductGroup='SEM' ";
        $where_whx = "AND IT.WarehouseID =1 AND IT.DisposalID =1 AND IMG.ProductGroup='SEM' ";
      }

      $sql = "SELECT TOP 200
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
  
      CASE
      WHEN IT.WarehouseID =1 THEN GT.ItemNumber
      ELSE IT.ItemID 
      END AS ItemID,
      CASE
      WHEN IT.WarehouseID =1 THEN IMG.NameTH
      ELSE IM.NameTH 
      END AS NameTH,
      CASE
      WHEN IT.WarehouseID =1 THEN IMG.ProductGroup
      ELSE IM.ProductGroup 
      END AS ProductGroup,
    
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
      
      IT.Status,
      IT.WarehouseID,
      IT.DisposalID,
      
      D.DisposalDesc [Disposal],
      WH.Description [WH],
      LC.Description [LC],
      S.Description [Status],
      IT.Company,
      U.Name,
      U.Username,
      IT.UpdateDate,
      IT.CreateDate,
      (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        ) as DefectID,
        (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
        SELECT TOP 1 DefectID FROM InventTrans
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        AND IT.UpdateDate = InventTrans.CreateDate
        ORDER BY CreateDate DESC
        )
        ) as DefectDesc,
        (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        AND IT.UpdateDate = X.CreateDate
        ORDER BY X.CreateDate ASC
        ) as Shift
      
      FROM InventTable IT
      
      LEFT JOIN ItemMaster IM ON (
        CASE
            WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN REPLACE(IT.ItemID, 'Q', 'I')
            ELSE IT.ItemID
        END
        ) = IM.ID
        
      LEFT JOIN GreentireCodeMaster GT ON GT.ID = IT.GT_Code
      LEFT JOIN ItemMaster IMG ON IMG.ID = GT.ItemNumber
             
      LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
      LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
      LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      $where_wh
      ORDER BY IT.UpdateDate DESC";

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
                $sql = "SELECT TOP 100 * FROM (
                    SELECT 
                    IT.ID,
                    IT.Barcode,
                    IT.DateBuild,
                    IT.BuildingNo,
                    IT.GT_Code,
                    IT.CuringDate,
                    IT.CuringCode,
                
                    CASE
                    WHEN IT.WarehouseID =1 THEN GT.ItemNumber
                    ELSE IT.ItemID 
                    END AS ItemID,
                    CASE
                    WHEN IT.WarehouseID =1 THEN IMG.NameTH
                    ELSE IM.NameTH 
                    END AS NameTH,
                    CASE
                    WHEN IT.WarehouseID =1 THEN IMG.ProductGroup
                    ELSE IM.ProductGroup 
                    END AS ProductGroup,
                  
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
                    
                    IT.Status,
                    IT.WarehouseID,
                    IT.DisposalID,
                    
                    D.DisposalDesc [Disposal],
                    WH.Description [WH],
                    LC.Description [LC],
                    S.Description [Status],
                    IT.Company,
                    U.Name,
                    U.Username,
                    IT.UpdateDate,
                    IT.CreateDate,
                    (
                      SELECT TOP 1 DefectID FROM InventTrans
                      WHERE DefectID IS NOT NULL
                      AND IT.Barcode = InventTrans.Barcode
                      AND IT.UpdateDate = InventTrans.CreateDate
                      ORDER BY CreateDate DESC
                      ) as DefectID,
                      (
                      SELECT DF.Description FROM Defect DF
                      WHERE DF.ID = (
                      SELECT TOP 1 DefectID FROM InventTrans
                      WHERE DefectID IS NOT NULL
                      AND IT.Barcode = InventTrans.Barcode
                      AND IT.UpdateDate = InventTrans.CreateDate
                      ORDER BY CreateDate DESC
                      )
                      ) as DefectDesc,
                      (
                      SELECT TOP 1 S.Description FROM InventTrans X
                      LEFT JOIN ShiftMaster S ON S.ID = X.Shift
                      WHERE X.Shift IS NOT NULL
                      AND IT.Barcode = X.Barcode
                      AND IT.UpdateDate = X.CreateDate
                      ORDER BY X.CreateDate ASC
                      ) as Shift
                    
                    FROM InventTable IT
                    
                    LEFT JOIN ItemMaster IM ON (
                      CASE
                          WHEN SUBSTRING(IT.ItemID, 1, 1) = 'Q' THEN REPLACE(IT.ItemID, 'Q', 'I')
                          ELSE IT.ItemID
                      END
                      ) = IM.ID
                      
                    LEFT JOIN GreentireCodeMaster GT ON GT.ID = IT.GT_Code
                    LEFT JOIN ItemMaster IMG ON IMG.ID = GT.ItemNumber
                           
                    LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
                    LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
                    LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
                    LEFT JOIN Location LC ON LC.ID = IT.LocationID
                    LEFT JOIN InventStatus S ON S.ID = IT.Status
                    LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
                    LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
                    ) X " . $where .$where_whx. "ORDER BY X.UpdateDate DESC";
            }
        }

        $query = Sqlsrv::queryJson(
            $conn,
            $sql
        );


        return $query;
  }
}
