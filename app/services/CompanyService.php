<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class CompanyService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM CompanyMaster");
	}

	public function create($company_name, $internal_code)
	{
		if (self::checkExist($internal_code, $company_name) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO CompanyMaster(ID, Description)
				VALUES(?, ?)",
				[trim($internal_code), trim($company_name)]
			);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($company_name, $internal_code)
	{
		if (self::checkExist($internal_code, $company_name) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::update(
				$conn,
				"UPDATE CompanyMaster
				SET Description = ?
				WHERE ID = ?",
				[trim($company_name), trim($internal_code)]
			);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function checkExist($internal_code, $company_name) 
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
				$conn,
				"SELECT ID, Description FROM CompanyMaster
				WHERE ID = ?
				AND Description = ?",
				[trim($internal_code), trim($company_name)]
			);
		return $query;
	}
}