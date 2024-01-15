<?php

namespace App\Models;

use Wattanar\Sqlsrv;
use App\Components\Database as DB;

class ItemSet
{
	public $id = null;
	public $item_set_id = null;
	public $item_id = null;

	public function fetchAll()
	{
		$conn = DB::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			ITSM.id,
			ITSM.item_set_id,
			ITSM.item_id,
			ITM.NameTH [item_name],
			ITM_II.NameTH [item_set_name]
			FROM ItemSetMaster ITSM
			LEFT JOIN ItemMaster ITM ON ITM.ID = ITSM.item_id
			LEFT JOIN ItemMaster ITM_II ON ITM_II.ID = ITSM.item_set_id
			ORDER BY ITSM.id DESC"
		);
	}

	public function getItemId() 
	{
		$conn = DB::connect();
		$item_id = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 item_id 
			FROM ItemSetMaster
			WHERE item_set_id = ?",
			[
				$this->item_set_id
			]
		);

		return $item_id;
	}

	public function getItemIdV2($itemSetId) 
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

  public function isItemSetExist() 
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
        $conn,
        "SELECT item_set_id FROM ItemSetMaster
        WHERE item_set_id = ?",
        [
            $this->item_set_id
        ]
    );
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

	public function save() {
		$conn = DB::connect();
		$save = Sqlsrv::insert(
			$conn,
			"INSERT INTO ItemSetMaster(item_set_id, item_id)
			VALUES(?, ?)",
			[
				$this->item_set_id,
				$this->item_id
			]
		);
		
		if( $save ) {
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		$conn = DB::connect();
		$update = Sqlsrv::update(
			$conn,
			"UPDATE ItemSetMaster
			SET item_set_id = ?,
			item_id = ?
			WHERE id = ?",
			[
				$this->item_set_id,
				$this->item_id,
				$this->id
			]
		);

		if ( $update ) {
			return true;
		} else {
			return false;
		}
	}

}