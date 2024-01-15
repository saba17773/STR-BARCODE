<?php

namespace App\Components;

use App\Services\PermissionService;
use App\Components\Database;
use Wattanar\Sqlsrv;
use Hashids\Hashids;

class Security
{
	/**
	 * encode barcode
	 * @param  string $data barcode
	 * @return string       barcode encode
	 */
	public static function _encode($data) {
		// $data = substr($data, 1); 
		// $hashids = new Hashids;
		// return "I".$hashids->encode((int)$data);
		return $data;
	} 

	/**
	 * decode barcode
	 * @param  [string] $data [barcode]
	 * @return [string]       [decode barcode]
	 */
	public static function _decode($data) {
		// $data = substr($data, 1); 
		// $hashids = new Hashids;
		// $temp = $hashids->decode($data);
		// if (count($temp) > 0) {
		// 	return "I".$temp[0];
		// } else {
		// 	return "none";
		// }
		return $data;
	}

	/**
	 * check user can access route
	 * @return boolean
	 */
	public static function isAccess()
	{
		$permission_id = $_SESSION["user_permission"];
		
		
		$menu = json_decode((new PermissionService)->getMenu($permission_id));
		
		if (count($menu) > 0) {
			
			$allMenu = $menu[0]->Permission;
			
		} else {
			return false;
		}

		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT Link FROM MenuMaster WHERE ID IN ($allMenu)"
		);
		
		$current_uri = explode("?", str_replace(APP_ROOT, "", $_SERVER["REQUEST_URI"]))[0];
		$current_uri = trim(str_replace('?show=0', '', $current_uri));
		$temp = [];
		
		if (count($query) <= 0) {
			return false;
		}

		foreach ($query as $value) {
			$temp[] = trim(str_replace('?show=0', '', $value["Link"]));
		}

		if ($_SESSION['user_name'] === 'admin') {
			
			return true;
		} else if (in_array($current_uri, $temp)) {
			return true;
		} else {
			return false;
		}
	} 
}