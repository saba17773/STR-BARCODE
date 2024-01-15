<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class AuthorizeService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM AuthorizeMaster"
		);
	}

	public function create($id, $description, $type)
	{
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		if (count($type) > 0) {

			if (in_array(0, $type)) {
				$unhold_unrepair_gt = 1;
			} else {
				$unhold_unrepair_gt = 0;
			}

			if (in_array(1, $type)) {
				$unhold_unrepair_final = 1;
			} else {
				$unhold_unrepair_final = 0;
			}

			if (in_array(2, $type)) {
				$loading = 1;
			} else {
				$loading = 0;
			}

			if (in_array(3, $type)) {
				$adjust_gt = 1;
			} else {
				$adjust_gt = 0;
			}

			if (in_array(4, $type)) {
				$adjust_final = 1;
			} else {
				$adjust_final = 0;
			}

			if (in_array(5, $type)) {
				$adjust_fg = 1;
			} else {
				$adjust_fg = 0;
			}

			if (in_array(6, $type)) {
				$movementReverse = 1;
			} else {
				$movementReverse = 0;
			}

			if (in_array(7, $type)) {
				$unbom = 1;
			} else {
				$unbom = 0;
			}

			if (in_array(8, $type)) {
				$postbackmovement = 1;
			} else {
				$postbackmovement = 0;
			}
			
		} else {
			$unhold_unrepair_gt = 0;
			$unhold_unrepair_final = 0;
			$loading = 0;
			$adjust_gt = 0;
			$adjust_final = 0;
			$adjust_fg = 0;
			$movementReverse = 0;
			$unbom = 0;
			$postbackmovement = 0;
		}
		
		$q = Sqlsrv::update(
			$conn,
			"UPDATE AuthorizeMaster
			SET Description = ?,
			Unhold_Unrepair_GT = ?,
			Unhold_Unrepair_Final = ?,
			Loading = ?,
			Adjust_GT = ?,
			Adjust_Final = ?,
			Adjust_FG = ?,
			MovementReverse = ?,
			Unbom = ?,
			PostbackMovement = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO AuthorizeMaster(
				Description, 
				Unhold_Unrepair_GT,
				Unhold_Unrepair_Final,
				Loading,
				Adjust_GT,
				Adjust_Final,
				Adjust_FG,
				MovementReverse,
				Unbom,
				PostbackMovement
			)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$description,
				$unhold_unrepair_gt,
				$unhold_unrepair_final,
				$loading,
				$adjust_gt,
				$adjust_final,
				$adjust_fg,
				$movementReverse,
				$unbom,
				$postbackmovement,
				$id,
				$description,
				$unhold_unrepair_gt,
				$unhold_unrepair_final,
				$loading,
				$adjust_gt,
				$adjust_final,
				$adjust_fg,
				$movementReverse,
				$unbom,
				$postbackmovement
			]
		);

		if ($q) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 404;
		}
	}

	public function edit(
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
	)
	{
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		
		$query = Sqlsrv::update(
			$conn,
			"UPDATE AuthorizeMaster
			SET Description = ?,
			Unhold_Unrepair_GT = ?,
			Unhold_Unrepair_Final = ?,
			Adjust_GT = ?,
			Adjust_Final = ?,
			Adjust_FG = ?,
			Loading = ?,
			MovementReverse = ?,
			Unbom = ?,
			PostbackMovement = ?
			WHERE ID = ?",
			[
				$Description,
				$Unhold_Unrepair_GT,
				$Unhold_Unrepair_Final,
				$Adjust_GT,
				$Adjust_Final,
				$Adjust_FG,
				$Loading,
				$MovementReverse,
				$Unbom,
				$PostbackMovement,
				$id
			]
		);

		if ($query) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}
}