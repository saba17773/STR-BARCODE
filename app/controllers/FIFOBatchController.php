<?php

namespace App\Controllers;

use App\Services\FIFOBatchService;
use App\Components\Database;

class FIFOBatchController
{
	public function all()
	{
		echo (new FIFOBatchService)->all();
	}

	public function insertData($product_grp, $qty_min , $aging_date)
	{   
        $product_grp = $product_grp;
        $qty_min = (int)$qty_min;
        $aging_date = (int)$aging_date;
		$create_new = (new FIFOBatchService)->insertData($product_grp, $qty_min , $aging_date);

		if ($create_new["status"] === 200) {
			echo json_encode(["status" => 200]);
		} else {
			echo json_encode(["status" => 404]);
		}
		
     }
    public function updateData($product_grp, $qty_min , $aging_date , $id)
	{   
        $product_grp = $product_grp;
        $qty_min = (int)$qty_min;
        $aging_date = (int)$aging_date;
        $id = (int)$id;
		$create_new = (new FIFOBatchService)->updateData($product_grp, $qty_min , $aging_date , $id);
		
		if ($create_new["status"] === 200) {
			echo json_encode(["status" => 200]);
		} else {
			echo json_encode(["status" => 404]);
		}
	 }
	 public function chkProductGrp($product_grp)
	{   
        $product_grp = $product_grp;
        
		$chkProductGrp = (new FIFOBatchService)->chkProductGrp($product_grp);
		
		if ($chkProductGrp["status"] === 200) {
			echo json_encode(["status" => 200]);
		} else {
			echo json_encode(["status" => 404]);
		}
	 }
}