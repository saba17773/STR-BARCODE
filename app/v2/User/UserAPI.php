<?php

namespace App\V2\User;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Services\PermissionService;
use App\V2\Database\Connector;

class UserAPI
{
  private $conn = null;

  public function __construct() {
    $this->conn = (new Connector)->dbConnect();
  }

  public function auth() {
    if (!isset($_SESSION['user_login'])) {
      header('Location: /');
      exit;
    }
  }

  public function authAPI() {
    if (!isset($_SESSION['user_login'])) {
      return false;
    } else {
      return true;
    }
  }

  public function saveLastLogin($userId) {
    $update = sqlsrv_query(
      $this->conn,
      "UPDATE UserMaster
      SET LastLogin = ?
      WHERE ID = ?",
      [
        date('Y-m-d H:i:s'),
        $userId
      ]
    );

    if ($update) {
      return [
        'result' => true,
        'message' => 'update success'
      ];
    } else {
      return [
        'result' => false,
        'message' => 'update failed'
      ];
    }
  }

  public function logLogin($userId,$typeLogin,$type) {
    $computername = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $dateTime = date('Y-m-d H:i:s');
    $dateatimezone = explode(" ", $dateTime);

    $SerchEmp = Sqlsrv::queryArray(
      $this->conn,
      "SELECT * FROM UserMaster
      WHERE ID = ?",
      [
        $userId
      ]
    );
    $InsertLog = sqlsrv_query(
      $this->conn,
      "INSERT INTO [192.168.90.30\DEVELOP].[WEB_CENTER].[dbo].[LoginLogs] (EmployeeID,ComputerName,Username,LoginDevice,LoginDate,ProjectID)
							VALUES (?,?,?,?,?,?)",
							[
								$SerchEmp[0]["EmployeeID"],
								$computername,
								$SerchEmp[0]["Username"],
								$typeLogin,
								$dateTime,
								1

							]

    );

    $InsertlogApp = sqlsrv_query(
      $this->conn,
      "INSERT INTO [192.168.90.30\DEVELOP].[EA_APP].[dbo].[TB_LOG_APP] (EMP_CODE,USER_NAME,HOST_NAME,".$type.",PROJECT_NAME)
      VALUES (?,?,?,?,?)",
      array(
          $SerchEmp[0]["EmployeeID"],
          $SerchEmp[0]["Username"],
          $computername,
          date('Y-m-d H:i:s'),
          'STR Barcode'
      )
    );

  }

  public function APIloglogin() {


    return Sqlsrv::queryJson(
      $this->conn,
    "SELECT
		        LI.EmployeeID,
            HR.EMPNAME,
            HR.EMPLASTNAME,
            HR.COMPANYNAME,
            HR.DIVISIONNAME,
            HR.POSITIONNAME,
            HR.DEPARTMENTNAME,
            LI.LoginDevice,
            LI.LoginDate,
            LI.Username,
              case when LI.LoginDevice = 1 then 'Computer'
	           when LI.LoginDevice =2 then 'Mobile'
	            end [typeLog]



              FROM [192.168.90.30\DEVELOP].[HRTRAINING].[dbo].[EMPLOYEE] HR
               JOIN [192.168.90.30\DEVELOP].[WEB_CENTER].[dbo].[LoginLogs] LI ON LI.EmployeeID = HR.CODEMPID WHERE ProjectID = '1' ORDER by LI.LoginDate desc"
    );

  }


  public function userAccess() {

    $permission_id = $_SESSION["user_permission"];

		$menu = json_decode((new PermissionService)->getMenu($permission_id));

		if (count($menu) > 0) {
			$allMenu = $menu[0]->Permission;
		} else {
      renderView('page/404');
      exit;
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

		if (in_array($current_uri, $temp)) {
			return true;
		} else {
      renderView('page/404');
      exit;
		}
  }

  public function logLogOut_alluser($userId,$typeLogin,$type,$machine) 
  {
    $computername = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $dateTime = date('Y-m-d H:i:s');
    $dateatimezone = explode(" ", $dateTime);

    $SerchEmp = Sqlsrv::queryArray(
      $this->conn,
      "SELECT * FROM UserMaster
      WHERE ID = ?",
      [
        $userId
      ]
    );

    $InsertLog = sqlsrv_query(
      $this->conn,
      "INSERT INTO [192.168.90.30\DEVELOP].[WEB_CENTER].[dbo].[LoginLogs] (EmployeeID,ComputerName,Username,LoginDevice,LoginDate,ProjectID)
              SELECT U.EmployeeID,".$computername.",U.Username,".$typeLogin.",".$dateTime.",1
                FROM RateTrans R JOIN 
                UserMaster U ON R.UserId = U.ID 
                WHERE R.Machine = ".$machine."
                AND R.LogoutDate IS NULL 
                AND R.UserId <> ".$userId.""
      );

    $InsertlogApp = sqlsrv_query(
      $this->conn,
      "INSERT INTO [192.168.90.30\DEVELOP].[EA_APP].[dbo].[TB_LOG_APP] (EMP_CODE,USER_NAME,HOST_NAME,".$type.",PROJECT_NAME)
        SELECT U.EmployeeID,U.Username,".$computername.",".date('Y-m-d H:i:s').",'STR Barcode'
        FROM RateTrans R JOIN 
        UserMaster U ON R.UserId = U.ID 
        WHERE R.Machine = ".$machine."
        AND R.LogoutDate IS NULL 
        AND R.UserId <> ".$userId.""
    );

  }
}
