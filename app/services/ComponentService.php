<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class ComponentService
{
	public function pastcodecheck($item)
	{
		$conn = Database::connect();
		$section = $_SESSION["user_componentsection"];

		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ItemComponent 
			WHERE ItemID=? AND SectionID IN ($section)",[$item]
		);

		return $query;
	}

	public function pastcode($item)
	{
		$conn = Database::connect();
		$section = $_SESSION["user_componentsection"];

		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM ItemComponent 
			WHERE ItemID=? AND SectionID IN ($section)",[$item]
		);

		return $query;
	}

	public function defect($defectid)
	{
		$conn = Database::connect();
		$section = $_SESSION["user_componentsection"];

		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentReference 
			WHERE DefectID=? AND SectionID IN ($section)",[$defectid]
		);

		return $query;
	}

	public function loaddefect($item)
	{
		$conn = Database::connect();
		
		$dataitem = Sqlsrv::queryArray(
			$conn,
			"SELECT *
			FROM  ItemComponent
			WHERE ItemID =?",[$item]
		);

		$section = $dataitem[0]['SectionID'];
		
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT R.GroupID,G.GroupName,G.GroupDescription,G.GroupDescriptionDetail,R.DefectID
			FROM  ComponentSection S
			LEFT JOIN ComponentReference R ON S.SectionID = R.SectionID
			LEFT JOIN ComponentGroupDefect G ON R.GroupID = G.GroupID
			WHERE S.SectionID = ? ",[$section]
		);

		return $query;
	}

	public function loadunit()
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM ComponentUnit"
		);

		return $query;
	}

	public function loadsection()
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM ComponentSection"
		);

		return $query;
	}

	public function loaddate($id)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM ComponentTable
			WHERE ID=?",[$id]
		);

		return $query;
	}

	public function load($date_component,$date_component_plus,$shift)
	{
		$conn = Database::connect();
		$section = $_SESSION["user_componentsection"];

		// if ($shift==1) {
			$query = Sqlsrv::queryJson(
				$conn,
				"SELECT T.*,
						I.PastCodeID,
						R.GroupID,
						G.GroupName,
						G.GroupDescription,
						U.Name,
						S.SectionName,
						UM.Description [UnitPD],
						UMM.Description [UnitScrap]
				FROM ComponentTable T
				LEFT JOIN ItemComponent I ON T.ItemID=I.ItemID 
				LEFT JOIN ComponentReference R ON T.DefectID=R.DefectID AND R.SectionID = I.SectionID
				LEFT JOIN ComponentGroupDefect G ON R.GroupID = G.GroupID
				LEFT JOIN UserMaster U ON T.CreateBy = U.ID
				LEFT JOIN ComponentSection S ON I.SectionID = S.SectionID
				LEFT JOIN UnitMaster UM ON S.UnitPD = UM.ID
				LEFT JOIN UnitMaster UMM ON S.UnitScrap = UMM.ID
				WHERE T.SCHDate = ? AND T.Shift = ?
				-- AND CONVERT(time,T.CreateDate) BETWEEN '08:01' AND '20:00'
				AND I.SectionID IN ($section)
				AND T.SCH != 0
				ORDER BY T.CreateDate DESC",
				[$date_component,$shift]
			);
			return $query;
		// }else{
		// 	$datetime = $date_component.' 20:01';
		// 	$datetimeplus = $date_component_plus.' 08:00';
		// 	$query = Sqlsrv::queryJson(
		// 		$conn,
		// 		"SELECT T.*,
		// 				I.PastCodeID,
		// 				R.GroupID,
		// 				G.GroupName,
		// 				G.GroupDescription,
		// 				U.Name,
		// 				S.SectionName,
		// 				UM.Description [UnitPD],
		// 				UMM.Description [UnitScrap]
		// 		FROM ComponentTable T
		// 		LEFT JOIN ItemComponent I ON T.ItemID=I.ItemID 
		// 		LEFT JOIN ComponentReference R ON T.DefectID=R.DefectID AND R.SectionID = I.SectionID
		// 		LEFT JOIN ComponentGroupDefect G ON R.GroupID = G.GroupID
		// 		LEFT JOIN UserMaster U ON T.CreateBy = U.ID
		// 		LEFT JOIN ComponentSection S ON I.SectionID = S.SectionID
		// 		LEFT JOIN UnitMaster UM ON S.UnitPD = UM.ID
		// 		LEFT JOIN UnitMaster UMM ON S.UnitScrap = UMM.ID
		// 		WHERE T.CreateDate BETWEEN ? AND ? 
		// 		AND I.SectionID IN ($section)
		// 		ORDER BY T.CreateDate DESC",
		// 		[$datetime,$datetimeplus]
		// 	);
		// 	return $query;
		// }
		
	}

	public function insert_barcode($item,$qty,$batch,$df,$shift)
	{
		$conn = Database::connect();
		$data_unit 	= 	self::getUnitData(); 
		$data_opr 	= 	self::getOprData($item); 
		$unitPD 	=	$data_unit[0]["UnitPD"];
		$unitScarp 	=	$data_unit[0]["UnitScarp"];
		$oprID 		=	$data_opr[0]["OperationID"];

		$query = sqlsrv_query(
			$conn,
			"UPDATE ComponentTable
			SET SCH += ?
			WHERE ItemID=? AND SCHDate =? AND Shift =?
			IF @@ROWCOUNT = 0
			INSERT ComponentTable (ItemID,SCH,Batch,CreateBy,CreateDate,Company,UnitGoodQty,UnitErrorQty,OperationID,Shift,SCHDate,Status)
			VALUES (?,?,?,?,GETDATE(),?,?,?,?,?,?,?)",
			[
				$qty,
				$item,
				$df,
				$shift,
				$item,
				$qty,
				$batch,
				$_SESSION["user_login"],
				$_SESSION["user_company"],
				$unitPD,
				$unitScarp,
				$oprID,
				$shift,
				$df,
				1
			]
		);

		if ($query) {
			return true;
		}else{
			return false;
		}

	}

	public function update_error($id)
	{
		$conn = Database::connect();
		$data 	= self::check_datacomponent($id);
		$data_good   = $data[0]['GoodQty'];
		$data_error  = $data[0]['ErrorQty'];
		$data_defect = $data[0]['DefectID'];

		if ($data_defect == "") {

			$query = sqlsrv_query(
				$conn,
				"UPDATE ComponentTable SET ErrorQty=?, UpdateDate=GETDATE(), UpdateBy=?
				WHERE ID=?",
				[
					0,
					$_SESSION["user_login"],
					$id
				]
			);
			if ($query) {
				echo json_encode(["status" => 200]);
			}else{
				echo json_encode(["status" => 404]);
			}

		}else{
			echo json_encode(["status" => 201]);
		}
	}
	public function update_barcode($good,$error,$item,$id)
	{
		$conn = Database::connect();
		
		$data 	= self::check_datacomponent($id);
		$data_good   = $data[0]['GoodQty'];
		$data_error  = $data[0]['ErrorQty'];
		$data_defect = $data[0]['DefectID'];
		$data_schdate= $data[0]['SCHDate'];
		$data_shift  = $data[0]['Shift'];

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable 
			WHERE SCHDate=? AND Shift =? AND Status =? AND ItemID =?",[$data_schdate,$data_shift,3,$item]
		);
		if ($queryTrans) {
			echo json_encode(["status" => 202, "message" => "status completed"]);
		}else{

			if ($good != $data_good) {
				
				$query = sqlsrv_query(
					$conn,
					"UPDATE ComponentTable SET GoodQty=?, ErrorQty=?, UpdateDate=GETDATE(), UpdateBy=?
					WHERE ID=?",
					[
						$good,
						$error,
						$_SESSION["user_login"],
						$id
					]
				);
				if ($query) {
					echo json_encode(["status" => 201]);
				}else{
					echo json_encode(["status" => 404]);
				}

			}else{
				if ($data_error == null && $error == 0) {
					echo json_encode(["status" => 201]);
					exit();
				}
				if ($error == 0) {
					
					$query = sqlsrv_query(
						$conn,
						"UPDATE ComponentTable SET GoodQty=?, ErrorQty=?, UpdateDate=GETDATE(), UpdateBy=?
						WHERE ID=?",
						[
							$good,
							$error,
							$_SESSION["user_login"],
							$id
						]
					);
					if ($query) {
						$update_defect = self::update_defect(null,$id);
					}else{
						echo json_encode(["status" => 404]);
					}

				}

				if ($error != $data_error) {
					
					$query = sqlsrv_query(
						$conn,
						"UPDATE ComponentTable SET GoodQty=?, ErrorQty=?, UpdateDate=GETDATE(), UpdateBy=?
						WHERE ID=?",
						[
							$good,
							$error,
							$_SESSION["user_login"],
							$id
						]
					);
					if ($query) {
						echo json_encode(["status" => 200]);
					}else{
						echo json_encode(["status" => 404]);
					}

				}else{
					echo json_encode(["status" => 404]);
				}

			}

		}
		

	}

	public function check_datacomponent($id)
	{
		$conn = Database::connect();
		
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM ComponentTable 
			WHERE ID=?",[$id]
		);

		// $error_qty		=	$query[0]["ErrorQty"];
		return $query;
	}

	public function update_defect($defectid,$id)
	{
		$conn = Database::connect();
		$data 	= self::check_datacomponent($id);
		$data_schdate= $data[0]['SCHDate'];
		$data_shift  = $data[0]['Shift'];

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable C 
			WHERE C.ID=? AND C.Status =?",[$id,3]
		);
		if ($queryTrans) {
			return false;
		}else{

			$query = sqlsrv_query(
				$conn,
				"UPDATE ComponentTable SET DefectID=?
				WHERE ID=?",
				[
					$defectid,
					$id
				]
			);

			if ($query) {
				return true;
			}else{
				return false;
			}

		}

	}

	public function update_time($st,$et,$id)
	{
		$conn = Database::connect();

		$query = sqlsrv_query(
			$conn,
			"UPDATE ComponentTable SET StartTime=?, EndTime=?
			WHERE ID=?",
			[
				$st,
				$et,
				$id
			]
		);

		if ($query) {
			return true;
		}else{
			return false;
		}

	}

	public function getUnitData()
	{
		$depunit = $_SESSION["user_component"];
		if ($depunit === null) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
				$conn,
				"SELECT U.*,MG.Description[UnitGood],MS.Description[UnitScrap]
					FROM ComponentUnit U
					LEFT JOIN UnitMaster MG ON U.UnitPD=MG.ID
					LEFT JOIN UnitMaster MS ON U.UnitScarp=MS.ID
				WHERE U.ID = ?",
				[$depunit]
			);
		return $query;
	}

	public function getSectionData()
	{
		$section = $_SESSION["user_componentsection"];
		if ($section === null) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT * 
				FROM ComponentSection
				WHERE SectionID IN ($section)"
			);
		return $query;
	}

	public function getOprData($item)
	{
		if ($item === null) {
			return false;
		}

		$conn = Database::connect();

		$sectionitem = Sqlsrv::queryArray(
				$conn,
				"SELECT *
				FROM ItemComponent
				WHERE ItemID = ?",
				[$item]
			);

		$section = $sectionitem[0]["SectionID"];

		$query = Sqlsrv::queryArray(
				$conn,
				"SELECT *
				FROM ComponentSection
				WHERE SectionID = ?",
				[$section]
			);
		return $query;
	}

	public function Get_report($df,$report_type,$shift)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT SUM(T.SCH)[SCH],
						SUM(T.GoodQty)[GoodQty],
						SUM(T.ErrorQty)[ErrorQty],
						T.ItemID,
						I.PastCodeID,
						G.GroupName,
						CS.SectionName
				FROM ComponentTable T
				LEFT JOIN ItemComponent I ON T.ItemID = I.ItemID
				LEFT JOIN UserMaster U ON T.CreateBy = U.ID
				LEFT JOIN ComponentReference S ON T.DefectID = S.DefectID AND S.SectionID = I.SectionID
				LEFT JOIN ComponentGroupDefect G ON S.GroupID = G.GroupID
				LEFT JOIN ComponentSection CS ON I.SectionID = CS.SectionID
				WHERE T.SCHDate = ? AND T.Shift = ?
				AND I.SectionID=?
				AND T.SCH != 0
				GROUP BY T.ItemID ,G.GroupName,I.PastCodeID,CS.SectionName",
				// [$df,$df_,$_SESSION["user_componentsection"]]
				[$df,$shift,$report_type]
			);
		return $query;
	}

	public function Get_reportCMC($df,$report_type)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT SUM(T.SCH)[SCH],
						SUM(T.GoodQty)[GoodQty],
						SUM(T.ErrorQty)[ErrorQty],
						T.ItemID,
						I.PastCodeID,
						G.GroupName,
						CS.SectionName,
						I.Location
				FROM ComponentTable T
				LEFT JOIN ItemComponent I ON T.ItemID = I.ItemID
				LEFT JOIN UserMaster U ON T.CreateBy = U.ID
				LEFT JOIN ComponentReference S ON T.DefectID = S.DefectID AND S.SectionID = I.SectionID
				LEFT JOIN ComponentGroupDefect G ON S.GroupID = G.GroupID
				LEFT JOIN ComponentSection CS ON I.SectionID = CS.SectionID
				WHERE T.SCHDate = ? AND T.Shift = ?
				AND I.SectionID=?
				AND I.Location='CMC'
				AND T.SCH != 0
				GROUP BY T.ItemID ,G.GroupName,I.PastCodeID,CS.SectionName,I.Location",
				// [$df,$df_,$_SESSION["user_componentsection"]]
				[$df,$shift,$report_type]
			);
		return $query;
	}

	public function Get_reportBBR($df,$report_type,$shift)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT SUM(T.SCH)[SCH],
						SUM(T.GoodQty)[GoodQty],
						SUM(T.ErrorQty)[ErrorQty],
						T.ItemID,
						I.PastCodeID,
						G.GroupName,
						CS.SectionName,
						I.Location
				FROM ComponentTable T
				LEFT JOIN ItemComponent I ON T.ItemID = I.ItemID
				LEFT JOIN UserMaster U ON T.CreateBy = U.ID
				LEFT JOIN ComponentReference S ON T.DefectID = S.DefectID AND S.SectionID = I.SectionID
				LEFT JOIN ComponentGroupDefect G ON S.GroupID = G.GroupID
				LEFT JOIN ComponentSection CS ON I.SectionID = CS.SectionID
				WHERE T.SCHDate = ? AND T.Shift = ?
				AND I.SectionID=?
				AND I.Location='BBR'
				AND T.SCH != 0
				GROUP BY T.ItemID ,G.GroupName,I.PastCodeID,CS.SectionName,I.Location",
				// [$df,$df_,$_SESSION["user_componentsection"]]
				[$df,$shift,$report_type]
			);
		return $query;
	}

	public function getSCHDate($id)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
				$conn,
				"SELECT SCHDate
					FROM ComponentTable
				WHERE ID = ?",
				[$id]
			);
		return $query;
	}

	public function update_shift($shift_change,$id)
	{
		$conn = Database::connect();

		$data_datesch 	= 	self::getSCHDate($id); 
		$schdate 		=	date('Y-m-d', strtotime($data_datesch[0]["SCHDate"]));
		$sch 			= 	str_replace('-', '/', $schdate);
		$tomorrow 		= 	date('Y-m-d',strtotime($sch . "+1 days"));

		$data 		 = self::check_datacomponent($id);
		$data_schdate= $data[0]['SCHDate'];
		$data_shift  = $data[0]['Shift'];

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable C 
			WHERE C.ID=? AND C.Status =?",[$id,3]
		);
		if ($queryTrans) {
			return false;
		}else{

			if ($shift_change==1) {

				$query = sqlsrv_query(
					$conn,
					"UPDATE ComponentTable SET Shift=?,SCHDate=?
					WHERE ID=?",
					[
						$shift_change,
						$tomorrow,
						$id
					]
				);

				if ($query) {
					return true;
				}else{
					return false;
				}

			}else{

				$query = sqlsrv_query(
					$conn,
					"UPDATE ComponentTable SET Shift=?
					WHERE ID=?",
					[
						$shift_change,
						$id
					]
				);

				if ($query) {
					return true;
				}else{
					return false;
				}

			}	

		}

	}

	public function Get_origin_report($df,$section,$shift)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT SUM(T.SCH)[SCH],
						SUM(T.GoodQty)[GoodQty],
						SUM(T.ErrorQty)[ErrorQty],
						T.ItemID,
						I.PastCodeID,
						G.GroupName,
						CS.SectionName,
						G.GroupDescription
				FROM ComponentTable T
				LEFT JOIN ItemComponent I ON T.ItemID = I.ItemID
				LEFT JOIN UserMaster U ON T.CreateBy = U.ID
				LEFT JOIN ComponentReference S ON T.DefectID = S.DefectID AND S.SectionID = I.SectionID
				LEFT JOIN ComponentGroupDefect G ON S.GroupID = G.GroupID
				LEFT JOIN ComponentSection CS ON I.SectionID = CS.SectionID
				WHERE T.SCHDate = ? AND T.Shift = ?
				AND I.SectionID=?
				AND T.SCH != 0
				GROUP BY T.ItemID ,G.GroupName,I.PastCodeID,CS.SectionName,G.GroupDescription
				ORDER BY I.PastCodeID ASC",
				[$df,$shift,$section]
			);
		return $query;
	}

	public function loadpastcode($section)
	{
		$conn = Database::connect();
		// $section = $_SESSION["user_componentsection"];
		if ($section!='') {
			$query = Sqlsrv::queryJson(
				$conn,
				"SELECT I.*,S.SectionName FROM ItemComponent I
	  			LEFT JOIN ComponentSection S ON I.SectionID=S.SectionID
				WHERE I.SectionID IN ($section)"
			);
		}else{
			$query = Sqlsrv::queryJson(
				$conn,
				"SELECT I.*,S.SectionName FROM ItemComponent I
	  			LEFT JOIN ComponentSection S ON I.SectionID=S.SectionID"
			);
		}
		
		return $query;
	}

	public function loaditem($item)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT I.*,S.SectionName FROM ItemComponent I
  			LEFT JOIN ComponentSection S ON I.SectionID=S.SectionID
  			WHERE I.ID IN ($item)
  			ORDER BY I.PastCodeID ASC"
		);		

		return $query;
	}

	public function create_item($pastcode,$item,$item_name,$section_id)
	{
		$conn = Database::connect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 * FROM ItemComponent 
			WHERE SectionID=?",[$section_id]
		);

		$wh		=	$query[0]["Warehouse"];
		$lc		=	$query[0]["Location"];

		$query = sqlsrv_query(
			$conn,
			"INSERT INTO ItemComponent 
			(PastCodeID,ItemID,ItemName,SectionID,Warehouse,Location)
			VALUES (?,?,?,?,?,?)",
			[
				$pastcode,
				$item,
				$item_name,
				$section_id,
				$wh,
				$lc
			]
		);

		if ($query) {
			return true;
		}else{
			return false;
		}

	}

	public function update_item($pastcode,$item,$item_name,$section_id,$id)
	{
		$conn = Database::connect();

		$query = sqlsrv_query(
			$conn,
			"UPDATE ItemComponent SET PastCodeID=?,ItemID=?,ItemName=?,SectionID=?
			WHERE ID=?",
			[
				$pastcode,
				$item,
				$item_name,
				$section_id,
				$id
			]
		);

		if ($query) {
			return true;
		}else{
			return false;
		}

	}

	public function update_status($date_component,$shift,$section_str)
	{
		$conn = Database::connect();
		$getdate = date("Y-m-d");
		
		$query = Sqlsrv::hasRows(
			$conn,
			"SELECT C.*,I.SectionID FROM ComponentTable C
			LEFT JOIN ItemComponent I ON C.ItemID=I.ItemID
			WHERE SCHDate=? AND Shift=? AND I.SectionID=?",[$date_component,$shift,$_SESSION['user_componentcomplete']]
		);

		if ($query) {
			
			$arr_section = explode(',', $section_str);
			
			foreach ($arr_section as $k => $v) {
        		$insert = sqlsrv_query(
					$conn,
					"INSERT INTO ComponentConfirm 
					(SCHDate,SCHShift,CompleteDate,SectionID)
					VALUES (?,?,?,?)",
					[
						$date_component,
						$shift,
						$getdate,
						$v
					]
				);
        	}

			// $insert = sqlsrv_query(
			// 	$conn,
			// 	"INSERT INTO ComponentConfirm 
			// 	(SCHDate,SCHShift,CompleteDate,SectionID)
			// 	VALUES (?,?,?,?)",
			// 	[
			// 		$date_component,
			// 		$shift,
			// 		$getdate,
			// 		$_SESSION['user_componentcomplete']
			// 	]
			// );

			// if ($insert) {

				$update = sqlsrv_query(
					$conn,
					"UPDATE C
					SET
					  C.Status = ?
					FROM
					  ComponentTable C
					INNER JOIN
					  ItemComponent I
					ON
					  C.ItemID=I.ItemID
					WHERE
					  C.SCHDate=? AND C.Shift=? AND I.SectionID IN ($section_str)",
					[
						3,
						$date_component,
						$shift
					]
				);

				if ($update) {
					echo json_encode(["status" => 200, "message" =>"Completed"]);
				}else{
					echo json_encode(["status" => 400, "message" =>"Completed Failed!"]);
				}

			// }else{
			// 	echo json_encode(["status" => 400]);
			// }

		}else{
			echo json_encode(["status" => 404, "message" =>"Completed not found!"]);
		}
		
	}

	public function check_status($date_component,$shift)
	{
		$conn = Database::connect();

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable 
			WHERE SCHDate=? AND Shift =?",[$date_component,$shift]
		);

		if ($queryTrans) {
			$queryConfirm = Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM ComponentConfirm 
				WHERE SCHDate=? AND SCHShift =? AND SectionID =?",[$date_component,$shift,$_SESSION['user_componentcomplete']]
			);
			return $queryConfirm;
		}else{
			return true;
		}

		
	}

	public function check_status_number($date_component,$shift)
	{
		$conn = Database::connect();
		$data_opr 	= 	self::getOprData($item); 
		$oprID 		=	$data_opr[0]["OperationID"];

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable 
			WHERE SCHDate =? AND Shift =? AND Status =? AND OperationID =?",[$date_component,$shift,3,$oprID]
		);
		return $queryTrans;
	}

	public function checkqty($date_component,$shift,$item,$qty)
	{
		$conn = Database::connect();

		$queryTrans = Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM ComponentTable 
			WHERE SCHDate =? AND Shift =? AND ItemID =?",[$date_component,$shift,$item]
		);

		if ($queryTrans) {
			
			$queryTrans = Sqlsrv::queryArray(
				$conn,
				"SELECT * FROM ComponentTable 
				WHERE SCHDate =? AND Shift =? AND ItemID =?",[$date_component,$shift,$item]
			);
			$qtysch = $queryTrans[0]['SCH'];

			if (($qty+$qtysch)<0) {
				return false;
			}else{
				return true;
			}

		}else{
			return true;
		}
		

	}

}