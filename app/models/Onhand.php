<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Onhand 
{
    public $ID = null;
    public $CodeID = null;
    public $WarehouseID = null;
    public $LocationID = null;
    public $Batch = null;
    public $QTY = null;
    public $Company = null;

    public function update()
    {
        return true;
        // $conn = DB::connect();
        // $update = Sqlsrv::update(
        //     $conn,
        //     "UPDATE Onhand 
        //     SET QTY += ?
        //     WHERE CodeID = ?
        //     AND WarehouseID = ?
        //     AND LocationID = ?
        //     AND Batch = ?
        //     AND Company = ?",
        //     [
        //         $this->QTY,
        //         $this->CodeID,
        //         $this->WarehouseID,
        //         $this->LocationID,
        //         $this->Batch,
        //         $this->Company
        //     ]
        // );

        // if ( $update ) {
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function save() 
    {
        
        $save = Sqlsrv::insert(
            $conn,
            "INSERT INTO Onhand(
                CodeID,
                WarehouseID,
                LocationID,
                Batch,
                QTY,
                Company
            ) VALUES(?, ?, ?, ?, ?, ?)",
            [
                $this->CodeID,
                $this->WarehouseID,
                $this->LocationID,
                $this->Batch,
                $this->QTY,
                $this->Company
            ]
        );

        if ( $save ) {
            return true;
        } else {
            return false;
        }
    }

    public function isItemExist()
    {
        $conn = DB::connect();
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
                $this->WarehouseID,
                $this->LocationID,
                $this->Batch,
                $this->Company,
                $this->CodeID
            ]
        );
    }

    public function onhandDetailByItemId()
    {
        $conn = DB::connect();
        return Sqlsrv::queryArray(
            $conn,
            "SELECT CodeID FROM Onhand
            WHERE CodeID = ?",
            [
                $this->CodeID
            ]
        );
    }
    
}