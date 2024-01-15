<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Models\Location;
use App\Models\InventTable;
use App\Models\InventTrans;
use App\Models\Onhand;

class GreentireService
{

	private $db = null;
	private $utils = null;
	private $location = null;
	private $inventTable = null;
	private $inventTrans = null;
	private $onhand = null;

	public function __construct()
	{
		$this->db = Database::connect();
		$this->utils = new Utils();
		$this->location = new Location();
		$this->inventTable = new InventTable();
		$this->inventTrans = new InventTrans();
		$this->onhand = new Onhand();
	}

	public function isExist($greentire_code)
	{
		$conn = $this->db;
		return Sqlsrv::hasRows(
			$conn,
			"SELECT GCM.ID
			FROM GreentireCodeMaster GCM
			WHERE GCM.ID = ?",
			[$greentire_code]
		);
	}

	public function dateDifference($date_1, $date_2, $differenceFormat = '%i')
	{
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format('%i.%s');
	}

	public function receive($barcode, $building_no, $gt_code, $weight)
	{
		$user_login = $_SESSION["user_login"];
		$user_company = $_SESSION["user_company"];
		$user_warehouse = $_SESSION["user_warehouse"];
		$user_location = $_SESSION["user_location"];
		$check = 0;

		// $weightPoint = substr($weight, -2);
		// $weight = str_replace($weightPoint, ".".$weightPoint, $weight);

		$date = date("Y-m-d H:i:s");
		// $ddate = new \DateTime();
		// $week = date("Y") . "-" . $ddate->format("W");
		// $week = $this->utils->getWeek($date);
		$week = Utils::getWeekNormal($date, true, $check);
		// return $week;
		// exit();

		// if ($week === '2017-30') {
		// 	$week = '2017-31';
		// }

		$barcode_decode = Security::_decode($barcode);

		$building_no = strtoupper($building_no);
		$gt_code = strtoupper($gt_code);



		$conn =  $this->db; //Database::connect();

		// if VMI01 or VMI02 just ignore. #golf edit 20/01/2020
		if ($this->isSkippingDelay() === false && $building_no !== "VMI01" && $building_no !== "VMI02") {
			$datetime_lockbuild = sqlsrv_query(
				$conn,
				"SELECT LockBuild FROM BuildingMaster
				WHERE ID = ?",
				[$building_no]
			);

			$datetime_lockbuild = Sqlsrv::queryArray(
				$conn,
				"SELECT LockBuild FROM BuildingMaster
				WHERE ID = ?",
				[$building_no]
			);

			$date_diff = $this->dateDifference(date('Y-m-d H:i:s'), $datetime_lockbuild[0]['LockBuild']);

			if ((float) $date_diff <= 1.59) {
				return "ต้องรอ 2 นาทีเพื่อทำรายการต่อไป";
			}
		}

		if (sqlsrv_begin_transaction($conn) === false) {
			return "ไม่สามารถเชื่อต่อฐานข้อมูลได้";
		}

		$insert_lockbuild = sqlsrv_query(
			$conn,
			"UPDATE BuildingMaster
			SET LockBuild = ?
			WHERE ID = ?",
			[date('Y-m-d H:i:s'), $building_no]
		);

		if (!$insert_lockbuild) {
			sqlsrv_rollback($conn);
			return "Update lock build failed!";
		}

		$_location = $this->location; //new Location;
		$_location->ID = $_SESSION['user_location'];
		// get user location
		$get_location = $_location->getUserLocation();

		$_inventtable = $this->inventTable; //new InventTable;
		$_inventtable->Barcode = $barcode_decode;
		$_inventtable->DateBuild = $date;
		$_inventtable->Batch = $week;
		$_inventtable->BuildingNo = $building_no;
		$_inventtable->GT_Code = $gt_code;
		$_inventtable->QTY = 1;
		$_inventtable->Unit = 1;
		$_inventtable->DisposalID = $get_location[0]["DisposalID"];
		$_inventtable->WarehouseID = $get_location[0]["WarehouseID"];
		$_inventtable->LocationID = $get_location[0]["ReceiveLocation"];
		$_inventtable->Status = 1;
		$_inventtable->Company = $_SESSION['user_company'];
		$_inventtable->UpdateBy = $_SESSION['user_login'];
		$_inventtable->UpdateDate = $date;
		$_inventtable->CreateBy = $_SESSION['user_login'];
		$_inventtable->CreateDate = $date;
		$_inventtable->Weight = $weight;

		$create_inventtable = sqlsrv_query(
			$conn,
			"INSERT INTO InventTable(
          Barcode, DateBuild, Batch, BuildingNo, GT_Code,
          QTY, Unit, DisposalID, WarehouseID, LocationID,
          Status, Company, UpdateBy, UpdateDate, CreateBy,
          CreateDate, Weight
      )VALUES(
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?
      )",
			[
				$_inventtable->Barcode,
				$_inventtable->DateBuild,
				$_inventtable->Batch,
				$_inventtable->BuildingNo,
				$_inventtable->GT_Code,
				$_inventtable->QTY,
				$_inventtable->Unit,
				$_inventtable->DisposalID,
				$_inventtable->WarehouseID,
				$_inventtable->LocationID,
				$_inventtable->Status,
				$_inventtable->Company,
				$_inventtable->UpdateBy,
				$_inventtable->UpdateDate,
				$_inventtable->CreateBy,
				$_inventtable->CreateDate,
				$_inventtable->Weight
			]
		);

		if (!$create_inventtable) {
			sqlsrv_rollback($conn);
			return "insert invent table error.";
		}

		$_inventtrans = $this->inventTrans; //new InventTrans;
		$_inventtrans->TransID = Utils::genTransId($barcode_decode) . 1;
		$_inventtrans->Barcode = $barcode_decode;
		$_inventtrans->CodeID = $gt_code;
		$_inventtrans->Batch = $week;
		$_inventtrans->DisposalID = $get_location[0]["DisposalID"];
		$_inventtrans->DefectID = null;
		$_inventtrans->WarehouseID = $get_location[0]["WarehouseID"];
		$_inventtrans->LocationID = $get_location[0]["ReceiveLocation"];
		$_inventtrans->QTY = 1;
		$_inventtrans->UnitID = 1;
		$_inventtrans->DocumentTypeID = 1;
		$_inventtrans->Company = $_SESSION['user_company'];
		$_inventtrans->CreateBy = $_SESSION['user_login'];
		$_inventtrans->CreateDate = $date;
		$_inventtrans->Shift = $_SESSION["Shift"];

		$create_inventtrans = sqlsrv_query(
			$conn,
			"INSERT INTO InventTrans(
            TransID,
            Barcode,
            CodeID,
            Batch,
            DisposalID,
            DefectID,
            WarehouseID,
            LocationID,
            QTY,
            UnitID,
            DocumentTypeID,
            Company,
            CreateBy,
            CreateDate,
            Shift,
            InventJournalID,
            AuthorizeBy,
            ScrapSide,
            RefDocId
        ) VALUES(
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )",
			[
				$_inventtrans->TransID,
				$_inventtrans->Barcode,
				$_inventtrans->CodeID,
				$_inventtrans->Batch,
				$_inventtrans->DisposalID,
				$_inventtrans->DefectID,
				$_inventtrans->WarehouseID,
				$_inventtrans->LocationID,
				$_inventtrans->QTY,
				$_inventtrans->UnitID,
				$_inventtrans->DocumentTypeID,
				$_inventtrans->Company,
				$_inventtrans->CreateBy,
				$_inventtrans->CreateDate,
				$_inventtrans->Shift,
				$_inventtrans->InventJournalID,
				$_inventtrans->AuthorizeBy,
				$_inventtrans->ScrapSide,
				$_inventtrans->RefDocId
			]
		);

		if (!$create_inventtrans) {
			sqlsrv_rollback($conn);
			return "insert invent trans error.";
		}

		$_onhand = $this->onhand; //new Onhand;
		$_onhand->QTY = 1;
		$_onhand->WarehouseID = $get_location[0]["WarehouseID"];
		$_onhand->LocationID = $get_location[0]["ReceiveLocation"];
		$_onhand->Company = $_SESSION['user_company'];
		$_onhand->CodeID = $gt_code;
		$_onhand->Batch = $week;

		if ($_onhand->isItemExist()) {

			// $update_onhand = sqlsrv_query(
			// 	$conn,
			// 	"UPDATE Onhand
			//      SET QTY += ?
			//      WHERE CodeID = ?
			//      AND WarehouseID = ?
			//      AND LocationID = ?
			//      AND Batch = ?
			//      AND Company = ?",
			// 	[
			// 		$_onhand->QTY,
			// 		$_onhand->CodeID,
			// 		$_onhand->WarehouseID,
			// 		$_onhand->LocationID,
			// 		$_onhand->Batch,
			// 		$_onhand->Company
			// 	]
			// );

			// if (!$update_onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "update onhand error.";
			// }
		} else {

			// $create_onhand = sqlsrv_query(
			// 	$conn,
			// 	"INSERT INTO Onhand(
			//          CodeID,
			//          WarehouseID,
			//          LocationID,
			//          Batch,
			//          QTY,
			//          Company
			//      ) VALUES(?, ?, ?, ?, ?, ?)",
			// 	[
			// 		$_onhand->CodeID,
			// 		$_onhand->WarehouseID,
			// 		$_onhand->LocationID,
			// 		$_onhand->Batch,
			// 		$_onhand->QTY,
			// 		$_onhand->Company
			// 	]
			// );

			// if (!$create_onhand) {
			// 	sqlsrv_rollback($conn);
			// 	return "create onhand error.";
			// }
		}

		sqlsrv_commit($conn);
		return 200;
	}

	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT G.*, ITM.NameTH [ItemName] FROM GreentireCodeMaster G
				left join ItemMaster ITM ON G.ItemNumber = ITM.ID
				ORDER BY G.ID ASC"
		);

		return $query;
	}

	public function create($id, $description)
	{
		if ($this->isExist($id) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO GreentireCodeMaster(ID, Name, Company) VALUES (?, ?, ?)",
			[$id, $description, $_SESSION["user_company"]]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $description, $_id)
	{

		$conn = $this->db;
		$query = Sqlsrv::update(
			$conn,
			"UPDATE GreentireCodeMaster
				SET	Name = ?
		        WHERE ID = ?",
			[
				$description,
				$id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id)
	{
		$conn = $this->db;
		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM GreentireCodeMaster GCM
			LEFT JOIN InventTable IT ON IT.GT_Code = GCM.ID
			WHERE GCM.ID = ?
			AND IT.GT_Code IS NULL",
			[$id]
		);
		return $q;
	}

	public function isSkippingDelay()
	{
		$conn = $this->db;
		return sqlsrv_has_rows(sqlsrv_query(
			$conn,
			"SELECT ID FROM UserMaster
			WHERE SkipingDelay = 1
			AND ID = ?",
			[
				$_SESSION['user_login']
			]
		));
	}

	public function updateLockBuild($build_id)
	{
		$conn = $this->db;

		$q = sqlsrv_query(
			$conn,
			"UPDATE BuildingMaster
			SET LockBuild = ?
			WHERE ID = ?",
			[
				date('Y-m-d H:i:s'),
				$build_id
			]
		);

		if ($q) {
			return true;
		} else {
			return false;
		}
	}

	public function mapItem($_id, $item)
	{
		$conn = $this->db;

		$query = sqlsrv_query(
			$conn,
			"UPDATE GreentireCodeMaster
			SET ItemNumber = ?
			WHERE ID = ?",
			[
				$item,
				$_id
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function insert_buildtrans($barcode, $machine)
	{

		$conn = $this->db;
		$date = date("Y-m-d H:i:s");

		$insert_buildtrans = Sqlsrv::insert(
			$conn,
			" INSERT INTO BuildTrans (
					Barcode,
					CreateBy,
					CreateDate,
					Machine
				   )

					SELECT
					? as Barcode,
					UserId,
					? as CreateDate,
					Machine

					 FROM RateTrans
					 WHERE LogoutDate IS NULL
					 AND Machine = ?",
			[
				$barcode,
				$date,
				$machine
			]
		);

		if ($insert_buildtrans) {
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

	public function update_totalratetrans($machine)
	{

		$conn = $this->db;
		$date = date("Y-m-d H:i:s");

		$update_total = Sqlsrv::insert(
			$conn,
			" UPDATE RateTrans
				SET Total = Total+1
				WHERE UserId IN (
						SELECT UserId FROM RateTrans
						WHERE LogoutDate IS NULL
						AND Machine = ?
					)
					AND LogoutDate IS NULL",
			[
				$machine
			]
		);

		if ($update_total) {
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
	public function GreentireQcInventTable($barcode)
	{
		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 Barcode FROM InventTable
				WHERE Barcode = ?
				AND GT_InspectionDate IS NOT NULL",
			[$barcode]
		);
	}

	public function isstatushekQC($barcode)
	{
		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 Barcode FROM InventTable
				WHERE Barcode = ?
				AND Status <> 1",
			[$barcode]
		);
	}
	public function GreentireQcCheckWarehouse($barcode)
	{
		$conn = Database::connect();

		return Sqlsrv::hasRows(
			$conn,
			"SELECT TOP 1 Barcode FROM InventTable
				WHERE Barcode = ?
				AND GT_InspectionDate IS NULL
				AND WarehouseID <> 1",
			[$barcode]
		);
	}

	public function GreentireQcSaveInventable($barcode_decode)
	{

		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		$barcode_decode = trim($barcode_decode);
		if (sqlsrv_begin_transaction($conn) === false) {
			return "begin transaction failed";
		}

		if (!isset($_SESSION["user_location"])) {
			return 'session failed';
		}



		$get_location = Sqlsrv::queryArray(
			$conn,
			"SELECT
			L.ID,
			LL.WarehouseID,
			L.ReceiveLocation,
			L.Company,
			L.DisposalID
			FROM Location L
			LEFT JOIN Location LL ON L.ReceiveLocation = LL.ID
			WHERE L.ID = ?
			AND L.InUse = 1",
			[$_SESSION["user_location"]]
		);

		if (!$get_location) {
			sqlsrv_rollback($conn);
			return "Cannot use location";
		}

		$get_barcode_info = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$trans_id = Utils::genTransId($barcode_decode);

		$move_out_trans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 1,
				$barcode_decode,
				$get_barcode_info[0]["GT_Code"],
				$get_barcode_info[0]["Batch"],
				$get_barcode_info[0]["DisposalID"],
				null,
				$get_barcode_info[0]["WarehouseID"],
				$get_barcode_info[0]["LocationID"],
				-1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				2, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);


		if (!$move_out_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move out error.";
		}

		// Update invent table
		$update_inventtble = Sqlsrv::update(
			$conn,
			"UPDATE InventTable
			SET DisposalID = ?,
			GT_InspectionDate = ?,
			UpdateBy = ?,
			UpdateDate = ?,
			CheckBuild = ?
			WHERE Barcode = ?",
			[
				26,
				$date,
				$_SESSION["user_login"],
				$date,
				1,
				$barcode_decode
			]
		);

		if (!$update_inventtble) {
			sqlsrv_rollback($conn);
			return "update inventtble error.";
		}

		$trans_id = Utils::genTransId($barcode_decode);
		$get_barcode_trans = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM InventTable
			WHERE Barcode = ?",
			[$barcode_decode]
		);

		$move_in_trans = Sqlsrv::insert(
			$conn,
			"INSERT INTO InventTrans(
				TransID,
				Barcode,
				CodeID,
				Batch,
				DisposalID,
				DefectID,
				WarehouseID,
				LocationID,
				QTY,
				UnitID,
				DocumentTypeID,
				Company,
				CreateBy,
				CreateDate,
				Shift
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?,
				?, ?, ?, ?, ?
			)",
			[
				$trans_id . 2,
				$barcode_decode,
				$get_barcode_trans[0]["GT_Code"],
				$get_barcode_trans[0]["Batch"],
				$get_barcode_trans[0]["DisposalID"],
				null,
				$get_barcode_trans[0]["WarehouseID"],
				$get_barcode_trans[0]["LocationID"],
				1, // qty
				$get_barcode_info[0]["Unit"], // unit id
				1, // docs type
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date,
				$_SESSION["Shift"]
			]
		);

		if (!$move_in_trans) {
			sqlsrv_rollback($conn);
			return "insert trans move in error.";
		}
		if (
			$update_inventtble &&
			$move_out_trans &&
			$move_in_trans
		) {

			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return "transaction failed";
		}
	}
	public function greentireInspectionReport($time, $product_group, $pressBOI)
	{

		if ($pressBOI == "" || $pressBOI == 1) {
			$whereBOI = "";
		} else {
			$whereBOI =	" AND BMM.BOI ='$pressBOI' ";
		}

		$sql = "SELECT
 		 -- IT.Barcode,
 			I.BuildingNo,
 			I.GT_Code,
 			IM.ID,
 			IM.NameTH,
			IT.Batch,
 			SUM(IT.QTY)[QTY]
 			FROM InventTrans IT
 			LEFT JOIN InventTable I ON I.Barcode = IT.Barcode
 			--LEFT JOIN GreentireCodeMaster GM ON GM.ID = IT.CodeID
			LEFT JOIN BuildingMaster BMM ON BMM.ID = I.BuildingNo
			LEFT JOIN GreentireCodeMaster GM ON GM.ID = IT.CodeID
 			LEFT JOIN ItemMaster IM ON GM.ItemNumber = IM.ID
 			WHERE IT.DisposalID = '26' AND IT.WarehouseID = '1' AND IT.DocumentTypeID = '1'
			AND IM.SubGroup = '$product_group'
			 $whereBOI
			AND ($time)
 			GROUP BY
		--	IT.Barcode,
			I.BuildingNo,
			I.GT_Code,
			IM.ID,
			IM.NameTH,
			IT.Batch
			ORDER BY IT.Batch,I.GT_Code ASC";

		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function checkgreentireall()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			[Type],
			CASE WHEN [Type] = 1 THEN 'Greentire' ELSE 'Cure' END [TypeName],
			Active,
			CASE WHEN Active = 0 THEN 'เปิดใช้งาน' ELSE 'ปิดใช้งาน' END [NameActive],
      		UpdatedBy,
      		UpdatedDate
		 FROM SetupMaster"
		);
	}
	public function updatecheckgreentire($_id, $_active)
	{
		$conn = $this->db;
		$date = date("Y-m-d H:i:s");
		$query = Sqlsrv::update(
			$conn,
			"UPDATE SetupMaster
				SET	Active = ?
					,UpdatedBy = ?
					,UpdatedDate =?
		        WHERE [Type] = ?",
			[
				$_active,
				$_SESSION["user_login"],
				$date,
				$_id
			]
		);

		$log = sqlsrv_query(
			$conn,
			"INSERT INTO SetupLog(
          		[Type]
				,[Active]
				,[CreatedBy]
      			,[CreatedDate]
      )VALUES(
          ?, ?, ?, ?
      )",
			[
				$_id,
				$_active,
				$_SESSION["user_login"],
				$date


			]
		);

		if ($query && $log) {
			return true;
		} else {
			return false;
		}
	}
}
