<?php

namespace App\Controllers;

use App\Services\AuthorizeService;
use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Models\Authorize;
use App\Models\User;

class AuthorizeController
{
	public function all()
	{
		echo (new AuthorizeService)->all();
	}

	public function create()
	{
		$description = filter_input(INPUT_POST, "description");
		$type = $_POST["type"];
		$id = filter_input(INPUT_POST, "_id");

		$result = (new AuthorizeService)->create($id, $description, $type);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]));
		} else {
			exit(json_encode(["status" => 403, "message" => $result]));
		}
	}

	public function edit($id)
	{
		$Description = filter_input(INPUT_POST, "Description");
		$Unhold_Unrepair_GT = filter_input(INPUT_POST, "Unhold_Unrepair_GT", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Unhold_Unrepair_Final = filter_input(INPUT_POST, "Unhold_Unrepair_Final", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Adjust_GT = filter_input(INPUT_POST, "Adjust_GT", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Adjust_Final = filter_input(INPUT_POST, "Adjust_Final", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Adjust_FG = filter_input(INPUT_POST, "Adjust_FG", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Loading = filter_input(INPUT_POST, "Loading", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$MovementReverse = filter_input(INPUT_POST, "MovementReverse", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$Unbom = filter_input(INPUT_POST, "Unbom", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$PostbackMovement = filter_input(INPUT_POST, "PostbackMovement", FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

		$result = (new AuthorizeService)->edit(
			$id,
			$Description,
			$Unhold_Unrepair_GT,
			$Unhold_Unrepair_Final,
			$Adjust_GT,
			$Adjust_Final,
			$Adjust_FG,
			$Loading,
			$MovementReverse,
			$Unbom,
			$PostbackMovement
		);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]));
		} else {
			exit(json_encode(["status" => 403, "message" => $result]));
		}
	}

	public function getPermissionField()
	{
		$conn = (new Database)->connect();
		echo Sqlsrv::queryJson(
			$conn,
			"SELECT COLUMN_NAME
			FROM STR_BARCODE_DEV3.INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_NAME = N'AuthorizeMaster'
			AND COLUMN_NAME NOT IN ('ID', 'Description')"
		);
	}

	public function isAuthorize()
	{
		$code = filter_input(INPUT_POST, 'code');
		$password = filter_input(INPUT_POST, 'password');

		$u = new User;
		$a = new Authorize;
		
		$u->username = $code;
		$u->password = $password;
		
		if ( !$u->isLogin() ) {
			return json_encode([
				'result' => false,
				'message' => 'รหัสผ่านไม่ถูกต้อง'
			]);
		}
		
		$authorize_id = $u->getAuthorizeId($code);
		$a->ID = $authorize_id[0]['Authorize'];
		
		if ( $a->isAuthorize('Unbom') ) {
			return json_encode([
				'result' => true,
				'message' => 'Authorize Successful!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => 'User not authorized'
			]);
		}
	}
}