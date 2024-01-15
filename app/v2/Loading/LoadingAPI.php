<?php

namespace App\V2\Loading;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class LoadingAPI
{
  public function saveAddLoading($itemId, $batch, $cusotmer_group, $checkCustomerGroup)
  {
    $conn = (new Connector)->dbConnect();

    $insert = \sqlsrv_query(
      $conn,
      "INSERT INTO DenyLoading(ItemId, Batch, CustomerGroup, CheckCustomerGroup, CreateDate)
      VALUES(?, ?, ?, ?, ?)",
      [
        strtoupper($itemId),
        $batch,
        $cusotmer_group,
        $checkCustomerGroup,
        date("Y-m-d H:i:s")
      ]
    );

    if ($insert) {
      return [
        "result" => true,
        "message" => "Save success"
      ];
    } else {
      return [
        "result" => false,
        "message" => "Save failed"
      ];
    }
  }

  public function getDenyLoading()
  {
    $conn = (new Connector)->dbConnect();

    return Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM DenyLoading"
    );
  }

  public function saveDeleteDenyLoading($id)
  {
    $conn = (new Connector)->dbConnect();

    $delete =  sqlsrv_query(
      $conn,
      "DELETE FROM DenyLoading WHERE Id = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return [
        "result" => true,
        "message" => "Delete success"
      ];
    } else {
      return [
        "result" => false,
        "message" => "Delete failed"
      ];
    }
  }

  public function export()
  {
    $conn = (new Connector)->dbConnect();

    $result = Sqlsrv::queryJson(
      $conn,
      "SELECT DL.ItemId,IM.NameTH,DL.Batch,BM.BrandDescription
      FROM DenyLoading AS DL WITH(NOLOCK)
      JOIN ItemMaster AS IM WITH(NOLOCK) ON DL.ItemId = IM.ID
      LEFT JOIN BrandMaster AS BM WITH(NOLOCK) ON IM.Brand = BM.BrandName"
    );

    return json_decode($result);
  }
}
