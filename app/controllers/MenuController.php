<?php

namespace App\Controllers;

use App\Services\MenuService;
use App\Services\PermissionService;
use App\Components\Database;

class MenuController
{
	public function __construct()
	{
		$this->permission = new PermissionService;
	}
	public function all()
	{
		echo (new MenuService)->all();
	}

	public function create()
	{
		$form_type = filter_input(INPUT_POST, "form_type");
		$description = filter_input(INPUT_POST, "description");
		$typecheck = filter_input(INPUT_POST, "typecheck");
		$link = filter_input(INPUT_POST, "link");
		$id = filter_input(INPUT_POST, "_id");

		if ($form_type == 'create') {
			if ((new MenuService)->create($description, $link, $typecheck) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		}

		if ($form_type == 'update') {
			if ((new MenuService)->update($id, $description, $link, $typecheck) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		}
	}

	public function generate()
	{
		$permission_id = $_SESSION["user_permission"];
		$data = json_decode($this->permission->getMenu($permission_id));
		// $menu = explode(",", $data[0]->PermissionMenu);
		echo (new MenuService)->getMenu($data[0]->Permission);
		// echo json_encode($menu);
	}

	public function generateMobile()
	{
		header('Content-Type: application/json; charset=utf-8');
		$permission_id = $_SESSION["user_permission"];
		$data = json_decode($this->permission->getMenu($permission_id));
		// $menu = explode(",", $data[0]->PermissionMenu);
		echo (new MenuService)->getMenuMobile($data[0]->Permission);
		// echo json_encode($menu);
	}
}
