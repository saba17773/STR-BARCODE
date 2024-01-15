<?php

namespace App\Services;

use App\Components\Database;

class GreentireunholdService
{
	public function  checkuser($userbarcode ='')
	{
	    $conn = Database::connect();
		$result = "SELECT *
		FROM USERMASTER U
		WHERE U.Barcode = ?";

		$parms = array($userbarcode);
		$query = sqlsrv_query($conn, $result,$parms);
		$row_count = sqlsrv_has_rows($query);
		
		return  $row_count;
	}
	public function  checkauthorize($userbarcode ='')
	{
	    $conn = Database::connect();
		$result = "SELECT *
		FROM USERMASTER U
		WHERE U.Barcode = ?
		AND U.Authorize = 1";

		$parms = array($userbarcode);
		$query = sqlsrv_query($conn, $result,$parms);
		$row_count = sqlsrv_has_rows($query);
		return  $row_count;
	}
	public function  getuser($userbarcode ='')
	{
	    $conn = Database::connect();
		$result = "SELECT *
		FROM USERMASTER U
		WHERE U.Barcode = ?
		AND U.Authorize = 1";

		$parms = array($userbarcode);
		$query = sqlsrv_query($conn, $result,$parms);
		$record = [];
        while ($res = sqlsrv_fetch_object($query))
        {
        	$record[] = $res;
        }
        return $record;
	}
}