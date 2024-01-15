<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class MenuService
{
	public function all()
	{
		$conn = Database::connect();

		if ($_SESSION["user_login"] === 1) {
			$sql = "SELECT *  
			,CASE WHEN TypeCheck = 0 THEN 'Desktop' ELSE 'WindowsMobile' END AS NameType FROM MenuMaster ";
		} else {
			$sql = "SELECT *  
			,CASE WHEN TypeCheck = 0 THEN 'Desktop' ELSE 'WindowsMobile' END AS NameType FROM MenuMaster WHERE Status = 1";
		}

		$query = Sqlsrv::queryJson(
			$conn,
			$sql
		);
		return $query;
	}

	public function create($description, $link, $typecheck)
	{
		$conn = Database::connect();

		if (self::checkExist($description) === true) {
			return false;
		}

		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO MenuMaster(
					Link, Description, Status, TypeCheck
				) VALUES (?, ?, ?, ?)",
				[$link, trim($description), 1, $typecheck]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $description, $link, $typecheck)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE MenuMaster
				SET Description = ?,
				Link = ?,
				TypeCheck = ?
				WHERE ID = ?",
				[$description, $link, $typecheck, $id]
			);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function checkExist($description)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM MenuMaster
				WHERE Description = ?",
			[trim($description)]
		);
		return $query;
	}

	public function getMenu($menu_id)
	{
		$conn = Database::connect();

		$q = Sqlsrv::queryJson(
			$conn,
			"SELECT Description, Link FROM MenuMaster
			WHERE ID IN ($menu_id) AND Status = 1  AND TypeCheck = 0"
		);

		if ($q) {
			return $q;
		} else {
			return false;
		}
	}

	public function getMenuMobile($menu_id)
	{
		$conn = Database::connect();

		$q = Sqlsrv::queryJson(
			$conn,
			"SELECT Description, Link FROM MenuMaster
			WHERE ID IN ($menu_id) AND Status = 1 AND TypeCheck = 1"
		);

		if ($q) {
			return $q;
		} else {
			return false;
		}
	}
}
