<?php

namespace App\V2\Curing;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class CuringAPI
{

  public function test1($data)
  {
    $ID = explode("@", $data);
    $IDex = explode("(", $ID[3]);
    $IDex = str_replace("%20", " ", $IDex[0]);
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryArray(

      $conn,
      "SELECT
            SL.Brand
             FROM CureCodeMaster ST JOIN ItemMaster SL ON  ST.ItemID = SL.ID where ST.ID ='$IDex'"




    );
  }
}
