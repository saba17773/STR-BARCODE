<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;

class PermissionService
{
	public function all()
	{	
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM PermissionMaster ORDER BY ID ASC");
	}

	public function create(
		$des_name, 
		$pDesktop, 
		$pMobile, 
		$default_page_desktop, 
		$default_page_mobile,
		$user_actions,
		$status
	)
	{
		if (isset($status)){
			$status=1;
		}else{
			$status=0;
		}

    $records_desktop = Utils::arr2str($pDesktop);
    $records_mobile = Utils::arr2str($pMobile);
    $records_actions = Utils::arr2str($user_actions);
    $des_name = trim($des_name);

    $conn = Database::connect();

    if (sqlsrv_begin_transaction($conn) === false) {
    	return 'can\'t connect database!';
    }

		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO PermissionMaster(
				Description,
				PermissionDesktop, 
				PermissionMobile,
				DefaultDesktop,
				DefaultMobile,
				actions,
				Status
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)",
			[
				$des_name, 
				$records_desktop, 
				$records_mobile, 
				$default_page_desktop,
				$default_page_mobile,
				$records_actions,
				$status
			]
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return "Create failed.";
		} else {
			sqlsrv_commit($conn);
			return 200;
		}
	}

	public function update(
		$des_name,
		$pDesktop, 
		$pMobile, 
		$default_page_desktop, 
		$default_page_mobile,
		$user_actions,
		$status,
		$per_id
	)
	{
		if (isset($status)){
			$status=1;
		}else{
			$status=0;
		}

		$records_desktop = Utils::arr2str($pDesktop);
		$records_mobile = Utils::arr2str($pMobile);
		$records_actions = Utils::arr2str($user_actions);
    $des_name = trim($des_name);

		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'can\'t connect database!';
		}

		// if ($user_actions !== 0) {
		// 	$insertUserActionState = [];
  //   	// update or insert new
  //   	foreach ($user_actions as $action) {
  //   		$updateUserActions = Sqlsrv::update(
	 //    		$conn,
	 //    		'UPDATE user_actions 
	 //    		SET status = 1
	 //    		WHERE permission_id = ?
	 //    		AND user_action = ? 
	 //    		AND status = 0
	 //    		IF @@ROWCOUNT = 0
	 //    		INSERT INTO user_actions(permission_id, user_action)
	 //    		VALUES(?, ?)',
	 //    		[
	 //    			$per_id, 
	 //    			$action,
	 //    			$per_id,
	 //    			$action
	 //    		]
	 //    	);

	 //    	if (!$updateUserActions) {
	 //    		$insertUserActionState[] = 400;
	 //    	} else {
	 //    		$insertUserActionState[] = 200;
	 //    	}

	 //    	if (in_array(400, $insertUserActionState)) {
	 //    		sqlsrv_rollback($conn);
	 //    		return 400;
	 //    	}
  //   	}

  //   	$getActions = Sqlsrv::queryArray(
  //   		$conn,
  //   		'SELECT * FROM user_actions
  //   		WHERE permission_id = ? 
  //   		AND status = 1',
  //   		[
  //   			$per_id
  //   		]
  //   	);

  //   	if (count($getActions) > 0) {
    		
  //   		$updateOldActionsState = [];
    		
  //   		foreach ($getActions as $action) {
  //   			if (!in_array($action['user_action'], $user_actions)) {
  //   				// update old 
		// 	    	$updateOldActions = Sqlsrv::update(
		// 	    		$conn,
		// 	    		'UPDATE user_actions
		// 	    		SET status = 0 
		// 	    		WHERE permission_id = ?
		// 	    		AND status = 1
		// 	    		AND user_action = ?',
		// 	    		[
		// 	    			$per_id,
		// 	    			$action['user_action']
		// 	    		]
		// 	    	);

		// 	    	if ($updateOldActions) {
		// 	    		$updateOldActionsState[] = 200;
		// 	    	} else {
		// 	    		$updateOldActionsState[] = 400;
		// 	    	}
  //   			}
  //   		}

  //   		if (in_array(400, $updateOldActionsState)) {
  //   			sqlsrv_rollback($conn);
  //   			return 'update old action failed!';
  //   		}
  //   	}
  //   } else {
  //   	$deactiveUserActions = Sqlsrv::update(
  //   		$conn,
  //   		'UPDATE user_actions
  //   		SET status = 0
  //   		WHERE permission_id = ?
  //   		AND Status = 1',
  //   		[$per_id]
  //   	);

  //   	if (!$deactiveUserActions) {
  //   		sqlsrv_rollback($conn);
  //   		return 'reset action failed!';
  //   	}
  //   }
		
		$query = Sqlsrv::update(
			$conn,
			"UPDATE PermissionMaster 
			SET Description = ?,
			PermissionDesktop = ?,
			PermissionMobile = ?,
			DefaultDesktop = ?,
			DefaultMobile = ?,
			actions = ?,
			Status = ?
			WHERE ID =?",
			[
				$des_name,
				$records_desktop, 
				$records_mobile, 
				$default_page_desktop,
				$default_page_mobile,
				$records_actions,
				$status,
				$per_id
			]
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return "update failed.";
		} else {
			sqlsrv_commit($conn);
			return 200;
		}
	}

	public function checkWhExist($des_name)
	{	
		$des_name = trim($des_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM PermissionMaster 
				WHERE Description = ? ",
				[$des_name]
			);
	}

	public function checkWhExistUpdate($des_name,$records_desktop, $records_mobile ,$status,$per_id)
	{	
		
		$des_name = trim($des_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM PermissionMaster 
				WHERE Description = ?",
				[$des_name]
			);
	}

	public function getMenu($permission_id)
	{
		$conn = Database::connect();
		$device = $_SESSION["user_device"];
		if ($device === "desktop") {
			
			$q = Sqlsrv::queryJson(
				$conn,
				"SELECT PermissionDesktop as Permission FROM PermissionMaster
				WHERE Status = 1
				AND ID = ?",
				[$permission_id]
			);
		} else if ($device === "mobile") {

			$q = Sqlsrv::queryJson(
				$conn,
				"SELECT PermissionMobile as Permission FROM PermissionMaster
				WHERE Status = 1
				AND ID = ?",
				[$permission_id]
			);
		} else {
			$q = Sqlsrv::queryJson(
				$conn,
				"SELECT PermissionDesktop as Permission FROM PermissionMaster
				WHERE Status = 1
				AND ID = ?",
				[$permission_id]
			);
		}
		
		return $q;
	}

	public function actionsUser()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			'SELECT id, description, slug, status FROM actions WHERE status = 1'
		);
		return $query;
	}

	public function actionsUserByPermissionDesktop($menu_desktop_id)
	{
		$conn = Database::connect();

		if ($menu_desktop_id === 'all') {

			$query = Sqlsrv::queryJson(
				$conn,
				'SELECT id, description, slug, status
				FROM actions 
				WHERE status = 1'
			);

			return $query;
		} else {
			$actions = [];
			$menu_lists = explode(',', $menu_desktop_id);

			foreach ($menu_lists as $menu) {
				$query = Sqlsrv::queryArray(
					$conn,
					'SELECT id, description, slug, status
					FROM actions 
					WHERE status = 1',
					[$menu]
				);

				foreach ($query as $rows) {
					$actions[] = [
						'id' => $rows['id'],
						'description' => $rows['description'],
						'slug' => $rows['slug'],
						'status' => $rows['status']
					];
				}				
			}
			return json_encode($actions);
		}
		
	}

	public function getUserAction($user_permission , $action)
	{
		
		$conn = Database::connect();
		
		$getActions = Sqlsrv::queryArray(
			$conn,
			'SELECT actions FROM PermissionMaster
			WHERE ID = ?',
			[
				$user_permission
			]
		);

		if (!$getActions) {
			return false;
		}

		if ($getActions[0]['actions'] === null) {
			return false;
		}

		$actionsAll = $getActions[0]['actions'];

		$actionsList = Sqlsrv::queryArray(
			$conn,
			"SELECT slug FROM actions WHERE id IN ($actionsAll)"
		);

		if (!$actionsList) {
			return false;
		}

		$currentActions = [];

		foreach ($actionsList as $value) {
			$currentActions[] = $value['slug'];
		}

		if (count($currentActions) > 0) {	
			if (!in_array($action, $currentActions)) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function userActionActive($permission_id = null)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			'SELECT user_action FROM user_actions
			WHERE menu_id = ?
			AND status = 1',
			[$permission_id]
		);
	}

	public function actionsAll()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			'SELECT * FROM actions ORDER BY id DESC'
		);
	}

	public function actionsEdit($id, $description, $slug, $menu_id, $status)
	{
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'can\'t connect database!';
		}

		$update = Sqlsrv::update(
			$conn,
			'UPDATE actions 
			SET description = ?,
			slug = ?,
			status = ?,
			menu_id = ?
			WHERE id = ?',
			[
				$description,
				$slug,
				$status,
				$menu_id,
				$id
			]
		);

		if ($update) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

	public function actionsCraete($description, $slug, $permission_id)
	{
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return 'can\'t connect database!';
		}

		$create = Sqlsrv::insert(
			$conn,
			'INSERT INTO actions(description, slug)
			VALUES(?, ?)',
			[
				$description,
				$slug
			]
		);

		if ($create) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}
}