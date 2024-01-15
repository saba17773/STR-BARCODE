<?php

namespace App\Controllers;

use App\Services\RateMasterService;
use App\Components\Database;

class RateMasterController
{
    public function RateGroup()
	{
		echo (new RateMasterService)->RateGroup();
    }
    
	public function bindGridBuild1($group,$buildtype)
	{
        echo (new RateMasterService)->bindGridBuild1($group,$buildtype);
    }

    public function bindGridBuild2($group,$buildtype)
	{
        echo (new RateMasterService)->bindGridBuild2($group,$buildtype);
    }

    public function getMachine($buildtype)
	{
		echo (new RateMasterService)->getMachine($buildtype);
    }

    public function getMachineType($machine)
	{
        $getMachineType = (new RateMasterService)->getMachineType($machine);
		if ($getMachineType["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "TBR"]);
        } 
        else 
		{
			echo json_encode(["status" => 404, "message" => "PCR"]);
		}
    }

    public function insertBuild_Builder($machine)
	{
        
        $qty1 = filter_input(INPUT_POST, "Qty1");
        $qty2 = filter_input(INPUT_POST, "Qty2");
        $qty3 = filter_input(INPUT_POST, "Qty3");
        $ratep1 = filter_input(INPUT_POST, "RatePrice1");
        $ply = filter_input(INPUT_POST, "ply"); //ply
        if($ply === "")
        {
            $ply = 0;
        }

        if (filter_input(INPUT_POST, "RatePrice2") === "")
        {
            $ratep2 = 0;
        } 
        else
        {
            $ratep2 = filter_input(INPUT_POST, "RatePrice2");
        }
        
        $ratep3 = filter_input(INPUT_POST, "RatePrice3");
        $remark = filter_input(INPUT_POST, "remark");
        $createby = $_SESSION["user_login"];
        
        //   $message = $machine. " " . $ply . " " . $qty1 . " " . $qty2 . " " . $qty3 . " " . $ratep1 . " " . $ratep2 . " " . $ratep3 . " " . $remark ;
        $RateType = (new RateMasterService)->getRateType($machine);
        //   exit(json_encode(["status" => 404, "message" => $message]));

        $insertBuild_Builder = (new RateMasterService)->insertBuild_Builder(
            $machine,
            $qty1,
            $qty2,
            $qty3,
            $ratep1,
            $ratep2,
            $ratep3,
            $remark,
            $createby,
            $RateType,
            $ply
        );
		if ($insertBuild_Builder["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "Insert OK"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
		}
    }
    
    public function insertBuild_ChangeCode($machine)
	{
        
        $ratep1 = filter_input(INPUT_POST, "CRatePrice1");
        $cqty1 = filter_input(INPUT_POST, "CQty1");
        $createby = $_SESSION["user_login"];
        $remark = filter_input(INPUT_POST, "Cremark");
        $ply2 = filter_input(INPUT_POST, "ply2");
        
        $RateType = (new RateMasterService)->getRateType($machine);

        $insertBuild_ChangeCode = (new RateMasterService)->insertBuild_ChangeCode($machine,$ratep1,$RateType,$createby,$remark,$cqty1,$ply2);
		if ($insertBuild_ChangeCode["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "Insert OK"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
		}
    }
    
    public function updateBuild_Builder()
	{
        $id = filter_input(INPUT_POST, "Eid");
        $qty1 = filter_input(INPUT_POST, "EQty1");
        $qty2 = filter_input(INPUT_POST, "EQty2");
        $qty3 = filter_input(INPUT_POST, "EQty3");
        $ratep1 = filter_input(INPUT_POST, "ERatePrice1");
        $ratep2 = filter_input(INPUT_POST, "ERatePrice2");
        $ratep3 = filter_input(INPUT_POST, "ERatePrice3");
        $remark = filter_input(INPUT_POST, "Eremark");
        $updateby = $_SESSION["user_login"];
        
        // $message = $machine . " " . $qty1 . " " . $qty2 . " " . $qty3 . " " . $ratep1 . " " . $ratep2 . " " . $ratep3 . " " . $remark ;
        // $RateType = (new RateMasterService)->getRateType($machine);
        //exit(json_encode(["status" => 404, "message" => $RateType]));

        $updateBuild_Builder = (new RateMasterService)->updateBuild_Builder(
            $id,
            $qty1,
            $qty2,
            $qty3,
            $ratep1,
            $ratep2,
            $ratep3,
            $remark,
            $updateby
        );
		if ($updateBuild_Builder["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "Update OK"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
		}
    }

    public function updateBuild_ChangeCode()
	{
        $id = filter_input(INPUT_POST, "ECid");
        $ecqty1 = filter_input(INPUT_POST, "ECQty1");
        $ratep1 = filter_input(INPUT_POST, "ECRatePrice1");
        $remark = filter_input(INPUT_POST, "ECremark");
        $updateby = $_SESSION["user_login"];
        
        // $message = $id . " /" . $ratep1 . " /" . $remark . " /" . $updateby;
        // exit(json_encode(["status" => 404, "message" => $message]));

        $updateBuild_ChangeCode = (new RateMasterService)->updateBuild_ChangeCode(
            $id,
            $ratep1,
            $updateby,
            $remark,
            $ecqty1
        );
		if ($updateBuild_ChangeCode["status"] === 200) 
		{
			echo json_encode(["status" => 200, "message" => "Update OK"]);
		} else 
		{
			echo json_encode(["status" => 404, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
		}
    }

    public function bindGridPLY($machine)
	{
		echo (new RateMasterService)->bindGridPLY($machine);
    }

    public function bindGridPLY2($machine)
	{
		echo (new RateMasterService)->bindGridPLY2($machine);
    }

    public function getMac()
	{
		echo (new RateMasterService)->getMac();
    }

    public function insertCure($line)
	{
        $cureprice = filter_input(INPUT_POST, "CurePrice");
        $createby = $_SESSION["user_login"];

        $curetype = $_POST['curetype'];

		$type = 'TBR';
		if ($curetype === 'TBR') 
		{
			$type = 'TBR';
		}
		else //if ($curetype === 'PCR') 
		{
			$type = 'PCR';
		} 
		// else 
		// {
		// 	$type = 'BIA';
        // }
        // chkMacInsert
        $chkinsert = (new RateMasterService)->chkMacInsert($line);
        if ($chkinsert["status"] === 200) 
        {
            $insertCure = (new RateMasterService)->insertCure($line,$cureprice,$createby,$type);
            if ($insertCure["status"] === 200) 
            {
                echo json_encode(["status" => 200, "message" => "Insert Cure OK"]);
            } else 
            {
                echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
            }
        }
        else
        {
            $updateCure = (new RateMasterService)->updateCure($line,$cureprice,$createby,$type);
            if ($updateCure["status"] === 200) 
            {
                echo json_encode(["status" => 200, "message" => "UpdateCure Cure OK"]);
            } else 
            {
                echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้ !!"]);
            }
        }
    }

    public function updateCureByMachine($machine)
	{

        $cureprice = filter_input(INPUT_POST, "ECurePrice");
        $updateBy = $_SESSION["user_login"];

        $curetype = $_POST['Ecuretype'];

		$type = 'TBR';
		if ($curetype === 'TBR') 
		{
			$type = 'TBR';
		}
		else //if ($curetype === 'PCR') 
		{
			$type = 'PCR';
		} 
		// else 
		// {
		// 	$type = 'BIA';
        // }
      
        $updateCure = (new RateMasterService)->updateCureByMachine($machine,$cureprice,$updateBy,$type);
            if ($updateCure["status"] === 200) 
            {
                echo json_encode(["status" => 200, "message" => "UpdateCure Cure By Machine OK"]);
            } else 
            {
                echo json_encode(["status" => 404, "message" => "ไม่สามารถบันทึกข้อมูลได้"]);
            }
    }

    public function bindGridCure()
	{
        echo (new RateMasterService)->bindGridCure();
    }

    //getPayment
    //RateMaster_V2
    public function getPayment()
	{
		echo (new RateMasterService)->getPayment();
    }

    public function bindGrid_SEQ()
	{
        echo (new RateMasterService)->bindGrid_SEQ();
    }

}