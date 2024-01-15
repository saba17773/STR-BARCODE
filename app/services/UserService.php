<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\V2\Database\Connector;

class UserService
{

	public function __construct()
	{
		$this->conn = (new Connector)->dbConnect();
	}

	public function create(
		$username,
		$password,
		$fullname,
		$department,
		$warehouse,
		$location,
		$status,
		$auth,
		$permission,
		$employee,
		$company,
		// $default_page,
		// $default_page_mobile,
		$shift,
		$time_check,
		$unit,
		$section
	) {
		$conn = Database::connect();

		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT Username FROM UserMaster
				WHERE Username = ?",
			[$username]
		);

		if ($query === true) {
			return 'username นี้มีอยู่แล้วในระบบ';
		}

		// if (sqlsrv_begin_transaction($conn) === false) {
		// 	return 'ไม่สามารถเชื่อมต่อได้';
		// }

		$create_user = Sqlsrv::insert(
			$conn,
			"INSERT INTO UserMaster(
					Username, 
					Password,
					Name, 
					Department,
					Warehouse, 
					Location,
					Authorize, 
					EmployeeID,
					Barcode, 
					Shift,
					Status, 
					PermissionID,
					Company,
					SkipingDelay,
					UnitComponent,
					SectionComponent
					-- DirectTo,
					-- DirectToMobile
				)VALUES(
					?, ?, ?, ?, ?, 
					?, ?, ?, ?, ?, 
					?, ?, ?, ?,	?,
					?
				)",
			[
				$username,
				$password,
				$fullname,
				$department,
				(int) $warehouse,
				(int) $location,
				(int) $auth,
				$employee,
				$username,
				$shift,
				(int) $status,
				(int) $permission,
				$company,
				$time_check,
				$unit,
				$section
			]
		);

		if ($create_user) {
			$computername = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$dateTime = date('Y-m-d H:i:s');
			$InsertLogUser = sqlsrv_query(
				$conn,
				"INSERT INTO [MORMONT\DEVELOP].[EA_APP].[dbo].[TB_USER_APP] (EMP_CODE,USER_NAME,HOST_NAME,PROJECT_NAME,CREATE_DATE)
			  VALUES (?,?,?,?,?)",
				[
					$employee,
					$username,
					$computername,
					'STR BarCode',
					$dateTime
				]
			);

			// if (!$InsertLogUser) {
			// 	sqlsrv_rollback($conn);
			// 	return 400;
			// }
			// sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function update(
		$username,
		$password,
		$fullname,
		$department,
		$warehouse,
		$location,
		$status,
		$auth,
		$permission,
		$employee,
		$company,
		$id,
		// $default_page,
		// $default_page_mobile,
		$shift,
		$time_check,
		$unit,
		$section
	) {
		$conn = Database::connect();
		$dateTime = date('Y-m-d H:i:s');

		$update_user = Sqlsrv::insert(
			$conn,
			"UPDATE UserMaster 
				SET Username = ?, 
				Password =?,
				Name = ?,
				Department = ?,
				Warehouse = ?, 
				Location = ?,
				Authorize = ?, 
				EmployeeID = ?,
				Barcode = ?, 
				Shift = ?,
				Status = ?, 
				PermissionID = ?,
				Company = ?,
				SkipingDelay = ?,
				UnitComponent = ?,
				SectionComponent = ?
				-- DirectTo = ?,
				-- DirectToMobile = ?
				WHERE ID = ?",
			[
				$username,
				$password,
				$fullname,
				$department,
				(int) $warehouse,
				(int) $location,
				(int) $auth,
				$employee,
				$username,
				(int) $shift,
				(int) $status,
				(int) $permission,
				$company,
				$time_check,
				$unit,
				$section,
				// $default_page,
				// $default_page_mobile,
				$id
			]
		);

		if ($update_user) {

			if ($status === 1) {
				$InsertLogUser = sqlsrv_query(
					$conn,
					"INSERT INTO [192.168.90.30\DEVELOP].[EA_APP].[dbo].[TB_USER_APP] (EMP_CODE,USER_NAME,HOST_NAME,PROJECT_NAME,CREATE_DATE)
		          VALUES (?,?,?,?,getdate())",
					[
						$employee,
						$username,
						gethostbyaddr($_SERVER['REMOTE_ADDR']),
						'STR Barcode'
					]
				);

				// $InsertLogUserUSER_APP_CODE = sqlsrv_query(
				// 	$conn,
				// 	"INSERT INTO [192.168.90.30\DEVELOP].[EA_APP].[dbo].[USER_APP_CODE] (EMP_CODE,HOST_NAME,USER_NAME,PROJECT_NAME,CREATE_DATE,STATUS)
				//   VALUES (?,?,?,?,getdate())",
				// 	[
				// 		$employee,
				// 		gethostbyaddr($_SERVER['REMOTE_ADDR']),
				// 		$username,
				// 		'STR Barcode',
				// 		$dateTime,
				// 		1

				// 	]
				// );

				// if ($InsertLogUser) {
				// 	sqlsrv_rollback($conn);
				// 	return 400;
				// }
			}

			if ($status === 0) {
				// $DeleteLogUser = sqlsrv_query(
				// 	$conn,
				// 	"DELETE FROM [192.168.90.30\DEVELOP].[EA_APP].[dbo].[TB_USER_APP] WHERE EMP_CODE = ? AND  USER_NAME= ? AND PROJECT_NAME = ?",
				// 	[
				// 		$employee,
				// 		$username,
				// 		'STR Barcode'
				// 	]
				// );

				$DeleteLogUser =  Sqlsrv::insert(
					$conn,
					"UPDATE [EA_APP].[dbo].[TB_USER_APP]
						SET UPDATE_DATE = ?, 
						 STATUS = ?
						WHERE USER_APP_CODE = (SELECT MAX(USER_APP_CODE) AS USER_APP_CODE FROM  [EA_APP].[dbo].[TB_USER_APP] WHERE  PROJECT_NAME = ? AND USER_NAME = ?)",
					[
						$dateTime,
						0,
						'STR Barcode',
						$username


					]
				);
			}

			return 200;
		} else {
			return 400;
		}
	}

	public function isExist($username, $password)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT Username, Password
				FROM UserMaster
				-- WHERE Username = ?
				-- AND Password = ?
				WHERE Username COLLATE Latin1_General_CS_AS = ?
				AND Password COLLATE Latin1_General_CS_AS = ?
				",
			[$username, $password]
		);

		return $query;
	}

	public function getUserData($username)
	{
		if ($username === null) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT U.*, 
				P.DefaultDesktop, 
				P.DefaultMobile, 
				DPM.Description AS DPMDESC
				FROM UserMaster U 
				LEFT JOIN PermissionMaster P ON U.PermissionID = P.ID
				LEFT JOIN DepartmentMaster DPM ON DPM.Code = U.Department
				WHERE Username = ?",
			[$username]
		);
		return $query;
	}

	public function all()
	{
		$conn = Database::connect();
		$department_desc = $_SESSION["user_department_desc"];
		$warehouse = $_SESSION["user_warehouse"];
		$location = $_SESSION["user_location"];

		if ($_SESSION['user_name'] === 'admin') {
			$sql = "SELECT 
				U.Username,
				U.Password,
				U.ID, 
				U.Name,
				D.Code [Department],
				D.Description [DepartmentDesc],
				W.ID [Warehouse],
				W.Description [WarehouseDesc],
				L.ID [Location],
				L.Description [LocationDesc],
				U.Authorize,
				AM.Description [AuthorizeDesc],
				U.EmployeeID,
				U.Barcode,
				U.Shift,
				S.Description [ShiftDesc],
				U.Status,
				U.PermissionID,
				P.Description [PermissionDesc],
				U.Company,
				U.DirectTo,
				U.DirectToMobile,
				U.SkipingDelay,
				U.UnitComponent,
				U.SectionComponent
				FROM UserMaster U
				LEFT JOIN DepartmentMaster D
					ON U.Department = D.Code
				LEFT JOIN WarehouseMaster W
					ON W.ID = U.Warehouse
				LEFT JOIN Location L
					ON L.ID = U.Location
				LEFT JOIN ShiftMaster S
					ON S.ID = U.Shift
				LEFT JOIN PermissionMaster P
					ON P.ID = U.PermissionID
				LEFT JOIN AuthorizeMaster AM
					ON AM.ID = U.Authorize 
				ORDER BY U.ID DESC";
		} else {
			$sql = "SELECT 
				U.Username,
				U.Password,
				U.ID, 
				U.Name,
				D.Code [Department],
				D.Description [DepartmentDesc],
				W.ID [Warehouse],
				W.Description [WarehouseDesc],
				L.ID [Location],
				L.Description [LocationDesc],
				U.Authorize,
				AM.Description [AuthorizeDesc],
				U.EmployeeID,
				U.Barcode,
				U.Shift,
				S.Description [ShiftDesc],
				U.Status,
				U.PermissionID,
				P.Description [PermissionDesc],
				U.Company,
				U.DirectTo,
				U.DirectToMobile,
				U.SkipingDelay
				FROM UserMaster U
				LEFT JOIN DepartmentMaster D
					ON U.Department = D.Code
				LEFT JOIN WarehouseMaster W
					ON W.ID = U.Warehouse
				LEFT JOIN Location L
					ON L.ID = U.Location
				LEFT JOIN ShiftMaster S
					ON S.ID = U.Shift
				LEFT JOIN PermissionMaster P
					ON P.ID = U.PermissionID
				LEFT JOIN AuthorizeMaster AM
					ON AM.ID = U.Authorize
				WHERE
				--  D.Description = '$department_desc' 
				-- AND 
				U.Warehouse = '$warehouse'
				AND U.Location = '$location'
				ORDER BY U.ID DESC";
		}
		$query = Sqlsrv::queryJson(
			$conn,
			$sql
		);
		return $query;
	}

	public function isActive($username)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Status FROM UserMaster
				WHERE Username = ? 
				AND Status = 1",
			[$username]
		);
	}

	public function isUserBarcodeExist($barcode_user)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Barcode FROM UserMaster
			WHERE Barcode = ?",
			[$barcode_user]
		);
	}

	public function isAuthorize($barcode_user, $password, $type)
	{
		// $type = Field Name in Table AuthorizeMaster
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT U.Username FROM UserMaster U
			LEFT JOIN AuthorizeMaster AM ON U.Authorize = AM.ID
			WHERE U.Username = ? 
			AND AM.$type = 1 
			AND U.Password = ?",
			[$barcode_user, $password]
		);
	}

	public function isDepartmentTrue($barcode_user, $type)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT U.Barcode FROM UserMaster U
			LEFT JOIN AuthorizeMaster AM ON U.Authorize = AM.ID
			WHERE U.Barcode COLLATE Latin1_General_CS_AS = ? 
			AND AM.$type = 1
			AND U.Department = ?",
			[$barcode_user, $_SESSION["user_department"]]
		);
	}

	public function getDefaultPage($permission, $device)
	{
		if ($device === 'mobile') {
			$sql = 'SELECT M.Link FROM PermissionMaster P
						LEFT JOIN MenuMaster M ON P.DefaultMobile = M.ID AND M.Status = 1
						WHERE P.ID = ?';
		} else {
			$sql = 'SELECT M.Link FROM PermissionMaster P
						LEFT JOIN MenuMaster M ON P.DefaultDesktop = M.ID AND M.Status = 1
						WHERE P.ID = ?';
		}

		$conn = Database::connect();

		$defaultUrl = Sqlsrv::queryArray(
			$conn,
			$sql,
			[$permission]
		);

		if ($defaultUrl) {
			return APP_ROOT . $defaultUrl[0]['Link'];
		} else {
			return APP_ROOT;
		}
	}
	public function chkWHUser($username)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$getwh = Sqlsrv::queryArray(
			$conn,
			"SELECT Warehouse,Location
				FROM UserMaster 
				WHERE Username = ?",
			[
				$username
			]
		);

		if ($getwh[0]["Warehouse"] == '1' && $getwh[0]["Location"] == '1') {
			sqlsrv_commit($conn);
			//= greentire
			return true;
		} else {
			sqlsrv_commit($conn);
			return false;
		}
	}
	public function insertRate($user_id, $shift, $location, $build_mc, $total)
	{
		try {


			$conn = Database::connect();
			$date = date("Y-m-d H:i:s");

			if (sqlsrv_begin_transaction($conn) === false) {
				throw new \Exception("begin transaction failed.");
			}

			$insert_rate = Sqlsrv::insert(
				$conn,
				"INSERT INTO RateTrans(
					UserId,
					Machine, 
					LoginDate, 
					Shift,
					Total,
					LocationId,
					Status,
					BuildTypeId,
					CreateBy,
					CreateDate,
					UpdateBy,
					UpdateDate,
					RateGroupID
				)VALUES(
					?, ?, ?, ?, 
					?, ?, ?, ?,
					?, ?, ?, ?,
					?
				)",
				[
					$user_id,
					$build_mc,
					$date,
					$shift,
					$total,
					$location,
					1,
					1,
					$user_id,
					$date,
					$user_id,
					$date,
					1
				]
			);

			if ($insert_rate) {
				sqlsrv_commit($conn);
				return [
					"status" => 200,
					"message" => "insert success"
				];
			} else {
				sqlsrv_rollback($conn);
				throw new \Exception("Insert Rate failed.");
			}
		} catch (\Exception $e) {
			return [
				"status" => 404,
				"message" => $e->getMessage()
			];
		}
	}
	public function insertMore($user_id, $shift, $location, $build_mc, $build_type, $total)
	{

		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		sqlsrv_begin_transaction($conn);

		$insert_rate = Sqlsrv::insert(
			$conn,
			"INSERT INTO RateTrans(
					UserId,
					Machine, 
					LoginDate, 
					Shift,
					Total,
					LocationId,
					Status,
					BuildTypeId,
					CreateBy,
					CreateDate,
					UpdateBy,
					UpdateDate,
					RateGroupID
				)VALUES(
					?, ?, ?, ?, 
					?, ?, ?, ?, 
					?, ?, ?, ?,
					?
				)",
			[
				$user_id,
				$build_mc,
				$date,
				$shift,
				$total,
				$location,
				1,
				$build_type,
				$user_id,
				$date,
				$user_id,
				$date,
				1
			]
		);

		if ($insert_rate) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function ShowallUser($build_mc)
	{
		$conn = Database::connect();
		$getLog = Sqlsrv::queryJson(
			$conn,
			"SELECT REPLACE(U.Name, 'null', '') Name,B.Description BuildType,R.* 
			FROM RateTrans R JOIN 
			UserMaster U ON R.UserId = U.ID JOIN
			BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId
			WHERE R.Machine = ?
			AND R.LogoutDate IS NULL
			ORDER BY R.BuildTypeId,R.LoginDate",
			[
				$build_mc
			]
		);
		return $getLog;
	}
	public function chkUserLogin($user_id)
	{
		$conn = Database::connect();

		sqlsrv_begin_transaction($conn);

		$qChkUser = Sqlsrv::queryArray(
			$conn,
			"SELECT Machine
			FROM RateTrans 
			WHERE LogoutDate IS NULL AND UserId = ?",
			[
				$user_id
			]
		);

		if ($qChkUser[0]["Machine"] === null) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function chkUserLogin2($username)
	{
		$conn = Database::connect();

		sqlsrv_begin_transaction($conn);

		$qChkUser = Sqlsrv::queryArray(
			$conn,
			"SELECT R.Machine
			FROM RateTrans R  JOIN
			UserMaster U ON R.UserId = U.ID
			WHERE R.LogoutDate IS NULL AND U.Username = ?",
			[
				$username
			]
		);

		if (!$qChkUser) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function getMachine($user_id)
	{
		$conn = Database::connect();
		$getMac = Sqlsrv::queryArray(
			$conn,
			"SELECT Machine
			FROM RateTrans 
			WHERE LogoutDate IS NULL AND UserId = ? ",
			[
				$user_id
			]
		);
		return $getMac[0]['Machine'];
	}
	public function getMachine2($username)
	{
		$conn = Database::connect();
		$getMac = Sqlsrv::queryArray(
			$conn,
			"SELECT R.Machine
			FROM RateTrans R JOIN
			UserMaster U ON R.UserId = U.ID
			WHERE R.LogoutDate IS NULL AND U.Username = ? ",
			[
				$username
			]
		);
		return $getMac[0]['Machine'];
	}
	public function updateLogoutDate($user_id, $id)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$date = date("Y-m-d H:i:s");

		$update_logout = Sqlsrv::insert(
			$conn,
			"UPDATE RateTrans SET LogoutDate = ?,
					UpdateBy = ?,
					UpdateDate = ?
					WHERE UserId = ? AND Id = ?
				",
			[
				$date,
				$user_id,
				$date,
				$user_id,
				$id
			]
		);

		if ($update_logout) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function getId($user_id)
	{
		$conn = Database::connect();
		$getId = Sqlsrv::queryArray(
			$conn,
			"SELECT Id
			FROM RateTrans
			WHERE UserId = ? AND LogoutDate IS NULL ",
			[
				$user_id
			]
		);
		return $getId[0]['Id'];
	}
	public function chkLogout($build_mc, $user_id)
	{
		$conn = Database::connect();
		$getId = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(UserId) CHK_LOGOUT
			FROM RateTrans
			WHERE Machine = ? AND LogoutDate IS NULL AND UserId <> ?  ",
			[
				$build_mc,
				$user_id
			]
		);
		return $getId[0]['CHK_LOGOUT'];
	}
	public function logoutRequest($user_id)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$date = date("Y-m-d H:i:s");

		$update_logout = Sqlsrv::insert(
			$conn,
			"UPDATE RateTrans SET LogoutDate = ?,
					UpdateBy = ?,
					UpdateDate = ?
				WHERE UserId = ? 
				AND LogoutDate IS NULL
				",
			[
				$date,
				$user_id,
				$date,
				$user_id
			]
		);

		if ($update_logout) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function chkBuildType($build_mc, $build_type)
	{
		$conn = Database::connect();

		sqlsrv_begin_transaction($conn);

		$qChkUser = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(UserId) C_USER
			FROM RateTrans 
			WHERE LogoutDate IS NULL AND BuildTypeId = ?
			AND Machine = ?",
			[
				$build_type,
				$build_mc

			]
		);
		if ($build_type == "1") {
			if ($qChkUser[0]["C_USER"] >= 3) {
				sqlsrv_commit($conn);
				return [
					"status" => 404,
					"message" => "จำนวน Builder ครบแล้ว"
				];
			} else {
				sqlsrv_commit($conn);
				return [
					"status" => 200
				];
			}
		} elseif ($build_type == "2") {
			if ($qChkUser[0]["C_USER"] >= 1) {
				sqlsrv_commit($conn);
				return [
					"status" => 404,
					"message" => "จำนวน Change Code ครบแล้ว"
				];
			} else {
				sqlsrv_commit($conn);
				return [
					"status" => 200
				];
			}
		}
	}
	public function chkLogoutMainSession($username)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 U.Username
			FROM RateTrans R JOIN
			UserMaster U ON R.UserId = U.ID
			WHERE R.LogoutDate IS NULL 
			AND R.BuildTypeId = 1
			AND U.Username <> ? 
			ORDER BY LoginDate",
			[
				$username
			]
		);
		return $query[0]['Username'];
	}
	public function chkUserPass_logout($username, $password, $id)
	{
		$conn = Database::connect();
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT U.Username, U.Password
				FROM RateTrans R JOIN
				UserMaster U ON R.UserId = U.Id
				WHERE U.Username COLLATE Latin1_General_CS_AS = ?
				AND U.Password COLLATE Latin1_General_CS_AS = ?
				AND R.Id = ?
				",
			[$username, $password, $id]
		);

		return $query;
	}
	public function updateLogoutDate_AllUser($machine, $user_id)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$date = date("Y-m-d H:i:s");

		$update_logout = Sqlsrv::insert(
			$conn,
			"UPDATE RateTrans SET LogoutDate = ?,
					UpdateBy = ?,
					UpdateDate = ?
				 WHERE Machine = ? AND LogoutDate IS NULL
				",
			[
				$date,
				$user_id,
				$date,
				$machine
			]
		);

		if ($update_logout) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
	public function getTotalByUser($username)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		$time = date("H:i:s");
		if ($time > "08:00" && $time < "20.00") {
			$tstart = date("Y-m-d") . " 8:00:00";
			$tend = date("Y-m-d") . " 19:59:59";
			//exit(json_encode(["status" => 404, "message" => $tstart ." ". $tend ]));
		} else {
			$today = date("Y-m-d");
			$subtoday = str_replace('-', '/', $today);
			$tomorrow = date('Y-m-d', strtotime($subtoday . "+1 days"));

			$tstart = date("Y-m-d") . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			// exit(json_encode(["status" => 404, "message" => $tstart ." ". $tend ]));
		}


		$getTotal = Sqlsrv::queryArray(
			$conn,
			"SELECT B.CreateBy,COUNT(B.Barcode) 'Act'
			FROM BuildTrans B JOIN
			UserMaster U ON B.CreateBy = U.ID
			WHERE U.Username = ? AND 
			B.CreateDate  BETWEEN ? AND ?
			GROUP BY B.CreateBy ",
			[
				$username,
				$tstart,
				$tend
			]
		);
		return $getTotal[0]['Act'];
	}
	public function chkTypeMC($machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT Type MC_TYPE
			FROM BuildingMaster
			WHERE ID = ?",
			[
				$machine
			]
		);
		return $query[0]['MC_TYPE'];
	}
	public function firstRows_Session($machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 U.Username
			FROM RateTrans R JOIN
			UserMaster U ON R.UserId = U.ID
			WHERE R.Machine = ? AND R.LogoutDate IS NULL
			ORDER BY R.LoginDate",
			[
				$machine
			]
		);
		return $query[0]['Username'];
	}
	public function chkMC($machine)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(ID) as MC
			FROM BuildingMaster
			where ID = ? ",
			[
				$machine
			]
		);
		return $query[0]['MC'];
	}
	public function chkUserLogon($machine)
	{
		$conn = Database::connect();

		sqlsrv_begin_transaction($conn);

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(R.Id) Useron,B.Type,R.BuildTypeId
			FROM RateTrans R JOIN
			BuildingMaster B ON R.Machine = B.ID
			WHERE R.LogoutDate IS NULL AND R.Machine = ?
			GROUP BY B.Type ,R.BuildTypeId ",
			[
				$machine
			]
		);

		if ($query[0]['Type'] == "TBR" && $query[0]['Useron'] == 3 && $query[0]['BuildTypeId'] == 1) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "จำนวนคนที่เครื่องนี้ครบแล้ว"
			];
		} else if ($query[0]['Type'] == "PCR" && $query[0]['Useron'] == 3 && $query[0]['BuildTypeId'] == 1 && ($machine == "VMI01" || $machine == "VMI02")) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "จำนวนคนที่เครื่องนี้ครบแล้ว"
			];
		} else if ($query[0]['Type'] == "PCR" && $query[0]['BuildTypeId'] == 1 && $query[0]['Useron'] == 3) {
			sqlsrv_rollback($conn);
			return [
				"status" => 404,
				"message" => "จำนวนคนที่เครื่องนี้ครบแล้ว"
			];
		} else {
			sqlsrv_commit($conn);
			return [
				"status" => 200,
				"message" => "OK"
			];
		}
	}

	public function chkBuildTypeId($machine, $user_id)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 BuildTypeId
			FROM RateTrans
			WHERE Machine = ? AND LogoutDate IS NULL
			AND UserId <> ?
			ORDER BY BuildTypeId",
			[
				$machine,
				$user_id
			]
		);
		return $query[0]['BuildTypeId'];
	}

	public function chkCureUser($username)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$getwh = Sqlsrv::queryArray(
			$conn,
			"SELECT Warehouse,Location
				FROM UserMaster 
				WHERE Username = ?",
			[
				$username
			]
		);

		if ($getwh[0]["Warehouse"] == '4' && $getwh[0]["Location"] == '3') {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_commit($conn);
			return false;
		}
	}

	public function insertRate_Cure($user_id, $shift, $location)
	{

		try {

			$conn = Database::connect();
			$date = date("Y-m-d H:i:s");

			if (sqlsrv_begin_transaction($conn) === false) {
				throw new \Exception("begin transaction failed.");
			}

			$insert = Sqlsrv::insert(
				$conn,
				"INSERT INTO RateTrans(
					UserId,
					Machine, 
					LoginDate, 
					Shift,
					Total,
					LocationId,
					Status,
					BuildTypeId,
					CreateBy,
					CreateDate,
					UpdateBy,
					UpdateDate,
					RateGroupID
				)VALUES(
					?, ?, ?, ?, 
					?, ?, ?, ?,
					?, ?, ?, ?,
					?
				)",
				[
					$user_id,
					"CUR",
					$date,
					$shift,
					0,
					$location,
					1,
					0,
					$user_id,
					$date,
					$user_id,
					$date,
					2
				]
			);

			if ($insert) {

				$getID = Sqlsrv::queryArray(
					$conn,
					"SELECT Id
					FROM RateTrans 
					WHERE LoginDate = ? AND UserId = ?
					",
					[
						$date,
						$user_id
					]
				);
				// return $getID[0]['Id'];

				sqlsrv_commit($conn);
				return [
					"status" => 200,
					"getID" => $getID[0]['Id'],
					"message" => "insert success"
				];
			} else {
				sqlsrv_rollback($conn);
				throw new \Exception("Insert Rate failed.");
			}
		} catch (\Exception $e) {
			return [
				"status" => 404,
				"message" => $e->getMessage()
			];
		}
	}

	public function updateLogoutDate_Cure($user_id, $id)
	{
		$conn = Database::connect();
		sqlsrv_begin_transaction($conn);
		$date = date("Y-m-d H:i:s");

		$update_logout = Sqlsrv::insert(
			$conn,
			"UPDATE RateTrans SET LogoutDate = ?,
					UpdateBy = ?,
					UpdateDate = ?
					WHERE Id = ?
				",
			[
				$date,
				$user_id,
				$date,
				$id
			]
		);

		if ($update_logout) {
			sqlsrv_commit($conn);
			return [
				"status" => 200
			];
		} else {
			sqlsrv_rollback($conn);
			return [
				"status" => 404
			];
		}
	}
}
