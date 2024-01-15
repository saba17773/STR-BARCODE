<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;

class EmployeeService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT 
			E.Code , 
			E.FirstName,
			E.LastName,
			(E.FirstName+' '+ E.LastName) as Name ,
			E.DivisionCode as DivisionCode,
			E.DepartmentCode as DepartmentCode,
			D.Description as DepartmentDesc,
			DV.Description as DivisionDesc,
			E.EmpStatus
			FROM Employee E		
			LEFT JOIN DepartmentMaster D ON D.Code = E.DepartmentCode
			LEFT JOIN DivisionMaster DV ON DV.Code = E.DivisionCode
			ORDER BY E.Code"
		);
	}

	public function allByDepartmentName($department_name)
	{
		$department_name = str_replace("%20", " ", trim($department_name));
		$conn = Database::connect();

		if ($_SESSION["user_name"] === "admin") {
			return Sqlsrv::queryJson(
				$conn, 
				"SELECT 
				E.Code , 
				E.FirstName,
				E.LastName,
				(E.FirstName+' '+ E.LastName) as Name ,
				E.DivisionCode as DivisionCode,
				E.DepartmentCode as DepartmentCode,
				D.Description as DepartmentDesc,
				DV.Description as DivisionDesc,
				E.EmpStatus
				FROM Employee E		
				LEFT JOIN DepartmentMaster D ON D.Code = E.DepartmentCode
				LEFT JOIN DivisionMaster DV ON DV.Code = E.DivisionCode
				ORDER BY E.Code"
			);
		} else  {
			return Sqlsrv::queryJson(
				$conn, 
				"SELECT 
				E.Code , 
				E.FirstName,
				E.LastName,
				(E.FirstName+' '+ E.LastName) as Name ,
				E.DivisionCode as DivisionCode,
				E.DepartmentCode as DepartmentCode,
				D.Description as DepartmentDesc,
				DV.Description as DivisionDesc,
				E.EmpStatus
				FROM Employee E		
				LEFT JOIN DepartmentMaster D ON D.Code = E.DepartmentCode
				LEFT JOIN DivisionMaster DV ON DV.Code = E.DivisionCode
				WHERE D.Description = '$department_name'
				ORDER BY E.Code"
			);
		}
	}

	public function allByStatus()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT 
			E.Code, 
			E.FirstName, 
			E.LastName, 
			E.DivisionCode, 
			(E.FirstName+' '+E.LastName) as Name ,
			E.EmpStatus,
			DV.Description as DivisionDesc
			FROM Employee  E
			LEFT JOIN DivisionMaster DV ON DV.Code = E.DivisionCode
			WHERE E.EmpStatus = 1
			ORDER BY E.Code"
		);
	}

	public function setStatus($id, $status)
	{
		
		if ($status === "true") {
			$status = 1;
		} else {
			$status = 0;
		}

		$conn = Database::connect();
		$q = Sqlsrv::update(
			$conn,
			"UPDATE Employee 
			SET EmpStatus = ?
			WHERE Code = ?",
			[
				$status,
				$id
			]
		);

		if ($q) {
			return 200;
		} else {
			return "ไม่สามารถทำรายการได้";
		}
	}

	public function getDivisionByEmpCode($empCode)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT D.Code, D.Description FROM Employee E
			LEFT JOIN DivisionMaster D ON E.DivisionCode = D.Code
			WHERE E.Code = ?",
			[$empCode]
		);
	}

	public function sync()
	{
		$conn = Database::connect();
		$query = sqlsrv_query(
			$conn,
			"INSERT INTO Employee(Code,FirstName, LastName,DivisionCode,DepartmentCode)
			SELECT 
			CODEMPID,EMPNAME, EMPLASTNAME, DIVISIONCODE, DEPARTMENTCODE
			from [192.168.90.30\develop].[HRTRAINING].[dbo].[Employee]
			where Status <> 9
			and Departmentcode is not null
			and SUBSTRING(CODEMPID,1,1) IN (5,6)
			and EMPLASTNAME IS NOT NULL
			and CODEMPID NOT IN (
				SELECT Code from Employee
			)
			group by CODEMPID, EMPNAME, EMPLASTNAME, DIVISIONCODE, DEPARTMENTCODE"
		);

		if ($query) {
			return true;
		} else {
			return sqlsrv_errors();
		}
	}

}