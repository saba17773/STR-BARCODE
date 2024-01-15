<?php

namespace App\compound;

use App\Services\BarcodeService;
use App\Services\ItemService;
use App\compound\CompoundAPI;
use App\Services\InventService;
use App\Services\FinalService;

class CompoundController
{
//page
  public function coumpounmaster()
  {
    renderView('Page_compound/compoundmaster');
  }

  public function compoundMc()
  {
    renderView('Page_compound/compoundMc');
  }

  public function CompoundMcPallet($McID)
  {
    renderView("Page_compound/McPallet",[
    "McID" => $McID
    ]);
  }

  public function CompoundPress()
  {
    renderView('Page_compound/CompoundPress');
  }

  public function CompoundNoPress()
  {
    renderView('Page_compound/CompountNopress');
  }

  public function allMovementType()
  {
	   echo (new CompoundAPI)->allMovementType();
  }

  public function remillandMix($datase)
  {
	   echo (new CompoundAPI)->remillandMix($datase);
  }

  public function Mc()
  {
	   echo (new CompoundAPI)->Mc();
  }
  public function Compound_Code($MC)
  {
    echo (new CompoundAPI)->Compound_Code($MC);
  }

  public function allMovementIssue()
  {
	   echo (new CompoundAPI)->allMovementIssue();
  }
  public function getLatestJournalTransByJournalId($journalId)
  {
	   echo (new CompoundAPI)->getLatestJournalTransByJournalId($journalId);
  }

  public function saveJournalTable()
  {
	   $employee_code = filter_input(INPUT_POST, "employee_code");
	   $division = filter_input(INPUT_POST, "division_value");
	   $user = filter_input(INPUT_POST, "user");
	   $pass = filter_input(INPUT_POST, "pass");
     $result = (new CompoundAPI)->saveJournalTable($employee_code, $division,$user,$pass);
     if ($result["status"] === 200) {
			    echo json_encode(["status" => 200, "journal" => $result["journal"],"test"=>$result["test"]]);
		 } else {
			    echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
  }

  public function save()
  {
	   $idid = filter_input(INPUT_POST, "idid");
	   $id = filter_input(INPUT_POST, "id");
	   $Compound_Code = filter_input(INPUT_POST, "description");
     $Mixcode = filter_input(INPUT_POST, "Mix");
	   $Remillcode = filter_input(INPUT_POST, "Remill");
	   if($Mixcode =="")
	   {
		     $Mix = $Remillcode;
	   }
     if($Remillcode =="")
	   {
		     $Mix = $Mixcode;
	   }
     $Weight = filter_input(INPUT_POST, "Weight");
     $result = (new CompoundAPI)->save($id, $Compound_Code,$Mix,$Weight,$idid);
     if ($result['status'] === 200) {
			    echo json_encode(["status" => 200, "message" => $result['message']]);
	   }else {
			    echo json_encode(["status" => 404, "message" => $result['message']]);
	       }
	 }

   public function savePallet()
   {
	    $Mc = filter_input(INPUT_POST, "aa");
	    $Compound_Code = filter_input(INPUT_POST, "Compound_Code");
      $createdate = filter_input(INPUT_POST, "data_date");
	    $Pallet_ID = filter_input(INPUT_POST, "PalleID");
	    $statuscheck = filter_input(INPUT_POST, "statuscheck");
      $result = (new CompoundAPI)->savePallet($Mc, $Compound_Code,$createdate,$Pallet_ID,$Weight,$statuscheck);
      if ($result["status"] == 200) {
        exit(json_encode([
			       "status" => 200,
			        "message" => $result["message"],
			        "totalPallet" => $result["totalPallet"]
            ]));
		   } else {
		       //	exit(json_encode(["status" => 404, "message" => $cure]));
		   exit(json_encode([
			      "status" => 404,
			      "message" => $result["message"],
			      "totalPallet" => $result["totalPallet"]
          ]));
		      }
	 }

   public function deleteCompound($data)
   {
     $result = (new CompoundAPI)->deleteCompound($data);
     if ($result === 200) {
		     echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		 }else {
			   echo json_encode(["status" => 404, "message" => $result['message']]);
		}
  }
  public function tb($McID)
  {
    $result = (new CompoundAPI)->tb($McID);
    if ($result["status"] == 200) {
        exit(json_encode([
			       "status" => 200,
			       "McID" => $result["McID"],
			       "CompoundCodeID" => $result["CompoundCodeID"],
			       "total_Pallet" => $result["total_Pallet"],
			       "datecreadte" => $result["datecreadte"]
           ]));
		} else {
		    //	exit(json_encode(["status" => 404, "message" => $cure]));
		   exit(json_encode([
			      "status" => 404,
			      "message" => $result["status"],
			      "test1" => $result["test1"],
			      'batch' => ''
		        ]));
		        }
  }

  public function updatestatus($data)
  {
	   $remark = filter_input(INPUT_POST, "remark");
	    $result = (new CompoundAPI)->updatestatus($data,$remark);
      if ($result === 200) {
			     echo json_encode(["status" => 200, "message" => "ดำเนินการเสร็จสิ้น"]);
		  }else {
			     echo json_encode(["status" => 404, "message" => $result['message']]);
		  }
    }
}
