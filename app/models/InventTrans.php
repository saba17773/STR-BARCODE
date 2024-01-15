<?php

namespace App\Models;

use Wattanar\Sqlsrv;
use App\Components\Database as DB;

class InventTrans
{
    public $id = null;
    public $TransID = null;
    public $Barcode = null;
    public $CodeID = null;
    public $Batch = null;
    public $DisposalID = null;
    public $DefectID = null;
    public $WarehouseID = null;
    public $LocationID = null;
    public $QTY = null;
    public $UnitID = null;
    public $DocumentTypeID = null;
    public $Company = null;
    public $CreateBy = null;
    public $CreateDate = null;
    public $Shift = null;
    public $InventJournalID = null;
    public $AuthorizeBy = null;
    public $ScrapSide = null;
    public $RefDocId = null;

    public function update()
    {
        $conn = DB::connect();

        $update = Sqlsrv::update(
            $conn,
            "UPDATE InventTrans
            SET TransID = ?,
            Barcode = ?,
            CodeID = ?,
            Batch = ?,
            DisposalID = ?,
            DefectID = ?,
            WarehouseID = ?,
            LocationID = ?,
            QTY = ?,
            UnitID = ?,
            DocumentTypeID = ?,
            Company = ?,
            CreateBy = ?,
            CreateDate = ?,
            Shift = ?,
            InventJournalID = ?,
            AuthorizeBy = ?,
            ScrapSide = ?,
            RefDocId = ?
            WHERE id = ?
            ",
            [
                $this->TransID,
                $this->Barcode,
                $this->CodeID,
                $this->Batch,
                $this->DisposalID,
                $this->DefectID,
                $this->WarehouseID,
                $this->LocationID,
                $this->QTY,
                $this->UnitID,
                $this->DocumentTypeID,
                $this->Company,
                $this->CreateBy,
                $this->CreateDate,
                $this->Shift,
                $this->InventJournalID,
                $this->AuthorizeBy,
                $this->ScrapSide,
                $this->RefDocId,
                $this->id
            ]
        );

        if ( $update ) {
            return true;
        } else {
            return false;
        }
    }

    public function save()
    {
        $conn = DB::connect();
        $save = Sqlsrv::insert(
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
                $this->TransID,
                $this->Barcode,
                $this->CodeID,
                $this->Batch,
                $this->DisposalID,
                $this->DefectID,
                $this->WarehouseID,
                $this->LocationID,
                $this->QTY,
                $this->UnitID,
                $this->DocumentTypeID,
                $this->Company,
                $this->CreateBy,
                $this->CreateDate,
                $this->Shift,
                $this->InventJournalID,
                $this->AuthorizeBy,
                $this->ScrapSide,
                $this->RefDocId
            ]
        );

        if ( $save ) {
            return true;
        } else {
            return false;
        }
    }

    public function greentireHoldUnholdAndRepair($date, $product_group,$pressBOI)
    {
        $date = date('Y-m-d', strtotime($date));
        $conn = DB::connect();
        if($pressBOI =="" || $pressBOI == 1){
			    $whereBOI = "";
        }
        else{
		       $whereBOI =	" AND BMM.BOI ='$pressBOI' ";
         }

        return Sqlsrv::queryJson(
            $conn,
            "SELECT
            I.Barcode[barcode],
            I.CodeID[code_id],
            I.AuthorizeBy[authorize_by],
            I.CreateDate[create_date],
            DTU.DisposalDesc [disposal],
            (
                SELECT TOP 1 F.DefectID FROM InventTrans F
                where F.WarehouseID = 1
                and F.LocationID IN (9, 10)
                and F.DocumentTypeID = 1
                and F.Barcode = I.Barcode
                and CONVERT(date, F.CreateDate) <= '$date'
                order by F.id desc
            )[defect_id],
            (
                SELECT TOP 1 DD.Description FROM InventTrans F
                left join Defect DD ON DD.ID = F.DefectID
                where F.WarehouseID = 1
                and F.LocationID IN (9, 10)
                and F.DocumentTypeID = 1
                and F.Barcode = I.Barcode
                and CONVERT(date, F.CreateDate) <= '$date'
                order by F.id desc
            )[defect_desc]
            from InventTrans I
            LEFT JOIN DisposalToUseIn DTU ON DTU.ID = I.DisposalID
            LEFT JOIN InventTable IT ON  IT.Barcode = I.Barcode
            LEFT JOIN BuildingMaster BMM ON BMM.ID = IT.BuildingNo
            where I.WarehouseID = 1
            and I.LocationID IN (9, 10)
            and I.DocumentTypeID = 2
            and I.AuthorizeBy IS NOT NULL
            and CONVERT(date, I.CreateDate) = '$date'
            $whereBOI
            AND I.CodeID IN
            (
                SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM
                LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
                WHERE CCM.GreentireID = I.CodeID
                AND IM.ProductGroup = ?
            )
            order by I.Barcode ASC",
            [
                $product_group
            ]
        );
    }

    public function finalHoldUnholdAndRepair($date, $product_group, $pressBOI)
    {
        $date = date('Y-m-d', strtotime($date));
        if($pressBOI =="" || $pressBOI == 1){
        	$whereBOI = "";
        }
        else{
          $whereBOI =	" AND CB.BOI ='$pressBOI' ";
        }
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT
            I.Barcode[barcode],
            CCM.ID[code_id],
            I.AuthorizeBy[authorize_by],
            I.CreateDate[create_date],
            DTU.DisposalDesc [disposal],
            (
                SELECT TOP 1 IT.DefectID
                FROM InventTrans IT
                WHERE IT.Barcode = I.Barcode
                AND IT.DisposalID IN (10, 12)
                AND IT.DefectID is not null
                AND CONVERT(date, I.CreateDate) <= '$date'
                ORDER BY IT.id DESC
            ) [defect_id],
            (
                SELECT TOP 1 DD.Description
                FROM InventTrans IT
                LEFT JOIN Defect DD ON DD.ID = IT.DefectID
                WHERE IT.Barcode = I.Barcode
                AND IT.DisposalID IN (10, 12)
                AND IT.DefectID is not null
                AND CONVERT(date, I.CreateDate) <= '$date'
                ORDER BY IT.id DESC
            ) [defect_desc]
            from InventTrans I
            LEFT JOIN DisposalToUseIn DTU ON DTU.ID = I.DisposalID
            LEFT JOIN CureCodeMaster CCM ON CCM.ItemID = I.CodeID OR CCM.ItemQ = I.CodeID
            LEFT JOIN InventTable IT ON IT.Barcode = I.Barcode
            LEFT JOIN PressMaster PM ON PM.ID = IT.PressNo
            LEFT JOIN CuringBOI CB ON CB.Barcode = IT.Barcode
            where I.WarehouseID = 2
            and I.LocationID IN (11, 12)
            and I.DocumentTypeID = 2
            and I.AuthorizeBy IS NOT NULL
            $whereBOI
            and CONVERT(date, I.CreateDate) = '$date'
            -- AND I.CodeID IN
            -- (
            --     SELECT TOP 1 IM.ID FROM ItemMaster IM
            --     WHERE IM.ProductGroup = ?
            --     AND IM.ID = I.CodeID
            -- )
            AND (
              I.CodeID IN
              (
               SELECT TOP 1 IM.ID FROM ItemMaster IM
               WHERE IM.ProductGroup = 'TBR'
               AND IM.ID = I.CodeID
             )
             OR
             I.CodeID IN
             (
              SELECT TOP 1 CCM.ItemQ FROM ItemMaster IM
               WHERE IM.ProductGroup = 'TBR'
               AND IM.ID = I.CodeID
               OR IM.ID = CCM.ItemID
             )

           )
            order by I.Barcode ASC",
            [
                $product_group
            ]
        );
    }
}
