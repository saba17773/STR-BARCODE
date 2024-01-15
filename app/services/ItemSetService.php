<?php

namespace App\Services;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class ItemSetService 
{
	public function getItemIdFromItemSet($itemSetId) 
	{
		$conn = DB::connect();
		$item_id = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 item_id 
			FROM ItemSetMaster
			WHERE item_set_id = ?",
			[
				$itemSetId
			]
		);
		return $item_id;
	}

	public function isItemSetExists($itemSetId) 
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
        $conn,
        "SELECT item_set_id FROM ItemSetMaster
        WHERE item_set_id = ?",
        [
            $itemSetId
        ]
    );
  }
}