<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\MenuService;
use App\Components\Utils;
use App\V2\User\UserAPI;

class UserController
{
	private $userService = null;

	public function __construct()
	{
		$this->userService = new UserService();
	}

	public function all()
	{
		return $this->userService->all();
	}

	public function create()
	{
		$username = trim(filter_input(INPUT_POST, "username"));
		$password = trim(filter_input(INPUT_POST, "password"));
		$fullname = trim(filter_input(INPUT_POST, "fullname"));
		$department = trim(filter_input(INPUT_POST, "department"));
		$warehouse = trim(filter_input(INPUT_POST, "warehouse"));
		$location = trim(filter_input(INPUT_POST, "location"));
		$status = trim(filter_input(INPUT_POST, "status")) ? 1 : 0;
		$time_check = trim(filter_input(INPUT_POST, "time_check")) ? 1 : 0;
		$auth = trim(filter_input(INPUT_POST, "auth"));
		$permission = trim(filter_input(INPUT_POST, "permission"));
		$formtype = trim(filter_input(INPUT_POST, "form_type"));
		$employee = trim(filter_input(INPUT_POST, "empid"));
		$company = trim(filter_input(INPUT_POST, "company"));
		$component = trim(filter_input(INPUT_POST, "component")) ? 1 : 0;

		if ($component === 1) {
			$unit = trim(filter_input(INPUT_POST, "unit"));
			$section = Utils::arr2str($_POST['section']);
		} else {
			$unit = null;
			$section = null;
		}

		// $default_page = trim(filter_input(INPUT_POST, "default_page"));
		// $default_page_mobile = trim(filter_input(INPUT_POST, "default_page_mobile"));
		$id = filter_input(INPUT_POST, "_id");
		$shift = filter_input(INPUT_POST, "shift");

		if (!$username) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Username"]));
		}

		if (!$password) {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Password"]));
		}

		if (!$department) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก department"]));
		}

		if (!$warehouse) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก warehouse"]));
		}

		if (!$location) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก location"]));
		}

		if (!$permission) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก permission"]));
		}

		if (!$employee) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก employee"]));
		}

		if (!$company) {
			exit(json_encode(["status" => 404, "message" => "กรุณาเลือก company"]));
		}

		// if (!$default_page) {
		// 	exit(json_encode(["status" => 404, "message" => "กรุณาเลือก default page"]));
		// }

		// if (!$default_page_mobile) {
		// 	exit(json_encode(["status" => 404, "message" => "กรุณาเลือก default page for mobile device"]));
		// }
		if ($formtype == "create") {
			$create_user = $this->userService->create(
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
			);

			if ($create_user === 200) {
				return json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				return json_encode(["status" => 404, "message" => $create_user]);
			}
		}

		if ($formtype == "update") {
			$edit_user = $this->userService->update(
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
			);

			if ($edit_user === 200) {
				return json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				return json_encode(["status" => 404, "message" => $edit_user]);
			}
		}

		return json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
	}

	public function handheldAuth()
	{
		$username = filter_input(INPUT_POST, "hh_username");
		$password = filter_input(INPUT_POST, "hh_password");

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "message" => "Please fill Username data"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "message" => "Please fill Password data"]));
		}

		$is_exist = $this->userService->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "message" => "User not found"]));
		}

		$user_data = $this->userService->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "message" => "Can't fetch user data."]));
		}

		if ($user_data[0]["Location"] !== 3) {
			exit(json_encode(["status" => 404, "message" => "You don't have permission to access this section."]));
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			$_SESSION["user_device"] = "mobile";
		} else {
			$_SESSION["user_device"] = "desktop";
		}

		$_SESSION["user_name"] = $user_data[0]["Username"];
		$_SESSION["user_login"] = $user_data[0]["ID"];
		$_SESSION["user_company"] = $user_data[0]["Company"];
		$_SESSION["user_warehouse"] = $user_data[0]["Warehouse"];
		$_SESSION["user_location"] = $user_data[0]["Location"];
		$_SESSION["Shift"] = $user_data[0]["Shift"];
		$_SESSION["user_permission"] = $user_data[0]["PermissionID"];
		$_SESSION["user_department"] = $user_data[0]["Department"];
		$_SESSION["user_department_desc"] = $user_data[0]["DPMDESC"];

		$saveLastLogin = (new UserAPI)->saveLastLogin($user_data[0]["ID"]);
		$logLogin = (new UserAPI)->logLogin($user_data[0]["ID"], $typelogin = 1, 'LOGIN_DATE');

		if ((new UserService)->chkCureUser($username) === true) {
			$insertRate_Cure = (new UserService)->insertRate_Cure($_SESSION["user_login"], $_SESSION["Shift"], $_SESSION["user_location"]);
			$_SESSION["getID"] = $insertRate_Cure["getID"];
		}

		echo json_encode([
			"status" => 200,
			"message" => "ดำเนินการเสร็จสิ้น",
			"location" => $user_data[0]["Location"]
		]);
	}

	public function desktopAuth()
	{
		$username = filter_input(INPUT_POST, "username_login");
		$password = filter_input(INPUT_POST, "password_login");
		$shift = filter_input(INPUT_POST, "shift");

		// $username = str_replace("@", "_", $username);

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		if (trim($shift) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]));
		}

		$is_exist = $this->userService->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "message" => "ข้อมูลไม่ถูกต้อง "]));
		}

		if ($this->userService->isActive($username) === false) {
			exit(json_encode(["status" => 404, "message" => "ผู้ใช้นี้ยังไม่ได้เปิดใช้งาน"]));
		}

		$user_data = $this->userService->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "message" => "ไม่สามารถดึงข้อมูลผู้ใช้ได้"]));
		}

		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			$_SESSION["user_device"] = "mobile";
			$device = $user_data[0]["DefaultMobile"];
			$logLogin = (new UserAPI)->logLogin($user_data[0]["ID"], $typelogin = 2, 'LOGIN_DATE');
		} else {
			$_SESSION["user_device"] = "desktop";
			$device = $user_data[0]["DefaultDesktop"];
			$logLogin = (new UserAPI)->logLogin($user_data[0]["ID"], $typelogin = 1, 'LOGIN_DATE');
		}

		$_SESSION["user_name"] = $user_data[0]["Username"];
		$_SESSION["user_login"] = $user_data[0]["ID"];
		$_SESSION["user_company"] = $user_data[0]["Company"];
		$_SESSION["user_warehouse"] = $user_data[0]["Warehouse"];
		$_SESSION["user_location"] = $user_data[0]["Location"];
		$_SESSION["Shift"] = $shift;
		$_SESSION["user_permission"] = $user_data[0]["PermissionID"];
		$_SESSION["user_department"] = $user_data[0]["Department"];
		$_SESSION["user_department_desc"] = $user_data[0]["DPMDESC"];
		$_SESSION["user_component"] = $user_data[0]["UnitComponent"];
		$_SESSION["user_componentsection"] = $user_data[0]["SectionComponent"];
		$_SESSION["user_componentcomplete"] = $user_data[0]["SectionComplete"];
		$_SESSION["user_employee"] = $user_data[0]["EmployeeID"];

		$saveLastLogin = (new UserAPI)->saveLastLogin($user_data[0]["ID"]);

		if ((int) $device !== 0) {

			$get_menu = (new MenuService)->getMenu($device);

			if ($get_menu === false) {
				$defaultLink = '/tracking';
			} else {
				$link_direct = json_decode($get_menu);
				$defaultLink = $link_direct[0]->Link;
			}
		} else {
			$defaultLink = '/tracking';
		}


		// $link_direct = self::getDefaultPage();

		// echo json_encode([
		// 	"status" => 200,
		// 	"message" => "ดำเนินการเสร็จสิ้น",
		// 	"user_location" => $_SESSION["user_location"],
		// 	"redirectTo" => $defaultLink
		// ]);

		if ($this->userService->chkWHUser($username) === false) {
			//*Cure
			// if ((new UserService)->chkCureUser($username) === true) 
			// {
			// 	$insertRate_Cure = (new UserService)->insertRate_Cure($_SESSION["user_login"],$_SESSION["Shift"],$_SESSION["user_location"]);
			// 	$_SESSION["getID"] = $insertRate_Cure["getID"];
			// }

			return json_encode([
				"status" => 200,
				// "getID" => $_SESSION["getID"],
				"user_location" => $_SESSION["user_location"],
				"redirectTo" => $defaultLink
			]);
		} else {
			//insert in table RateTrans
			//$insertRate = (new UserService)->insertRate($_SESSION["user_login"],$_SESSION["Shift"],$_SESSION["user_location"]);

			// $link =  (new UserService)->getDefaultPage(
			// 	$_SESSION['user_permission'],
			// 	$_SESSION["user_device"]
			// );
			$_SESSION["build_mc"] = "";
			return json_encode([
				"status" => 200,
				"message" => "Greentire User",
				"user_location" => $_SESSION["user_location"],
				"build_mc" => $_SESSION["build_mc"],
				"redirectTo" => '/addUser'
			]);
		}
	}

	public function bindGrvUser()
	{
		$build_mc = $_SESSION["build_mc"];

		return $this->userService->ShowallUser($build_mc);
	}
	public function chkUserLogin()
	{
		$user = $_SESSION["user_login"];

		$create_new = $this->userService->chkUserLogin($user);

		if ($create_new["status"] === 200) {

			return json_encode(["status" => 200, "message" => "OK"]);
		} else {
			return json_encode(["status" => 404, "message" => "มีการเข้าสู่ระบบแล้ว ที่เครื่อง " . $this->userService->getMachine($user) . " ต้องการออกจากระบบหรือไม่ ?"]);
		}
	}
	public function chkUserLogin2($username)
	{
		$username = filter_input(INPUT_POST, "username_login");
		$create_new = $this->userService->chkUserLogin2($username);
		$user_data = $this->userService->getUserData($username);
		$user = $user_data[0]["ID"];

		if ($create_new["status"] === 200) {

			return json_encode(["status" => 200, "message" => "OK"]);
		} else {
			return json_encode(["status" => 404, "message" => "มีการเข้าสู่ระบบแล้ว ที่เครื่อง " . (new UserService)->getMachine($user) . " ต้องการออกจากระบบหรือไม่ ?"]);
		}
	}
	public function insertRate($build_mc)
	{

		$user_id = $_SESSION["user_login"];
		$shift = $_SESSION["Shift"];
		$location = $_SESSION["user_location"];


		$username = $_SESSION["user_name"];

		$chkUserLogon = $this->userService->chkUserLogon($build_mc);
		if ($chkUserLogon["status"] === 404) {
			return json_encode(["status" => 404, "message" => $chkUserLogon["message"]]);
		}

		$chkMC = $this->userService->chkMC($build_mc);
		if ($chkMC <= 0) {
			return json_encode(["status" => 404, "message" => "เลขเครื่องไม่ถูกต้องตรวจสอบอีกครั้ง"]);
		}
		$_SESSION["build_mc"] = $build_mc;

		$gettotal = $this->userService->getTotalByUser($username);
		if ($gettotal === null) {
			$total = 0;
		} else {
			$total = $gettotal;
		}

		$insertRate = $this->userService->insertRate($user_id, $shift, $location, $_SESSION["build_mc"], $total);
		if ($insertRate["status"] === 200) {
			return json_encode(["status" => 200, "message" => "OK"]);
		} else {
			return json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
		}

		$id_rate = $this->userService->getId($_SESSION["user_login"]);
		$_SESSION["id_rate"] = $id_rate;
	}
	public function insertMore($username, $password, $build_type)
	{

		$username = filter_input(INPUT_POST, "username_login");
		$password = filter_input(INPUT_POST, "password_login");

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Username"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "message" => "กรุณากรอก Password"]));
		}

		$is_exist = $this->userService->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "message" => "ข้อมูลไม่ถูกต้อง"]));
		}

		if ($this->userService->isActive($username) === false) {
			exit(json_encode(["status" => 404, "message" => "ผู้ใช้นี้ยังไม่ได้เปิดใช้งาน"]));
		}
		if ($this->userService->chkWHUser($username) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่มีสิทธิ์เข้าถึง สามารถใช้ได้เฉพาะแผนก Build เท่านั้น"]));
		}
		$user_data = $this->userService->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "message" => "ไม่สามารถดึงข้อมูลผู้ใช้ได้"]));
		}

		$user_id = $user_data[0]["ID"];
		$shift = $_SESSION["Shift"];
		$location = $user_data[0]["Location"];
		$build_mc = $_SESSION["build_mc"];

		$gettotal = $this->userService->getTotalByUser($username);
		if ($gettotal === null) {
			$total = 0;
		} else {
			$total = $gettotal;
		}

		$insertRate = $this->userService->insertMore($user_id, $shift, $location, $build_mc, $build_type, $total);
		if ($insertRate["status"] === 200) {
			$logLogin = (new UserAPI)->logLogin($user_id, $typelogin = 2, 'LOGIN_DATE');
			echo json_encode(["status" => 200, "message" => "insert ok"]);
		} else {
			echo json_encode(["status" => 404, "message" => $insertRate["message"]]);
		}
	}
	public function updateLogoutDate($username, $password, $id)
	{
		$username = filter_input(INPUT_POST, "username_logout");
		$password = filter_input(INPUT_POST, "password_logout");

		if (trim($username) == "") {
			exit(json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "กรุณากรอก Username"]));
		}

		if (trim($password) == "") {
			exit(json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "กรุณากรอก Password"]));
		}

		$is_exist = $this->userService->isExist($username, $password);

		if ($is_exist === false) {
			exit(json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "ข้อมูลไม่ถูกต้อง "]));
		}

		$chk_userpass = $this->userService->chkUserPass_logout($username, $password, $id);

		if ($chk_userpass === false) {
			exit(json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "ข้อมูลไม่ถูกต้อง "]));
		}
		$user_data = $this->userService->getUserData($username);

		if (!$user_data) {
			exit(json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "ไม่สามารถดึงข้อมูลผู้ใช้ได้"]));
		}

		$user_id = $user_data[0]["ID"];
		$id = $id;

		$chkLogout = $this->userService->chkLogout($_SESSION["build_mc"], $user_id);
		if ($chkLogout <= 0) {
			$updateLogoutDate = $this->userService->updateLogoutDate($user_id, $id);
			if ($updateLogoutDate["status"] === 200) {
				$logLogin = (new UserAPI)->logLogin($user_id, $typelogin = 1, 'LOGOUT_DATE');
				echo json_encode([
					"status" => 400,
					"message" => "logout ok",
					"redirectTo" => '/user/logout'
				]);
			}
		} else {
			if ($user_id === $_SESSION["user_login"]) {
				$chkMainSession = $this->userService->chkLogoutMainSession($username);
				if (!$chkMainSession) {
					exit(json_encode([
						"status" => 404,
						"redirectTo" => '/addUser',
						"message" => "กรุณาให้ Change Code ออกจากระบบ"
					]));
				}

				$user_data2 = $this->userService->getUserData($chkMainSession);

				$_SESSION["user_name"] = $user_data2[0]["Username"];
				$_SESSION["user_login"] = $user_data2[0]["ID"];
				$_SESSION["user_company"] = $user_data2[0]["Company"];
				$_SESSION["user_warehouse"] = $user_data2[0]["Warehouse"];
				$_SESSION["user_location"] = $user_data2[0]["Location"];
				// $_SESSION["Shift"] = $shift;
				$_SESSION["user_permission"] = $user_data2[0]["PermissionID"];
				$_SESSION["user_department"] = $user_data2[0]["Department"];
				$_SESSION["user_department_desc"] = $user_data2[0]["DPMDESC"];
				$_SESSION["user_component"] = $user_data2[0]["UnitComponent"];
				$_SESSION["user_componentsection"] = $user_data2[0]["SectionComponent"];
				$_SESSION["user_componentcomplete"] = $user_data2[0]["SectionComplete"];

				$updateLogoutDate = $this->userService->updateLogoutDate($user_id, $id);

				if ($updateLogoutDate["status"] === 200) {
					echo json_encode([
						"status" => 400,
						"redirectTo" => '/addUser',
						"message" => 'Change Session'
					]);
				} else {
					echo json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "can't logout"]);
				}
			} else {
				$updateLogoutDate = $this->userService->updateLogoutDate($user_id, $id);
				if ($updateLogoutDate["status"] === 200) {
					echo json_encode(["status" => 200, "message" => "logout ok"]);
				} else {
					echo json_encode(["status" => 404, "redirectTo" => '/addUser', "message" => "can't logout"]);
				}
			}
		}
	}

	public function logoutRequest()
	{

		// $chkUserLogon = $this->userService->chkUserLogon($build_mc);
		// if ($chkUserLogon["status"] === 404) {
		// 	exit(json_encode(["status" => 404, "message" => $chkUserLogon["message"]]));
		// }
		$getMac = $this->userService->getMachine($_SESSION["user_login"]);
		$chkBuildTypeId = $this->userService->chkBuildTypeId($getMac, $_SESSION["user_login"]);
		if ($chkBuildTypeId ===  2) {
			exit(json_encode(["status" => 404, "message" => "เครื่องที่เคยLoginไว้มีตำแหน่ง Change Code ยังไม่ได้ออกจากระบบ"]));
		}

		$logoutRequest = $this->userService->logoutRequest($_SESSION["user_login"]);
		if ($logoutRequest["status"] === 200) {
			echo json_encode(["status" => 200, "message" => "logout ok"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ไม่สามารถออกจากระบบได้"]);
		}
	}
	public function logoutRequest2()
	{

			// $chkUserLogon = $this->userService->chkUserLogon($build_mc);
			// if ($chkUserLogon["status"] === 404) {
			// 	exit(json_encode(["status" => 404, "message" => $chkUserLogon["message"]]));
			// }
		;
		$username = filter_input(INPUT_POST, "username_login");
		$user_data = $this->userService->getUserData($username);
		$user_id = $user_data[0]["ID"];

		$getMac = $this->userService->getMachine2($username);
		$chkBuildTypeId = $this->userService->chkBuildTypeId($getMac, $user_id);
		if ($chkBuildTypeId ===  2) {
			exit(json_encode(["status" => 404, "message" => "เครื่องที่เคยLoginไว้มีตำแหน่ง Change Code ยังไม่ได้ออกจากระบบ"]));
		}

		$logoutRequest = $this->userService->logoutRequest($user_id);
		if ($logoutRequest["status"] === 200) {
			$logLogin = (new UserAPI)->logLogin($user_id, $typelogin = 1, 'LOGOUT_DATE');
			echo json_encode(["status" => 200, "message" => "logout ok"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ไม่สามารถออกจากระบบได้"]);
		}
	}

	public function getMachine($user_id)
	{


		$getMachine = $this->userService->getMachine($user_id);
		if ($getMachine === $_SESSION["build_mc"]) {
			echo json_encode(["status" => 404, "message" => "ผู้ใช้นี้อยู่ในกลุ่มนี้อยู่แล้ว " . $_SESSION["build_mc"]]);
		} else {
			echo json_encode(["status" => 200, "message" => "OK "]);
		}
	}
	public function getMachine2($username)
	{

		$username = filter_input(INPUT_POST, "username_login");
		$getMachine = $this->userService->getMachine2($username);
		if ($getMachine === $_SESSION["build_mc"]) {
			echo json_encode(["status" => 404, "message" => "ผู้ใช้นี้อยู่ในกลุ่มนี้อยู่แล้ว " . $_SESSION["build_mc"]]);
		} else {
			echo json_encode(["status" => 200, "message" => "OK "]);
		}
	}
	public function chkBuildType($build_type)
	{
		$build_mc = $_SESSION["build_mc"];
		$build_type = $build_type;


		$chkBuild = $this->userService->chkBuildType($build_mc, $build_type);


		if ($chkBuild["status"] === 200) {

			echo json_encode(["status" => 200, "message" => "OK"]);
		} else {

			echo json_encode(["status" => 404, "message" => $chkBuild["message"]]);
		}
	}
	public function chkTypeMC()
	{
		$build_mc = $_SESSION["build_mc"];
		$chkTypeMC = $this->userService->chkTypeMC($build_mc);
		if ($chkTypeMC === "TBR") {

			echo json_encode(["status" => 200, "message" => $chkTypeMC, "mc" => $build_mc]);
		} else if ($chkTypeMC === "PCR") {

			echo json_encode(["status" => 404, "message" => $chkTypeMC, "mc" => $build_mc]);
		}
	}
	public function firstRows_Session($machine)
	{

		$chkfirstRow = $this->userService->firstRows_Session($machine);
		if ($chkfirstRow ===  $_SESSION['user_name']) {
			echo json_encode(["status" => 200]);
		} else {

			$user_data2 = $this->userService->getUserData($chkfirstRow);

			$_SESSION["user_name"] = $user_data2[0]["Username"];
			$_SESSION["user_login"] = $user_data2[0]["ID"];
			$_SESSION["user_company"] = $user_data2[0]["Company"];
			$_SESSION["user_warehouse"] = $user_data2[0]["Warehouse"];
			$_SESSION["user_location"] = $user_data2[0]["Location"];
			// $_SESSION["Shift"] = $shift;
			$_SESSION["user_permission"] = $user_data2[0]["PermissionID"];
			$_SESSION["user_department"] = $user_data2[0]["Department"];
			$_SESSION["user_department_desc"] = $user_data2[0]["DPMDESC"];
			$_SESSION["user_component"] = $user_data2[0]["UnitComponent"];
			$_SESSION["user_componentsection"] = $user_data2[0]["SectionComponent"];
			$_SESSION["user_componentcomplete"] = $user_data2[0]["SectionComplete"];


			echo json_encode(["status" => 404]);
		}
	}
	public function clearSession()
	{
		session_destroy();
		echo json_encode(["status" => 200, "message" => "clear session successful!"]);
	}

	public function logout()
	{

		$detect = new \Mobile_Detect;

		if ((int) $_SESSION["user_location"] === 3 && $detect->isMobile() === true && (int) $_SESSION["user_warehouse"] !== 4) { // curing 
			$redirect_path = APP_ROOT; //. "/hh/auth";
		} else if ((int) $_SESSION["user_permission"] === 19 && $detect->isMobile() === true) { // final inspect
			$redirect_path = APP_ROOT;
		} else if ((int) $_SESSION["user_warehouse"] === 1  && (int) $_SESSION["user_location"] === 1  && $detect->isMobile() === true) {

			$updateLogoutDate = $this->userService->updateLogoutDate_AllUser($_SESSION["build_mc"], $_SESSION["user_login"]);
			if ($updateLogoutDate["status"] === 200) {
				$logLogin = (new UserAPI)->logLogOut_alluser($user_data[0]["ID"], $typelogin = 1, 'LOGOUT_DATE', $_SESSION["build_mc"]);
				$redirect_path = APP_ROOT;
			} else {
				$redirect_path = "/addUser";
			}
		} else if ((int) $_SESSION["user_permission"] === 3) {
			$updateLogoutDate_Cure = $this->userService->updateLogoutDate_Cure($_SESSION["user_login"], $_SESSION["getID"]);
			if ($updateLogoutDate_Cure["status"] === 200) {
				$redirect_path = "/hh/auth";
			} else {
				$redirect_path = "/curing?show=0";
			}
		}
		// else if ((int) $_SESSION["user_warehouse"] === 4  && (int) $_SESSION["user_location"] === 3  && $detect->isMobile() === true) {
		// 	$updateLogoutDate_Cure = $this->userService->updateLogoutDate_Cure($_SESSION["user_login"], $_SESSION["getID"]);
		// 	if ($updateLogoutDate_Cure["status"] === 200) {
		// 		$redirect_path = "/hh/auth";
		// 	} else {
		// 		$redirect_path = "/curing?show=0";
		// 	}
		// } 
		else {
			$redirect_path = APP_ROOT;
		}

		$user_data[0]["ID"] = "";

		$user_data = $this->userService->getUserData($_SESSION["user_name"]);

		if ($user_data[0]["ID"] !== "" && isset($_SESSION["user_name"])) {
			$logLogin = (new UserAPI)->logLogin($user_data[0]["ID"], $typelogin = 1, 'LOGOUT_DATE');
		}

		session_destroy();

		if ($redirect_path !== '') {
			header("Location: " . $redirect_path);
		} else {
			header("Location: / ");
		}
	}

	/**
	 * @param  string
	 * @param  string
	 * @return template
	 */
	public function genUserBarcode($username, $empCode, $password, $name)
	{
		renderView('page/user_barcode', [
			"usernamedata" => $username,
			"empCode" => $empCode,
			"name" => $name,
			"password" => base64_decode($password)
		]);
	}

	public function authorize()
	{
		$code = filter_input(INPUT_POST, "code");
		$user_password = filter_input(INPUT_POST, "password");
		$type = filter_input(INPUT_POST, "type");

		if ($this->userService->isUserBarcodeExist($code) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่มี User ในระบบ"]));
		}

		if ($this->userService->isAuthorize($code, $user_password, $type) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		// if ($this->userService->isDepartmentTrue($code, $type) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "Location incorrect."]));
		// }

		exit(json_encode(["status" => 200, "message" => "Authorize successful!"]));
	}

	public function getAuthorizeType()
	{
		$type = filter_input(INPUT_POST, "type");

		$u = ['Unhold_Unrepair_GT', 'Unhold_Unrepair_Final'];
		$a = ['Adjust_GT', 'Adjust_Final', 'Adjust_FG'];

		if ($_SESSION["user_warehouse"] === 1) {
			if ($type === 'unhold_unrepair') {
				return json_encode(["type" => $u[0]]);
			}

			if ($type === 'adjust') {
				return json_encode(["type" => $a[0]]);
			}
		}

		if ($_SESSION["user_warehouse"] === 2) {
			if ($type === 'unhold_unrepair') {
				return json_encode(["type" => $u[1]]);
			}

			if ($type === 'adjust') {
				return json_encode(["type" => $a[1]]);
			}
		}

		if ($_SESSION["user_warehouse"] === 3) {
			if ($type === 'adjust') {
				return json_encode(["type" => $a[2]]);
			}
		}

		return json_encode(["type" => $_SESSION["user_warehouse"]]);
		// End
	}

	public function getUserLocation()
	{
		if (isset($_SESSION['user_location']) && $_SESSION['user_location'] !== '') {
			echo json_encode(["location" => $_SESSION['user_location']]);
		} else {
			echo json_encode(["location" => '']);
		}
	}

	public function getDefaultPage()
	{
		$link = $this->userService->getDefaultPage(
			$_SESSION['user_permission'],
			$_SESSION["user_device"]
		);

		$wrap = '<h1>Permission Required!</h1>';
		$wrap .= '<p>You don\'t have permission to access this page!</p>';
		$wrap .= '<a class="btn btn-primary btn-lg" href="' . $link . '" role="button">Go to home page.</a>';
		return $wrap;
	}

	public function getDefaultLink()
	{
		$link = $this->userService->getDefaultPage(
			$_SESSION['user_permission'],
			$_SESSION["user_device"]
		);

		return $link;
	}
	public function APIloglogin()
	{
		echo (new UserAPI)->APIloglogin();
	}

	// public function insertRate_Cure($build_mc)
	// {

	// 	$user_id = $_SESSION["user_login"];
	// 	$shift = $_SESSION["Shift"];
	// 	$location = $_SESSION["user_location"];


	// 	$username = $_SESSION["user_name"];

	// 	$chkUserLogon = $this->userService->chkUserLogon($build_mc);
	// 	if ($chkUserLogon["status"] === 404) {
	// 		exit(json_encode(["status" => 404, "message" => $chkUserLogon["message"]]));
	// 	}

	// 	$chkMC = $this->userService->chkMC($build_mc);
	// 	if ($chkMC <= 0) {
	// 		exit(json_encode(["status" => 404, "message" => "เลขเครื่องไม่ถูกต้อง"]));
	// 	}
	// 	$_SESSION["build_mc"] = $build_mc;

	// 	$gettotal = $this->userService->getTotalByUser($username);
	// 	if ($gettotal === null) {
	// 		$total = 0;
	// 	} else {
	// 		$total = $gettotal;
	// 	}

	// 	$insertRate = $this->userService->insertRate($user_id, $shift, $location, $_SESSION["build_mc"], $total);
	// 	if ($insertRate["status"] === 200) {
	// 		echo json_encode(["status" => 200, "message" => "OK"]);
	// 	} else {
	// 		echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
	// 		exit;
	// 	}

	// 	$id_rate = $this->userService->getId($_SESSION["user_login"]);
	// 	$_SESSION["id_rate"] = $id_rate;
	// }

	public function logoutmobile()
	{



		session_destroy();


		header("Location: / ");
	}
}
