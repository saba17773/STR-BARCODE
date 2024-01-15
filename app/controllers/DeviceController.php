<?php

namespace App\Controllers;

use App\Services\DeviceService;

class DeviceController
{
	public function __construct()
	{
		$this->Device = new DeviceService;
	}

	public function allInventTable()
	{
		echo (new DeviceService)->allInventTable();
	}
	public function transDetail($barcode)
	{
		echo (new DeviceService)->transDetail($barcode);
	}

	public function vedorall()
	{
		echo (new DeviceService)->vedorall();
	}

	public function deviceall()
	{
		echo (new DeviceService)->deviceall();
	}

	public function userall()
	{
		echo (new DeviceService)->userall();
	}

	public function saveDeviceTable()
	{

		$add_FixedAssetNo = filter_input(INPUT_POST, "add_FixedAssetNo");
		$add_SN = filter_input(INPUT_POST, "add_SN");
		$add_MacAddr = filter_input(INPUT_POST, "add_MacAddr");
		$add_IpAddr = filter_input(INPUT_POST, "add_IpAddr");
		$add_VendorID = filter_input(INPUT_POST, "add_VendorID");
		$add_ReceiveBy = filter_input(INPUT_POST, "add_ReceiveByID");
		$add_ReceiveDate = filter_input(INPUT_POST, "add_ReceiveDate");
		$add_startWarrantyDate = filter_input(INPUT_POST, "add_startWarrantyDate");
		$add_EndWarrantyDate = filter_input(INPUT_POST, "add_EndWarrantyDate");
		$add_DeviceID = filter_input(INPUT_POST, "add_DeviceID");
		$add_Remark = filter_input(INPUT_POST, "add_Remark");
		$add_PO = filter_input(INPUT_POST, "add_PO");
		$checkTYpeInsert = filter_input(INPUT_POST, "check_datainsert");
		$add_IDupdate = filter_input(INPUT_POST, "add_IDupdate");
		$checkstartdate = strtotime($add_startWarrantyDate);
		$checkenddate = strtotime($add_EndWarrantyDate);
		if($checkstartdate > $checkenddate){
			return  json_encode(["status" => 404, "message" => "วันที่เริ่มต้นมากกว่าวันที่สุดท้าย"]);
		}



		$result = (new DeviceService)->saveDeviceTable(
			$add_FixedAssetNo,
			$add_SN,
			$add_MacAddr,
			$add_IpAddr,
			$add_VendorID,
			$add_ReceiveBy,
			$add_ReceiveDate,
			$add_startWarrantyDate,
			$add_EndWarrantyDate,
			$add_DeviceID,
			$add_Remark,
			$add_PO,
			$checkTYpeInsert,
			$add_IDupdate
		);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}

	public function saveDeviceTabletrans()
	{

		$add_MacAddr_trans = filter_input(INPUT_POST, "add_MacAddr_trans");
		$add_SN_trans = filter_input(INPUT_POST, "add_SN_trans");
		$add_ReceiveDate_trans = filter_input(INPUT_POST, "add_ReceiveDate_trans");
		$add_SVODate_trans = filter_input(INPUT_POST, "add_SVODate_trans");
		$add_ReturnDate_trans = filter_input(INPUT_POST, "add_ReturnDate_trans");
		$add_SendUserdate_trans = filter_input(INPUT_POST, "add_SendUserdate_trans");
		$add_Detail_trans = filter_input(INPUT_POST, "add_Detail_trans");
		$add_Remark_trans = filter_input(INPUT_POST, "add_Remark_trans");
		// $check_datainsert_trans = filter_input(INPUT_POST, "check_datainsert_trans");
		$add_IDupdate_trans = filter_input(INPUT_POST, "add_IDupdate_trans");




		$result = (new DeviceService)->saveDeviceTabletrans(
			$add_MacAddr_trans,
			$add_SN_trans,
			$add_ReceiveDate_trans,
			$add_SVODate_trans,
			$add_ReturnDate_trans,
			$add_SendUserdate_trans,
			$add_Detail_trans,
			$add_Remark_trans,
			// $check_datainsert_trans,
			$add_IDupdate_trans

		);

		if ($result["status"] === 200) {
			echo json_encode(["status" => 200, "message" => $result["message"]]);
		} else {
			echo json_encode(["status" => 404, "message" => $result["message"]]);
		}
	}
}
