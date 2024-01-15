<?php

namespace App\Libs;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Onhand
{
	public function isCodeExists($code, $warehouse, $location, $batch, $company)
	{
		$conn = DB::connect();
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT QTY 
                  FROM Onhand
                  WHERE WarehouseID = ?
                  AND LocationID = ?
                  AND Batch = ?
                  AND Company = ?
                  AND CodeID  = ?
                  AND QTY >= 0",
                  [
                  	$warehouse,
                  	$location,
                  	$batch,
                  	$company,
                  	$code
                  ]
		));
	}
}