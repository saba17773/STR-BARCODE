<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class InventTable
{
    public $ID = null;
    public $Barcode = null;
    public $BarcodeFoil = null;
    public $DateBuild = null;
    public $BuildingNo = null;
    public $GT_Code = null;
    public $CuringDate = null;
    public $CuringCode = null;
    public $ItemID = null;
    public $Batch = null;
    public $QTY = null;
    public $Unit = null;
    public $PressNo = null;
    public $PressSide = null;
    public $MoldNo = null;
    public $TemplateSerialNo = null;
    public $CuredTireReciveDate = null;
    public $CuredTireLineNo = null;
    public $FinalReceiveDate = null;
    public $GateReceiveNo = null;
    public $XrayDate = null;
    public $XrayNo = null;
    public $QTechReceiveDate = null;
    public $WarehouseReceiveDate = null;
    public $WarehouseTransReceiveDate = null;
    public $LoadingDate = null;
    public $DONo = null;
    public $PickingListID = null;
    public $OrderID = null;
    public $DisposalID = null;
    public $WarehouseID = null;
    public $LocationID = null;
    public $Status = null;
    public $Company = null;
    public $UpdateBy = null;
    public $UpdateDate = null;
    public $CreateBy = null;
    public $CreateDate = null;
    public $Weight = null;
    public $RefItemId = null;

    public function update()
    {
        $conn = DB::connect();
        $update = Sqlsrv::update(
            $conn,
            "UPDATE InventTable
            SET Barcode = ?,
            DateBuild = ?,
            BuildingNo = ?,
            GT_Code = ?,
            CuringDate = ?,
            CuringCode = ?,
            ItemID = ?,
            Batch = ?,
            QTY = ?,
            Unit = ?,
            PressNo = ?,
            PressSide = ?,
            MoldNo = ?,
            TemplateSerialNo = ?,
            CuredTireReciveDate = ?,
            CuredTireLineNo = ?,
            FinalReceiveDate = ?,
            GateReceiveNo = ?,
            XrayDate = ?,
            XrayNo = ?,
            QTechReceiveDate = ?,
            WarehouseReceiveDate = ?,
            WarehouseTransReceiveDate = ?,
            LoadingDate = ?,
            DONo = ?,
            PickingListID = ?,
            OrderID = ?,
            DisposalID = ?,
            WarehouseID = ?,
            LocationID = ?,
            Status = ?,
            Company = ?,
            UpdateBy = ?,
            UpdateDate = ?,
            CreateBy = ?,
            CreateDate = ?,
            Weight = ?,
            RefItemId = ?
            WHERE ID = ?",
            [
                $this->Barcode,
                $this->DateBuild,
                $this->BuildingNo,
                $this->GT_Code,
                $this->CuringDate,
                $this->CuringCode,
                $this->ItemID,
                $this->Batch,
                $this->QTY,
                $this->Unit,
                $this->PressNo,
                $this->PressSide,
                $this->MoldNo,
                $this->TemplateSerialNo,
                $this->CuredTireReciveDate,
                $this->CuredTireLineNo,
                $this->FinalReceiveDate,
                $this->GateReceiveNo,
                $this->XrayDate,
                $this->XrayNo,
                $this->QTechReceiveDate,
                $this->WarehouseReceiveDate,
                $this->WarehouseTransReceiveDate,
                $this->LoadingDate,
                $this->DONo,
                $this->PickingListID,
                $this->OrderID,
                $this->DisposalID,
                $this->WarehouseID,
                $this->LocationID,
                $this->Status,
                $this->Company,
                $this->UpdateBy,
                $this->UpdateDate,
                $this->CreateBy,
                $this->CreateDate,
                $this->Weight,
                $this->RefItemId,
                $this->ID
            ]
        );

        if ( $update ) {
            return true;
        } else {
            return false;
        }

    }

    public function updateBomInventTable()
    {
        $conn = DB::connect();
        $update = Sqlsrv::update(
            $conn,
            "UPDATE InventTable
            SET Unit = ?,
            ItemID = ?,
            RefItemId = ?,
            DisposalID = ?,
            WarehouseID = ?,
            LocationID = ?,
            UpdateBy = ?,
            UpdateDate = ?
            WHERE Barcode = ?",
            [
                $this->Unit,
                $this->ItemID,
                $this->RefItemId,
                $this->DisposalID,
                $this->WarehouseID,
                $this->LocationID,
                $this->UpdateBy,
                $this->UpdateDate,
                $this->Barcode
            ]
        );

        if ( $update ) {
            return true;
        } else {
            return sqlsrv_errors();
        }
    }

    public function isBarcodeExist($barcode = null)
    {
        if ($barcode === null) {
            $data = $this->Barcode;
        } else {
            $data = $barcode;
        }
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Barcode FROM InventTable
            WHERE Barcode = ?",
            [
                $data
            ]
        );
    }

    public function isBarcodeExists($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
          $conn,
          "SELECT Barcode 
          FROM InventTable
          WHERE Barcode = ?",
          [
            $barcode
          ]
        );
    }

    public function isBarcodeFoilExist()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT BarcodeFoil FROM InventTable
            WHERE BarcodeFoil = ?",
            [
                $this->Barcode
            ]
        );
    }

    public function isStatusReceive()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Status FROM InventTable
            WHERE Barcode = ? 
            AND Status = 1", // Receive
            [
                $this->Barcode
            ]
        );
    }

    public function isStatusReceiveV2($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Status FROM InventTable
            WHERE Barcode = ? 
            AND Status = 1", // Receive
            [
                $barcode
            ]
        );
    }

    public function isFoilStatusReceive()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Status FROM InventTable
            WHERE BarcodeFoil = ? 
            AND Status = 1", // Receive
            [
                $this->Barcode
            ]
        );
    }

    public function getBarcodeStatus()
    {
        $conn = DB::connect();
        $status = Sqlsrv::queryArray(
            $conn,
            "SELECT TOP 1 ISS.Description FROM InventTable IT
            LEFT JOIN InventStatus ISS ON ISS.ID = IT.Status
            WHERE IT.BarcodeFoil = ?",
            [
                $this->Barcode
            ]
        );

        if ( $status ) {
            return $status[0]["Description"];
        } else {
            return "Error";
        }
    }

    public function getBarcodeStatusV2($barcode)
    {
        $conn = DB::connect();
        $status = Sqlsrv::queryArray(
            $conn,
            "SELECT TOP 1 ISS.Description FROM InventTable IT
            LEFT JOIN InventStatus ISS ON ISS.ID = IT.Status
            WHERE IT.BarcodeFoil = ?",
            [
                $barcode
            ]
        );

        if ( $status ) {
            return $status[0]["Description"];
        } else {
            return "";
        }
    }

    public function isWarehouseReceiveDateNull()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT WarehouseReceiveDate 
            FROM InventTable
            WHERE Barcode = ?
            AND WarehouseReceiveDate IS NULL",
            [
                $this->Barcode
            ]
        );
    }

    public function isWarehouseReceiveDateNullV2($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT WarehouseReceiveDate 
            FROM InventTable
            WHERE Barcode = ?
            AND WarehouseReceiveDate IS NULL",
            [
                $barcode
            ]
        );
    }

    public function getBarcodeDetail()
    {
        $conn = DB::connect();
        return Sqlsrv::queryArray(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ?",
            [
                $this->Barcode
            ]
        );
    }

    public function getBarcodeDetailV2($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::queryArray(
          $conn,
          "SELECT * FROM InventTable
          WHERE Barcode = ?",
          [
              $barcode
          ]
        );
    }

    public function getBarcodeFoilDetail()
    {
        $conn = DB::connect();
        return Sqlsrv::queryArray(
            $conn,
            "SELECT * FROM InventTable
            WHERE BarcodeFoil = ?",
            [
                $this->Barcode
            ]
        );
    }

    public function isBarcodeFoilNull()
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM InventTable
            WHERE Barcode = ? 
            AND BarcodeFoil IS NULL",
            [
                $this->Barcode
            ]
        );
    }

    public function saveFoil()
    {
        $conn = DB::connect();
        $update = Sqlsrv::update(
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
                $this->BarcodeFoil,
                $this->DisposalID,
                $this->WarehouseID,
                $this->LocationID,
                $this->Status,
                $this->UpdateBy,
                $this->UpdateDate,
                $this->Barcode
            ]
        );

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    public function createInventTableGreentireIncoming()
    {
        $conn = DB::connect();
        
        $create = Sqlsrv::insert(
            $conn,
            "INSERT INTO InventTable(
                Barcode, DateBuild, Batch, BuildingNo, GT_Code,
                QTY, Unit, DisposalID, WarehouseID, LocationID, 
                Status, Company, UpdateBy, UpdateDate, CreateBy,
                CreateDate, Weight
            )VALUES(
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?
            )",
            [
                $this->Barcode, 
                $this->DateBuild,
                $this->Batch,
                $this->BuildingNo,
                $this->GT_Code,
                $this->QTY,
                $this->Unit,
                $this->DisposalID,
                $this->WarehouseID,
                $this->LocationID,
                $this->Status,
                $this->Company,
                $this->UpdateBy,
                $this->UpdateDate,
                $this->CreateBy,
                $this->CreateDate,
                $this->Weight
            ]
        );

        if ($create) {
            return true;
        } else {
            return false;
        }
    }

    public function isCuring($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            'SELECT Barcode 
            FROM InventTable 
            WHERE CuringDate is not null
            AND Barcode = ?',
            [
                $barcode
            ]
        );
    }

    public function iswmscheck($barcode)
    {
      
      $conn = DB::connect();
      return Sqlsrv::hasRows(
        $conn,
        'SELECT Barcode
        FROM InventTable
        WHERE  WarehouseID = 3 AND Status = 1 
        AND Barcode = ?',
        [
          $barcode
        ]
      );
    }

    public function insert_wms($barcode)
    {
        $conn = DB::connect();
        
        $create = Sqlsrv::insert(
            $conn,
            "INSERT INTO WMS_BarcodeSendTemp(
                [STORERKEY]
                ,[RECEIPTKEY]
                ,[RECEIPTLINENUMBER]
                ,[FORCEID]
                ,[SERIALNUMBER]
                ,[SKU]
                ,[DESCRIPTION]
                ,[LANENO]
                ,[LPN]
                ,[BATCHNO]
                ,[MANDATE]
                ,[EXPDATE]
                ,[PROCTYPE]
                ,[UOM]
                ,[QTY]
                ,[CREATEDATE]
                ,[UPDATEDATE]
                ,[CREATEBY]
                ,[UPDATEBY]
                ,[INTSTS]
                ,[ERRMSG]
            )
            SELECT
                'STR',
                '',
                '',
                '0',
                IT.Barcode,
                IT.ItemID,
                SUBSTRING(IM.NameTH,1,80) AS NameTH,
                '3LANE01',
                '',
                LEFT(IT.Batch,7),
                dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year,convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4 - datepart(dw, dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year, convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4) + 2,
                dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year,convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4 - datepart(dw, dateadd (week, convert (int,SUBSTRING(IT.Batch, 6, 2))-1, dateadd (year, convert (int,LEFT(IT.Batch,4))-1900, 0)) - 4) + 2 +180,
                0,
                'PCS',
                1,
                convert(varchar, IT.UpdateDate, 121),
                GETDATE(),
                -- IT.updateby,
                UM.Username,
                '',
                '0',
                ''
                FROM InventTable IT 
                INNER Join UserMaster UM ON UM.ID = IT.UpdateBy
                INNER Join Itemmaster IM ON IM.ID = IT.ItemID
                where IT.Barcode  = '$barcode'"
        );

        if ($create) {
            return true;
        } else {
            return false;
        }
    }
}