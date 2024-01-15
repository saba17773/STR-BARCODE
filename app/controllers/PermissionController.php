<?php

namespace App\Controllers;

use App\Services\PermissionService;

class PermissionController
{
	public function all()
	{
		echo (new PermissionService)->all();
	}

	public function create()
	{
		$des_name = filter_input(INPUT_POST, "des_name");
		$default_page_desktop = filter_input(INPUT_POST, 'default_page_desktop');
		$default_page_mobile = filter_input(INPUT_POST, 'default_page_mobile');

		if (isset($_POST["permission_desktop"])) {
			$permission_desktop = $_POST["permission_desktop"];
		} else {
			$permission_desktop = null;
		}
	
		if (isset($_POST["permission_mobile"])) {
			$permission_mobile = $_POST["permission_mobile"];
		} else {
			$permission_mobile = null;
		}

		if (isset($_POST['user_actions'])) {
			$user_actions = $_POST['user_actions'];
		} else {
			$user_actions = null;
		}

		if (!isset($default_page_mobile)) {
			$default_page_mobile = 0;
		}

		if (!isset($default_page_desktop)) {
			$default_page_desktop = 0;
		}

		$status   = filter_input(INPUT_POST, "status");
		$per_id   = filter_input(INPUT_POST, "per_id");
		$form_type = filter_input(INPUT_POST, "form_type");

		if ($form_type == "create") {

			$create = (new PermissionService)->create(
				$des_name,
				$permission_desktop, 
				$permission_mobile,
				$default_page_desktop,
				$default_page_mobile,
				$user_actions,
				$status
			);

			if ($create === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);	
			} else {
				echo json_encode(["status" => 404, "message" => $create]);
			}
		}

		if ($form_type == "update") {

			$update = (new PermissionService)->update(
				$des_name, 
				$permission_desktop, 
				$permission_mobile, 
				$default_page_desktop,
				$default_page_mobile,
				$user_actions,
				$status, 
				$per_id
			);

			if ($update === 200) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);	
			} else {
				echo json_encode(["status" => 404, "message" => $update]);
			}
		}
	}

	public function actionsUser()
	{
		echo (new PermissionService)->actionsUser();
	}

	public function actionsUserByPermissionDesktop($menu_desktop_id)
	{
		echo (new PermissionService)->actionsUserByPermissionDesktop($menu_desktop_id);
	}

	public function getUserAction($permission_id , $action)
	{
		return (new PermissionService)->getUserAction($permission_id, $action);
	}

	public function userActionActive($permission_id)
	{
		echo (new PermissionService)->userActionActive($permission_id);
	}

	public function actionsAll()
	{
		echo (new PermissionService)->actionsAll();
	}

	public function actionsEdit()
	{
		$id = filter_input(INPUT_POST, 'id');
		$description = filter_input(INPUT_POST, 'description');
		$slug = filter_input(INPUT_POST, 'slug');
		$menu_id = filter_input(INPUT_POST, 'menu_id');
		$status = filter_input(INPUT_POST, 'status');

		$result = (new PermissionService)->actionsEdit(
			$id, 
			$description, 
			str_replace(' ', '_', strtolower($slug)), 
			$menu_id, 
			$status
		);

		if ($result === 200) {
			echo json_encode(['status' => 200, 'message' => 'update successful!']);
		} else {
			echo json_encode(['status' => 400, 'message' => 'update failed!']);
		}
	}

	public function actionsCraete()
	{
		$result = (new PermissionService)->actionsCraete('default name', 'sample_slug', 0);

		if ($result === 200) {
			echo json_encode(['status' => 200, 'message' => 'create successful!']);
		} else {
			echo json_encode(['status' => 400, 'message' => 'create failed!']);
		}
	}
}