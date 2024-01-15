<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Location
{
	public $ID;
	public $Description;
	public $WarehouseID;
	public $ReceiveLocation;
	public $AutoIssue;
	public $InUse;
	public $Company;
	public $DisposalID;
	public $ReverseReceiveLocation;
	public $ReturnReceiveLocation;
	public $UnpickReceiveLocation;

	public function getUserLocation()
	{
		$conn = DB::connect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM Location L
			WHERE L.ID = ? AND InUse = 1",
			[
				$this->ID
			]
		);
	}
}